<?php
// Cette constantes nous permet de savoir combien il y a eut réellement de posts
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
 * Retourne la premiere ligne d'un CSV (d'un post)
 *
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @since : 2017-01-07
 * @package : easy-forum
 *
 * @param string Chemin du fichier
 * @param string Delimiter pour les colonnes pour csv par défaut ;
 * @param string Enclosure pour le séparateur de champs par défaut "
 * @param string Enclosure pour le séparateur de champs par défaut \ 
 * (on doit mettre un doucle \ comme ceci \\ pour échapper le caractére d'échappement ;-)
 */
function getFirstLine($sPathFile, $sDelimiter = ';', $sEnclosure = '"', $sEscape = '\\')
{
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
			$aPosts['nb_reponses'] = numberResponsesConsolide($aDataId[1]);

			// @see http://php.net/manual/fr/function.fclose.php
			fclose($rHandle);
		}
	}
	return $aPosts;
}

/**
 * Retourne la premiere ligne d'un ensemble de CSV (posts)
 *
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @since : 2017-01-07
 * @package : easy-forum
 *
 * @param array Chemin des fichiers
 * @all other params see method => readFirstLine
 */
function getAllFirstLine($aPathFile, $sDelimiter = ';', $sEnclosure = '"', $sEscape = '\\')
{
	$aPosts = array();
	// On boucle sur l'ensemble des fichiers des posts
	foreach ($aPathFile as $sPathFile)
	{
		// On récupére leur première ligne
		$aPosts[] = getFirstLine($sPathFile, $sDelimiter, $sEnclosure, $sEscape);
	}
	return $aPosts;
}

/**
 * Retourne la liste des fichier se trouvant dans le dossiers posts
 *
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @since : 2017-01-07
 * @package : easy-forum
 *
 * @param string La page souhaitée
 * @param string Nopmbre de fichiers a retourner
 *
 * @return array Liste des fichiers
 */
function getAllPosts($iPage = 1, $iLimit = 50)
{
	$aPosts = array();
	// On détermine le début (1X2-2=0, 2X2-2=2, 3X2-2=4 etc)
	$iStart = ($iPage*$iLimit)-$iLimit;

	// Permet de vérifier si le fichier (dossier dans ce cas) existe
	if (file_exists(DIR_POSTS)) {
		// @see : http://php.net/manual/fr/function.glob.php
		$aList = glob(DIR_POSTS.'post_*.csv');
		// Les plus récent en premier
		// @see http://php.net/manual/fr/function.rsort.php
		rsort($aList,SORT_NATURAL);

		// On compte le nombre de post
		$iNbPost = count($aList);

		for ($i = 1; $i < $iNbPost+1;  $i++) {
			// tant qu'on est pas au numéro de départ on continue
			if ($iStart >= $i) continue;

			$aPosts[] = $aList[$i-1]; // le tableau commence à 0

			if ($i >= ($iStart+$iLimit)) break;
		}
	}
	return $aPosts;
}

/**
 * Enregistre un post
 *
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @since : 2017-01-07
 * @package : easy-forum
 *
 * @param array Les données nécessaire à l'enregistrement
 *
 * @return array Liste des fichiers
 */
function savePost($aData)
{
	global $sIndexPosts; // cela permet d'accéder à une variable GLOBALS

	if ( !empty($aData['subject'])
		&&
		!empty($aData['message'])
	)
	{
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
		$rHandle = fopen(DIR_POSTS.'post_'.$iNbNextPost.'.csv','w+');

		// On écrit dans le fichier
		// @see http://php.net/manual/fr/function.fputcsv.php
		fputcsv($rHandle, $aStructure,';');

		// On peut fermer le fichier
		// @see http://php.net/manual/fr/function.fclose.php
		fclose($rHandle);	

		return $iNbNextPost;
	}

	return trad('Tous les champs sont obligatoires');
}

/** 
 * Cette fonction retourne le numéro du prochain post
 * et modifie le fichier de consolidation (FILENAME_NB_POST)
 *
 * @author : LECOMTE Cyril <cyrhades76@gmail.com>
 * @since : 2017-01-07
 * @package : easy-forum
 *
 * @return <integer> numéro du prochain postr
 */
function nextPostNumber()
{
	$sPathFile = DIR_POSTS.FILENAME_NB_POST;
	// on vérifie si le fichier posts.txt exists
	// @see http://php.net/manual/fr/function.file-exists.php
	if (!file_exists($sPathFile)) {
		// On le crée le fichier avec en valeur au minimum le nombre de post réel
		// @see http://php.net/manual/fr/function.file-put-contents.php
		file_put_contents(DIR_POSTS.FILENAME_NB_POST, numberPostsReal());
	}

	// On lit le fichier pour connaitre la valeur à l'intérieur
	// on incremente de 1, le (int) permet de caster le retour en entier
	$iNbNextPost = (int) numberPostsConsolide()+1; 
	// On écrit tout de suite
	// @see http://php.net/manual/fr/function.file-put-contents.php
	file_put_contents(DIR_POSTS.FILENAME_NB_POST, $iNbNextPost);
	// mais on vérifie quand même que le fichier n'existe pas
	
	// le fichier existe deja
	// @see http://php.net/manual/fr/function.file-exists.php
	if (file_exists(DIR_POSTS.'post_'.$iNbNextPost.'.csv')) {
		return nextPostNumber();
	}

	// On peut retourner le numéro du dernier post
	return $iNbNextPost;
}

/** 
 * Retourne le nombre de posts existant (en se basant sur la consolidation)
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @return integer nombre de posts consolides
 */
function numberPostsConsolide()
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
 * Retourne le nombre de posts existant (en comptant le nombre de fichier)
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @return integer nombre de posts reels
 */
function numberPostsReal()
{
	// @see http://php.net/manual/fr/function.count.php
	// @see http://php.net/manual/fr/function.glob.php
	return count(glob(DIR_POSTS.'*.csv'));
}	

/**
 * Retourne true si le post (le fichier) existe
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @return booleen true si fichier existe / false sinon
 */
function postExists($iIdPost)
{
	// @see http://php.net/manual/fr/function.file-exists.php
	return file_exists(DIR_POSTS.'post_'.(int) $iIdPost.'.csv');
}


/**
 * Retourne la premiere ligne d'un CSV (d'un post)
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param integer L'id du post
 * @param string Delimiter pour les colonnes pour csv par défaut ;
 * @param string Enclosure pour le séparateur de champs par défaut "
 * @param string Enclosure pour le séparateur de champs par défaut \ 
 * (on doit mettre un doucle \ comme ceci \\ pour échapper le caractére d'échappement ;-)
 */
function getPostById($iIdPost, $sDelimiter = ';', $sEnclosure = '"', $sEscape = '\\')
{
	$sPathFile = DIR_POSTS.'post_'.$iIdPost.'.csv';
	return getFirstLine($sPathFile, $sDelimiter, $sEnclosure, $sEscape);
}

/**
 * Enregistre une réponse
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param integer L'id du post parent
 * @param array Les données nécessaire à l'enregistrement
 *
 * @return true|string Message d'erreur
 */
function saveResponse($iIdPost, $aData)
{ 
	global $sIndexPosts; // cela permet d'accéder à une variable GLOBALS

	if (!empty($aData['message']))
	{
		// On construit notre tableau
		$aNewLine = array(
			$sIndexPosts['titre'] 			=> '',					// ici inutile car réponse
			$sIndexPosts['message'] 		=> $aData['message'],	// le message
			$sIndexPosts['date_creation'] 	=> date("Y-m-d H:i:s")	// la date
		);

		// fputCSV a besoin lui aussi d'une ressource sur un fichier
		// @see : http://php.net/manual/fr/function.fopen.php
		$rHandle = fopen(DIR_POSTS.'post_'.$iIdPost.'.csv','a+'); // Mode a+

		// On écrit notre nouvelle ligne
		// @see http://php.net/manual/fr/function.fputcsv.php
		fputcsv($rHandle, $aNewLine,';');
		// On peut fermer le fichier
		// @see http://php.net/manual/fr/function.fclose.php
		fclose($rHandle);

		// On enregistre le nombre de réponse en consolidation
		// @see http://php.net/manual/fr/function.file-put-contents.php
		file_put_contents(
			DIR_POSTS.'responses_post_'.$iIdPost.'.txt', 
			(numberResponsesConsolide($iIdPost)+1) 
		);

		return true;
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
 * @param string Delimiter pour les colonnes pour csv par défaut ;
 * @param string Enclosure pour le séparateur de champs par défaut "
 * @param string Enclosure pour le séparateur de champs par défaut \ 
 * (on doit mettre un doucle \ comme ceci \\ pour échapper le caractére d'échappement ;-)
 *
 * @return array Liste des fichiers
 */
function getAllResponses($iIdPost, $iPage = 1, $iLimit = 50, $sDelimiter = ';', $sEnclosure = '"', $sEscape = '\\')
{
	$aElements = array();

	$iStart = ($iPage*$iLimit)-$iLimit;

	// @see : http://php.net/manual/fr/function.fopen.php
	$rHandle = fopen(DIR_POSTS.'post_'.$iIdPost.'.csv','r'); // Mode r
	
	// On passe la ligne d'entete
	fgetcsv($rHandle, 0, $sDelimiter, $sEnclosure, $sEscape);

	$i = 1;
	while($aList = fgetcsv($rHandle, 0, $sDelimiter, $sEnclosure, $sEscape))
	{
		// Si on a pas de ligne
		if ($aList == false) break;

		// tant qu'on est pas au numéro de départ on continue
		if ($iStart >= $i) continue;

		$aElements[] = $aList; 

		if ($i >= ($iStart+$iLimit)) break;
		
		$i++;
	}
	// On peut fermer le fichier
	// @see http://php.net/manual/fr/function.fclose.php
	fclose($rHandle);
	
	return $aElements;
}

/** 
 * Retourne le nombre de réponse d'un post (en se basant sur la consolidation)
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @return integer nombre de réponse consolides pour un post
 */
function numberResponsesConsolide($iIdPost)
{
	$iNbPost = 0;
	$sPathFile = DIR_POSTS.'responses_post_'.(int) $iIdPost.'.txt';
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
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param integer L'id du post
 * @param string Delimiter pour les colonnes pour csv par défaut ;
 * @param string Enclosure pour le séparateur de champs par défaut "
 * @param string Enclosure pour le séparateur de champs par défaut \ 
 * (on doit mettre un doucle \ comme ceci \\ pour échapper le caractére d'échappement ;-)
 * @return integer nombre de réponses reels pour un post
 */
function numberResponsesReal($iIdPost, $sDelimiter = ';', $sEnclosure = '"', $sEscape = '\\')
{
	$sPathFile = DIR_POSTS.'post_'.$iIdPost.'.csv';
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

	// Cela nous donne le nombre de réponse
	// @see http://php.net/manual/fr/function.count.php
	return count($iContentFile)-1; // la ligne du sujet n'est pas une réponse
}	
