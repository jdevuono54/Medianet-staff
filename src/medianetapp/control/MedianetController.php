<?php


namespace medianetapp\control;


use medianetapp\model\Borrow;
use medianetapp\model\Document;
use medianetapp\model\User;
use medianetapp\model\SignUpRequest;
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

            $documents = explode(',',$filtredTxtDoc);
            foreach ($documents as $document){

                /*Update the real return date to system date*/
                $borrow = Borrow::where('id_User','=',$filtredTxtUser)->where('id_Document','=',$document)->first();
                if(!empty($borrow)) {


                    $borrow->real_return_date = date("Y-m-d");
                    $borrow->save();
                }

                /*Update the document state*/
                $doc = Document::where('id', '=', $document)->first();
                if(!empty($doc)) {
                    $doc->id_State = 1;
                    $doc->save();
                }

                /*Getting the returned documents*/
                $returnedDocuments[] = Document::where('id','=',$document)->first();
            }

            /*Get the unreturned Documents*/
            $unreturnedDocuments=$this->GetUnreturnedDocuments($filtredTxtUser);

            /*call the recap view*/
            $view = new MedianetView(["returnedDocuments"=>$returnedDocuments,"unreturnedDocuments"=>$unreturnedDocuments]);
            $view->render("return_recap");
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

            //
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
