<?php


namespace mf\router;


use mf\auth\Authentification;

class Router extends AbstractRouter
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run(){
        if(key_exists($this->http_req->path_info,self::$routes)){
            $auth = new Authentification();

            if($auth->checkAccessRight(self::$routes[$this->http_req->path_info][2])){
                $controller = self::$routes[$this->http_req->path_info][0];
                $methode = self::$routes[$this->http_req->path_info][1];
            }
            else{
                $controller = self::$routes[self::$aliases["default"]][0];
                $methode = self::$routes[self::$aliases["default"]][1];
            }

        }
        else{
            $controller = self::$routes[self::$aliases["default"]][0];
            $methode = self::$routes[self::$aliases["default"]][1];
        }

        $leController = new $controller();
        return $leController->$methode();
    }

    public function urlFor($route_name, $param_list=[]){
        if(key_exists($route_name,self::$aliases)){
            $url = $this->http_req->script_name.self::$aliases[$route_name];
            if($param_list == null){
                return $url;
            }
            else{
                $url = $url."?";
                foreach ($param_list as $param){
                    $url = $url.key($param)."=".$param[key($param)]."&";
                }
                $url = substr($url,0,-1);
                return $url;
            }
        }
        else{
            throw new \Exception("L'alias n'existe pas");
        }
    }


    public function setDefaultRoute($url){
        self::$aliases["default"] = $url;
    }

    public function addRoute($name, $url, $ctrl, $mth, $level){
        self::$routes[$url] = [$ctrl,$mth,$level];
        self::$aliases[$name] = $url;
    }

    public static function executeRoute($alias){
        if(key_exists($alias,self::$aliases)){
            $controller = self::$routes[self::$aliases[$alias]][0];
            $methode = self::$routes[self::$aliases[$alias]][1];

        }
        else{
            $controller = self::$routes[self::$aliases["default"]][0];
            $methode = self::$routes[self::$aliases["default"]][1];
        }

        $leController = new $controller();
        echo $leController->$methode();
    }
}