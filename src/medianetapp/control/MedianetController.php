<?php


namespace medianetapp\control;
use medianetapp\model\User as user;

use medianetapp\model\Borrow;
use medianetapp\model\Document;
use medianetapp\model\SignUpRequest;
use medianetapp\view\MedianetView;
use mf\router\Router;


class MedianetController extends \mf\control\AbstractController
{
    public function __construct(){
        parent::__construct();
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

        /*
         * Display the Check Signup Request
         */
        public function viewCheckSignupRequest(){
            $view = new MedianetView(null);
            $view->render("check_signup_request");
        }

        /*
         * Validate a Signup Request
         */
        public function validateSignupRequest(){
            if(isset($_REQUEST['txtMail'])) {
                $mail = $_REQUEST['txtMail'];
                $demande = SignUpRequest::where("mail", "=", $mail)->first();
                $newUser = new User();
                $newUser->name = $demande->name;
                $newUser->mail = $demande->mail;
                $newUser->password = $demande->password;
                $newUser->phone = $demande->phone;
                $newUser->membership_date = date("Y-m-d");
                $newUser->save();

                $demande->delete();
                $view = new MedianetView(null);
                $view->render("check_signup_request");
            }
        }


        public function addUser(){
            if(isset($_REQUEST['txtName']) && isset($_REQUEST['txtMail']) && isset($_REQUEST['txtPassword1'])
            && isset($_REQUEST['txtPassword2']) && isset($_REQUEST['txtPhone'])) {

                /*Valeurs filtrés*/
                $name = strip_tags(trim($_REQUEST['txtName']));
                $mail = strip_tags(trim($_REQUEST['txtMail']));
                $password1 = $_REQUEST['txtPassword1'];
                $password2 = $_REQUEST['txtPassword2'];
                $phone = strip_tags(trim($_REQUEST['txtPhone']));

                if(User::where('mail','=',$mail)->first() or SignupRequest::where('mail','=',$mail)->first()) {
                    $view = new MedianetView(["error_message"=>"L'adresse email existe déja!"]);
                    $view->render("check_signup_request");
                }
                else {
                    if($password1 != $password2) {
                        $view = new MedianetView(["error_message"=>"Les champs de mot de passe ne sont pas identiques!"]);
                        $view->render("check_signup_request");
                    }
                    else {
                        $newUser = new User();
                        $newUser->name = $name;
                        $newUser->mail = $mail;
                        $newUser->password = $this->hashPassword($password1);
                        $newUser->phone = $phone;
                        $newUser->membership_date = date("Y-m-d");
                        $newUser->save();
                        Router::executeRoute("home");
                    }
                }
            }
    }


    protected function hashPassword($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

}
