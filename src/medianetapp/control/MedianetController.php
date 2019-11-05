<?php


namespace medianetapp\control;
use medianetapp\model\User as user;

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

        if($_POST["user"] != null && $_POST["documents"] != null){
            $user = User::where("id","=",$_POST["user"])->first();

            if($user === null){
                $vue = new MedianetView(["error_message" => "L'utilisateur n'existe pas"]);
                $vue->render("borrow");
            }
            else{
                $references = explode(",",$_POST["documents"]);
                $error_reference = [];


                foreach ($references as $reference){
                    $document = Document::where("reference", "=", $reference)->first();

                    if($document === null){
                        $error_reference[] = $reference;
                    }
                }

                if($error_reference === []){
                    echo "ok";
                }
                else{
                    $message_erreur = "Ajout d'emprunt échoué, la ou les référence(s) : ";

                    foreach ($error_reference as $reference){
                        $message_erreur .= $reference.",";
                    }

                   $message_erreur = substr($message_erreur, 0, -1);
                    $message_erreur .= " n'éxiste(s) pas.";

                    $vue = new MedianetView(["error_message" => $message_erreur]);
                    $vue->render("borrow");
                }
            }
        }
        else{
            $vue = new MedianetView(["error_message" => "Veuillez saisir l'id de l'utilisateur et l'id du/des document(s)"]);
            $vue->render("borrow");
        }




    }
}