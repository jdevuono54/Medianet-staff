<?php



namespace medianetapp\control;
use medianetapp\view\MedianetView;
use medianetapp\model\Borrow;
use medianetapp\model\Document;
use medianetapp\model\User;


class MedianetController extends \mf\control\AbstractController
{
    public function __construct(){
        parent::__construct();
    }


    public function viewUser(){
      if(isset($_GET["id"]) && filter_var($_GET["id"],FILTER_VALIDATE_INT)){
            $user = User::where("id","=",$_GET["id"])->first();

            if($user != null){
                $vue = new MedianetView($user);
                $vue->render("user");
            }
        }

        else{
          $vue = new MedianetView(null);
          $vue->render("user");
        }
    }
}
