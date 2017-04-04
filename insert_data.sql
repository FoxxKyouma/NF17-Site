INSERT INTO Pays VALUES ('Angleterre');
INSERT INTO Pays VALUES ('France');
INSERT INTO Pays VALUES ('Belgique');
INSERT INTO Pays VALUES ('Suisse');

-- Nouveaux Utilisateurs
INSERT INTO Utilisateur (pseudo, prenom, nom, date_naissance, sexe, pays, email, password, titre_profil, prive) VALUES ('Jclaude','Jean-claude','Convenant','1960-06-06','homme', 'France', 'convenant@gmail.com','0000', 'JCProfil',true);
INSERT INTO Utilisateur (pseudo, prenom, nom, date_naissance, sexe, pays, email, password, titre_profil, prive) VALUES ('Rernard','Bernard','Renard','1960-12-06','homme', 'France', 'rernard@gmail.com','1111', 'RernardProfil',false);
INSERT INTO Utilisateur (pseudo, prenom, nom, date_naissance, sexe, pays, email, password, titre_profil, prive) VALUES ('foo', 'Martin', 'Dupont', '1990-01-01', 'homme', 'France', 'martin.dupont@foo.fr', '1234', 'Martin', false);
INSERT INTO Utilisateur(pseudo, prenom, nom, date_naissance, sexe, pays, email, password, titre_profil, prive) VALUES ('jojo', 'John', 'John', '1991-12-02', 'homme', 'France',  'johnjohn@google.com', 'azerty', 'JohnJohn', false);
-- Doit être rejeté
--INSERT INTO Utilisateur(pseudo, prenom, nom, date_naissance, sexe, pays, email, password, titre_profil, prive) VALUES ('foo', 'John', 'John', '1991-12-02', 'homme', 'France',  'johnjohn@google.com', 'azerty', 'JohnJohn', false);

-- Amitié !
INSERT INTO Ami (suiveur, suivie) VALUES ('foo', 'jojo');
-- Doivent être rejetés
--INSERT INTO Ami (suiveur, suivie) VALUES ('foo', 'jojo');
--INSERT INTO Ami (suiveur, suivie) VALUES ('foo', 'foo');

--Demande d'ami
INSERT INTO Demande_Ami (suiveur,suivie) VALUES ('Jclaude', 'foo');

-- Nouvelle Photo
INSERT INTO Objet_Aimable (id_objet, proprietaire) VALUES (1, 'foo');
INSERT INTO Photo (id_photo, titre, legende, url, album) VALUES (1, 'Panda', 'Joli panda', 'http://intelligenttravel.nationalgeographic.com/files/2010/03/China_wildlife_panda.jpg', NULL);

-- Nouvel album
INSERT INTO Album (id_album, titre, legende, proprietaire, vignette) VALUES (1, 'Pandas', 'Panorama de panda', 'foo', 1);
-- Ajout de la photo précédente
UPDATE Photo SET album = 1 WHERE Photo.id_photo = 1;

-- Ajout d'un commentaire
INSERT INTO Objet_Aimable (id_objet, proprietaire) VALUES (2, 'jojo');
INSERT INTO Commentaire (id_com, photo, texte) VALUES (2, 1, 'C&amp;est moche');

-- Ajout des tag
INSERT INTO Tag (texte) VALUES ('Yolo');
INSERT INTO Tag (texte) VALUES ('UTC');
INSERT INTO Tag (texte) VALUES ('FLR');
INSERT INTO Tag (texte) VALUES ('Paris');
INSERT INTO Tag (texte) VALUES ('BLC');
INSERT INTO Tag (texte) VALUES ('cestpasbien');
--Doit etre rejete
--INSERT INTO Tag (texte) VALUES ('cest pasbien');

-- Ajout des tags au commentaire précédent
INSERT INTO Assoc_TagCommentaire (id_com, tag) VALUES (2, 'Yolo');
INSERT INTO Assoc_TagCommentaire (id_com, tag) VALUES (2, 'UTC');
INSERT INTO Assoc_TagCommentaire (id_com, tag) VALUES (2, 'FLR');


-- Doit etre rejete
--INSERT INTO Assoc_TagCommentaire (id_com, tag) VALUES (2, 'FLR');

-- doit être accepté

INSERT INTO Assoc_TagCommentaire (id_com, tag) VALUES (2, 'Paris');
INSERT INTO Assoc_TagCommentaire (id_com, tag) VALUES (2, 'BLC');

-- Doit etre rejeté (6ème)
--INSERT INTO Assoc_TagCommentaire (id_com, tag) VALUES (2, 'cestpasbien');

-- J'aime !
INSERT INTO Aimer (objet, utilisateur, nature) VALUES (1, 'jojo', 1);
