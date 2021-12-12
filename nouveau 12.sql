SELECT P.libelSession AS SESSION_STAGE, "En entreprise" AS stage_type, ST.description AS DETAIL_SUJET, CONCAT("du ",ST.dateDebutEffective," au ",ST.dateFinEffective) AS DUREE,
      "O" AS CONV_ELEVE, "N" AS CONV_ENTREPRISE, EL.nom as NOM_ELEVE, EL.prenom as PRENOM_ELEVE, EP.codeSection as CLASSE, CONCAT(PR.nom, " ",PR.prenom) AS REFERENT, RS.civilite AS CIVILITE, RS.nom as NOM, RS.prenom AS PRENOM,
	  RS.mail AS MEMAIL, RS.telFixe AS MFIXENum, RS.telPortable AS MPORTABLENum, RS.fonction AS FONCTION, ET.activite AS ACTIVITE, L.adresse AS LADRES_1, L.adresse2 AS LADRES_2, L.ville as LVILLE,L.numTel AS LFIXENum, L.numFax AS LFAXNum,
	  L.siret AS LIEUSIRET
	  FROM Periode P
	  INNER JOIN Stage ST
	  ON ST.idPeriode = P.idPeriode
	  INNER JOIN elevepromotion EP
	  ON EP.idEleve = ST.idEleve
	  INNER JOIN Eleve EL
	  ON EL.idEleve = EP.idEleve
	  INNER JOIN Promotion PRO
	  ON PRO.codeSection = P.codeSection
	  INNER JOIN Superviser SP
	  ON SP.codeSection = PRO.codeSection
	  INNER JOIN Professeur PR
	  ON PR.idProfesseur = SP.idProfesseur
	  INNER JOIN Responsable RS
	  ON RS.idResponsable = ST.idResponsable
	  INNER JOIN Entreprise ET
	  ON ET.idEntreprise = ST.idEntreprise
	  INNER JOIN Lieu L
	  ON L.idEntreprise = ET.idEntreprise
	  WHERE ST.export = 0
	  AND ST.convEntre = 0
	  AND dateValidation IS NOT NULL
	  