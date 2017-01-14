# HO_cours_easy_forum
Easy Forum est utilis� pour faire un cours de PHP en proc�dural, il est con�u de fa�on � introduire l'�tudiant au pattern MVC

______________________________________________________________________________________________
POSTULAT

Le point d'entr�e est VOTRE chemin vers easy_forum (exemple http://127.0.0.1/easy_forum)
il est automatiquement redirig� dans l'index ./public/index.php 
Ci besoin modifier le fichier ./private/config.php en modifiant 
la constante URL_SITE actuellement "http://127.0.0.1/easy_forum/"
______________________________________________________________________________________________



/!\ Ne mettez pas en l'�tat sur un serveur de production (en ligne).
	easy-forum est un code permettant une d�couverte de la structure MVC
	sans connaitre la POO, il est tr�s comment� pour guider dans l'apprentissage.
	
	
______________________________________________________________________________________________
  
  
**LE PRE-REQUIS POUR COMPRENDRE EASY-FORUM v1**

 - Connaissance de HTML / CSS 
 - Connaissance de la syntaxe de PHP 
 - Structure MVC
 - D�claration de variables
 - D�claration de constantes
 - Conditions en PHP
 - Switch
 - Boucles for, foreach, while
 - Cr�ation et appel de fonctions
 - Compr�hension de la documentation sur http://php.net/manual/fr
 
  	
	______________________________________________________________________________________________
  
  
  
 **Liste des fonctions natives PHP utilis�es dans Easy-Forum**
 
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
  
  
  
 
 Pour le moment la s�curit� n'est pas tr�s pr�sente, n'importe qui peut poster 
 sur le forum sans �tre identifi�, il n'y pas de systeme de captcha ou d'antiflood
 vous pouvez vous amusez � cr�er des modules compl�mentaire pour ce forum
 
 - Enregistrement utilisateur
 - Systeme antifllod
 - Captcha (peut �tre un peu dur pour le moment (indice librairie GD))
 - multi langue
 - multi theme
 - mode CSV / mode SQL
 - ou autres
  
 