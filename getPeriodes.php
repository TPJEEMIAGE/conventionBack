<?php
	header('Content-Type: application/json; charset=utf-8');
	include "pdoConfig.php";
	$codeSection = $_GET["codeSection"];
	$query = $pdo->prepare("SELECT * FROM periode WHERE codeSection=:codeSection");
	$query->bindParam(":codeSection",$codeSection,PDO::PARAM_STR);
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);
?>