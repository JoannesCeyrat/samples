<?php

namespace App\Comint\yousign;

use App\Comint\Interfaces\Signature;
use App\Yousign;
use App\Mandat;

class Yousignature implements Signature
{
    /**
     * Cette fonction est appelée lorsque le conseiller décide d'envoyer
     * le mandat pour signature
     * @param Mandat $M or DemandeResa or Devis
     * @return array ['error'=>] or ['ok'=>]
     */
    public function create($Emetteur)
    {
        $S= new Yousign;
        $S->emetteur_id= $Emetteur->id;
        $emetteur_type= (new \ReflectionClass($Emetteur))->getShortName();
        $S->emetteur_type= "App\\".$emetteur_type;
        $S->save();
        if (empty($S)) {
            return ["error"=> "erreur création Signature Model Yousign"];
        }
        if (strtolower($emetteur_type) == "mandat") {
            return $S->createProcedureFromMandat($Emetteur);
        } elseif (strtolower($emetteur_type) == "reservation") {
            return $S->createProcedureFromReservation($Emetteur);
        } elseif (strtolower($emetteur_type) == "souslocation") {
            return $S->createProcedureFromSousLocation($Emetteur);
        } else {
            return ["error"=> "Yousignature->create ni mandat ni résa"];
        }
    }

    public function update()
    {
        return "update";
    }

    /**
     * renvoie un Objet Yousign
     * @param int
     * @return Yousign
     */
    public function find($sid)
    {
        $Y= Yousign::find($sid);
        return empty($Y) ? null : $Y;
    }

    /**
     * Renvoie un Objet Yousign
     * @param string
     * @return Yousign
     */
    public function getByUrlCode($str)
    {
        $Y= Yousign::where('url_code', $str)->first();
        return empty($Y) ? null : $Y;
    }
}
