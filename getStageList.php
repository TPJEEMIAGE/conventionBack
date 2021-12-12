<?php
	header('Content-Type: application/json; charset=utf-8');
	include "pdoConfig.php";
	$secId = $_GET["secId"];
	$profId = $_GET["profId"];
	$query = $pdo->prepare("
	SELECT S.idStage, S.idEntreprise, S.description, S.dateValidation, S.idEleve, EL.nom AS nomEleve, EL.prenom As prenomEleve, R.nom, R.prenom, R.fonction, R.telFixe, R.telPortable, R.mail, ET.raisonSociale, ET.activite, L.adresse, L.adresse2, L.ville, L.pays, L.numTel, L.numFax
	FROM stage S, elevepromotion EP, eleve EL, responsable R, entreprise ET, lieu L, promotion PR, periode P, superviser SV
	WHERE S.codeSection = EP.codeSection
	AND S.annee = EP.annee
	AND S.idEleve = EP.idEleve
	AND EP.idEleve = EL.idEleve
	AND S.idResponsable = R.idResponsable
	AND R.idEntreprise = ET.idEntreprise
	AND ET.idEntreprise = L.idEntreprise
	AND S.idPeriode = P.idPeriode
	AND P.codeSection = PR.codeSection
	AND P.annee = PR.annee
	AND PR.codeSection = SV.codeSection
	AND PR.annee = SV.annee
	AND S.codeSection = :codeSection
	AND idProfesseur = :idProfesseur");
	$query->bindParam(":codeSection",$secId,PDO::PARAM_STR);
	$query->bindParam(":idProfesseur",$profId,PDO::PARAM_STR);
	$query->execute();
	$finalTab = array();
	while($ligne = $query->fetch(PDO::FETCH_ASSOC)){
		$finalTab[] = $ligne; 
	}
	echo json_encode($finalTab);

?>