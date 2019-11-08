<?php

namespace medianetapp\view;
use medianetapp\model\SignUpRequest;

use mf\router\Router;
use mf\utils\HttpRequest;

class MedianetView extends \mf\view\AbstractView
{
    public function __construct( $data ){
        parent::__construct($data);
    }

    private function renderHeader(){
        $httpRequet = new HttpRequest();
        $src = $httpRequet->root;

        $router = new Router();
        $homeRoute = $router->urlFor("home");

        return" <div class='head' id='headerapp' > <a href='".$homeRoute."' id='a1'><img src='".$src."/html/books.png' alt=''/></a><a href='".$homeRoute."' id='a2'> <b>M</b>EDIA.NET</a> </div>";
    }
    private function renderFooter(){
        return"<div class='foot' id='footerapp'> Copyright@2019</div>";
    }
    private function renderHome(){

        $router = new Router();



        
        $GestionEmprunt = $router->urlFor('borrow');
        $GestionRetours = $router->urlFor('return');
        $GestionUsager = $router->urlFor('user');
        $html = <<<EQT
    <section id="sectionMenu">
        <article id="articlMenu">
            <div id="divMenu">
            
               <div id="MenuGestionEmprunt"><a href="$GestionEmprunt">Gestion des emprunts </a> </div>
                <div id="MenuGestionRetour"><a href="$GestionRetours">Gestion des retours</a></div>
                <div id="MenuGestionUsager"><a href="$GestionUsager">Gestion des usagers</a></div>
                
            
            </div>
            
            
        </article>
    
    </section>
EQT;
        return $html;
    }
    private function renderHome(){
        return "home";
    }

    private function renderBorrow(){
        $message = $this->data["error_message"];
        $html = <<< EQT
        <h1 class="nomPage">Ajouter un emprunt</h1>
        <div id="borrow_form">
            <form action="add_borrow" method="post">
            <div id="borrow_form_user">
                <label>Usager : </label>
                <input type='text' name="user" required>
            </div>
            <div id="borrow_form_reference">
                <label>Référence(s)*: </label>
                <input type='text' name="reference" required>
            </div>
            <div id="borrow_form_other">
                <div id="borrow_small_text">
                    <small>* pour emprunter plusieurs documents d'un coup séparer les références par des ,</small>
                </div>
                <div>
                    <input class="validate_btn" type='submit'>
                </div>
            </div>
    
                <p class="error_message">${message}</p>
            </form>
        </div>
EQT;
        return $html;
    }
    private function borrow_recap(){
        $lesEmprunts = $this->data['documents'];

        $nbEmprunt = count($lesEmprunts);

        $divEmprunt ='';
        foreach ($lesEmprunts as $unEmprunt){


            $divEmprunt .= <<<EQT
                
                <li>
                
                $unEmprunt->title
                </li>

EQT;
        }
        $html = <<< EQT

        <section id="sectionEmprunt">
            
            <article id="articlEmprunt">
                 <h1 class="nomPage">Récapitulatif d'emprunt</h1>
                     <div id="lstEmprunt"> 
                         <ul>
                         ${divEmprunt}
                         </ul>
                     </div>
                     <div id="TTemprunt">
                       
                     Total d'emprunts : ${nbEmprunt}
                     
                     </div>
            
            </article>
        </section>
       
EQT;

        return $html;
    }

    private function renderUser(){
        $user = $this->data;
        if($user == null){
            $html = "<h1 class=\"nomPage\">Voir un utilisateur</h1>
            <article class='ar1'>
            <form class='forms' action='user' method='get'>
              <input type='text' class='in1' name='id' id='idUser' placeholder='Id usager ..'>
              <button class='bt1' type='submit'>Rechercher</button>
            </form>
          </article>";
        }
        else{
            $html = "<h1 class=\"nomPage\">Voir un utilisateur</h1>
            <article class='ar1'>
            <form class='forms' action='user' method='get'>
              <input type='text' class='in1' name='id' id='idUser' placeholder='Id usager ..'>
              <button class='bt1' type='submit'>Rechercher</button>
            </form>
          </article> <br><br>
          <div class='profil' ><h1 class='h1'>Profil :</h1>
                 <table class='tableprofil'>
                    <tr>
                   <th id='thName'>Nom :</th>
                   <th id='thMail'>Mail :</th>
                   <th id='thPhone'>Phone : </th>
                   <th id='thDate'>Date d'adhésion:</th>
                   </tr>
                  <tr>
                  <td> ".$user->name."</td>
                  <td>".$user->mail."</td>
                  <td>".$user->phone."</td>
                  <td> ".$user->membership_date."</td></tr></table></div>
                  <h1 class='h2'>Les documents empruntés :</h1>
                  <div class='divProfil' id='divProfil'>
                  <table class='tableprofil' id='tableProfil'>
                  <tr>
                  <th>Document :</th>
                  <th>Titre :</th>
                  <th>Description : </th>
                  <th>Réference :</th>
                  <th>Date d'emprunt  :</th>
                  <th>Date prévu du retour :</th>
                  </tr>
          ";
            $borrows = $user->Emprunts()->where("real_return_date","=",null)->get();
            foreach ($borrows as $borrow){
                $html .= $this->constructBlocBorrow($borrow);
            }
            $html .= "</table></div>";
        }
        return $html;
    }
    private function constructBlocBorrow($borrow){
        $html ="
                       <tr>
                        <td> ".$borrow->id_Document."</td>
                        <td> ".$borrow->document()->first()->title."</td>
                        <td>".$borrow->document()->first()->description."</td>
                        <td> ".$borrow->document()->first()->reference."</td>
                        <td> ".$borrow->borrow_date."</td>
                        <td> ".$borrow->return_date."</td>
                      </tr>
";
        return $html;
    }


    protected function renderBody($selector=null){
        $header = $this->renderHeader();
        $footer = $this->renderFooter();

        switch ($selector){
            case "borrow":
                $content = $this->renderBorrow();
                break;
            case "borrow_recap":
                $content = $this->borrow_recap();
                break;
            case "home":
                $content = $this->renderHome();
                break;
            case "return":
                $content = $this->renderFormReturn();
                break;
            case "return_recap":
                $content=$this->renderRecap();
                break;
            case "user":
                $content = $this->renderUser();
                break;
            case "check_signup_request":
                $body=$this->renderCheckSignupRequest();
                break;
        }


        $body = <<< EQT
            <header>
                ${header}
            </header>
    
                    ${content}
                    
            <footer>
                ${footer}
            </footer>
EQT;
        return $body;
    }

    /*
     * Method That return a form for saving a return
     */
    private function renderFormReturn(){
        $errorMessage="";
        if(isset($this->data["error_message"])){
            $errorMessage="<p class='error_message'>{$this->data["error_message"]}</p>";
        }
        return "<h1 class='nomPage'>Ajouter des retours</h1>
                 <div id='return_form'>
                    <form method='post' action ='add_return'>
                    <div id='return_form_user'>
                        <label for='txtUser'>Usager :</label>
                        <input type = 'text' name = 'txtUser' required/>
                    </div>
                    <div id='return_form_reference'>
                        <label for='txtDoc' class='doc'>Documents *:</label>
                        <input type = 'text' name = 'txtDoc' required/>
                    </div>
                    <div id='return_form_other'>
                        <div id='return_small_text'>
                        <small>* pour ajouter plusieurs documents d'un coup séparer les références par des , </small>
                        </div>
                        <input type='submit' value='Enregistrer' class='validate_btn' value = 'Enregistrer'/>
                    </div>.$errorMessage
                    </form>
                </div>";
    }
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

        /*Get the number of returned and unreturned documents*/
        $nbReturnedDocs= count($this->data["returnedDocuments"]);
        $nbUnReturnedDocs=count($this->data["unreturnedDocuments"]);

        /*Put each returned document in li*/
        $returnedDocs ='';
        foreach ($this->data["returnedDocuments"] as $document){
            $returnedDocs.="<li>$document->title</li>";
        }

        /*Put each unreturned document in li*/
        $unReturnedDocs ='';
        foreach ($this->data["unreturnedDocuments"] as $document){
            $unReturnedDocs.="<li>$document->title</li>";
        }

        /*Build html for returned documents*/
        $section = "<section id='sectionReturn'>
                                <article id='articleReturn'>
                                    <h1 class='nomPage'>Récapitulatif retour</h1>
                                    <div id='lstReturn'>
                                        <div class='list_documents'>
                                            <div>
                                                <p>Retournés : </p>
                                                <ul>
                                                    {$returnedDocs}
                                                </ul>
                                            </div>
                                            
                                            <div id='TTreturn'>
                                             Total retournés : {$nbReturnedDocs}
                                            </div>
                                        </div>
                                    
                                        <div class='list_documents'>
                                            <div>
                                                <p>Reste à rendre : </p>
                                                <ul>
                                                    {$unReturnedDocs}
                                                </ul>
                                            </div>
                                            <div id='TTreturn'>
                                             Total à rendre : {$nbUnReturnedDocs}
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </section>";

        return $section;
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
