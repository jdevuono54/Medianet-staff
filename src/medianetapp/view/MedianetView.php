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
    $httpRequet = new HttpRequest();
     $src = $httpRequet->root;
    $router = new Router();
    $homeRoute = $router->urlFor("home");
  return" <header class='head' id='headerapp' > <a href='".$homeRoute."' id='a1'><img src='".$src."/html/books.png' alt=''/></a>
  <a href='".$homeRoute."' id='a2'> <b>M</b>EDIA.NET</a> </header>";

  }
  private function renderFooter(){
    return"<footer class='foot' id='footerapp'> Copyright@2019</footer>";

  }

  protected function renderBody($selector=null){
    $header = $this->renderHeader();
    $footer = $this->renderFooter();

    //$menu = $this->renderTopMenu();

    switch ($selector){

        case "user":
            $article = $this->renderUser();
            break;
    }

    $body = <<< EQT

            ${header}



                ${article}


            ${footer}
EQT;


    return $body;

  $html = "<article class='ar1'>";

  }

  private function renderUser(){
    $user = $this->data;

if($user == null){
  $html = "<article class='ar1'>

            <form class='forms' action='user' method='get'>
              <input type='text' class='in1' name='id' id='idUser' placeholder='Id usager ..'>
              <button class='bt1' type='submit'>Rechercher</button>
            </form>
          </article>";
}
else{
  $html = "<article class='ar1'>
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

                $html .= $this->constructBlocBorrow($borrow,true);

          }

          $html .= "</table></div>";
}



    return $html;

  }
  private function constructBlocBorrow($borrow,$closeborrowdiv){



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

  private function renderDisplayUser(){

    $user = $this->data;

    $html = "<div class='profil' ><h1 class='h1'>Profil :</h1>
           <table class='tableprofil' id='tableProfil'>

              <tr>
             <th id='thName'>Nom :</th>
             <th id='thMail'>Mail :</th>
             <th id='thPhone'>Phone : </th>
             <th id='thDate'>Date d'adhésion:</th>
             </tr>
            <td > ".$user->name."</td>
            <td>".$user->mail."</td>
            <td>".$user->phone."</td>
            <td> ".$user->membership_date."</td></table></div>
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
            "
            ;



    $borrows = $user->Emprunts()->where("real_return_date","=",null)->get();

    foreach ($borrows as $borrow){

          $html .= $this->constructBlocBorrow($borrow,true);

    }

    $html .= "</table></div>";

    return $html;

  }

}
