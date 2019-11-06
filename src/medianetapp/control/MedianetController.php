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
      $vue = new MedianetView(null);
      $vue->render("user");
    }

    public function displayUser(){
       echo $_GET['idUser'];
      if(isset($_GET["idUser"]) && filter_var($_GET["idUser"],FILTER_VALIDATE_INT)){
            $user = User::where("id","=",$_GET["idUser"])->first();

            if($user != null){
                $vue = new MedianetView($user);
                $vue->render("display_user");
            }
            else{
                throw new \Exception("Le user n'existe pas");
            }

        }
    }
}
