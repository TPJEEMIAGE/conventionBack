#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: Entreprise
#------------------------------------------------------------

CREATE TABLE Entreprise(
        idEntreprise     Int  Auto_increment  NOT NULL ,
        codePronote      Varchar (50) NOT NULL ,
        raisonSociale    Varchar (255) ,
        accepteStagiaire Boolean ,
        horaires         Varchar (255) ,
        SIRET            Varchar (255) ,
        numTel           Varchar (255) ,
        numFax           Varchar (255) ,
        activite         Varchar (255)
	,CONSTRAINT Entreprise_PK PRIMARY KEY (idEntreprise)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Eleve
#------------------------------------------------------------

CREATE TABLE Eleve(
        idEleve      Int  Auto_increment  NOT NULL ,
        nom          Varchar (50) NOT NULL ,
        prenom       Varchar (50) NOT NULL ,
        idPronote    Varchar (50) NOT NULL ,
        email        Varchar (255) NOT NULL ,
        loginPronote Varchar (255) NOT NULL
	,CONSTRAINT Eleve_PK PRIMARY KEY (idEleve)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Section
#------------------------------------------------------------

CREATE TABLE Section(
        codeSection    Varchar (50) NOT NULL ,
        libelleSection Varchar (50) NOT NULL
	,CONSTRAINT Section_PK PRIMARY KEY (codeSection)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Professeur
#------------------------------------------------------------

CREATE TABLE Professeur(
        idProfesseur Varchar (50) NOT NULL ,
        idPronote    Varchar (50) NOT NULL ,
        mdp          Varchar (50) NOT NULL ,
        nom          Varchar (50) NOT NULL ,
        prenom       Varchar (50) NOT NULL ,
        email        Varchar (255) NOT NULL
	,CONSTRAINT Professeur_PK PRIMARY KEY (idProfesseur)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Promotion
#------------------------------------------------------------

CREATE TABLE Promotion(
        codeSection Varchar (50) NOT NULL ,
        annee       Varchar (50) NOT NULL
	,CONSTRAINT Promotion_PK PRIMARY KEY (codeSection,annee)

	,CONSTRAINT Promotion_Section_FK FOREIGN KEY (codeSection) REFERENCES Section(codeSection)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: ElevePromotion
#------------------------------------------------------------

CREATE TABLE ElevePromotion(
        codeSection Varchar (50) NOT NULL ,
        annee       Varchar (50) NOT NULL ,
        idEleve     Int NOT NULL
	,CONSTRAINT ElevePromotion_PK PRIMARY KEY (codeSection,annee,idEleve)

	,CONSTRAINT ElevePromotion_Promotion_FK FOREIGN KEY (codeSection,annee) REFERENCES Promotion(codeSection,annee)
	,CONSTRAINT ElevePromotion_Eleve0_FK FOREIGN KEY (idEleve) REFERENCES Eleve(idEleve)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Periode
#------------------------------------------------------------

CREATE TABLE Periode(
        idPeriode    Int  Auto_increment  NOT NULL ,
        dateDebut    Date NOT NULL ,
        dateFin      Date NOT NULL ,
        libelSession Varchar (50) NOT NULL ,
        codeSection  Varchar (50) NOT NULL ,
        annee        Varchar (50) NOT NULL
	,CONSTRAINT Periode_PK PRIMARY KEY (idPeriode)

	,CONSTRAINT Periode_Promotion_FK FOREIGN KEY (codeSection,annee) REFERENCES Promotion(codeSection,annee)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Responsable
#------------------------------------------------------------

CREATE TABLE Responsable(
        idResponsable Int  Auto_increment  NOT NULL ,
        codePronote   Varchar (50) NOT NULL ,
        civilite      Varchar (255) NOT NULL ,
        nom           Varchar (255) NOT NULL ,
        prenom        Varchar (255) NOT NULL ,
        fonction      Varchar (255) NOT NULL ,
        telFixe       Varchar (255) NOT NULL ,
        telPortable   Varchar (255) NOT NULL ,
        mail          Varchar (255) NOT NULL ,
        adresse       Varchar (1000) NOT NULL ,
        idEntreprise  Int NOT NULL
	,CONSTRAINT Responsable_PK PRIMARY KEY (idResponsable)

	,CONSTRAINT Responsable_Entreprise_FK FOREIGN KEY (idEntreprise) REFERENCES Entreprise(idEntreprise)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Stage
#------------------------------------------------------------

CREATE TABLE Stage(
        idStage            Int  Auto_increment  NOT NULL ,
        codePronote        Varchar (50) NOT NULL ,
        typeStage             Varchar (50) NOT NULL ,
        dateDebutEffective Date NOT NULL ,
        dateFinEffective   Date NOT NULL ,
        convEleve          Boolean NOT NULL ,
        convEntre          Boolean NOT NULL ,
        description        Varchar (1000) NOT NULL ,
        dateValidation     Date ,
        export             Boolean NOT NULL ,
        codeSection        Varchar (50) NOT NULL ,
        annee              Varchar (50) NOT NULL ,
        idEleve            Int NOT NULL ,
        idEntreprise       Int NOT NULL ,
        idPeriode          Int NOT NULL ,
        idResponsable      Int NOT NULL
	,CONSTRAINT Stage_PK PRIMARY KEY (idStage)

	,CONSTRAINT Stage_ElevePromotion_FK FOREIGN KEY (codeSection,annee,idEleve) REFERENCES ElevePromotion(codeSection,annee,idEleve)
	,CONSTRAINT Stage_Entreprise0_FK FOREIGN KEY (idEntreprise) REFERENCES Entreprise(idEntreprise)
	,CONSTRAINT Stage_Periode1_FK FOREIGN KEY (idPeriode) REFERENCES Periode(idPeriode)
	,CONSTRAINT Stage_Responsable2_FK FOREIGN KEY (idResponsable) REFERENCES Responsable(idResponsable)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Lieu
#------------------------------------------------------------

CREATE TABLE Lieu(
        idLieu       Int  Auto_increment  NOT NULL ,
        siret        Varchar (50) NOT NULL ,
		siretSiege   Varchar (50) NOT NULL ,
        adresse      Varchar (1000) NOT NULL ,
        adresse2     Varchar (1000) NOT NULL ,
        ville        Varchar (255) NOT NULL ,
        pays         Varchar (255) NOT NULL ,
        numTel       Varchar (50) NOT NULL ,
        numFax       Varchar (50) NOT NULL ,
        estSiege     Boolean NOT NULL ,
        idEntreprise Int NOT NULL
	,CONSTRAINT Lieu_PK PRIMARY KEY (idLieu)

	,CONSTRAINT Lieu_Entreprise_FK FOREIGN KEY (idEntreprise) REFERENCES Entreprise(idEntreprise)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Superviser
#------------------------------------------------------------

CREATE TABLE Superviser(
        codeSection  Varchar (50) NOT NULL ,
        annee        Varchar (50) NOT NULL ,
        idProfesseur Varchar (50) NOT NULL ,
        principal    Boolean NOT NULL
	,CONSTRAINT Superviser_PK PRIMARY KEY (codeSection,annee,idProfesseur)
	,CONSTRAINT Superviser_Promotion_FK FOREIGN KEY (codeSection,annee) REFERENCES Promotion(codeSection,annee)
	,CONSTRAINT Superviser_Professeur0_FK FOREIGN KEY (idProfesseur) REFERENCES Professeur(idProfesseur)
)ENGINE=InnoDB;
