<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
use App\Comint\Traits\YousignTrait;

class Yousign extends Model
{
    protected $table = 'yousign';
    public $timestamps = true;
    protected $fillable = [
        "emetteur_id",
        "emetteur_type",
        "procedure",
        "files",
        "members",
        "file_objects",
        "file_images",
        "put",
        "started",
        "finished",
        "refused",
        "date_expriration",
        "url_code"
    ];
    /**
    * cast JSON
    **/
    protected $casts = [
        'procedure'=> 'array',
        'files'=> 'array',
        'members'=> 'array',
        'file_objects'=> 'array',
        'file_images'=> 'array',
        'put'=> 'array'
    ];
    use YousignTrait;

    /**
     *
     * Eloquent
     *
     */
    public function emetteur()
    {
        return $this->morphTo();
    }
}
