<?php

use medianetapp\model\User as User;
use medianetapp\model\Document as Document;
use medianetapp\model\Borrow as Borrow;
use mf\router\Router as router;

require_once 'src/mf/utils/ClassLoader.php';
/* pour le chargement automatique des classes d'Eloquent (dans le répertoire vendor) */
require_once 'vendor/autoload.php';

$autolaod = new mf\utils\ClassLoader("src");
$autolaod->register();




$config = parse_ini_file('conf/config.ini');

/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection( $config ); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */

$router = new router();

$router->addRoute('borrow_recap', '/borrow_recap', '\medianetapp\control\MedianetController', 'borrowRecap',null);

$router->run();
