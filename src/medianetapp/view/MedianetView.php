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
        <form action="add_borrow" method="post">
            <label>Usager</label>
            <input type='text' name="user">
            <label>Documents</label>
            <input type='text' name="documents">
            <input type='submit'>
            <p>${message}</p>
        </form>
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