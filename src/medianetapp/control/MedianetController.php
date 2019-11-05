<?php


namespace medianetapp\control;


use medianetapp\model\Borrow;
use medianetapp\model\Document;
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

        if($_POST["user"] != null && $_POST["documents"] != null){
            $user = User::where("id","=",$_POST["user"])->first();

            if($user === null){
                $vue = new MedianetView(["error_message" => "L'utilisateur n'existe pas"]);
                $vue->render("borrow");
            }
            else{
                $references = explode(",",$_POST["documents"]);


                foreach ($references as $reference){
                    $document = Document::where("reference", "=", $reference)->first();

                    if($document === null){
                        $vue = new MedianetView(["error_message" => "Ajout d'emprunt échoué, la référence ".$reference." n'existe pas."]);
                        $vue->render("borrow");
                    }
                }
            }
        }
        else{
            $vue = new MedianetView(["error_message" => "Veuillez saisir l'id de l'utilisateur et l'id du/des document(s)"]);
            $vue->render("borrow");
        }




    }
}