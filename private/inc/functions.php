<?php

/**
 * routeur
 * Gére les controllers et les vues à charger 
 * en fonction de l'appel de l'utilisateur.
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param string page souhaitée
 *
 * @return array Infos pour routage (controller et vue)
 */
function routeur($sPage)
{
	// Par defaut le routeur nous emmene sur la home
	$aRouting = array(
		'view' 			=> 'home', 
		'controller' 	=> 'home'
	);

	switch ($sPage)
	{
		// Rien à faire car ceux sont les valeurs par défaut de notre tableau ($aRouting)
		case 'home': break;  

		// ICI ON GERE TOUTES NOS PAGES (CONTROLLER ET VUE)
		case 'read_post': 
			// Page pour voir un post (et y répondre)
			$aRouting = array(
				'view' 			=> 'read_post', 
				'controller' 	=> 'readPost'
			);
			break;  
		case 'add_post': 
			// Page pour créer un post
			$aRouting = array(
				'view' 			=> 'add_post', 
				'controller' 	=> 'addPost'
			);
			break;  
	}

	// On retourne notre tableau
	return $aRouting;
}

/**
 * Anticipation pour une gestion avancé du routage (pour un éventuel url rewriting)
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param string Page souhaitée
 * @return string Url de la page souhaitée
 */
function go($sUrl, $aParams = array())
{
	switch ($sUrl) {
		case '' :
		case '/' :
		case 'home' :
			// @see http://php.net/manual/fr/function.http-build-query.php
			$aArgsGet = ( is_array($aParams) && count($aParams) > 0 
							? '?'.http_build_query($aParams) 
							: ''
			);
			$sUrl = URL_SITE.'index.php'.$aArgsGet;
			break;
		default :
			// @see http://php.net/manual/fr/function.http-build-query.php
			$aArgsGet = ( is_array($aParams) && count($aParams) > 0 
							? '&'.http_build_query($aParams) 
							: ''
			);
			// @see http://php.net/manual/fr/function.urlencode.php
			$sUrl = URL_SITE.'index.php?page='.urlencode($sUrl).$aArgsGet;
	}
	
	return $sUrl;
}

/**
 * Anticipation du multi-langue ;-)
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param string chaine à traduire
 * @return string chaine traduite (pas encore géré)
 */
function trad($sText)
{
	return nohtml($sText); // Pour le momemt on retourne directement la chaine
}

/**
 * @alias htmlspecialchars
 * Protection contre les failles XSS
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param string chaine à protéger
 * @return string chaine protégé contre faille XSS
 */
function nohtml($sText)
{
	return htmlspecialchars($sText);
}

/**
 * @todo Formatage de la date
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param string Date à formater
 * @return string Date formatée (non géré actuellemnt)
 */
function print_date($sDate)
{
	return nohtml($sDate); // Pour le momemt on retourne directement la chaine
}

/**
 * Retourne la valeur d'une variable GET ou la valeur envoyé  
 * en second paramétre ($mDefault) si l'index n'existe pas dans GET 
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param string index recherché dans GET
 *
 * @return mixed valeur renvoyé si l'index n'existe pas dans GET
 */
function varGet($sIndexTarget, $mDefault = null)
{

	if (isset($_GET) && isset($_GET[$sIndexTarget])) {
		return $_GET[$sIndexTarget];
	}
	return $mDefault;
}

/**
 * Retourne la valeur d'une variable POST ou la valeur envoyé  
 * en second paramétre ($mDefault) si l'index n'existe pas dans POST 
 *
 * @author LECOMTE Cyril <cyrhades76@gmail.com>
 * @since 2017-01-07
 * @package easy-forum
 *
 * @param string index recherché dans POST
 * @return mixed valeur renvoyé si l'index n'existe pas dans POST
 */
function varPost($sIndexTarget, $mDefault = null) {
	if (isset($_POST) && isset($_POST[$sIndexTarget]))
	{
		return $_POST[$sIndexTarget];
	}
	return $mDefault;
}

/**
 * asset
 * @param string Url vers l'asset
 * @param string Dossier où se trouve l'asset (image, css, js)
 *
 * @return string chemin complet de l'asset
 */
function asset($sAssetName, $sAssetDirectory = '')
{
	if (!empty($sAssetDirectory)) {
		$sAssetDirectory .= '/'; 
	}
	// On retourne le chemin complet
	return URL_ASSETS.$sAssetDirectory.$sAssetName;
}

/**
 * view retourne le chemin complet pour accéder à une vue
 *
 * @param string|array nom_vue || [dossier, nom_vue]
 *
 * @return string chemin complet vers la vue
 */
function view($mView)
{
	// is_array verifie si la variable est de type array
	// count compte le nombre d'élément
	if (is_array($mView) && count($mView) == 2) {
		// On retourne le chemin complet
		return DIR_VIEWS.$mView[0].DS.$mView[1].'.phtml';
	} 
	// is_string vérifie si la variable est une chaine (string)
	elseif (is_string($mView) && $mView != '') {
		// On retourne le chemin complet
		return DIR_VIEWS.$mView.'.phtml';
	}
	else {
		// On verra plus tard mais la on a pas recu ce que nous attendions (Exception)
	}
}

/**
 * Génére un tableau pour créer la pagination
 *
 * @param string Type de la pagination (pour accéder à la bonne méthode)
 * @param integer Numéro de la page courante
 * @param integer Nombre d'éléments par pages
 * @param array Les éventuels paramétres à fournir à la page
 *
 * @return array numéro page => lien vers les pages
 */
function pagination($sType, $iPage = 1, $iLimit = 50, $aParams = array())
{
	$iNbElement = 0;	// Nombre total d'élément
	$sGoto = '';		// par défaut c'est la home
	$aPages = array();	// Liste des pages à afficher


	switch ($sType)
	{
		case 'posts' :	
			// Nombre de posts existants
			$iNbElement = numberPostsReal(); 
			break;
		case 'responses' : 	
			$sGoto = 'read_post';
			if (isset($aParams['id_post'])) {
				$iNbElement = numberResponsesReal($aParams['id_post']); 
			}
			break;
	}

	// @see http://php.net/manual/fr/function.ceil.php
	// retourne l'entier supérieur
	$iNbPagePossible = (int) ceil($iNbElement/$iLimit);
	// On ne va pas afficher plus de 10 boutons de pagination
	if ($iNbPagePossible > 10) {
		// on est pas à la premiere page on peut créer des << et <
		if ($iPage > 1) {
			// Premiere page
			$aPages['<<'] = go(	$sGoto, 
								array_merge(
									array('num_page' => 1) ,
									$aParams
								)
							); 	// page 1 
			// page precedente
			$aPages['<'] = go(	$sGoto, 
								array_merge(
									array('num_page' => $iPage-1),
									$aParams
								)
							);	// $iPage-1
		}

		// @todo créer les pages inétermédiaire

		// page courante
		$aPages[$iPage] = 'javascript:;';


		// on est pas à la derniere page on peut créer des > et >>
		if ($iPage < $iNbPagePossible) {
			// page precedente
			$aPages['>'] = go(	$sGoto, 
								array_merge(
									array('num_page' => $iPage+1),
									$aParams 
								)
							);	// $iPage+1
			// Derniere page
			$aPages['>>'] = go(	$sGoto, 
								array_merge(
									array('num_page' => $iNbPagePossible),
									$aParams
								)
							); 	// page $iNbPagePossible 
		}
	}
	else {
		// On peut tout afficher
		for ($i = 1; $i <= $iNbPagePossible;  $i++) {
			$aPages[$i] = ($i == $iPage 
				? 'javascript:;' 
				: go(	$sGoto, 
						array_merge(
							array('num_page' => $i),  
							$aParams
						)
					));
		}
	}
	return $aPages;
}

