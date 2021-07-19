<?php

namespace App\Comint\yousign;

class Curler
{

    const KEY = 'hidden';
    public $adr;
    private $options;
    private $fh;

    public function __construct()
    {
        //$this->fh= fopen(storage_path('curler.txt'), "w");

        $this->adr= 'https://api.yousign.com/';
        $this->options = [
                CURLOPT_RETURNTRANSFER => true,         // return web page
                CURLOPT_FOLLOWLOCATION => false,         // follow redirects
                CURLOPT_ENCODING       => "UTF-8",           // handle all encodings
                CURLOPT_AUTOREFERER    => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,         // set referer on redirect
                //CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
                //CURLOPT_TIMEOUT        => 120,          // timeout on response
                CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects    
                CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
                CURLOPT_SSL_VERIFYPEER => false,        //
                CURLOPT_VERBOSE        => true,
                CURLOPT_FRESH_CONNECT => true,
                CURLOPT_COOKIE  => "",
                CURLINFO_HEADER_OUT => true,
                CURLOPT_HTTPHEADER=> [
                    "Authorization: Bearer ".self::KEY,
            
                    'Content-Type: application/json'
                ],
        ];

    }

    /**
    * Cette fonction renvoie l'array de retour
    * Si erreur, il y aura la clé error dans l'array de retour
    *
    *@param string $url à requeter, json $data
    *@param array of parameters of the request
    *@param string method of the request
    *@return array => json décodé de la réponse
    *
    **/
    public function request($url, $data = [], $method = "POST", $verbose = false)
    {
        if (empty($url)) {
            return ["error"=> "Url de requete vide, merci de préciser"];
        }
        /**
         * init et création curler $ch
         */
        $ch = curl_init($this->adr . $url);
        if (empty($ch)) {
            return ["error"=>"Curl pas initialisé"];
        }
        /**
         * init du mouchard
         */
        if ($verbose) {
            $fh= fopen(storage_path('curler.txt'), "w");
        }
        /**
         * passage des options & entetes
         */
        curl_setopt_array($ch, $this->options);
        /**
         *  passage de json de paramètres
         * */
        if (!empty($data)) {
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                json_encode($data, JSON_UNESCAPED_SLASHES)
            );
            //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
        }
        /**
         *  precision de la methode get ou post
         * */
        if ($method == "GET") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); // i am sending get data
        } elseif ($method == "PUT") {
            //curl_setopt($ch, CURLOPT_PUT, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        } elseif ($method == "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        } else {
            //curl_setopt($ch, CURLOPT_HTTPGET, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // i am sending post data
            //curl_setopt($ch, CURLOPT_PUT, 0);
        }
        if ($verbose) {
            curl_setopt($ch, CURLOPT_STDERR, $fh);
            curl_setopt($ch, CURLOPT_FILE, $fh);
        }
        /**
         * execution de la requete curl
         * **/
        // set sortie sur fichier hand $fh

        $tab_return = json_decode(curl_exec($ch), true);
        /**
         * retour des erreurs
         */
        if (curl_errno($ch)) {
            return ['error'=> curl_error($ch)];
        }
        if (empty($tab_return)) {
            return ["error"=> curl_getinfo($ch)];
        }
        /**
         * fermeture du curl
         */
        curl_close($ch);
        if ($verbose) {
            fclose($fh);
        }
        
        return $tab_return;
    }


    /**
    * Cette fonction envoie le PDF à signer
    * Si erreur, il y aura la clé error dans l'array de retour
    *
    *@param string $url à requeter
    *@param string path of the file
    * @param string name of the file => pdf ref
    *@return array => json décodé de la réponse
    *
    **/
    public function sendPDF($url, $path, $name, $proc_id, $type)
    {
        /**
         * interception des mauvais paramètres
         */
        if (empty($url)) {
            return ["error"=> "Url de requete vide, merci de préciser"];
        } elseif (empty($path)) {
            return ["error"=> "Merci de préciser le chemin du fichier"];
        } elseif (empty($name)) {
            return ["error"=> "Merci de préciser la ref du fichier"];
        } elseif (empty($proc_id)) {
            return ["error"=> "Merci de préciser la ref de la procedure"];
        }
        /**
         * init et création curler $ch
         */
        $ch = curl_init($this->adr . $url);
        if (empty($ch)) {
            return ["error"=>"Curl pas initialisé"];
        }
        /**
         * passage des paramètres options & entetes Curl
         */
        curl_setopt_array($ch, $this->options);
        /**
         * envoie en post
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        /**
         * initialiser le POSTFIELDS
         */
        $data = file_get_contents($path);
        $post_array= [
            'name' => $name,
            'content' => base64_encode($data),
            'procedure'=> $proc_id,
            'type'=> $type
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_array));
        /**
         * envoyer la requete
         */
        $tab_return= json_decode(curl_exec($ch), true);
        /**
         * retour des erreurs
         */
        if (curl_errno($ch)) {
            return ['error'=> curl_error($ch)];
        }
        if (empty($tab_return)) {
            return ["error"=> curl_getinfo($ch)];
        }
        /**
         * fermeture du curl
         */
        curl_close($ch);

        return $tab_return;


    }

    /**
    * Cette fonction envoie le PDF signé
    * Si erreur, la string de retour contien err
    *
    * @param string name of the file => pdf ref
    *@return string => reponse
    *
    **/
    public function getPDF($name)
    {
        /**
         * interception des mauvais paramètres
         */
        if (empty($name)) {
            return ["error"=> "Merci de préciser la ref du fichier"];
        }
        /**
         * init et création curler $ch
         */
        $ch = curl_init($this->adr .$name);
        if (empty($ch)) {
            return ["error"=>"Curl pas initialisé"];
        }
        /**
         * passage des paramètres options & entetes Curl
         */
        curl_setopt_array($ch, $this->options);
        /**
         * envoie en get
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return "cURL Error ".curl_error($ch);
        }
        curl_close($ch);

        return $response;
    }
}
