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
        <div id="borrow_form">
            <form action="add_borrow" method="post">
            <div id="borrow_form_user">
                <label>Usager : </label>
                <input type='text' name="user">
            </div>
            <div id="borrow_form_reference">
                <label>Référence du/des document(s) : </label>
                <input type='text' name="reference">
            </div>
            <div id="borrow_form_other">
                <small>* pour emprunter plusieurs documents d'un coup séparer les références par des ,</small>
                <input class="validate_btn" type='submit'>
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
                 <h1 id="titreEmprunt">Récapitulatif d'emprunt</h1>
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
                $article = $this->renderBorrow();
                break;
            case "borrow_recap":
                $article = $this->borrow_recap();
                break;
            case "home":
                $article = $this->renderHome();
                break;
        }


        $body = <<< EQT
            <header>
                ${header}
            </header>
    
            <section>
                <article class="theme-backcolor2">
                    ${article}
                </article>
            </section>
            <footer>
                ${footer}
            </footer>
EQT;
        return $body;
    }
}