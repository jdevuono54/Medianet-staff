<?php


namespace medianetapp\control;


use medianetapp\model\User;
use medianetapp\view\MedianetView;
use mf\auth\exception\AuthentificationException;

class MedianetController extends \mf\control\AbstractController
{
    public function __construct(){
        parent::__construct();
    }

    public function viewHome(){

    }

    public function viewBorrow(){
        $vue = new MedianetView(null);
        $vue->render("borrow");
    }

    public function add_borrow(){
        $vue = new MedianetView(null);

        $user = User::where("id","=",$_POST["user"])->first();

        if($user === null){
            $vue->render("borrow");
            throw new AuthentificationException("L'utilisateur n'existe pas");
        }

        $documents = explode(",",$_POST["documents"]);

        print_r($documents);
    }
}