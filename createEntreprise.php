<?php
	header('Content-Type: application/json; charset=utf-8');
	$data = json_decode(file_get_contents("php://input"));
	include "pdoConfig.php";
	//Insert Entreprise
	
	$queryStr = "INSERT INTO entreprise(raisonSociale, accepteStagiaire, SIRET, numTel, numFax, activite) 
				VALUES (:rs,1,:siret,:numTel,:numFax,:activite)";
	$query = $pdo->prepare($queryStr);
	$query->bindParam(":rs",$data->rs,PDO::PARAM_STR);
	$query->bindParam(":siret",$data->siretSiege,PDO::PARAM_STR);
	$query->bindParam(":numTel",$data->numTel,PDO::PARAM_STR);
	$query->bindParam(":numFax",$data->numFax,PDO::PARAM_STR);
	$query->bindParam(":activite",$data->activite,PDO::PARAM_STR);
	$query->execute();

	$query = $pdo->prepare("SELECT LAST_INSERT_ID() AS id;");
	$query->execute();
	$ligne = $query->fetch(PDO::FETCH_ASSOC);
	$idEntreprise = $ligne["id"];
	
	//Insert Lieu
	/*
	
	*/
	$queryStr = "INSERT INTO lieu(siret, siretSiege, adresse, adresse2, ville, pays, numTel, numFax, idEntreprise,estSiege) 
				VALUES (:siret,:siretStage,:adr,:adr2,:ville,:pays,:numTel,:numFax,:idEntreprise,:estSiege)";
	$monBool = strpos($data->siret,$data->siretSiege) !== false;
	$query = $pdo->prepare($queryStr);
	$query->bindParam(":siret",$data->siret,PDO::PARAM_STR);
	$query->bindParam(":siretStage",$data->siretSiege,PDO::PARAM_STR);
	$query->bindParam(":adr",$data->adresse,PDO::PARAM_STR);
	$query->bindParam(":adr2",$data->adresse2,PDO::PARAM_STR);
	$query->bindParam(":ville",$data->ville,PDO::PARAM_STR);
	$query->bindParam(":pays",$data->pays,PDO::PARAM_STR);
	$query->bindParam(":numTel",$data->numTelLieu,PDO::PARAM_STR);
	$query->bindParam(":numFax",$data->numFaxLieu,PDO::PARAM_STR);
	$query->bindParam(":idEntreprise",$idEntreprise,PDO::PARAM_STR);
	$query->bindParam(":estSiege",$monBool,PDO::PARAM_BOOL);
	$query->execute();
	
	$query = $pdo->prepare("SELECT * FROM entreprise E INNER JOIN lieu L ON E.idEntreprise = L.idEntreprise AND L.idEntreprise = :idEntreprise");
	$query->bindParam(":idEntreprise",$idEntreprise,PDO::PARAM_STR);
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);
?>