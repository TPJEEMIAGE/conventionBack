<?php
	header('Content-Type: application/json; charset=utf-8');
	include "pdoConfig.php";
	$secId = $_GET["secId"];
	$query = $pdo->prepare("SELECT CONCAT(P.idProfesseur, codeSection) AS idKey,P.idProfesseur, nom, prenom, codeSection 
	FROM professeur P INNER JOIN superviser S ON S.idProfesseur = P.idProfesseur 
	WHERE codeSection = :codeSection");
	$query->bindParam(":codeSection",$secId,PDO::PARAM_STR);
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);
?>