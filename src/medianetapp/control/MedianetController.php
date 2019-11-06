<?php


namespace medianetapp\control;
use medianetapp\model\User as user;

use medianetapp\model\Borrow;
use medianetapp\model\Document;
use medianetapp\view\MedianetView;

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
        /* VERFIE QUE LES CHAMPS NE SONT PAS VIDES */
        if($_POST["user"] != null && $_POST["reference"] != null){
            $user = User::where("id","=",filter_var($_POST["user"],FILTER_SANITIZE_NUMBER_INT))->first();

            /* VERIFIE QUE L'UTILISATEUR EXISTE SINON RENVOIE SUR LA PAGE AVEC MSG ERREUR */
            if($user === null){
                $vue = new MedianetView(["error_message" => "L'utilisateur n'existe pas"]);
                $vue->render("borrow");
            }
            else{
                /* SI USER EXISTE TRANSFORME LE CHAMP DE REF DE DOC EN TABLEAU, PREPARE UNE COLLECTION DE DOCUMENTS ET UNE COLLECTION DE MESSAGE D'ERREUR POUR SAVOIR QUELLE REF BUG */
                $references = explode(",",$_POST["reference"]);
                $error_reference = [];
                $documents = [];

                /* PARCOURS LES REFERENCES ET SELECT CHAQUE REF DANS LA BASE, SI EXISTE AJOUTE LE DOC DANS LE TABLEAU DOC SINON AJOUTE LA REF AU TABLEAU D'ERREUR */
                foreach ($references as $reference){
                    $reference = filter_var($reference,FILTER_SANITIZE_NUMBER_INT);
                    $document = Document::where("reference", "=", $reference)->first();

                    if($document === null){
                        $error_reference[] = $reference;
                    }
                    else{
                        $documents[] = $document;
                    }
                }

                /* SI TABLEAU ERREUR EST VIDE VERIFIE QUE L'ETAT EST DISPONIBLE SINON AJOUTE LA REF DANS TABLEAU ERREUR */
                if($error_reference === []){
                    foreach ($documents as $document){
                        if($document->id_State != 1){
                            $error_reference[] = $document->reference;
                        }
                    }
                        /* SI TABLEAU ERREUR EST VIDE ENREGISTRE L'EMPRUNT DANS EN BDD ET MODIFIE ETAT DOCUMENT */
                        if($error_reference === []){
                            foreach ($documents as $document){
                                $borrow = new Borrow;

                                $date = date("Y-m-d");
                                $return_date = date("Y-m-d",strtotime($date."+7 day"));

                                $borrow->id_User = filter_var($_POST["user"],FILTER_SANITIZE_NUMBER_INT);
                                $borrow->id_Document = $document->id;
                                $borrow->borrow_date = $date;
                                $borrow->return_date = $return_date;

                                $borrow->save();

                                $document->id_State=2;
                                $document->update();
                            }
                        }else{
                            /* SI DOCUMENT INDISPONIBLE */
                            $message_erreur = $this->errorMessage("Ajout d'emprunt échoué, la ou les référence(s) :",$error_reference);
                            $message_erreur .= " est/sont indisponible.";

                            $vue = new MedianetView(["error_message" => $message_erreur]);
                            $vue->render("borrow");
                        }


                }
                else{
                    /* SI DOCUMENT EXISTE PAS */
                    $message_erreur = $this->errorMessage("Ajout d'emprunt échoué, la ou les référence(s)",$error_reference);
                    $message_erreur .= " n'éxiste(s) pas.";

                    $vue = new MedianetView(["error_message" => $message_erreur]);
                    $vue->render("borrow");
                }
            }
        }
        else{
            /* SI FORMULAIRE NON REMPLI */
            $vue = new MedianetView(["error_message" => "Veuillez saisir l'id de l'utilisateur et l'id du/des document(s)"]);
            $vue->render("borrow");
        }
    }

    /* PERMET DE CREE UN MESSAGE D'ERREUR ET D'AFFICHER LES SOURCES DE L'ERREUR */
    private function errorMessage($startMessage, $error_target = []){
        $message_erreur = " ".$startMessage." ";

        if($error_target != []){
            foreach ($error_target as $target){
                $message_erreur .= $target.",";
            }
            $message_erreur = substr($message_erreur, 0, -1);
        }

        return $message_erreur;
    }
}