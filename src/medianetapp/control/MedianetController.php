<?php


namespace medianetapp\control;
use medianetapp\model\User as user;

use medianetapp\model\Borrow;
use medianetapp\model\Document;
use medianetapp\view\MedianetView;
use mf\router\Router;


class MedianetController extends \mf\control\AbstractController
{
    public function __construct(){
        parent::__construct();
    }

    public function viewHome(){
        $vue = new MedianetView(null);
        $vue->render("home");
    }

    public function viewBorrow(){
        $vue = new MedianetView(null);
        $vue->render("borrow");
    }

    public function viewUser(){
        if(isset($_GET["id"]) && filter_var($_GET["id"],FILTER_VALIDATE_INT)){
            $user = User::where("id","=",$_GET["id"])->first();
            if($user != null){
                $vue = new MedianetView($user);
                $vue->render("user");
            }
            else{
                $vue = new MedianetView(null);
                $vue->render("user");
            }
        }
        else{
            $vue = new MedianetView(null);
            $vue->render("user");
        }
    }

    public function borrowRecap(){
        if(isset($_POST["user"])){
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
        else{
            Router::executeRoute("borrow");
        }
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
                            Router::executeRoute("borrow_recap");
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
    /*
     * Display the return view
     */
    public function viewReturn(){
        $view = new MedianetView(null);
        $view->render("return");
    }

        public function addReturn(){
        if(isset($_REQUEST['txtUser'])&&isset($_REQUEST['txtDoc'])){

            /*Filtred values*/
            $filtredTxtUser=strip_tags(trim($_REQUEST['txtUser']));
            $filtredTxtDoc=strip_tags(trim($_REQUEST['txtDoc']));

            //var for getting the the returned documents
            $returnedDocuments = [];

            //var for getting the unreturned documents
            $unreturnedDocuments=[];

            //Explode the input into a var
            $documents = explode(',',$filtredTxtDoc);

            //Error if the user is not valid
            if(!Borrow::where('id_User','=',$filtredTxtUser)->first()){

                //Redirect to return view because of the not valid user
                $view = new MedianetView(["error_message"=>"Veuillez verifier l'id de l'usager"]);
                $view->render("return");
            }
            else{
                foreach ($documents as $document){
                    //Get the document id to get the exact borrow and update the state of the document
                    $doc = Document::where('reference','=',$document)->first();

                    $borrow = Borrow::where('id_User','=',$filtredTxtUser)->where('id_Document','=',$doc->id)->first();
                    if(!$borrow){

                        //Redirect to return view because of the not valid document
                        $view = new MedianetView(["error_message"=>"Veuillez verifier l'id du document"]);
                        $view->render("return");
                        return;
                    }
                    else{

                        //Update the real return date
                        $borrow->real_return_date=date("Y-m-d");
                        $borrow->save();

                        //Update the document state
                        $docToUpdate = Document::where('id','=',$doc->id)->first();
                        $docToUpdate->id_State=1;
                        $docToUpdate->save();

                        //Fill the var with the returned documents
                        $returnedDocuments[] = $docToUpdate;

                    }
                }

                //Get the unreturned documents
                $unreturnedDocuments=$this->GetUnreturnedDocuments($filtredTxtUser);

                //Call the recap view
                $view = new MedianetView(["returnedDocuments"=>$returnedDocuments,"unreturnedDocuments"=>$unreturnedDocuments]);
                $view->render("return_recap");
            }

        }
    }

        /*
         * function thath return the unretuned documents
         */
        private function GetUnreturnedDocuments($iduser){
            $unreturnedDocuments=[];

            /*Getting the not returned documents*/
            $user = User::where('id','=',$iduser)->first();

            //Get the emprunts with null real return date
                $emprunts=$user->Emprunts()->where('real_return_date','=',null)->get();
                foreach ($emprunts as $emprunt) {

                    $doc=$emprunt->document()->first();
                    $unreturnedDocuments[]=$doc;
                }
                return $unreturnedDocuments  ;
        }
}