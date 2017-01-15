<?php
/**
 * Ce fichier gére les posts au format CSV, lecture, écriture, suppression, consolidation, etc
 */

// Ce fichier nous permet de savoir combien il y a eut réellement de posts
const FILENAME_NB_POST = 'posts.txt';

// On crée le dossier post si il n'existe pas
if (!file_exists(DIR_POSTS)) mkdir(DIR_POSTS, 0777, true);


// Cette variable nous permettra de manipuler le CSV avec des index compréhensible
$sIndexPosts = array(
	'titre' 			=> 0, // la colonne 1 correspond au titre
	'message' 			=> 1, // la colonne 2 correspond au message
	'date_creation' 	=> 2, // la colonne 3 correspond a la date de création
);

/**
 * @param string Delimiter pour les colonnes pour csv par défaut ;
 * @param string Enclosure pour le séparateur de champs par défaut "
 * @param string Enclosure pour le séparateur de champs par défaut \ 
 */
$sDelimiter	 = ';';
$sEnclosure	 = '"';
$sEscape	 = '\\'; // comme ceci \\ pour échapper le caractére d'échappement ;-)

/**
 * Retourne la premiere ligne d'un CSV (d'un post)
 *
 * @since : 2017-01-15
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post
 *
 * @return string path du fichier
 */
function getPathFilePostById($iIdPost)
{
	return DIR_POSTS.'post_'.$iIdPost.'.csv';
}

/**
 * Retourne la premiere ligne d'un CSV (d'un post)
 *
 * @since : 2017-01-15
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post
 *
 * @return string path du fichier
 */
function getPathFileResponseById($iIdPost)
{
	return DIR_POSTS.'responses_post_'.$iIdPost.'.txt';
}

/**
 * Retourne la premiere ligne d'un CSV (d'un post)
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param string Chemin du fichier
 */
function getFirstLine($sPathFile)
{
	global $sDelimiter, $sEnclosure, $sEscape;

	$aPosts = array();
	// Permet de vérifier si le fichier existe
	if (file_exists($sPathFile)) {
		
		// fgetCSV a besoin d'une ressource sur un fichier
		//@see : http://php.net/manual/fr/function.fopen.php
		$rHandle = fopen($sPathFile, 'r'); // r pour lecture seule

		// Seulement si pas d'échec d'ouvertue (généralement lié à des problemes de droit)
		if ($rHandle !== false) {

			// @see : http://php.net/manual/fr/function.fgetcsv.php
			$aPosts = fgetcsv($rHandle, 0, $sDelimiter, $sEnclosure, $sEscape);

			// @see : http://php.net/manual/fr/function.preg-match.php
			// On veut capturer
			preg_match('/.*post_([0-9]+).csv$/U', $sPathFile, $aDataId); 

			// On récupére notre capture
			$aPosts['id_post'] = $aDataId[1];
			$aPosts['nb_reponses'] = numberResponses_CSV($aDataId[1]);

			// @see http://php.net/manual/fr/function.fclose.php
			fclose($rHandle);
		}
	}
	return $aPosts;
}


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
function postExists_CSV($iIdPost)
{
	// @see http://php.net/manual/fr/function.file-exists.php
	return file_exists(getPathFilePostById($iIdPost));
}

/**
 * Retourne la premiere ligne d'un CSV (d'un post)
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post
 */
function getPost_CSV($iIdPost)
{
	global $sDelimiter, $sEnclosure, $sEscape;

	$sPathFile = getPathFilePostById($iIdPost);
	return getFirstLine($sPathFile, $sDelimiter, $sEnclosure, $sEscape);
}

/**
 * Retourne la liste des fichiers se trouvant dans le dossiers posts
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param string On commence a iStart
 * @param string Nombre de post à retourner
 *
 * @return array Liste des posts
 */
function getAllPosts_CSV($iStart = 0, $iLimit = 50)
{
	$aPosts = array();
	
	// Permet de vérifier si le fichier (dossier dans ce cas) existe
	if (file_exists(DIR_POSTS)) {
		// @see : http://php.net/manual/fr/function.glob.php
		$aList = glob(DIR_POSTS.'post_*.csv');
		// Les plus récent en premier
		// @see http://php.net/manual/fr/function.rsort.php
		rsort($aList, SORT_NATURAL);

		// On compte le nombre de post
		$iNbPost = count($aList);

		for ($i = 1; $i < $iNbPost+1;  $i++) {
			// tant qu'on est pas au numéro de départ on continue
			if ($iStart >= $i) continue;

			$aPosts[] = getFirstLine($aList[$i-1]); // le tableau commence à 0

			if ($i >= ($iStart+$iLimit)) break;
		}
	}
	
	return $aPosts;
}

/**
 * Enregistre un post au format CSV
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param array Les données nécessaire à l'enregistrement
 *
 * @return false|integer ID du post
 */
function savePost_CSV($aData)
{
	global $sIndexPosts; // cela permet d'accéder à une variable GLOBALS
	global $sDelimiter, $sEnclosure, $sEscape;
	// On construit notre tableau
	$aStructure = array(
		$sIndexPosts['titre'] 			=> $aData['subject'],	// Titre du message (sujet)
		$sIndexPosts['message'] 		=> $aData['message'],	// le message
		$sIndexPosts['date_creation'] 	=> date("Y-m-d H:i:s")	// la date
	);

	// On trie le tableau, si la structure change inutile de revenir ici du coup
	// il suffira de modifier la variable GLOBALS sIndexPosts
	// @see http://php.net/manual/fr/function.ksort.php
	ksort($aStructure);

	// le numéro du prochain post
	$iNbNextPost = nextPostNumber();

	// fputCSV a besoin lui aussi d'une ressource sur un fichier
	// @see : http://php.net/manual/fr/function.fopen.php
	$rHandle = fopen(getPathFilePostById($iNbNextPost),'w+');

	if ($rHandle) {
		// On écrit dans le fichier
		// @see http://php.net/manual/fr/function.fputcsv.php
		fputcsv($rHandle, $aStructure, $sDelimiter, $sEnclosure, $sEscape);

		// On peut fermer le fichier
		// @see http://php.net/manual/fr/function.fclose.php
		fclose($rHandle);

		return $iNbNextPost;
	}

	return false;
}

/**
 * Enregistre une réponse au format CSV
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post parent
 * @param array Les données nécessaire à l'enregistrement
 *
 * @return true|string Message d'erreur
 */
function saveResponse_CSV($iIdPost, $aData)
{ 
	global $sIndexPosts; // cela permet d'accéder à une variable GLOBALS
	global $sDelimiter, $sEnclosure, $sEscape;

	// On construit notre tableau
	$aNewLine = array(
		$sIndexPosts['titre'] 			=> '',					// ici inutile car réponse
		$sIndexPosts['message'] 		=> $aData['message'],	// le message
		$sIndexPosts['date_creation'] 	=> date("Y-m-d H:i:s")	// la date
	);

	// fputCSV a besoin lui aussi d'une ressource sur un fichier
	// @see : http://php.net/manual/fr/function.fopen.php
	$rHandle = fopen(getPathFilePostById($iIdPost),'a+'); // Mode a+
	if ($rHandle) {


		// On écrit notre nouvelle ligne
		// @see http://php.net/manual/fr/function.fputcsv.php
		fputcsv($rHandle, $aNewLine, $sDelimiter, $sEnclosure, $sEscape);
		// On peut fermer le fichier
		// @see http://php.net/manual/fr/function.fclose.php
		fclose($rHandle);

		// On enregistre le nombre de réponse en consolidation
		// @see http://php.net/manual/fr/function.file-put-contents.php
		file_put_contents(
			getPathFileResponseById($iIdPost),
			(numberResponses_CSV($iIdPost)+1) 
		);

		return true;
	}
	return false;
}

/**
 * Retourne la liste de réponses d'un post
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post
 * @param integer numéro de la page
 * @param integer nombre d'élément par page
 *
 * @return array Liste des reponses
 */
function getAllResponses_CSV($iIdPost, $iStart = 0, $iLimit = 50)
{
	global $sDelimiter, $sEnclosure, $sEscape;

	$aElements = array();
	// @see : http://php.net/manual/fr/function.fopen.php
	$rHandle = fopen(getPathFilePostById($iIdPost),'r'); // Mode r
	
	// On passe la ligne d'entete (on l'ignore)
	fgetcsv($rHandle, 0, $sDelimiter, $sEnclosure, $sEscape);

	$i = 0;
	while($aList = fgetcsv($rHandle, 0, $sDelimiter, $sEnclosure, $sEscape))
	{
		$i++;
		// Si on a pas de ligne
		if ($aList == false) break;

		// tant qu'on est pas au numéro de départ on continue
		if ($iStart >= $i) continue;

		$aElements[] = $aList; 

		if ($i >= ($iStart+$iLimit)) break;
		
		
	}
	// On peut fermer le fichier
	// @see http://php.net/manual/fr/function.fclose.php
	fclose($rHandle);

	return $aElements;
}


/** 
 * Retourne le nombre de posts existant (en se basant sur la consolidation)
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @return integer nombre de posts consolides
 */
function numberPosts_CSV()
{
	$iNbPost = 0;
	$sPathFile = DIR_POSTS.FILENAME_NB_POST;
	// on vérifie si le fichier posts.txt exists
	// @see http://php.net/manual/fr/function.file-exists.php
	if (file_exists($sPathFile)) {
		// @see http://php.net/manual/fr/function.file-get-contents.php
		$iNbPost = (int) file_get_contents($sPathFile);
	}
	return $iNbPost;
}	

/** 
 * Retourne le nombre de réponse d'un post (en se basant sur la consolidation)
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @return integer nombre de réponse consolides pour un post
 */
function numberResponses_CSV($iIdPost)
{
	$iNbPost = 0;
	$sPathFile = getPathFileResponseById($iIdPost);
	// on vérifie si le fichier responses_post_xxxx.txt exists
	// @see http://php.net/manual/fr/function.file-exists.php
	if (file_exists($sPathFile)) {
		// @see http://php.net/manual/fr/function.file-get-contents.php
		$iNbPost = file_get_contents($sPathFile);
	}

	return $iNbPost;
}

/**
 * Retourne le nombre de reponses existantes (en comptant le nombre de ligne)
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @param integer L'id du post
 * @return integer nombre de réponses reels pour un post
 */
function consolideNumberResponses($iIdPost)
{
	global $sDelimiter, $sEnclosure, $sEscape;

	$iContentFile = 0;
	$sPathFile = getPathFileResponseById($iIdPost);
	// @see http://php.net/manual/fr/function.file-exists.php
	if (file_exists($sPathFile)) {
		// @see : http://php.net/manual/fr/function.fopen.php
		$rHandle = fopen($sPathFile,'r'); // Mode r

		$iContentFile = 0;
		while($aList = fgetcsv($rHandle, 0, $sDelimiter, $sEnclosure, $sEscape))
		{
			// Si on a pas de ligne
			if ($aList == false) break;
			$iContentFile++;
		}
		// On peut fermer le fichier
		// @see http://php.net/manual/fr/function.fclose.php
		fclose($rHandle);
	}

	
	file_put_contents(
		getPathFileResponseById($iIdPost),
		count($iContentFile)-1 // la ligne du sujet n'est pas une réponse (-1)
	); 
}

/**
 * Retourne le nombre de posts existant (en comptant le nombre de fichier)
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @return integer nombre de posts reels
 */
function consolideNumberPosts()
{
	// @see http://php.net/manual/fr/function.count.php
	// @see http://php.net/manual/fr/function.glob.php
	file_put_contents(
		DIR_POSTS.FILENAME_NB_POST,
		count(glob(DIR_POSTS.'*.csv'))
	); 
}	

/** 
 * Cette fonction retourne le numéro du prochain post
 * et modifie le fichier de consolidation (FILENAME_NB_POST)
 *
 * date modifier : 2017-01-15
 * @since : 2017-01-07
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @package : easy-forum
 *
 * @return <integer> numéro du prochain post
 */
function nextPostNumber()
{
	$sPathFile = DIR_POSTS.FILENAME_NB_POST;
	// on vérifie si le fichier posts.txt exists
	// @see http://php.net/manual/fr/function.file-exists.php
	if (!file_exists($sPathFile)) {
		// On le crée le fichier avec en valeur au minimum le nombre de post réel
		consolideNumberPosts();
	}

	// On lit le fichier pour connaitre la valeur à l'intérieur
	// on incremente de 1, le (int) permet de caster le retour en entier
	$iNbNextPost = (int) numberPosts_CSV()+1; 
	// On écrit tout de suite
	// @see http://php.net/manual/fr/function.file-put-contents.php
	file_put_contents(DIR_POSTS.FILENAME_NB_POST, $iNbNextPost);
	// mais on vérifie quand même que le fichier n'existe pas
	
	// le fichier existe deja
	// @see http://php.net/manual/fr/function.file-exists.php
	if ( file_exists(getPathFilePostById($iNbNextPost)) ) {
		return nextPostNumber();
	}

	// On peut retourner le numéro du dernier post
	return $iNbNextPost;
}
