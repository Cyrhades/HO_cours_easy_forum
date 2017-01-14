<?php
// Chargement de la config
require '../private/config.php';

// Chargement des  functions
require DIR_INC.'functions.php';

// On récupére toutes les infos de routing
$aRoutingPageInfos = routeur((!empty($_GET['page']) ? $_GET['page'] : 'home'));

// Si il y a un controleur (l'info provient de la fonction routeur)
// http://php.net/manual/fr/function.isset.php
if (isset($aRoutingPageInfos['controller']) 
	&& $aRoutingPageInfos['controller'] != false) 
{
	// file_exists vérifie si un fichier (fichier ou dossier) existe (il faut le chemin complet)
	// @see http://php.net/manual/fr/function.file-exists.php
	if (file_exists(DIR_INC.$aRoutingPageInfos['controller'].'Controller.php')) {
		require DIR_INC.$aRoutingPageInfos['controller'].'Controller.php';
	}
}

// $sView est la vue qui sera intégrée dans la vue principale
$sView = $aRoutingPageInfos['view'];

// Chargement de la vue principale (index.phtml)
include view('index');
