# HO_cours_easy_forum
Easy Forum est utilisé pour faire un cours de PHP en procédural, il est conçu de façon à introduire l'étudiant au pattern MVC

______________________________________________________________________________________________
POSTULAT

Le point d'entrée est VOTRE chemin vers easy_forum (exemple http://127.0.0.1/easy_forum)
il est automatiquement redirigé dans l'index ./public/index.php 
Ci besoin modifier le fichier ./private/config.php en modifiant 
la constante URL_SITE actuellement "http://127.0.0.1/easy_forum/"
______________________________________________________________________________________________



/!\ Ne mettez pas en l'état sur un serveur de production (en ligne).
	easy-forum est un code permettant une découverte de la structure MVC
	sans connaitre la POO, il est très commenté pour guider dans l'apprentissage.
	
	
______________________________________________________________________________________________
  
  
**LE PRE-REQUIS POUR COMPRENDRE EASY-FORUM v1**

 - Connaissance de HTML / CSS 
 - Connaissance de la syntaxe de PHP 
 - Structure MVC
 - Déclaration de variables
 - Déclaration de constantes
 - Conditions en PHP
 - Switch
 - Boucles for, foreach, while
 - Création et appel de fonctions
 - Compréhension de la documentation sur http://php.net/manual/fr
 
  	
	______________________________________________________________________________________________
  
  
  
 **Liste des fonctions natives PHP utilisées dans Easy-Forum**
 
 - require, require_once, include, include_once
 - isset, empty, is_array
 - file_exists, is_file, is_dir
 - str_getcsv, fgetCSV, fputCSV
 - file_get_contents, file_put_contents, fopen, fclose, file
 - sizeof / count, 
 - header
 - urlencode, http_build_query
 - unset, unlink
 - glob	
 - sort , ksort, rsort, asort, ... (methode de tri)
 
  
  
  	
	______________________________________________________________________________________________
  
  
  
 
 Pour le moment la sécurité n'est pas très présente, n'importe qui peut poster 
 sur le forum sans être identifié, il n'y pas de systeme de captcha ou d'antiflood
 vous pouvez vous amusez à créer des modules complémentaire pour ce forum
 
 - Enregistrement utilisateur
 - Systeme antifllod
 - Captcha (peut être un peu dur pour le moment (indice librairie GD))
 - multi langue
 - multi theme
 - mode CSV / mode SQL
 - ou autres
  
 