<?php
/******************************************************************************
 ** Définition de la config de notre forum
 ******************************************************************************/

// define('NOM_CONSTANTE', 'VALEUR_CONSTANTE'); equivaut à : const NOM_CONSTANTE = 'VALEUR_CONSTANTE';
// cependant nous ne pouvons pas concatener avec const : 
// /!\ INTERDIT : const NOM_CONSTANTE = CONST_1_DECLARE_AVANT.'VALEUR_CONSTANTE'; 
// Mais on peut faire : define('NOM_CONSTANTE', CONST_1_DECLARE_AVANT.'VALEUR_CONSTANTE');

// Constante de l'url du site (le seul paramètre à modifier pour faire fonctionner
// Easy-Forum sur un autre serveur)
define('URL_SITE','http://127.0.0.1/HO_cours_easy_forum/');

// constante des assets
define('URL_ASSETS',URL_SITE.'public/assets/');

define('DS','/'); // DS = DIRECTORY SEPARATOR

// Affichage pour la pagination
define('NB_LIMIT_POSTS', 20);
define('NB_LIMIT_RESPONSES', 20);


// Définition des différents chemins de nos dossiers
define('DIR_PRIVATE', __DIR__.DS);
	define('DIR_DATA', DIR_PRIVATE.'data'.DS);
		define('DIR_POSTS', DIR_DATA.'posts'.DS);

	define('DIR_INC', DIR_PRIVATE.'inc'.DS);
	define('DIR_VIEWS', DIR_PRIVATE.'views'.DS);
