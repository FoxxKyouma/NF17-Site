DROP TABLE if exists Assoc_TagCommentaire CASCADE;
DROP TABLE if exists Tag CASCADE;
DROP TABLE if exists Commentaire CASCADE;
DROP TABLE if exists Aimer CASCADE;
ALTER TABLE Album DROP if exists vignette;
DROP TABLE if exists Photo CASCADE;
DROP TABLE if exists Album CASCADE;
DROP TABLE if exists Objet_Aimable CASCADE;
DROP TABLE if exists Demande_Ami CASCADE;
DROP TABLE if exists Ami CASCADE;
DROP TABLE if exists Utilisateur CASCADE;
DROP TABLE if exists Pays CASCADE;

CREATE TABLE Pays (
nom VARCHAR,
PRIMARY KEY (nom)
);

CREATE TABLE Utilisateur (
pseudo  VARCHAR,
prenom VARCHAR NOT NULL,
nom VARCHAR NOT NULL,
date_naissance DATE NOT NULL,
sexe VARCHAR CHECK (sexe in ('homme','femme')),
pays VARCHAR NOT NULL,
email VARCHAR NOT NULL,
password VARCHAR NOT NULL,
titre_profil VARCHAR UNIQUE,
prive boolean NOT NULL,
PRIMARY KEY (pseudo),
FOREIGN KEY (pays) REFERENCES Pays (nom)
);

CREATE TABLE Ami(
suiveur VARCHAR REFERENCES Utilisateur(pseudo),
suivie VARCHAR REFERENCES Utilisateur(pseudo),
PRIMARY KEY(suiveur,suivie),
CHECK( suiveur != suivie )
);

CREATE TABLE Demande_Ami(
suiveur VARCHAR REFERENCES Utilisateur(pseudo),
suivie VARCHAR REFERENCES Utilisateur(pseudo),
PRIMARY KEY(suiveur,suivie),
CHECK( suiveur != suivie )
);

-- Classe mère de Photos et des Commentaires

CREATE TABLE Objet_Aimable (
id_objet INTEGER PRIMARY KEY,
proprietaire VARCHAR NOT NULL,
FOREIGN KEY (proprietaire) REFERENCES Utilisateur (pseudo)
);

CREATE TABLE Album(
id_album INTEGER PRIMARY KEY,
titre VARCHAR NOT NULL,
legende VARCHAR,
proprietaire VARCHAR NOT NULL,
FOREIGN KEY (proprietaire) REFERENCES Utilisateur (pseudo)
);


CREATE TABLE Photo(
id_photo INTEGER,
titre VARCHAR NOT NULL,
legende VARCHAR,
url VARCHAR NOT NULL,
album INTEGER,
FOREIGN KEY (id_photo) REFERENCES Objet_Aimable (id_objet),
FOREIGN KEY (album) REFERENCES Album (id_album),
PRIMARY KEY (id_photo)
);

ALTER TABLE Album ADD vignette INTEGER UNIQUE REFERENCES Photo (id_photo);


CREATE TABLE Aimer (
    objet INTEGER,
    utilisateur VARCHAR,
    nature INTEGER CHECK (nature in (-1, 1)),
    PRIMARY KEY (objet, utilisateur),
    FOREIGN KEY (objet) REFERENCES Objet_Aimable(id_objet),
    FOREIGN KEY (utilisateur) REFERENCES Utilisateur(pseudo)

    );


CREATE TABLE Commentaire(
    id_com INTEGER,
    photo INTEGER NOT NULL,
    texte TEXT NOT NULL,
    PRIMARY KEY(id_com),
    FOREIGN KEY (id_com) REFERENCES Objet_Aimable(id_objet),
    FOREIGN KEY (photo) REFERENCES Photo(id_photo)
) ;

CREATE TABLE Tag (
    texte VARCHAR,
    PRIMARY KEY(texte),
    CHECK(position( ' ' in texte)=0)
);

DROP FUNCTION if exists verifTag(id_commentaire INTEGER);

CREATE OR REPLACE FUNCTION verifTag(id_commentaire INTEGER)
RETURNS BOOLEAN AS $$
BEGIN
	IF (SELECT COUNT(*) FROM Assoc_TagCommentaire WHERE id_commentaire = Assoc_TagCommentaire.id_com) < 5 THEN
		RETURN TRUE;
	ELSE
    RETURN FALSE;
  END IF;
END;
$$ LANGUAGE plpgsql;



CREATE TABLE Assoc_TagCommentaire (
id_com INTEGER,
tag VARCHAR,
PRIMARY KEY (id_com, tag),
FOREIGN KEY (id_com) REFERENCES Commentaire(id_com),
FOREIGN KEY (tag) REFERENCES Tag(texte),
CHECK(verifTag(id_com))
);
