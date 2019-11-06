<?php


namespace medianetapp\control;


use medianetapp\model\Borrow;
use medianetapp\model\Document;
use medianetapp\model\User;
use medianetapp\view\MedianetView;

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

            $documents = explode(',',$filtredTxtDoc);
            foreach ($documents as $document){

                /*Update the real return date to system date*/
                $borrow = Borrow::where('id_User','=',$filtredTxtUser)->where('id_Document','=',$document)->first();
                if(!empty($borrow)) {


                    $borrow->real_return_date = date("Y-m-d");
                    $borrow->save();
                }

                /*Update the document state*/
                $doc = Document::where('id', '=', $document)->first();
                if(!empty($doc)) {
                    $doc->id_State = 1;
                    $doc->save();
                }

                /*Getting the returned documents*/
                echo "Ok";
                $returnedDocuments[] = Document::where('id','=',$document)->first();
            }

            /*Get the unreturned Documents*/
            $unreturnedDocuments=$this->GetUnreturnedDocuments($filtredTxtUser);

            /*call the recap view*/
            $view = new MedianetView(["returnedDocuments"=>$returnedDocuments,"unreturnedDocuments"=>$unreturnedDocuments]);
            $view->render("return_recap");
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

            //
            foreach ($emprunts as $emprunt) {

                $doc=$emprunt->document()->first();
                $unreturnedDocuments[]=$doc;
            }
            return $unreturnedDocuments  ;
        }
}