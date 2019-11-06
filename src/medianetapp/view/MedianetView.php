<?php

namespace medianetapp\view;
use mf\router\Router;
use mf\utils\HttpRequest;

class MedianetView extends \mf\view\AbstractView
{
    public function __construct( $data ){
        parent::__construct($data);
    }

    private function renderHeader(){
    return"  <a href='#' id='a1'><img src='html/books.png'  /></a>
    <a href='' id='a2'> <b>M</b>EDIA.NET</a>";

    }
    private function renderFooter(){
      return"<footer></footer>";

    }

    protected function renderBody($selector=null){
      $header = $this->renderHeader();
      $footer = $this->renderFooter();

      //$menu = $this->renderTopMenu();

      switch ($selector){

          case "user":
              $article = $this->renderUser();
              break;
          case "display_user":
              $article = $this->renderDisplayUser();
              break;
      }

      $body = <<< EQT
          <header class="theme-backcolor1">
              ${header}

          </header>

          <section>
              <article class="theme-backcolor2">
                  ${article}
              </article>
          </section>

          <footer class="theme-backcolor1">
              ${footer}
          </footer>
EQT;


      return $body;


    }

    private function renderUser(){

      $html = "<article class='ar1'>
                <form class='forms' action='display_user' method='get'>
                  <input type='text' class='in1' name='idUser' id='idUser' placeholder='Id usager ..'>
                  <button class='bt1' type='submit'>Rechercher</button>
                </form>
              </article>";

      return $html;

    }
    private function constructBlocBorrow($borrow,$closeborrowdiv){


        $html = "<div class='tweet'><h1>Ses Emprunts sont :</h1>
                <h2>Document : ".$borrow->id_Document."</h2>
                <h2>Date d'emprunt  : ".$borrow->borrow_date."</h2>
                <h2>Date prévu du retour : ".$borrow->return_date."</h2>
                <h2>Date de retour exact : ".$borrow->real_return_date."</h2></div>";

        if($closeTweetdiv == true){
            $html = $html."</div>";
        }

        return $html;
    }

    private function renderDisplayUser(){

      $user = $this->data;

      $html = "<h2>Nom : ".$user->name."</h2>
              <h2>Mail : ".$user->mail."</h2>
              <h2>Tel : ".$user->phone."</h2>
              <h2>Dates d'adhésion : ".$user->membership_date."</h2>";



      $borrows = $user->borrows()->get();

      foreach ($borrows as $borrow){
            $html = $html.$this->constructBlocBorrow($borrow,true);
      }


      return $html;

    }

}
