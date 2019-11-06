<?php

namespace medianetapp\view;

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

    private function renderBorrow(){
        $message = $this->data["error_message"];
        $html = <<< EQT
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

        <section>
            
            <article>
                 <h1>Récapitulatif d'emprunt</h1>
                     <div> 
                         <ul>
                         ${divEmprunt}
                         </ul>
                     </div>
                     <div>
                       
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