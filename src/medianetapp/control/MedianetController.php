<?php


namespace medianetapp\control;
use medianetapp\model\User as user;

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
    public function borrowRecap(){

        $_POST["user"] = 1;
        $user = user::where("id","=",$_POST["user"])->first();
        $borrows = $user->emprunts()->where("real_return_date","=",null)->get();

        $documents = [];

        foreach ($borrows as $borrow){
            $document = $borrow->document()->first();
            $documents[] = $document;
        }
        $vue = new \medianetapp\view\MedianetView(["documents" => $documents] );
        $vue->render('borrow_recap');
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