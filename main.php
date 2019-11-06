<?php



require_once 'src/mf/utils/ClassLoader.php';
/* pour le chargement automatique des classes d'Eloquent (dans le répertoire vendor) */
require_once 'vendor/autoload.php';

$autolaod = new mf\utils\ClassLoader("src");
$autolaod->register();

\medianetapp\view\MedianetView::addStyleSheet("html/css/G_atFor.css");
\medianetapp\view\MedianetView::addStyleSheet("html/css/G_cssGrid.css");
\medianetapp\view\MedianetView::addStyleSheet("html/css/G_mixins.css");


$config = parse_ini_file('conf/config.ini');

/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection( $config ); /* configuration avec nos paramètres */
$db->setAsGlobal();            /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           /* établir la connexion */




