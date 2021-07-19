<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Comint\yousign\Curler;
use App\Mandat;
use App\Yousign;
use App\Comint\Interfaces\Signature;
use Illuminate\Support\Facades\Auth;
use App\Comint\TabBord;
use App\TypeBien;
use App\TypeContrat;
use Mail;
use App\Comint\BailPDF;

class SignatureController extends Controller
{
    public function firstCall(Curler $Cr)
    {
        $M= Mandat::find(25);
        if (empty($M)) {
            return "pas de mandat";
        }
        return $Cr->sendPDF('files', $M->filepath, $M->ref.".pdf");
    }

    /**
     * https://staging-app.yousign.com/procedure/sign?members=%2Fmembers%2F07a21e73-678b-4f00-94b8-50e930aadc09&signatureUi=%2Fsignature_uis%2F492a407b-672a-47d8-9b65-cdd373c3510d
     * get members du champ put
     * send email IH de lien vers page sigature avec Iframe
     * téléchagement du document signé
     * envoi des emails finish avec le doc signé joint
     */
    public function webhookListener($event, $sid, Request $request, Signature $S)
    {
        $headers= $request->header('User-Agent');
        //mail('ceyrat@comint.fr', 'header yousign', $headers);
        if (isset($headers) && trim(mb_strtolower($headers)) == "yousign webhook bot") { // && trim(mb_strtolower($headers)) == "yousign webhook bot"
            // $Y= Yousign::find($sid);
            $Y= $S->find($sid);
            if (empty($Y)) {
                //mail('ceyrat@comint.fr', 'souci webhhook', $request->query());
                Mail::raw($request->query(), function ($message) {
                    $message->from("info@innov-home.fr", "INNOV'HOME");
                    $message->sender("info@innov-home.fr", "INNOV'HOME");
                    $message->to("ceyrat@comint.fr", "webmaster");
                    $message->subject("souci webhhook signature");
                });

                return response("error", 500)->header('Content-Type', 'text/plain');
            }
            $Y->$event= true;
            $Y->save();
           
            /**
             * appel de la fonction qui :
             * - envoie le bon mail
             * - ou lance la création de la signature de la SousLocation
             */
            if ($Y->checkData()) {
                // nom de la classe emettrice
                $E= $Y->emetteur;
                $emetteurClassName= (new \ReflectionClass($E))->getShortName();
                // si la signature a pour Emetteur une Reservation
                if ($emetteurClassName == "Reservation" && $event == "finished") {
                    $D= $E->devis;
                    // Si c'est un devis GC
                    if (!empty($D) && $D->type == "GC") {
                        // valider la confirmation du devis
                        $D->accepte= 1;
                        $D->save();
                        if ($D->send_contrat) {
                            // lancer la création de la signature du propriétaire
                            $R= $D->resas->first();
                            $B= $R->bien;
                            $Locataire= $R->loueur;
                            $arr_v2P= $D->makePdfGC($B, $Locataire, 'gcProprioV1');
                            if (empty($arr_v2P["pdfPath"]) || empty($arr_v2P["SL"])) {
                                // alerter webmaster
                                Mail::raw(
                                    (
                                        "Erreur création PDF Sous Location ".$D->id
                                    ." au retour signature entreprise Grand Compte ".json_encode($arr_v2P)
                                    ),
                                    function ($message) {
                                        $message->from("info@innov-home.fr", "INNOV-HOME");
                                        $message->sender("info@innov-home.fr", "INNOV-HOME");
                                        $message->to("ceyrat@comint.fr", "webmaster");
                                        $message->subject("Erreur #1 retour signature Grand Compte");
                                    }
                                );
                                // arreter ici car pas complet
                                return response("ok", 200)->header('Content-Type', 'text/plain');
                            }
                            // la SousLocation est créé par makePdfGC
                            $SL= $arr_v2P["SL"];
                            // créer la signature de la SousLocation
                            $arr_vs= $SL->createSignature($S);
                            if (!empty($arr_vs["error"])) {
                                // alerter webmaster
                                Mail::raw(
                                    (
                                        "Erreur création Signature Sous Location ".$SL->id
                                        ." au retour signature entreprise Grand Compte ".json_encode($arr_vs)
                                    ),
                                    function ($message) {
                                        $message->from("info@innov-home.fr", "INNOV-HOME");
                                        $message->sender("info@innov-home.fr", "INNOV-HOME");
                                        $message->to("ceyrat@comint.fr", "webmaster");
                                        $message->subject("Erreur #1 retour signature Grand Compte");
                                    }
                                );
                            }
                            // l'email de lien pour signature est envoyé à la reception du webhook started
                        }
                    }
                    // on envoie le mail aussi bien pour une résa ST que GC
                    $f= $event."Email";
                } else {
                    $f= $event."Email";
                }
                call_user_func(array($this, $f), $Y);
            }
            /**
             * retour serveur appelant
             */
            return response("ok", 200)->header('Content-Type', 'text/plain');
        }
    }


    /**
     * Prend en charge l'affichage de la page avec Iframe pour signature
     * Est appelée avec url_code en paramètre
     */
    public function displayIframe($url_code, Signature $S)
    {
        $Y= $S->getByUrlCode($url_code);
        if (empty($Y)) {
            return redirect()->route('home');
        }
        $url_iframe= $Y->getUrlIframe();
        if (empty($url_iframe)) {
            return redirect()->route('home');
        }
        $portable= $Y->getPortable();
        $mTabBord = new TabBord;
        $tab_resas_glob= [[]];
        $communecp= [];
        $typesBien= $mTabBord->makeList(TypeBien::all(), 'type');
        $contratsBien= $mTabBord->makeList(TypeContrat::all(), 'contrat');
        $meta_desc= "INNOV-HOME a mis en place la signature éléctronique pour tous ses documents, baux et mandats afin de garantir à chacun un cadre légal.";
        $E= $Y->emetteur;
        $document_type= "";
        $document_ref= "";
        switch ((new \ReflectionClass($E))->getShortName()) {
            case "Devis":
                $document= [
                    "type"=> "devis",
                    "ref"=> "D".$E->id
                ];
                break;
            case "Mandat":
                $document= [
                    "type"=> "mandat",
                    "ref"=> $E->ref
                ];
                break;
            case "Reservation":
                $document= [
                    "type"=> "bail",
                    "ref"=> $E->getBail()->resa_ref
                ];
                break;
            case "SousLocation":
                $document= [
                    "type"=> "bail",
                    "ref"=> $E->resa_ref
                ];
                break;
        }
        return view('signatures.index', [
            'authUser'=> Auth::user(),
            'isEdit'=> 0,
            'title'=> "Signature électronique INNOV-HOME",
            'merci'=> 0,
            'meta_desc'=> $meta_desc,
            'json_biens_resas'=> json_encode($tab_resas_glob) ,
            'typesBien'=> $typesBien,
            'contratsBien'=> $contratsBien,
            'url_iframe'=> $url_iframe,
            'document'=> $document,
            'portable'=> empty($portable) ? "" : "au ".$portable
        ]);
    }

    /**
     * Envoie l'email avec l'adresse de la page de signature
     * @param Yousign object
     * @return void
     */
    public function startedEmail(Yousign $Y)
    {
        $Y->sendStarted();
    }

    /**
     * Enregistre le document signé et l'envoi aux destinataires
     * @param Yousign object
     * @return void
     */
    public function finishedEmail(Yousign $Y)
    {
        $Y->sendFinished();
    }
}
