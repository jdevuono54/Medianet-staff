<?php


namespace medianetapp\control;


use medianetapp\view\MedianetView;

class MedianetController extends \mf\control\AbstractController
{
    public function __construct(){
        parent::__construct();
    }

    public function viewHome(){

    }

    public function viewBorrow(){
        $vue = new MedianetView(null);
        $vue->render("borrow");
    }
}