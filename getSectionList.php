<?php
	header('Content-Type: application/json; charset=utf-8');
	include "pdoConfig.php";
	$query = $pdo->prepare("SELECT DISTINCT codeSection FROM superviser S");
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);
?>