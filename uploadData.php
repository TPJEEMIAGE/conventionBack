<?php
	$pdo = new PDO('mysql:host=localhost;dbname=convention', 'root', '');
	$profQuery = "INSERT INTO professeur VALUES (:idProf,:idPronote,:mdp,:nom,:prenom,:email)";
	$elevesQuery = "INSERT INTO eleve (nom,prenom,idPronote,email,loginPronote) VALUES (:nom,:prenom,:idPronote,:email,:login)";
	$sectionQuery= "INSERT INTO section VALUES (:code,:libelle)";
	$promotionQuery = "INSERT INTO promotion VALUES(:code,:ann)";
	$elevePromotionQuery = "INSERT INTO elevepromotion VALUES(:code,:annee,:ideleve)";
	$entrepriseQuery = "INSERT INTO entreprise (codePronote, raisonSociale, accepteStagiaire, horaires, SIRET, numTel, numFax, activite) 
						VALUES (:codePronote,:raisonSociale,:accepteStagiaire,:horaires,:siret,:numTel,:numFax,:activite)";
	$lieuQuery = "INSERT INTO lieu (adresse, adresse2, ville, pays, idEntreprise, estSiege, numTel, numFax, siret, siretSiege) 
				VALUES (:adresse,:adresse2,:ville,:pays,:idEntreprise,:estSiege, :numTel, :numFax,:siret, :siretSiege)";
	$responsableQuery = "INSERT INTO responsable(codePronote, civilite, nom, prenom, fonction, telFixe, telPortable, mail, adresse, idEntreprise)
						 VALUES (:codePronote,:civilite,:nom,:prenom,:fonction,:telFixe,:telPortable,:mail,:adresse,:idEntreprise)";
	$periodeQuery = "INSERT INTO periode(dateDebut, dateFin, libelSession, codeSection, annee)
					VALUES (:dateDebut,:dateFin,:libel,:codeSection,:ann)";
	$superviserQuery = "INSERT INTO superviser(codeSection, annee, idProfesseur, principal) 
						VALUES (:codeSection,:ann,:idProf,:principal)";
	$stagesQuery = "INSERT INTO stage (typeStage,codePronote, dateDebutEffective, dateFinEffective, description, codeSection, annee, idEleve, idEntreprise, idPeriode, idResponsable,convEleve, convEntre) 
	VALUES (:type,:codePronote,:dateDebut,:dateFin,:desc,:codeSection,:ann,:idEleve,:idEntreprise,:idPeriode,:idResponsable,:convEleve,:convEntre)";
	
	$getStudentsID = "SELECT ideleve,idPronote FROM eleve";
	$getEntrID = "SELECT idEntreprise,codePronote FROM entreprise";
	$getRespNomPrenom = "SELECT nom,prenom FROM responsable";
	$getIDEntreprise = "SELECT E.idEntreprise As idEntr FROM entreprise E INNER JOIN lieu L on L.idEntreprise = E.idEntreprise where raisonSociale = :rs and L.adresse = :adr and L.adresse2 = :adr2";
	$getIdEleve = "SELECT E.idEleve AS idEl FROM eleve E INNER JOIN elevepromotion P ON P.idEleve = E.idEleve where nom = :nom AND prenom = :prenom AND codeSection = :codeSection";
	$getIdProf = "SELECT idProfesseur FROM professeur WHERE nom = :nom AND prenom = :prenom";
	$getIdPeriode = "SELECT idPeriode FROM periode WHERE libelSession = :libel";
	$getIdResp = "SELECT idResponsable FROM responsable WHERE idEntreprise = :idEntreprise AND nom = :nom AND prenom = :prenom";
	
	$once = false;
	
	$manageProfs = true;
	$manageStudents = true;
	$manageEntreprise = true;
	$manageInnerEntreprise = true;
	$manageResp = true;
	$manageSessions = true;
	$manageStages=true;
	
	$names = array();
	for($i = 0;count($_FILES["files"]["name"]) > $i;$i++){
		$names[] = $_FILES['files']['name'][$i];
		move_uploaded_file($_FILES['files']['tmp_name'][$i], "./".$_FILES['files']['name'][$i]);
	}
	
	foreach($names as $name){
		$finalFile = str_replace(".csv",".json",$name);
		$file=$name;
		$json = csvtojson($file,";");
		file_put_contents("./".$finalFile,$json);
	}
	

	//Managing Profs
	if($manageProfs){
		$profs = json_decode(file_get_contents("./EXP_PROFS.json"),true);
		$profsRequests = array();
		$i = 0;
		
		foreach($profs as $prof){
			$currentProf = array();
			$currentProf["idProf"] = $prof["Identifiant_PRONOTE-ENT"];
			$currentProf["idPronote"] = $prof["Identifiant_PRONOTE-ENT"];
			$currentProf["mdp"] = "";
			$currentProf["nom"] = $prof["Nom"];
			$currentProf["prenom"] = $prof["Prenom"];
			$currentProf["email"] = $prof["Adresse_E-mail"];
			
			$profsRequests[$i] = $currentProf;
			$i++;
		}
		
		foreach($profsRequests as $request){
			$query = $pdo->prepare($profQuery);
			$query->bindParam(":idProf",$request["idProf"], PDO::PARAM_STR);
			$query->bindParam(":idPronote",$request["idPronote"], PDO::PARAM_STR);
			$query->bindParam(":mdp",$request["mdp"], PDO::PARAM_STR);
			$query->bindParam(":nom",$request["nom"], PDO::PARAM_STR);
			$query->bindParam(":prenom",$request["prenom"], PDO::PARAM_STR);
			$query->bindParam(":email",$request["email"], PDO::PARAM_STR);
			$query->execute();
		}
	}
	//Managing Students
	if($manageStudents){
		$eleves = json_decode(file_get_contents("./EXP_ELEVE.json"),true);
		$eleveRequests = array();
		$section = array();
		$i = 0;
		
		
		
		foreach($eleves as $eleve){
			$currenteleve = array();
			$currenteleve["idPronote"] = $eleve["IDENT"];
			$currenteleve["loginPronote"] = $eleve["LOGIN"];
			$currenteleve["nom"] = $eleve["NOM"];
			$currenteleve["prenom"] = $eleve["PRENOM"];
			$currenteleve["email"] = $eleve["EMAIL"];
			$currenteleve["section"] = $eleve["CLASSES"];
			$section[] = $eleve["CLASSES"];
			$elevesRequests[$i] = $currenteleve;
			$i++;
		}
		
		$section = array_unique($section);
		$fixedYear = "2021-2022";
		
		foreach($section as $request){
			$query = $pdo->prepare($sectionQuery);
			$query->bindParam(":code",$request, PDO::PARAM_STR);
			$query->bindParam(":libelle",$request, PDO::PARAM_STR);
			$query->execute();
			
			
			
			$query = $pdo->prepare($promotionQuery);
			$query->bindParam(":code",$request, PDO::PARAM_STR);
			$query->bindParam(":ann",$fixedYear, PDO::PARAM_STR);
			$query->execute();
			
		}
		
		foreach($elevesRequests as $request){
			$query = $pdo->prepare($elevesQuery);
			$query->bindParam(":idPronote",$request["idPronote"], PDO::PARAM_STR);
			$query->bindParam(":login",$request["loginPronote"], PDO::PARAM_STR);
			$query->bindParam(":nom",$request["nom"], PDO::PARAM_STR);
			$query->bindParam(":prenom",$request["prenom"], PDO::PARAM_STR);
			$query->bindParam(":email",$request["email"], PDO::PARAM_STR);
			$query->execute();
		}
	
	
		//Linking Proms to Students
		$query = $pdo->prepare($getStudentsID);
		$query->execute();
		$studentsIdArray = array();
		while($result = $query->fetch(PDO::FETCH_ASSOC)){
			$id = $result["idPronote"];
			$studentsIdArray[$id] = $result["ideleve"];
		}
		
		foreach($elevesRequests as $request){
			$query = $pdo->prepare($elevePromotionQuery);
			$query->bindParam(":code",$request["section"], PDO::PARAM_STR);
			$query->bindParam(":annee",$fixedYear, PDO::PARAM_STR);
			$query->bindParam(":ideleve",$studentsIdArray[$request["idPronote"]], PDO::PARAM_STR);
			$query->execute();
		}
	
	}
	
	if($manageEntreprise){
		
		$entreprises = json_decode(file_get_contents("./EXP_ENTREPRISE.json"),true);
		$lieuxRequest = array();
		$responsablesRequest = array();
		$entrRequest = array();
		$i = 0;
		foreach($entreprises as $entreprise){
			$currententr = array();
			$currentlieu = array();
			$currentresp = array();
			$currententr["idPronote"] = $entreprise["NUMERO"];
			$currententr["rs"] = $entreprise["RAISONSOC"];
			$currententr["accepteStagiaire"] = strpos($entreprise["ACC_STAG"],"O")  !== false;
			$currententr["horaires"] = "";
			$currententr["siret"] = $entreprise["SIEGSIRET"];
			$currententr["telFixe"] = $entreprise["SFIXENum"];
			$currententr["fax"] = $entreprise["SFAXNum"];
			$currententr["activite"] = $entreprise["ACTIVITE"];
			
			$currentlieu["idPronote"] = $entreprise["NUMERO"];
			$currentlieu["ville"] = $entreprise["LVILLE"];
			$currentlieu["numFixe"] = $entreprise["LFIXENum"];
			$currentlieu["numFax"] = $entreprise["LFAXNum"];
			$currentlieu["estSiege"] = $entreprise["EST_SIEGE"];
			$currentlieu["pays"] = $entreprise["LPAYS"];
			$currentlieu["adresse"] = $entreprise["LADRES 1"];
			$currentlieu["adresse2"] = $entreprise["LADRES 2"];
			$currentlieu["siegeSiret"] = strpos($entreprise["SIEGSIRET"],"O") !== false;
			$currentlieu["lieuSiret"] = $entreprise["LIEUSIRET"];
			
			$currentresp["idPronote"] = $entreprise["NUMERO"];
			$currentresp["nom"] = $entreprise["NOMRESP"];
			$currentresp["prenom"] = $entreprise["PRENOMRESP"];
			$currentresp["fonction"] = $entreprise["FONCTIONRESP"];
			$currentresp["email"] = $entreprise["REMAIL"];
			$currentresp["telFixe"] = $entreprise["RFIXENum"];
			$currentresp["telPort"] = $entreprise["RPORTABLENum"];
			$currentresp["civilite"] = $entreprise["CIVRESP"];
			
			
			$entrRequest[$i] = $currententr;
			$responsablesRequest[$i] = $currentresp;
			$lieuxRequest[$i] = $currentlieu;
			$i++;
		}
		
		$fixedHoraires = "";
		if($manageInnerEntreprise){
			foreach($entrRequest as $entr){
				
				$query = $pdo->prepare($entrepriseQuery);
				$query->bindParam(":codePronote",$entr["idPronote"], PDO::PARAM_STR);
				$query->bindParam(":raisonSociale",$entr["rs"], PDO::PARAM_STR);
				$query->bindParam(":accepteStagiaire",$entr["accepteStagiaire"], PDO::PARAM_BOOL);
				$query->bindParam(":horaires",$fixedHoraires, PDO::PARAM_STR);
				$query->bindParam(":siret",$entr["siret"], PDO::PARAM_STR);
				$query->bindParam(":numTel",$entr["telFixe"], PDO::PARAM_STR);
				$query->bindParam(":numFax",$entr["fax"], PDO::PARAM_STR);
				$query->bindParam(":activite",$entr["activite"], PDO::PARAM_STR);
				$query->execute();
			}
		}
		
		$query = $pdo->prepare($getEntrID);
		$query->execute();
		$entrIdArray = array();
		while($result = $query->fetch(PDO::FETCH_ASSOC)){
			$id = $result["codePronote"];
			$entrIdArray[$id] = $result["idEntreprise"];
		}
		
		
		
		foreach($lieuxRequest as $entr){
			$query = $pdo->prepare($lieuQuery);
			$query->bindParam(":adresse",$entr["adresse"], PDO::PARAM_STR);
			$query->bindParam(":adresse2",$entr["adresse2"], PDO::PARAM_STR);
			$query->bindParam(":ville",$entr["ville"], PDO::PARAM_STR);
			$query->bindParam(":pays",$entr["pays"], PDO::PARAM_STR);
			$query->bindParam(":idEntreprise",$entrIdArray[$entr["idPronote"]], PDO::PARAM_STR);
			$query->bindParam(":estSiege",$entr["estSiege"], PDO::PARAM_BOOL);
			$query->bindParam(":numFax",$entr["numFax"], PDO::PARAM_STR);
			$query->bindParam(":numTel",$entr["numFixe"], PDO::PARAM_STR);
			$query->bindParam(":siret",$entr["lieuSiret"], PDO::PARAM_STR);
			$query->bindParam(":siretSiege",$entr["siegeSiret"], PDO::PARAM_STR);
			$query->execute();
			
		}
		
		$adrFixe ="";
		if($manageInnerEntreprise){
			foreach($responsablesRequest as $entr){
				$query = $pdo->prepare($responsableQuery);
				$query->bindParam(":codePronote",$entr["idPronote"], PDO::PARAM_STR);
				$query->bindParam(":civilite",$entr["civilite"], PDO::PARAM_STR);
				$query->bindParam(":nom",$entr["nom"], PDO::PARAM_STR);
				$query->bindParam(":prenom",$entr["prenom"], PDO::PARAM_STR);
				$query->bindParam(":fonction",$entr["fonction"], PDO::PARAM_STR);
				$query->bindParam(":idEntreprise",$entrIdArray[$entr["idPronote"]], PDO::PARAM_STR);
				$query->bindParam(":telPortable",$entr["telPort"], PDO::PARAM_STR);
				$query->bindParam(":mail",$entr["email"], PDO::PARAM_STR);
				$query->bindParam(":telFixe",$entr["telFixe"], PDO::PARAM_STR);
				$query->bindParam(":adresse",$adrFixe, PDO::PARAM_STR);
				$query->execute();
			}
		}
	}
	
	if($manageResp){
		$responsables = json_decode(file_get_contents("./EXP_MAITREDESTAGE.json"),true);
		$respRequest = array();
		$respExist = array();
		$query = $pdo->prepare($getRespNomPrenom);
		$query->execute();
		while($result = $query->fetch(PDO::FETCH_ASSOC)){
			$respExist[] = $result["nom"].$result["prenom"];
		}
		foreach($responsables as $responsable){
			if(!in_array($responsable["NOM"].$responsable["PRENOM"],$respExist)){
				
				$currentResp = array();
				$currentResp["codePronote"] = $responsable["NUMERO"];
				$currentResp["civilite"] = $responsable["CIVILITE"];
				$currentResp["nom"] = $responsable["NOM"];
				$currentResp["prenom"] = $responsable["PRENOM"];
				$currentResp["fonction"] = $responsable["FONCTION"];
				$currentResp["telFixe"] = $responsable["MFIXENum"];
				$currentResp["telPortable"] = $responsable["MPORTABLENum"];
				$currentResp["mail"] = $responsable["MEMAIL"];
				$currentResp["adresse"] = $responsable["LADRES 1"];
				$currentResp["adresse2"] = $responsable["LADRES 2"];
				$currentResp["rs"] = $responsable["RAISONSOC"];
				$respRequest[] = $currentResp;
			}
		}
		
		foreach($respRequest as $entr){
			
				$query2 = $pdo->prepare($getIDEntreprise);
				$query2->bindParam(":rs",$entr["rs"], PDO::PARAM_STR);
				$query2->bindParam(":adr",$entr["adresse"], PDO::PARAM_STR);
				$query2->bindParam(":adr2",$entr["adresse2"], PDO::PARAM_STR);
				$query2->execute();
				$result = $query2->fetch(PDO::FETCH_ASSOC);
				$idEntrepriseQ = null;
				try{
					$idEntrepriseQ = $result["idEntr"];
				}
				catch(Exception $e){
					
				}
				if(!$idEntrepriseQ == NULL){
					$currAdr = $entr["adresse"]." ".$entr["adresse2"];
					$query = $pdo->prepare($responsableQuery);
					$query->bindParam(":codePronote",$entr["codePronote"], PDO::PARAM_STR);
					$query->bindParam(":civilite",$entr["civilite"], PDO::PARAM_STR);
					$query->bindParam(":nom",$entr["nom"], PDO::PARAM_STR);
					$query->bindParam(":prenom",$entr["prenom"], PDO::PARAM_STR);
					$query->bindParam(":fonction",$entr["fonction"], PDO::PARAM_STR);
					$query->bindParam(":telPortable",$entr["telPortable"], PDO::PARAM_STR);
					$query->bindParam(":mail",$entr["mail"], PDO::PARAM_STR);
					$query->bindParam(":telFixe",$entr["telFixe"], PDO::PARAM_STR);
					$query->bindParam(":adresse",$currAdr, PDO::PARAM_STR);
					$query->bindParam(":idEntreprise",$idEntrepriseQ, PDO::PARAM_STR);
					$query->execute();
					
				}
				
			}
		
		
	}
	
	if($manageStages){
		$stages = json_decode(file_get_contents("./EXP_STAGIAIRE.json"),true);
		$sessions = array();
		$stageReqs = array();
		
		foreach($stages as $stage){
			$currentSession = array();
			$dateDebut = substr($stage["DUREE"],3,8);
			$dateFin = substr($stage["DUREE"],15,8);
			
			$currentStage["codePronote"] = $stage["NUMERO"];
			$currentStage["dateDebut"] = $dateDebut;
			$currentStage["dateFin"] = $dateFin;
			$currentStage["nomEleve"] = $stage["NOM ELEVE"];
			$currentStage["prenomEleve"] = $stage["PRENOM ELEVE"];
			$currentStage["classe"] = $stage["CLASSE"];
			$currentStage["adr"] = $stage["LADRES 1"];
			$currentStage["adr2"] = $stage["LADRES 2"];
			$currentStage["ref"] = $stage["REFERENT"];
			$currentStage["session"] = $stage["SESSION STAGE"];
			$currentStage["respEntrepriseNom"] = $stage["NOM"];
			$currentStage["respEntreprisePrenom"] = $stage["PRENOM"];
			$currentStage["convEleve"] = strpos($stage["CONV_ELEVE"],"O") !== false;
			$currentStage["convEntr"] = strpos($stage["CONV_ENTRE"],"O") !== false;
			$currentStage["type"] = $stage["TYPE"];
			$currentStage["rs"] = $stage["RAISONSOC"];
			
			$currentSession["dateDebut"] = $dateDebut;
			$currentSession["dateFin"] = $dateFin;
			$currentSession["libel"] = $stage["SESSION STAGE"];
			$currentSession["ref"] = $stage["REFERENT"];
			$currentSession["classe"] = $stage["CLASSE"];
			$sessions[] = $currentSession;
			$stageReqs[] = $currentStage;
		}
		
		$sessionFinal = array();
		$anneeFix = "2021-2022";
		$finalRefs = array();
		$libelTraite = array();
		$principalFix = true;
		foreach($sessions as $session){
			
			$refs = explode(",",$session["ref"]);
			for($i = 0;count($refs) > $i;$i++){
				$refsTemp = trim($refs[$i]);
				$refsTemp = explode(" ",$refsTemp);
				$refAdd = array();
				if(count($refsTemp) >= 2){
					$refAdd["civ"] = $refsTemp[0];
					$refAdd["nom"] = $refsTemp[1];
					$refAdd["prenom"] = $refsTemp[2];
					$refAdd["classe"] = $session["classe"];
					$finalRefs[$session["classe"].$refsTemp[1]] = $refAdd;
				}
			}
			
			
			if($manageSessions){
				if(!in_array($session["libel"],$libelTraite)){
					$dateDebut = DateTime::createFromFormat("d/m/y",$session["dateDebut"])->format("Y-m-d");
					$dateFin = DateTime::createFromFormat("d/m/y",$session["dateFin"])->format("Y-m-d");
					$query = $pdo->prepare($periodeQuery);
					$query->bindParam(":dateDebut",$dateDebut, PDO::PARAM_STR);
					$query->bindParam(":dateFin",$dateFin, PDO::PARAM_STR);
					$query->bindParam(":libel",$session["libel"], PDO::PARAM_STR);
					$query->bindParam(":codeSection",$session["classe"], PDO::PARAM_STR);
					$query->bindParam(":ann",$anneeFix, PDO::PARAM_STR);
					$query->execute();
					$libelTraite[]= $session["libel"];
				}
				foreach($finalRefs as $fnRef){
					$query = $pdo->prepare($getIdProf);
					$query->bindParam(":nom",$fnRef["nom"], PDO::PARAM_STR);
					$query->bindParam(":prenom",$fnRef["prenom"], PDO::PARAM_STR);
					$query->execute();
					
					$res = $query->fetch(PDO::FETCH_ASSOC);
					$idProf = $res["idProfesseur"];
					$query2 = $pdo->prepare($superviserQuery);
					$query2->bindParam(":codeSection",$fnRef["classe"], PDO::PARAM_STR);
					$query2->bindParam(":ann",$anneeFix, PDO::PARAM_STR);
					$query2->bindParam(":idProf",$idProf, PDO::PARAM_STR);
					$query2->bindParam(":principal",$principalFix, PDO::PARAM_BOOL);
					$query2->execute();
					
				}
			}
			
			
		}
		
		$annFix = "2021-2022";
		$descFix = " ";
		$once = true;
		foreach($stageReqs as $stgQuery){
			$queryIdEleve = $pdo->prepare($getIdEleve);
			$queryIdEntr = $pdo->prepare($getIDEntreprise);
			$queryIdPeriode = $pdo->prepare($getIdPeriode);
			$queryIdResp = $pdo->prepare($getIdResp);
			
			$queryIdEleve->bindParam(":nom",$stgQuery["nomEleve"],PDO::PARAM_STR);
			$queryIdEleve->bindParam(":prenom",$stgQuery["prenomEleve"],PDO::PARAM_STR);
			$queryIdEleve->bindParam(":codeSection",$stgQuery["classe"],PDO::PARAM_STR);
			$queryIdEleve->execute();
			$result = $queryIdEleve->fetch(PDO::FETCH_ASSOC);
			$idEleve = $result["idEl"];
			
			
			$queryIdEntr->bindParam(":rs",$stgQuery["rs"],PDO::PARAM_STR);
			$queryIdEntr->bindParam(":adr",$stgQuery["adr"],PDO::PARAM_STR);
			$queryIdEntr->bindParam(":adr2",$stgQuery["adr2"],PDO::PARAM_STR);
			$queryIdEntr->execute();
			$result = $queryIdEntr->fetch(PDO::FETCH_ASSOC);
			$idEntreprise = $result["idEntr"];
			
			$queryIdPeriode->bindParam(":libel",$stgQuery["session"],PDO::PARAM_STR);
			$queryIdPeriode->execute();
			$result = $queryIdPeriode->fetch(PDO::FETCH_ASSOC);
			$idPeriode = $result["idPeriode"];
			
			$queryIdResp->bindParam(":idEntreprise",$idEntreprise,PDO::PARAM_STR);
			$queryIdResp->bindParam(":nom",$stgQuery["respEntrepriseNom"],PDO::PARAM_STR);
			$queryIdResp->bindParam(":prenom",$stgQuery["respEntreprisePrenom"],PDO::PARAM_STR);
			$queryIdResp->execute();
			$result = $queryIdResp->fetch(PDO::FETCH_ASSOC);

			$idResponsable = $result["idResponsable"];
			
			$dateDebut = DateTime::createFromFormat("d/m/y",$stgQuery["dateDebut"])->format("Y-m-d");
			$dateFin = DateTime::createFromFormat("d/m/y",$stgQuery["dateFin"])->format("Y-m-d");
			
			$query = $pdo->prepare($stagesQuery);
			$query->bindParam(":type",$stgQuery["type"] , PDO::PARAM_STR);
			$query->bindParam(":codePronote",$stgQuery["codePronote"] , PDO::PARAM_STR);
			$query->bindParam(":dateDebut", $dateDebut, PDO::PARAM_STR);
			$query->bindParam(":dateFin", $dateFin, PDO::PARAM_STR);
			$query->bindParam(":desc", $descFix, PDO::PARAM_STR);
			$query->bindParam(":codeSection", $stgQuery["classe"] , PDO::PARAM_STR);
			$query->bindParam(":ann", $annFix, PDO::PARAM_STR);
			$query->bindParam(":idEleve", $idEleve, PDO::PARAM_STR);
			$query->bindParam(":idEntreprise",$idEntreprise , PDO::PARAM_STR);
			$query->bindParam(":idPeriode", $idPeriode, PDO::PARAM_STR);
			$query->bindParam(":idResponsable",$idResponsable , PDO::PARAM_STR);
			$query->bindParam(":convEleve",$stgQuery["convEleve"] , PDO::PARAM_BOOL);
			$query->bindParam(":convEntre", $stgQuery["convEntr"], PDO::PARAM_BOOL);
			$query->execute();
			if($once){
				$query->debugDumpParams();
				$once = false;
			}
		}
	}
	
	
	
	function csvtojson($file,$delimiter)
	{
		if (($handle = fopen($file, "r")) === false)
		{
				echo "Problem with File : ".$file;
				die("can't open the file.");
		}

		$csv_headers = fgetcsv($handle, 4000, $delimiter);
		$csv_json = array();

		while ($row = fgetcsv($handle, 4000, $delimiter))
		{
				$csv_json[] = array_combine($csv_headers, $row);
		}
		fclose($handle);
		
		return json_encode($csv_json);
	}
?>