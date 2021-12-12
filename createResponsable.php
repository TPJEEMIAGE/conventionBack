<?php
	header('Content-Type: application/json; charset=utf-8');
	$data = json_decode(file_get_contents("php://input"));
	include "pdoConfig.php";

	//Insert Responsable
	$queryStr = "INSERT INTO responsable(civilite, nom, prenom, fonction, telFixe, telPortable, mail, adresse, idEntreprise) 
	VALUES (:civilite,:nom,:prenom,:fonction,:telFixe,:telPortable,:mail,:adresse,:idEntreprise)";
	$query = $pdo->prepare($queryStr);
	$query->bindParam(":civilite",$data->civilite,PDO::PARAM_STR);
	$query->bindParam(":nom",$data->nom,PDO::PARAM_STR);
	$query->bindParam(":prenom",$data->prenom,PDO::PARAM_STR);
	$query->bindParam(":fonction",$data->fonction,PDO::PARAM_STR);
	$query->bindParam(":telFixe",$data->telFixe,PDO::PARAM_STR);
	$query->bindParam(":telPortable",$data->telPortable,PDO::PARAM_STR);
	$query->bindParam(":mail",$data->email,PDO::PARAM_STR);
	$query->bindParam(":adresse",$data->adresse,PDO::PARAM_STR);
	$query->bindParam(":idEntreprise",$data->idEntreprise,PDO::PARAM_STR);
	$query->execute();

	$query = $pdo->prepare("SELECT LAST_INSERT_ID() AS id;");
	$query->execute();
	$ligne = $query->fetch(PDO::FETCH_ASSOC);
	$idResponsable = $ligne["id"];
	
	$query = $pdo->prepare("SELECT * FROM Responsable WHERE idResponsable = :idResponsable");
	$query->bindParam(":idResponsable",$idResponsable,PDO::PARAM_STR);
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);
?>