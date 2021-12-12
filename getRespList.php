<?php
	header('Content-Type: application/json; charset=utf-8');
	include "pdoConfig.php";
	$idEntre = $_GET["idEntre"];
	$query = $pdo->prepare("SELECT * FROM responsable R WHERE idEntreprise=:idEntreprise");
	$query->bindParam(":idEntreprise",$idEntre,PDO::PARAM_STR);
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);
?>