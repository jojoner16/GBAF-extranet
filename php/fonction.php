<?php
/*
*   Fichier de fonctions génériques
*   - connect_bdd()
*       Permet de centraliser la connexion à MySQL, les infos de connexion
*       se trouvent ici (login/mot de passe, nom de la BDD).
*       Renvoie un objet PDO avec lequel on peut exécuter des requêtes.
*/
function connect_bdd()
{
    // connexion à la BDD
	try
	{
		$pdo_options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE =>  PDO::ERRMODE_EXCEPTION
        );
		$bdd = new PDO('mysql:host=localhost;dbname=gbaf', 'root', '', $pdo_options);
		return $bdd;
	}
	catch(Exception $e)
	{
		die('Erreur : '.$e->getMessage());
	}
}
?>