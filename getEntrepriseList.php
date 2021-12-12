<?php
	header('Content-Type: application/json; charset=utf-8');
	include "pdoConfig.php";
	$query = $pdo->prepare("SELECT * FROM entreprise E INNER JOIN lieu L ON E.idEntreprise = L.idEntreprise");
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);
?>