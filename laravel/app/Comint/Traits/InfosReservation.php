<?php

namespace App\Comint\Traits;

use Carbon\Carbon;
use App\Comint\mClasses\HelpersReservation;

trait InfosReservation
{
    /**
     * @param string ref
     * @return array
     */
    public function getInfos($ref_bail, $coll_R = null)
    {
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_ALL, 'fr_FR');

        $infos=[];
        $tot_sejours= 0;
        $Car_init= null;
        $first_R= true;

        if (empty($coll_R)) {
            $coll_R= $this::where('ref', $ref_bail)
                        ->orderBy('annee', 'asc')
                        ->orderBy('semaine', 'asc')
                        ->get();
        }


        /** determiner les séjours */
        //$Car_init= $coll_R->first()->getCarInit();
        foreach ($coll_R as $R) {
            $First_nuit= $R->dateResas->first();
            $Last_nuit= $R->dateResas->last();
            $loyer= null;
            if (!empty($R->echeanciers->count())) {
                $E= $R->echeanciers->first();
                $loyer= [
                    "montant"=> $E->montant,
                    "regle"=> $E->regle,
                    "erreur"=> $E->erreur_bank,
                    "methode"=> $E->methode,
                ];
            }
            /** première semaine traitée */
            if ($first_R) {
                $Car_init= $R->getCarInit();
                $infos[$tot_sejours]= [
                    //"titre"=> "Séjour 1",
                    "action"=> "none", // can be : edit, none, suppr
                    "du"=> utf8_encode(strftime("%A %d %B %Y", $R->getRealFirstNuit())),
                    "au"=> utf8_encode(strftime("%A %d %B %Y", ($R->getRealLastNuit() + 10800))),
                    "duUS"=>  date("Y-m-d", $R->getRealFirstNuit()),
                    "auUS"=>  date("Y-m-d", ($R->getRealLastNuit() + 10800)),
                    "detail"=> [[
                        "annee"=> $R->annee,
                        "semaine"=> $R->semaine,
                        "nb_pers"=> $R->nbpers,
                        "loyer"=> $loyer
                    ]],
                    "server"=> true,
                    "id"=> $tot_sejours,
                ];
                $first_R= false;
            } else { /** semaines suivantes */
                $Car_init->addWeek();
                $Car_ec= Carbon::createFromTimestamp($Last_nuit->nuit);
                if ($Car_init->weekOfYear == $Car_ec->weekOfYear) { /** même séjour */
                    $infos[$tot_sejours]["detail"][]= [
                        "annee"=> $R->annee,
                        "semaine"=> $R->semaine,
                        "nb_pers"=> $R->nbpers,
                        "loyer"=> $loyer
                    ];
                    $infos[$tot_sejours]["au"]= utf8_encode(strftime("%A %d %B %Y", ($R->getRealLastNuit() + 10800)));
                } else {  /** nouveau séjour */
                    $tot_sejours++;
                    $infos[$tot_sejours]= [
                        //"titre"=> "Séjour ".(1 + $tot_sejours),
                        "action"=> "none", // can be : edit, none, suppr

                        "du"=> utf8_encode(strftime("%A %d %B %Y", $R->getRealFirstNuit())),
                        "au"=> utf8_encode(strftime("%A %d %B %Y", ($R->getRealLastNuit() + 10800))),
                        "duUS"=>  date("Y-m-d", $R->getRealFirstNuit()),
                        "auUS"=>  date("Y-m-d", ($R->getRealLastNuit() + 10800)),
                        "detail"=> [[
                            "annee"=> $R->annee,
                            "semaine"=> $R->semaine,
                            "nb_pers"=> $R->nbpers,
                            "loyer"=> $loyer
                        ]],
                        "server"=> true,
                        "id"=> $tot_sejours,
                    ];
                }
            }
        }
        return $infos;
    }

    /**
     * @param void
     * @return Object Carbon
     */
    public function getCarInit()
    {
        $N= $this->dateResas->slice(1, 1)->first();
        return Carbon::createFromTimestamp($N->nuit);
    }

    /**
     * @param void
     * @return string
     */
    public function getInfosAirBnB($coll_R)
    {
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_ALL, 'fr_FR');

        $R= $coll_R->first();
        $B= $R->bien;
        if (!isset($R->dataBank["SUMMARY"])) {
            return "pas de sommaire";
        }
        $arr_du= explode("-", $R->dataBank["START"]);
        $arr_au= explode("-", $R->dataBank["END"]);
        $description= nl2br($R->dataBank["DESCRIPTION"]);
        $arr_detail= [];
        foreach ($coll_R as $R) {
            $arr_detail[]= [
                "annee"=> $R->annee,
                "semaine"=> $R->semaine,
            ];
        }
        $arr_infos= [
            "titre"=> "Séjour 1",
            "du"=> utf8_encode(strftime("%A %d %B %Y", mktime(17, 0, 0, $arr_du[1], $arr_du[2], $arr_du[0]))),
            "au"=> utf8_encode(strftime("%A %d %B %Y", mktime(10, 0, 0, $arr_au[1], $arr_au[2], $arr_au[0]))),
        ];


       
        $detail= "<ul>";
        foreach ($arr_detail as $d) {
            $detail.= "<li>semaine ".$d["semaine"]."</li>";
        }
        $detail.= "</ul>";
            
        $infosResa= "<span>
            <b>".$arr_infos["titre"]."</b><br>
            Du ".$arr_infos["du"]."<br>
            Au ".$arr_infos["au"]."<br>
            ".$detail."
        </span>";
        
        /** synthèse détail de la résa */
        $infos_loc= '<span">bien : '.$B->id.' - '.$B->communes->commune
            .' - '.$B->communes->departements->codeDEPT
            .' <br>'.$description
            .'<br><br>'
            .$infosResa
            .'</span>';
        return $infos_loc;
    }


    public function getInfosSansDatesResa($ref_bail, $coll_R = null)
    {
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_ALL, 'fr_FR');
        $H= new HelpersReservation;

        $infos=[];

        if (empty($coll_R)) {
            $coll_R= $this::where('ref', $ref_bail)
                        ->orderBy('annee', 'asc')
                        ->orderBy('semaine', 'asc')
                        ->get();
        }
        // array de séjours qui sont des array de Reservations
        $arr_sejours= $H->getSejours($coll_R);
        //mail('ceyrat@comint.fr', 'sejours', \json_encode($arr_sejours));
        for ($i=0; $i<count($arr_sejours); $i++) {
            $infos[$i]= [
                "action"=> "none", // can be : edit, none, suppr
                "du"=> utf8_encode(strftime("%A %d %B %Y", $H->getFirstTimestamp($arr_sejours[$i][0]))),
                "au"=> utf8_encode(strftime("%A %d %B %Y", $H->getLastTimestamp($arr_sejours[$i][(count($arr_sejours[$i]) -1 )]))),
                "duUS"=>  date("Y-m-d", $H->getFirstTimestamp($arr_sejours[$i][0])),
                "auUS"=>  date("Y-m-d", $H->getLastTimestamp($arr_sejours[$i][(count($arr_sejours[$i]) - 1)])),
                "detail"=> [], // rempli ensuite pour chaque R
                "server"=> true,
                "id"=> $i,
            ];
            // enrichir $arr_sejours
            foreach ($arr_sejours[$i] as $R) {
                $loyer= null;
                if (!empty($R->echeanciers->count())) {
                    $E= $R->echeanciers->first();
                    $loyer= [
                        "montant"=> $E->montant,
                        "regle"=> $E->regle,
                        "erreur"=> $E->erreur_bank,
                        "methode"=> $E->methode,
                        "lid"=> $E->id,
                        "monetico"=> $E->facture_intitule
                    ];
                }
                $infos[$i]["detail"][]= [
                    "annee"=> $R->annee,
                    "semaine"=> $R->semaine,
                    "nb_pers"=> $R->nbpers,
                    "loyer"=> $loyer
                ];
            }
        }
        return $infos;
    }

    public function getInfosFromSejours($coll_Se)
    {
        date_default_timezone_set('Europe/Paris');
        setlocale(LC_ALL, 'fr_FR');
        $H= new HelpersReservation;

        $infos=[];
        $i= 0;

        foreach ($coll_Se as $Se) {
            $Car_du= new Carbon($Se->start);
            $Car_au= new Carbon($Se->end);

            $infos[$i]= [
                "action"=> "none", // can be : edit, none, suppr
                "du"=> utf8_encode(strftime("%A %d %B %Y", $Car_du->getTimestamp())),
                "au"=> utf8_encode(strftime("%A %d %B %Y", $Car_au->startOfDay()->getTimestamp())),
                "duUS"=>  $Se->start,
                "auUS"=>  $Se->end,
                "detail"=> [], // rempli ensuite pour chaque R
                "server"=> true,
                "id"=> $i,
            ];
            // enrichir $arr_sejours
            $coll_R= $this::whereIn('id', $Se->resas_id)
                ->orderBy('annee', 'asc')
                ->orderBy('semaine', 'asc')
                ->get();
            foreach ($coll_R as $R) {
                $loyer= null;
                if (!empty($R->echeanciers->count())) {
                    $E= $R->echeanciers->first();
                    $loyer= [
                        "montant"=> $E->montant,
                        "regle"=> $E->regle,
                        "erreur"=> $E->erreur_bank,
                        "methode"=> $E->methode,
                        "lid"=> $E->id,
                        "monetico"=> $E->facture_intitule
                    ];
                }
                $infos[$i]["detail"][]= [
                    "annee"=> $R->annee,
                    "semaine"=> $R->semaine,
                    "nb_pers"=> $R->nbpers,
                    "loyer"=> $loyer
                ];
            }
            // incrémenter $i
            $i++;
        }
        return $infos;
    }
}
