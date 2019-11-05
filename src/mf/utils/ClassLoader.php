<?php
namespace mf\utils;

class ClassLoader
{
    private $prefix;
    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }
    function loadClass($nom_complet_classe){
        $nom_complet_classe = str_replace("\\",DIRECTORY_SEPARATOR,$this->prefix.DIRECTORY_SEPARATOR.$nom_complet_classe.".php");
        if(file_exists($nom_complet_classe)){
            require_once $nom_complet_classe;
        }
    }
    public function register(){
        spl_autoload_register(array($this,'loadClass'));
    }
}