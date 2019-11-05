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
        return "<label>Usager</label><input type='text'><label>Documents</label><input type='text'><input type='submit'>";
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