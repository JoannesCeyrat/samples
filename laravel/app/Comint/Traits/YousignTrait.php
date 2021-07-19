<?php

namespace App\Comint\Traits;

use App\Mandat;
use App\Comint\yousign\Curler;
use App\Bien;
use Carbon\Carbon;
use App\Reservation;
use Storage;
use Mail;
use App\Comint\BailPDF;
use App\SousLocation;

trait YousignTrait
{
    public function createProcedureFromMandat(Mandat $M)
    {
        $Car= new Carbon();
        $Expire= $Car->addWeeks(2);
        $this->url_code= md5($this->id);
        $this->date_expiration= $Expire->toDateString();
        $this->save();
        /**
         * requete 1 => procedures
         */
        $param_req1= [
            "name"=> "mandat ".$M->ref,
            "description"=> "signature du mandat ref ".$M->ref,
            "start"=> false,
            "expired"=> $Expire->toW3cString(),
            "config"=> [
                "webhook"=> [
                    "procedure.started"=> [
                        [
                            "url"=> secure_asset('proc-started/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                    "procedure.finished"=> [
                        [
                            "url"=> secure_asset('proc-finished/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                    "procedure.refused"=> [
                        [
                            "url"=> secure_asset('proc-refused/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                ]
            ]
        ];
        $C= new Curler;
        $arr_proc= $C->request('procedures', $param_req1, 'POST');
        $this->procedure= $arr_proc;
        $this->save();
        if (empty($arr_proc["id"])) {
            return ["error"=> "erreur requete 1 de procédure signature"];
        }
        /**
         * requete 2 => files
         */
        $arr_files= $C->sendPDF('files', $M->filepath, $M->ref.".pdf", $arr_proc["id"], "signable");
        $this->files= $arr_files;
        $this->save();
        if (empty($arr_files["id"])) {
            return ["error"=> "erreur requete 2 de procédure signature"];
        }
        /**
         * requete 3 => members
         */
        /** aller chercher le proprio */
        $B= Bien::find($M->biens[0]);
        if (empty($B)) {
            return ["error"=> "requete 3, pas trouvé le bien du mandat"];
        }
        $P= $B->proprio;
        $emetteur_type= (new \ReflectionClass($P))->getShortName();
        $tel= $P->checkPortable();
        if (empty($tel)) {
            return ["error"=> "Le propriétaire n'a pas renseigné son portable et ne peut donc pas signer électroniquement"];
        }
        $param_req3= [
            "firstname"=> $P->getPrenom(),
            "lastname"=> $P->getNom(),
            "email"=> $P->email,
            "phone"=> $tel,
            "procedure"=> $arr_proc["id"]
        ];
        $arr_members= $C->request('members', $param_req3, 'POST');
        $this->members= $arr_proc;
        $this->save();
        if (empty($arr_members["id"])) {
            return ["error"=> "erreur requete 3 de procédure signature ".\json_encode($param_req3)];
        }
        /**
         * requete 4-0
         *
         */
        if ($emetteur_type == "Entreprise") {
            $signataire= $P->representant_legal;
        } else {
            $signataire= $P->getPrenom()." ".$P->getNom();
        }
        $param_req4= [
            "file"=> $arr_files["id"],
            "member"=> $arr_members["id"],
            "page"=> $M->num_pages,
            "position"=> $M->pos_signature,
            "mention"=> "Lu et approuvé",
            "mention2"=> "Signé par ".$signataire,
            "reason"=> "Acceptation sans réserve du mandat ".$M->ref
        ];
        $arr_objects= $C->request('file_objects', $param_req4, 'POST');
        $this->file_objects= $arr_objects;
        $this->save();
        if (empty($arr_objects["id"])) {
            return ["error"=> "erreur requete 4 de procédure signature ".\json_encode($param_req4)];
        }
        /**
         * la procédure est complète
         * on lance la procedure et enregistre la date de validation
         */
        $param_req5= [
            "start"=> true
        ];
        $adr= \substr($arr_proc["id"], 1);
        $arr_put= $C->request($adr, $param_req5, 'PUT');
        $this->put= $arr_put;
        $this->save();
        if (empty($arr_put["id"]) || $arr_put["status"] != "active") {
            return ["error"=> "erreur requete 5 de procédure signature"];
        }
        


        return ["ok"=> $this->id];
    }

    public function createProcedureFromReservation(Reservation $R)
    {
        $Ba= $R->getBail();
        $B= $R->bien;
        if (empty($Ba) || empty($B)) {
             return ["error"=> "erreur récupération objets Bail ou Etat pour la réservation ".$R->ref];
        }
        $Car= new Carbon();
        $Expire= $Car->addDay();
        $this->url_code= md5($this->id);
        $this->date_expiration= $Expire->toDateString();
        $this->save();
        $bail_file_path= $Ba->filepath;
        $etat_file_path= storage_path('etats_lieux/'.$B->id.".pdf");

        /**
         * requete 1 => procedures
         */
        $param_req1= [
            "name"=> "bail ".$Ba->resa_ref,
            "description"=> "signature du bail ref ".$Ba->resa_ref,
            "start"=> false,
            "expired"=> $Expire->toW3cString(),
            "config"=> [
                "webhook"=> [
                    "procedure.started"=> [
                        [
                            "url"=> secure_asset('proc-started/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                    "procedure.finished"=> [
                        [
                            "url"=> secure_asset('proc-finished/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                    "procedure.refused"=> [
                        [
                            "url"=> secure_asset('proc-refused/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                ]
            ]
        ];
        $C= new Curler;
        $arr_proc= $C->request('procedures', $param_req1, 'POST');
        $this->procedure= $arr_proc;
        $this->save();
        if (empty($arr_proc["id"])) {
            return ["error"=> "erreur requete 1 de procédure signature"];
        }
        /**
         * requete 2 => files
         */
        $arr_files= $C->sendPDF('files', $bail_file_path, $Ba->resa_ref.".pdf", $arr_proc["id"], "signable");
        $this->files= $arr_files;
        $this->save();
        if (empty($arr_files["id"])) {
            return ["error"=> "erreur requete 2 de procédure signature"];
        }
        /**
         * requete 3 => members
         */
        /** aller chercher le locataire */
        $L= $R->loueur;
        if (empty($L)) {
            return ["error"=> "requete 3, pas trouvé le locataire du bail"];
        }
        $emetteur_type= (new \ReflectionClass($L))->getShortName();
        $tel= $L->checkPortable();
        if (empty($tel)) {
            return ["error"=> "Le propriétaire n'a pas renseigné son portable et ne peut donc pas signer électroniquement"];
        }
        $param_req3= [
            "firstname"=> $L->getPrenom(),
            "lastname"=> $L->getNom(),
            "email"=> $L->email,
            "phone"=> $tel,
            "procedure"=> $arr_proc["id"]
        ];
        $arr_members= $C->request('members', $param_req3, 'POST');
        $this->members= $arr_members;
        $this->save();
        if (empty($arr_members["id"])) {
            return ["error"=> "erreur requete 3 de procédure signature ".\json_encode($param_req3)];
        }
        /**
         * requete 4-0
         *
         */
        if ($emetteur_type == "Entreprise") {
            $signataire= $L->representant_legal;
        } else {
            $signataire= $L->getPrenom()." ".$L->getNom();
        }
        $param_req4= [
            "file"=> $arr_files["id"],
            "member"=> $arr_members["id"],
            "page"=> $Ba->num_pages,
            "position"=> $Ba->pos_signature,
            "mention"=> "Lu et approuvé",
            "mention2"=> "Signé par ".$signataire,
            "reason"=> "Acceptation sans réserve du bail ".$Ba->resa_ref
        ];
        $arr_objects= $C->request('file_objects', $param_req4, 'POST');
        $this->file_objects= $arr_objects;
        $this->save();
        if (empty($arr_objects["id"])) {
            return ["error"=> "erreur requete 4 de procédure signature ".\json_encode($param_req4)];
        }
        /**
         * la procédure est complète
         * on lance la procedure et enregistre la date de validation
         */
        $param_req5= [
            "start"=> true
        ];
        $adr= \substr($arr_proc["id"], 1);
        $arr_put= $C->request($adr, $param_req5, 'PUT');
        $this->put= $arr_put;
        $this->save();
        if (empty($arr_put["id"]) || $arr_put["status"] != "active") {
            return ["error"=> "erreur requete 5 de procédure signature"];
        }
        


        return ["ok"=> $this->id];
    }

    public function createProcedureFromSousLocation(SousLocation $SL)
    {
        $R= $SL->getResa();
        $B= $R->bien;
        if (empty($R) || empty($B)) {
            return [
                "error"=> "erreur createProcedureFromSousLocation récupération objets Bail 
                        ou Resa pour la SousLocation "
                        .$SL->id
            ];
        }
        $Car= new Carbon();
        $Expire= $Car->addDay();
        $this->url_code= md5($this->id);
        $this->date_expiration= $Expire->toDateString();
        $this->save();
        $bail_file_path= $SL->filepath;
        

        /**
         * requete 1 => procedures
         */
        $param_req1= [
            "name"=> "bail ".$SL->resa_ref,
            "description"=> "signature du bail ref ".$SL->resa_ref,
            "start"=> false,
            "expired"=> $Expire->toW3cString(),
            "config"=> [
                "webhook"=> [
                    "procedure.started"=> [
                        [
                            "url"=> secure_asset('proc-started/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                    "procedure.finished"=> [
                        [
                            "url"=> secure_asset('proc-finished/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                    "procedure.refused"=> [
                        [
                            "url"=> secure_asset('proc-refused/'.$this->id),
                            "method"=> "GET"
                        ]
                    ],
                ]
            ]
        ];
        $C= new Curler;
        $arr_proc= $C->request('procedures', $param_req1, 'POST');
        $this->procedure= $arr_proc;
        $this->save();
        if (empty($arr_proc["id"])) {
            return ["error"=> "erreur requete 1 de procédure signature GC propriétaire"];
        }
        /**
         * requete 2 => files
         */
        $arr_files= $C->sendPDF('files', $bail_file_path, $SL->resa_ref.".pdf", $arr_proc["id"], "signable");
        $this->files= $arr_files;
        $this->save();
        if (empty($arr_files["id"])) {
            return ["error"=> "erreur requete 2 de procédure signature GC propriétaire"];
        }
        /**
         * requete 3 => members
         */
        /** aller chercher le Propriétaire */
        $P= $B->proprio;
        if (empty($P)) {
            return ["error"=> "requete 3 signature GC propriétaire, pas trouvé le propriétaire du bail"];
        }
        $emetteur_type= (new \ReflectionClass($P))->getShortName();
        $tel= $P->checkPortable();
        if (empty($tel)) {
            return ["error"=> "Le propriétaire n'a pas renseigné son portable et ne peut donc pas signer électroniquement"];
        }
        $param_req3= [
            "firstname"=> $P->getPrenom(),
            "lastname"=> $P->getNom(),
            "email"=> $P->email,
            "phone"=> $tel,
            "procedure"=> $arr_proc["id"]
        ];
        $arr_members= $C->request('members', $param_req3, 'POST');
        $this->members= $arr_members;
        $this->save();
        if (empty($arr_members["id"])) {
            return ["error"=> "erreur requete 3 de procédure signature GC propriétaire ".\json_encode($param_req3)];
        }
        /**
         * requete 4-0
         *
         */
        if ($emetteur_type == "Entreprise") {
            $signataire= $P->representant_legal;
        } else {
            $signataire= $P->getPrenom()." ".$P->getNom();
        }
        $param_req4= [
            "file"=> $arr_files["id"],
            "member"=> $arr_members["id"],
            "page"=> $SL->num_pages,
            "position"=> $SL->pos_signature,
            "mention"=> "Lu et approuvé",
            "mention2"=> "Signé par ".$signataire,
            "reason"=> "Acceptation sans réserve du bail ".$SL->resa_ref
        ];
        $arr_objects= $C->request('file_objects', $param_req4, 'POST');
        $this->file_objects= $arr_objects;
        $this->save();
        if (empty($arr_objects["id"])) {
            return ["error"=> "erreur requete 4 de procédure signature GC propriétaire".\json_encode($param_req4)];
        }
        /**
         * la procédure est complète
         * on lance la procedure et enregistre la date de validation
         */
        $param_req5= [
            "start"=> true
        ];
        $adr= substr($arr_proc["id"], 1);
        $arr_put= $C->request($adr, $param_req5, 'PUT');
        $this->put= $arr_put;
        $this->save();
        if (empty($arr_put["id"]) || $arr_put["status"] != "active") {
            return ["error"=> "erreur requete 5 de procédure signature GC propriétaire"];
        }
        
        return ["ok"=> $this->id];
    }

    public function getUrlIframe()
    {
        $url= "https://webapp.yousign.com/procedure/sign?members=";
        if (empty($this->checkData())) {
            return null;
        }
        $arr= $this->put;
        return $url.$arr["members"][0]["id"];//."&signatureUi=/signature_uis/492a407b-672a-47d8-9b65-cdd373c3510d";
    }

    public function getPortable()
    {
        if (empty($this->checkData())) {
            return null;
        }
        $arr= $this->put;
        return $arr["members"][0]["phone"];
    }

    /**
     * verifie la présence des datas dans le champ put
     * @param void
     * @return boolean
     */
    public function checkData()
    {
        $arr= $this->put;
        if (empty($arr["status"])
            || $arr["status"] != "active"
            || empty($arr["members"])
            || empty($arr["members"][0])
            || empty($arr["members"][0]["id"])
        ) {
            return false;
        }
        return true;
    }

    /**
     * télécharge un document signé et l'enregistre dans le répertoire ad-hoc
     */
    public function getDocSigne()
    {
        $C= new Curler;
        $url= substr($this->put["files"][0]["id"]."/download?alt=media", 1);
        $data_file= $C->getPDF($url);
        $rep= "";
        $ref= "";
        $E= $this->emetteur;
        if (empty($data_file) || empty($E) || preg_match('/error/i', $data_file)) {
            Mail::raw(
                $data_file,
                function ($message) {
                    $message->from("info@innov-home.fr", "INNOV'HOME");
                    $message->sender("info@innov-home.fr", "INNOV'HOME");
                    $message->to("ceyrat@comint.fr", "webmaster");
                    $message->subject("INNOV'HOME Erreur curl finished");
                }
            );
            return null;
        }
        switch ($this->emetteur_type) {
            case 'App\\Mandat':
                $rep= "mandats";
                $ref= $E->ref;
                break;
            case 'App\\Reservation':
                // si la Resa est de Type CC
                $ref= $E->ref;
                $D= $E->devis;
                if (!empty($D) && $D->type == "GC") {
                    $rep= "baux-gc-entreprises";
                } else {
                    $rep= "baux";
                }
                break;
            case 'App\\SousLocation':
                $rep= "baux-gc-proprios";
                $ref= $E->resa_ref;
                break;
        }
        $file_short_path= $rep.'/'.$ref."-signe.pdf";
        $h= fopen(storage_path($file_short_path), 'w');
        if (!$h) {
            return null;
        }
        fwrite($h, $data_file, strlen($data_file));
        fclose($h);
        return storage_path($file_short_path);
    }

    public function sendStarted()
    {
        $arr_put= $this->put;
        $to= $arr_put["members"][0]["email"];
        if (empty($to)) {
            return null;
        }

        $url_iframe= secure_asset('sign-'.$this->url_code);

        $E= $this->emetteur;
        $G= $E->getGestionnaire();
        
        $sujet= "INNOV-HOME : ".$arr_put["description"];
        $titre= $arr_put["name"];
        $bcc= null;


        /**
         * determiner le template selon le type de document
         */
        switch ((new \ReflectionClass($E))->getShortName()) {
            case "Mandat":
                /**
                  * Mettre à jour mandat_id du bien
                   */
                $E->setBienMandatId();
                $template= "emails.mandats.sign";
                $data=[
                    "mandat_ref"=> $E->ref,
                    'url_iframe'=> $url_iframe,
                    'titre'=> $titre
                ];
                break;

            case "Reservation":
                // reservation à partir d'un Devis GC
                $D= $E->devis;
                $B= $E->bien;
                $src= !empty($B->images[0]) ? secure_asset("images-meubles/vignettes/".$B->images[0]["nom"]) : null;
                
                if (!empty($D) && $D->type == "GC") {
                    $template= "emails.GC.sign-entreprise";
                    $L= $E->loueur;
                    $loyerGC= $E->getLoyerGC();
                    $coll_S= $E->sejours;
                    $arr_dates= [];
                    $L= $E->loueur;
                    foreach ($coll_S as $S) {
                        $arr_dates[]= $S->getDetailBailGC();
                    }
                    $nb_semaines= 0;
                    foreach ($arr_dates as $a) {
                        $nb_semaines+= intval($a['nb_semaines']);
                    }
                    $sujet= "INNOV-HOME : Proposition de location ref ".$E->ref;
                    $data=[
                        "bail_ref"=> $E->ref,
                        'url_iframe'=> $url_iframe,
                        'titre'=> $titre,
                        'src'=> $src,
                        'ref'=> $B->id,
                        'link'=> $B->make_url(),
                        'societe'=> $L->name,
                        'loyerGC'=> $loyerGC,
                        'arr_dates'=> $arr_dates,
                        'coll_P'=> $E->prestations,
                        'nb_semaines'=> $nb_semaines
                    ];
                    break;
                } else { // Reservation ST
                    $template= empty($E->revision) ? "emails.baux.sign" : "emails.baux.signRevision";
                    $data=[
                        "bail_ref"=> $E->ref,
                        'url_iframe'=> $url_iframe,
                        'titre'=> $titre
                    ];
                    break;
                }

            case "SousLocation":
                $template= "emails.GC.sign-proprio";
                $R= $E->getResa();
                $L= $R->loueur;
                $B= $E->bien;
                $src= !empty($B->images[0]) ? secure_asset("images-meubles/vignettes/".$B->images[0]["nom"]) : null;
                $bcc= $G->email;
                $data=[
                    "bail_ref"=> $E->resa_ref,
                    'url_iframe'=> $url_iframe,
                    'titre'=> $titre,
                    'src'=> $src,
                    'ref'=> $B->id,
                    'link'=> $B->make_url(),
                    'societe'=> $L->name,
                ];
                break;

            default:
                $template= "emails.signatures.allerIframe";
                $data=[
                    'url_iframe'=> $url_iframe,
                    'titre'=> $titre
                ];
        }

        Mail::send(
            $template,
            [
            'data'=> $data
            ],
            function ($message) use ($to, $sujet, $bcc) {
                $message->subject($sujet);
                $message->from("info@innov-home.fr", "INNOV-HOME");
                $message->to($to); //$to
                $message->bcc((empty($bcc) ? "info@innov-home.fr" : $bcc));
            }
        );
        return true;
    }


    public function sendFinished()
    {
        $arr_put= $this->put;
        $E= $this->emetteur;
        /**
         * déterminer le Gestionnaire pour copie
         */
        $G= $E->getGestionnaire(); /** methode implémentée pour chaque emetteur dans son trait */
        /**
         * si Emetteur = reservation,
         * et bien en R+
         * envoyer copie au proprio
         */
        /**
         * télécharger le document
         */
        $attachment= $this->getDocSigne();
        if (empty($attachment)) {
            Mail::raw(
                "Attention, webhook finished n'a pas récupéré le document pour la signature ".$this->id,
                function ($message) {
                    $message->from("info@innov-home.fr", "INNOV'HOME");
                    $message->sender("info@innov-home.fr", "INNOV'HOME");
                    $message->to("ceyrat@comint.fr", "webmaster");
                    $message->subject("INNOV'HOME Erreur webhook finished");
                }
            );
            return null;
        }
        $annexe= null;
        $to= $arr_put["members"][0]["email"];
        if (empty($to)) {
            return null;
        }
        $titre= $arr_put["name"];
        $sujet= "INNOV-HOME : ".$titre." signé";
        /**
         * determiner le template selon le type de document
         */
        switch ((new \ReflectionClass($E))->getShortName()) {
            case "Mandat":
                /**
                  * Mettre à jour mandat_id du bien
                   */
                $E->setBienMandatId();
                $template= "emails.mandats.envoi";
                $G= $E->getGestionnaire();
                $data=[
                    "mandat_ref"=> $E->ref,
                    'gestionnaire_identite'=> ucwords($G->firstname)." ".strtoupper($G->lastname),
                    'gestionnaire_tel'=> !empty($G->portable) ? $G->portable : $G->fixe,
                    'gestionnaire_email'=> $G->email,
                ];
                $arr_bcc= [
                    $G->email,
                    "info@innov-home.fr"
                ];
                break;

            case "Reservation":
                $template= "emails.baux.envoi";
                $B= $E->bien;
                $G= $B->get_gestionnaire(true);
                $arr_bcc= [
                    $G->email,
                    "info@innov-home.fr"
                ];
                $D= $E->devis;
                if (!empty($D) && $D->type == "GC") {
                    $template= "emails.GC.confirm-entreprise";
                    $L= $B->loueur;
                    $src= !empty($B->images[0]) ? secure_asset("images-meubles/vignettes/".$B->images[0]["nom"]) : null;
                    $data=[
                        'src'=> $src,
                        'ref'=> $B->id,
                        'link'=> $B->make_url(),
                    ];
                } else {
                    $src= !empty($B->images[0]) ? secure_asset("images-meubles/vignettes/".$B->images[0]["nom"]) : secure_asset("images-meubles/vignettes/no-image.jpg");
                    $data=[
                        "bail_ref"=> $E->ref,
                        "bien_ref"=> $B->id,
                        "bien_link"=> $B->make_url(),
                        'is_cwg'=> ($B->typeContrat_id != 3) ? false : true,
                        'gestionnaire_identite'=> ucwords($G->firstname)." ".strtoupper($G->lastname),
                        'gestionnaire_tel'=> !empty($G->portable) ? $G->portable : $G->fixe,
                        'gestionnaire_email'=> $G->email,
                        "src"=> $src,
                        "alt"=> "Meublé INNOV-HOME ref ".$B->id,
                    ];
                    if ($B->typeContrat_id == 2) {
                        $arr_bcc[]= $B->proprio->email;
                    }
                }
                $annexe= storage_path('etats_lieux/'.$B->id.".pdf");
                if (!\file_exists($annexe)) {
                    $e= BailPDF::makeLieux($E->ref);
                    if (!empty($e["error"])) {
                        Mail::raw(("Erreur creation etat lieux ".$B->id." in file: signatureController function: finished"), function ($message) {
                            $message->from("info@innov-home.fr", "INNOV-HOME");
                            $message->sender("info@innov-home.fr", "INNOV-HOME");
                            $message->to("ceyrat@comint.fr", "webmaster");
                            $message->subject("Erreur creation etat lieux ".$B->id);
                        });
                    }
                }
                break;

            case "SousLocation":
                $template= "emails.GC.confirm-proprio";
                $B= $E->bien;
                $G= $B->get_gestionnaire(true);
                $R= $E->getResa();
                $L= $R->loueur;
                $src= !empty($B->images[0]) ? secure_asset("images-meubles/vignettes/".$B->images[0]["nom"]) : null;
                $arr_bcc= [
                    $G->email,
                    "info@innov-home.fr"
                ];
                $data=[
                    'src'=> $src,
                    'ref'=> $B->id,
                    'link'=> $B->make_url(),
                    'societe'=> $L->name,
                ];
                break;

            default:
                $template= "emails.signatures.docSigne";
                $data=[];
        }
        Mail::send(
            $template,
            [
            'data'=> $data
            ],
            function ($message) use ($to, $sujet, $G, $attachment, $annexe, $arr_bcc) {
                $message->subject($sujet);
                $message->from("info@innov-home.fr", "INNOV-HOME");
                $message->to($to);
                foreach ($arr_bcc as $adr) {
                    $message->bcc($adr);
                }
                $message->attach($attachment);
                if (!empty($annexe)) {
                    $message->attach($annexe);
                }
            }
        );
        return true;
    }

    
}
