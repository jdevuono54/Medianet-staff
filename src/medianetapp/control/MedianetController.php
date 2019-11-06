<?php


namespace medianetapp\control;


use medianetapp\model\Borrow;
use medianetapp\model\Document;
use medianetapp\model\User;
use medianetapp\view\MedianetView;
use mf\router\Router;
use mysql_xdevapi\Exception;

class MedianetController extends \mf\control\AbstractController
{
    public function __construct(){
        parent::__construct();
    }
    /*
     * Display the return view
     */
    public function viewReturn(){
        $view = new MedianetView(null);
        $view->render("return");
    }

        public function addReturn(){
        if(isset($_REQUEST['txtUser'])&&isset($_REQUEST['txtDoc'])){

            /*Filtred values*/
            $filtredTxtUser=strip_tags(trim($_REQUEST['txtUser']));
            $filtredTxtDoc=strip_tags(trim($_REQUEST['txtDoc']));

            //var for getting the the returned documents
            $returnedDocuments = [];

            //var for getting the unreturned documents
            $unreturnedDocuments=[];

            //Explode the input into a var
            $documents = explode(',',$filtredTxtDoc);

            //Error if the user is not valid
            if(!Borrow::where('id_User','=',$filtredTxtUser)->first()){

                //Redirect to return view because of the not valid user
                $view = new MedianetView(["error_message"=>"Veuillez verifier l'id de l'usager"]);
                $view->render("return");
            }
            else{
                foreach ($documents as $document){
                    $borrow = Borrow::where('id_User','=',$filtredTxtUser)->where('id_Document','=',$document)->first();
                    if(!$borrow){

                        //Redirect to return view because of the not valid document
                        $view = new MedianetView(["error_message"=>"Veuillez verifier l'id du document"]);
                        $view->render("return");
                        return;
                    }
                    else{

                        //Update the real return date
                        $borrow->real_return_date=date("Y-m-d");
                        $borrow->save();

                        //Update the document state
                        $docToUpdate = Document::where('id','=',$document)->first();
                        $docToUpdate->id_State=1;
                        $docToUpdate->save();

                        //Fill the var with the returned documents
                        $returnedDocuments[] = $docToUpdate;

                    }
                }

                //Get the unreturned documents
                $unreturnedDocuments=$this->GetUnreturnedDocuments($filtredTxtUser);

                //Call the recap view
                $view = new MedianetView(["returnedDocuments"=>$returnedDocuments,"unreturnedDocuments"=>$unreturnedDocuments]);
                $view->render("return_recap");
            }

        }
    }

        /*
         * function thath return the unretuned documents
         */
        private function GetUnreturnedDocuments($iduser){
            $unreturnedDocuments=[];

            /*Getting the not returned documents*/
            $user = User::where('id','=',$iduser)->first();

            //Get the emprunts with null real return date
                $emprunts=$user->Emprunts()->where('real_return_date','=',null)->get();
                foreach ($emprunts as $emprunt) {

                    $doc=$emprunt->document()->first();
                    $unreturnedDocuments[]=$doc;
                }
                return $unreturnedDocuments  ;
        }
}