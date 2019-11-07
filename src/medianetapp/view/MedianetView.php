<?php

namespace medianetapp\view;

use mf\router\Router;

class MedianetView extends \mf\view\AbstractView
{
    public function __construct( $data ){
        parent::__construct($data);
    }

    private function renderHeader(){
        return "";
    }
    private function renderFooter(){
        return "";
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
}