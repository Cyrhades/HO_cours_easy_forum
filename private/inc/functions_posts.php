<?php

// Plus tard on fera peut-être du SQL plutôt que du CSV
require 'functions_posts_csv.php';


/**
 * Retourne true si le post (le fichier) existe
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @return booleen true si fichier existe / false sinon
 */
function postExists($iIdPost)
{
	// @see http://php.net/manual/fr/function.file-exists.php
	return postExists_CSV($iIdPost);
}

/**
 * Retourne le contenu d'un post
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer Identifiant du post
 *
 * @return array Liste des posts
 */
function getPostById($iPost)
{
	return getPost_CSV($iPost);
}

/**
 * Retourne le nombre de post total
 *
 * @since : 2017-01-15
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @return integer Nombre de posts
 */
function numberPosts()
{
	return numberPosts_CSV(); 
}

/**
 * Retourne le nombre de réponse total sur un post
 *
 * @since : 2017-01-15
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post
 * @return integer Nombre de réponses
 */
function numberResponses($iIdPost)
{
	return numberResponses_CSV($iIdPost); 
}


/**
 * Retourne la liste des posts pour une page
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param string La page souhaitée
 * @param string Nombre de fichiers à retourner
 *
 * @return array Liste des posts
 */
function getAllPosts($iPage = 1, $iLimit = 50)
{
	// On détermine le début (1X2-2=0, 2X2-2=2, 3X2-2=4 etc)
	$iStart = ($iPage*$iLimit)-$iLimit;
	
	return getAllPosts_CSV($iStart, $iLimit);
}

/**
 * Enregistre un post
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param array Les données nécessaires à l'enregistrement
 *
 * @return integer ID du post
 */
function savePost($aData)
{
	global $sIndexPosts; // cela permet d'accéder à une variable GLOBALS

	if ( !empty($aData['subject'])
		&&
		!empty($aData['message'])
	)
	{
		// On sauvegarde
		$iIdPost = savePost_CSV($aData);
		if ($iIdPost > 0) {
			return $iIdPost;
		}
	}

	return trad('Tous les champs sont obligatoires');
}

/**
 * Enregistre une réponse
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post parent
 * @param array Les données nécessaires à l'enregistrement
 *
 * @return true|string Message d'erreur
 */
function saveResponse($iIdPost, $aData)
{ 
	global $sIndexPosts; // cela permet d'accéder à une variable GLOBALS

	if (!empty($aData['message']))
	{
		$bReturn = saveResponse_CSV($iIdPost, $aData);
		if ($bReturn) {
			return true;
		}
	}

	return trad('Votre réponse ne peut pas être vide.');
}

/**
 * Retourne la liste de réponses d'un post
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param integer L'id du post
 * @param integer numéro de la page
 * @param integer nombre d'élément par page
 *
 * @return array Liste des fichiers
 */
function getAllResponses($iIdPost, $iPage = 1, $iLimit = 50)
{
	$iStart = ($iPage*$iLimit)-$iLimit;

	return getAllResponses_CSV($iIdPost, $iStart, $iLimit);
}
	
