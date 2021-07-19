<?php

namespace App\Comint\Interfaces;

interface Signature
{
    public function create($Emetteur);
    public function update();
    public function find($int);
    public function getByUrlCode($str);
}
