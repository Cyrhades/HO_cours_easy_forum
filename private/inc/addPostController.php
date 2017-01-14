<?php
// Nous allons avoir besoin des functions pour enregistrer un post
require_once DIR_INC.'functions_posts.php';


$sMessageError = ''; // Par défaut pas d'erreur (vide), mais on déclare la variable

// On doit vérifier si un enregistrement est soumi
// @see http://php.net/manual/fr/function.sizeof.php (sizeof est un alias de count)
if (sizeof($_POST) > 0 && isset($_POST['save'])) {
	
	// POST['save'] est le nom que l'on a donné à autre bouton submit dans la vue "add_post.phtml"
	// en vérifiant celui ci on s'assure que l'utilisateur à cliqué sur enregistré

	// On laisse la méthode gérer l'enregistrement
	$mResponse = savePost($_POST);

	// Si l'enregistrement est OK la function nous retourne le numéro
	// (int) permet de caster (transformer) la variable en entier
	// @see http://php.net/manual/fr/language.types.type-juggling.php
	if ((int) $mResponse > 0) {
		
		// Le header Location permet de faire une redirection vers une page
		// ce qui permet d'éviter le probleme du F5 et de la soumission du post
		// @see http://php.net/manual/fr/function.header.php
		header( "Location:".go('read_post',array('id_post' => (int) $mResponse)) );
	}
	else {
		// Sinon la fonction nous retourne l'erreur pour l'utilisateur
		$sMessageError = $mResponse;
	}
}
