<?php

namespace medianetapp\view;
use medianetapp\model\SignUpRequest;

class MedianetView extends \mf\view\AbstractView
{
    public function __construct( $data ){
        parent::__construct($data);
    }

    private function renderHeader(){

    }
    private function renderFooter(){

    }
    private function renderHome(){
        return "home";
    }

    protected function renderBody($selector=null){
        switch ($selector){
            case "home":
                $body = $this->renderHome();
                break;
            case "return":
                $body = $this->renderFormReturn();
                break;
            case "return_recap":
                $body=$this->renderRecap();
                break;
            case "check_signup_request":
                $body=$this->renderCheckSignupRequest();
                break;

        }
        return $body;
    }

    /*
     * Method That return a form for saving a return
     */
     private function renderFormReturn(){
         $errorMessage="";
         if(isset($this->data["error_message"])){
             $errorMessage="<p>{$this->data["error_message"]}</p>";
         }
         return "<form method='post' action ='add_return'>
                         <input type = 'text' name = 'txtUser' required/>
                         <input type = 'text' name = 'txtDoc' required/>
                         <input type = 'submit'/>
                      </form>".$errorMessage;
     }


    /*
     * Method that return the recapitulatif view
     */
    private function renderRecap(){
        $contents = "<h1>Documents rendus ".count($this->data["returnedDocuments"])." :<br>";
        foreach ($this->data["returnedDocuments"] as $docRendu) {
          $contents .= "$docRendu->title<br>";
        }
        $contents .= "<h1>Documents encore en possession ".count($this->data["unreturnedDocuments"])." :<br>";
        foreach ($this->data["unreturnedDocuments"] as $docEncore) {
          $contents .= "$docEncore->title<br>";
        }

        return $contents;
    }

    /////
    private function renderCheckSignupRequest(){
        $demandes = SignUpRequest::all();
        $errorMessage="";
        $message="";
        if(isset($this->data["error_message"])){
            $errorMessage="<p class='error_message'>{$this->data["error_message"]}</p>";
        }
        $content="<h1 class='nomPage'>Ajouter un usager</h1>
                  <div id='signup_form'>
                    <form method='post' action ='add_user'>
                    <div>
                        <label for='txtName'>Nom :</label>
                        <input type = 'text' name = 'txtName' required/>
                    </div>
                    <div>
                        <label for='txtMail'>Mail :</label>
                        <input type = 'email' name = 'txtMail' required/>
                    </div>
                    <div>
                        <label for='txtPassword1'>Mot de passe :</label>
                        <input type = 'password' name = 'txtPassword1' required/>
                    </div>
                    <div>
                        <label for='txtPassword2'>Répeter le mot de passe :</label>
                        <input type = 'password' name = 'txtPassword2' required/>
                    </div>
                    <div>
                        <label for='txtPhone'>Tel (Format: 0123456789) :</label>
                        <input type = 'tel' name = 'txtPhone' pattern='[0-9]{10}' required/>
                    </div>
                    <input type='submit' value='Enregistrer' value = 'Enregistrer'/>$errorMessage
                    </form>
                  </div>
                  <h1 class='nomPage'>Les demandes d'inscription</h1>";

        if(!$demandes){
            $message="<p class='error_message'>Pas de demandes d'inscriptions</p>";
        }
        else {
            foreach ($demandes as $demande) {
                $content.="<h2 class='numDemande'>Demande $demande->id</h2>
                         <div id='demande_form'>
                            <form method='post' action ='validate_signup_request'>
                            <div>
                                <label for='txtName'>Nom :</label>
                                <input type = 'text' value='$demande->name' name = 'txtName' disabled/>
                            </div>
                            <div>
                                <label for='txtMail'>Mail :</label>
                                <input type = 'email' value='$demande->mail'  name = 'txtMail' readonly/>
                            </div>
                            <div>
                                <label for='txtPhone'>Tel :</label>
                                <input type = 'tel' value='$demande->phone'  name = 'txtPhone' disabled/>
                            </div>
                            <input type='submit' value='Valider' value = 'Valider'/>
                            </form>
                        </div>";
            }
        }

        return $content . $message;
    }


}
