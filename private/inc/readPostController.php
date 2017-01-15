<?php
// Nous allons avoir besoin des fonctions pour enregistrer un post
require_once DIR_INC.'functions_posts.php';

// Déclaration des variables

$iIdPost = ( isset($_GET['id_post']) ? $_GET['id_post'] : 0);
// Si une page en particulier est demandée
$iCurrentPage = (isset($_GET) && isset($_GET['num_page']) ? (int) $_GET['num_page'] : 1);
$aResponses = array(); 		// Par défaut pas de réponses
$sMessageError = ''; 		// Par défaut pas d'erreur
$aPagination = array();		// La pagination


// Si le post n'existe pas
if ($iIdPost == 0 || ! postExists($iIdPost)) {
	$sMessageError = trad('Ce post n\'existe pas ou a été supprimé.');
}
else {
	// On doit vérifier si un enregistrement est soumi (on recoit une réponse)
	// @see http://php.net/manual/fr/function.sizeof.php 
	if (sizeof($_POST) > 0 && isset($_POST['save'])) {

		// POST['save'] est le nom que l'on a donné à notre bouton submit 
		// dans la vue "read_post.phtml" en vérifiant celui ci on 
		// s'assure que l'utilisateur à cliqué sur "enregistrer"

		// On laisse la méthode gérer l'enregistrement
		$mResponse = saveResponse($iIdPost, $_POST);

		// Si l'enregistrement est OK la fonction retourne true ...
		if ($mResponse) {

			// Le header Location permet de faire une redirection vers une page
			// ce qui permet d'éviter le probleme du F5 et de la RE-soumission du post
			// @see http://php.net/manual/fr/function.header.php
			header( "Location:".go('read_post',array('id_post' => (int) $iIdPost)) );
		}
		else {
			// ... sinon la fonction nous retourne l'erreur pour l'utilisateur
			$sMessageError = $mResponse;
		}
	}

	// Récupére le sujet (post principal)
	$aPost = getPostById($iIdPost, ';');
	
	// Nous devons récupérer les réponses de ce post
	$aResponses = getAllResponses($iIdPost, $iCurrentPage, NB_LIMIT_RESPONSES);

	// Cette méthode va nous générer une tableau pour la pagination
	$aPagination = pagination(
		'responses', 
		$iCurrentPage, 
		NB_LIMIT_RESPONSES, 
		array('id_post'=>$iIdPost)
	);
}
