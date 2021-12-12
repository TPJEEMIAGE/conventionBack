<?php
	header('Content-Type: application/json; charset=utf-8');
	$data = json_decode(file_get_contents("php://input"));
	include "pdoConfig.php";
	
	$query = $pdo->prepare("INSERT INTO  stage (typeStage ,  dateDebutEffective ,  dateFinEffective ,  convEleve ,  convEntre ,  description , export , codeSection ,  annee ,  idEleve ,  idEntreprise ,  idPeriode ,  idResponsable ) 
	VALUES (:typeStage,:dateDebut,:dateFin,:convEleve,:convEntre,:desc,0,:codeSection,:annee,:idEleve,:idEntreprise,:idPeriode,:idResponsable)");
	$query->bindParam(":typeStage",$data->typeStage,PDO::PARAM_STR);
	$query->bindParam(":dateDebut",$data->dateDebutEffective,PDO::PARAM_STR);
	$query->bindParam(":dateFin",$data->dateFinEffective,PDO::PARAM_STR);
	$query->bindParam(":convEleve",$data->convEleve,PDO::PARAM_STR);
	$query->bindParam(":convEntre",$data->convEntre,PDO::PARAM_STR);
	$query->bindParam(":codeSection",$data->codeSection,PDO::PARAM_STR);
	$query->bindParam(":desc",$data->description,PDO::PARAM_STR);
	$query->bindParam(":annee",$data->annee,PDO::PARAM_STR);
	$query->bindParam(":idEleve",$data->idEleve,PDO::PARAM_STR);
	$query->bindParam(":idEntreprise",$data->idEntreprise,PDO::PARAM_STR);
	$query->bindParam(":idPeriode",$data->idPeriode,PDO::PARAM_STR);
	$query->bindParam(":idResponsable",$data->idResponsable,PDO::PARAM_STR);
	$query->execute();
?>