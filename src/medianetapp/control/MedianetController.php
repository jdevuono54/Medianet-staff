<?php


namespace medianetapp\control;


use medianetapp\model\Borrow;
use medianetapp\model\Document;
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
            //
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

                    /*Call the recap view*/
                    //TODO get the returned books and the not returned ones then pass them in the constructor of the view
                    $view = new MedianetView(null);
                    $view->render("return_recap");
                }
            }
        }
    }
}