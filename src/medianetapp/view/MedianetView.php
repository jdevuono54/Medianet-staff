<?php

namespace medianetapp\view;

class MedianetView extends \mf\view\AbstractView
{
    public function __construct( $data ){
        parent::__construct($data);
    }

    private function renderHeader(){

    }
    private function renderFooter(){

    }

    protected function renderBody($selector=null){
        switch ($selector){
            case "return":
                $body = $this->renderFormReturn();
                break;
            case "return_recap":
                $body=$this->renderRecap();
                break;

        }
        return $body;
    }

    /*
     * Method That return a form for saving a return
     */
    private function renderFormReturn(){
        $contents = "<form method='post' action ='add_return'>
                        <input type = 'text' name = 'txtUser' required/>
                        <input type = 'text' name = 'txtDoc' required/>
                        <input type = 'submit'/>
                     </form>";
        return $contents;
    }

    /*
     * Method that return the recapitulatif view
     */
    private function renderRecap(){
        $contents = var_dump($this->data["unreturnedDocuments"]);
        return $contents;
    }
}