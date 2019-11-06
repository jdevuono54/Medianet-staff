<?php

namespace medianetapp\model;

class User extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'User';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public    $timestamps = false;    /* si vrai la table doit contenir
                                      les deux colonnes updated_at,
                                      created_at */
    
    public function Emprunts(){
        return $this->hasMany('medianetapp\model\Borrow','id_User');
    }
}