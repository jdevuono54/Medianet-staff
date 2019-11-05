<?php



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

$router = new \mf\router\Router();

$router->addRoute("borrow","/borrow","medianetapp\control\MedianetController","viewBorrow",null);
$router->addRoute("home","/home/","medianetapp\control\MedianetController","viewHome",0);

$router->setDefaultRoute('/home/');

$router->run();