<?php


namespace medianetapp\model;


class Borrow extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'Borrow';  /* le nom de la table */
    protected $primaryKey = '';     /* le nom de la clé primaire */
    public    $timestamps = false;    /* si vrai la table doit contenir
                                      les deux colonnes updated_at,
                                      created_a*/

}