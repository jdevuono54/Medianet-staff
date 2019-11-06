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

    protected function renderBody($selector=null){
        $header = $this->renderHeader();
        $footer = $this->renderFooter();

        switch ($selector){
            case "borrow":
                $article = $this->renderBorrow();
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