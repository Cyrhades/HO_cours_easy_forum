<?php
// Sur la home nous voulons afficher la liste des posts existants

// Nous allons avoir besoin des functions pour accéder aux posts
require_once DIR_INC.'functions_posts.php';

// Si une page en particulier est demandée (on reçoit un numéro) via $_GET['num_page']
$iCurrentPage = (isset($_GET) && isset($_GET['num_page']) ? (int) $_GET['num_page'] : 1);

// On récupére la liste des posts de la page souhaitée
$aPosts = getAllPosts($iCurrentPage, NB_LIMIT_POSTS);

// On ne permet pas l'acces à une page où il n'y a pas de messages
// sauf si on est en page un (on peut avoir un forum sans message)
if (count($aPosts) == 0 && $iCurrentPage > 1) {
	// Les navigateurs peuvent imposer une limite de redirections, du coup
	// on ne fait pas location:index.php?num_page=$iCurrentPage-1 (mais vous pouvez tester ;-) )
	// @see http://php.net/manual/fr/function.header.php
	header('location:index.php');
}

// Cette méthode va nous générer un tableau pour la pagination
// (la variable sera utilisée dans la vue pagination.phtml)
$aPagination = pagination('posts', $iCurrentPage, NB_LIMIT_POSTS);

