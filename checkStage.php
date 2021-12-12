<?php
	header('Content-Type: application/json; charset=utf-8');
	include "pdoConfig.php";
	$idStage = $_GET["idStage"];
	$dateVal = $_GET["dateVal"];
	$query = null;
	if($dateVal === "cancel"){
		$query = $pdo->prepare("UPDATE stage set dateValidation=NULL WHERE idStage=:idStage");
	}
	else{
		$query = $pdo->prepare("UPDATE stage set dateValidation=:dateValidation WHERE idStage=:idStage");
		$query->bindParam(":dateValidation",$dateVal,PDO::PARAM_STR);
	}
	$query->bindParam(":idStage",$idStage,PDO::PARAM_STR);
	$query->execute();
?>