<?php
	header('Content-Type: application/json; charset=utf-8');
	$data = json_decode(file_get_contents("php://input"));
	include "pdoConfig.php";
	
	$queryStr = "SELECT P.libelSession AS SESSION_STAGE, \"En entreprise\" AS stage_type, ST.description AS DETAIL_SUJET, CONCAT(\"du \",ST.dateDebutEffective,\" au \",ST.dateFinEffective) AS DUREE,
      \"O\" AS CONV_ELEVE, \"N\" AS CONV_ENTREPRISE, EL.nom as NOM_ELEVE, EL.prenom as PRENOM_ELEVE, EP.codeSection as CLASSE, CONCAT(PR.nom, \" \",PR.prenom) AS REFERENT, RS.civilite AS CIVILITE, RS.nom as NOM, RS.prenom AS PRENOM,
	  RS.mail AS MEMAIL, RS.telFixe AS MFIXENum, RS.telPortable AS MPORTABLENum, RS.fonction AS FONCTION, ET.activite AS ACTIVITE, L.adresse AS LADRES_1, L.adresse2 AS LADRES_2, L.ville as LVILLE,L.numTel AS LFIXENum, L.numFax AS LFAXNum,
	  L.siret AS LIEUSIRET
	  FROM periode P
	  INNER JOIN stage ST
	  ON ST.idPeriode = P.idPeriode
	  INNER JOIN elevepromotion EP
	  ON EP.idEleve = ST.idEleve
	  INNER JOIN eleve EL
	  ON EL.idEleve = EP.idEleve
	  INNER JOIN promotion PRO
	  ON PRO.codeSection = P.codeSection
	  INNER JOIN superviser SP
	  ON SP.codeSection = PRO.codeSection
	  INNER JOIN professeur PR
	  ON PR.idProfesseur = SP.idProfesseur
	  INNER JOIN responsable RS
	  ON RS.idResponsable = ST.idResponsable
	  INNER JOIN entreprise ET
	  ON ET.idEntreprise = ST.idEntreprise
	  INNER JOIN lieu L
	  ON L.idEntreprise = ET.idEntreprise
	  WHERE ST.export = :export
	  AND ST.convEntre = :convEntre
	  AND dateValidation > :dateValidation";
	if(!$data->nonValid){
		$queryStr .= " AND dateValidation IS NOT NULL";
	}
	else{
		$queryStr .=" OR dateValidation IS NULL";
	}
	$query = $pdo->prepare($queryStr);
	$query->bindParam(":export",$data->alryExport,PDO::PARAM_BOOL);
	$query->bindParam(":convEntre",$data->convEntre,PDO::PARAM_BOOL);
	$query->bindParam(":dateValidation",$data->date,PDO::PARAM_STR);
	
	$query->execute();
	$finalTab = array();
	$nbCol = $query->columnCount();
	$CSVheader = array();
	for($i = 0;$i<$nbCol;$i++){
		$CSVheader[] = $query->getColumnMeta($i)["name"];
	}
	$finalTab[] = $CSVheader;
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	file_put_contents("./export.csv","");
	$filepointer = fopen("./export.csv","w");
	foreach($finalTab as $obj){
		fputcsv($filepointer,$obj,";");
	}
	fclose($filepointer);
?>