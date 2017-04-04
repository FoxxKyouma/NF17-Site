<HTML>
<HEAD>
<META charset=utf-8>
<TITLE> Inscription à Instalol </TITLE>
</HEAD>
<BODY>
<?php
include('config.php');
if(isset($_POST['envoyer']))
{
	// Vérifie que la date est au bon format
	function valid_date($a) {
		return (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $a));
	}

	// On vérifie que tous les champs sont bien définis
	if (!isset($_POST['pseudo']) || !isset($_POST['prenom']) || !isset($_POST['nom']) || !isset($_POST['datenaiss']) || !isset($_POST['sexe']) || !isset($_POST['pays']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['password2']) || !isset($_POST['titre_profil']) || !isset($_POST['confidentialite']))
	{
		die("Erreur : tous les champs ne sont pas définis. Contactez l'administrateur !");

	}

	$pseudo = $_POST['pseudo'];
	$prenom = $_POST['prenom'];
	$nom = $_POST['nom'];
	$naissance=$_POST['datenaiss'];
	$sexe=$_POST['sexe'];
	$pays= $_POST['pays'];
	$email=$_POST['email'];
	$password=$_POST['password'];
	$password2=$_POST['password2'];
	$confidentialite = $_POST['confidentialite'];
	$titre_profil = $_POST['titre_profil'];
	if ($debug)
	{
		echo "<br>$pseudo";
		echo "<br>$prenom";
		echo "<br>$nom";
		echo "<br>$naissance";
		echo "<br>$sexe";
		echo "<br>$pays";
		echo "<br>$email";
		echo "<br>$password";
		echo "<br>$password2";
		echo "<br>$titre_profil";
		echo "<br>$confidentialite<br><br>";
	}

	// Vérification du remplissage de tous les champs
	if(empty($pseudo) or empty($prenom) or empty($nom) or empty($naissance) or empty($sexe) or empty($pays) or empty($email) or empty($password) or empty($password2) or empty($confidentialite)){
		exit("Veuillez remplir tous les champs <meta http-equiv=Refresh content='5; URL=index.php'>");
	}

	// Vérification de la validité des données
	if($password!=$password2){
		exit("Veuillez confirmer correctement votre MDP <meta http-equiv=Refresh content='5; URL=index.php'>");
	}
	if(!valid_date($naissance)){
		exit("Date non valide <meta http-equiv=Refresh content='3; URL=index.php'>");
	}

	// Connexion à la base de données



	$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=index.php'>");
	// Vérification de la disponibilité du pseudo
	$query = "SELECT * FROM Utilisateur WHERE pseudo = '".$pseudo."'";
	if ($debug) echo $query."<br>";
	$result = pg_query($connect,$query);
	$error = pg_last_error($connect);
  if ($error != "")
    die($error);
	$nombreDePseudo = pg_num_rows($result);
	if($nombreDePseudo !=0){
		exit("Ce pseudo est déjà pris <meta http-equiv=Refresh content='5; URL=index.php'>");
	}

	// Vérification de la disponibilité du titre du profil
	if (!empty($titre_profil))
	{
		$query = "SELECT * FROM Utilisateur WHERE titre_profil = '".$titre_profil."'";
		if ($debug) echo $query."<br>";
		$result = pg_query($connect,$query);
		$error = pg_last_error($connect);
	  if ($error != "")
	    die($error);
		$nombreDePseudo = pg_num_rows($result);
		if($nombreDePseudo !=0){
			exit("Ce profil est déjà pris <meta http-equiv=Refresh content='5; URL=index.php'>");
		}
	}


	// Requête d'insertion d'un nouvel utilisateur
	$query = "INSERT INTO Utilisateur(pseudo, prenom, nom, date_naissance, sexe, pays, email, password, titre_profil, prive) VALUES ('$pseudo', '$prenom', '$nom', '$naissance', '$sexe', '$pays', '$email', '$password', '$titre_profil', $confidentialite)";

	// On affiche en cas de débug
	if ($debug) echo $query."<br>";
	$result=pg_query($connect,$query);
	$error = pg_last_error($connect);
  if ($error != "")
    die($error);
	if (!$result)
		exit("Pas possible de rajouter un utilisateur ! <meta http-equiv=Refresh content='5; URL=index.php'>");
	echo "<h3>Vous allez être redirigé vers la page de connexion... </h3>";
	echo "<meta http-equiv=Refresh content='3; URL=connexion.php'>";

	pg_close($connect);

}
else{

?>

<FORM METHOD=POST ACTION="index.php">
pseudo: <INPUT TYPE=text NAME="pseudo" REQUIRED><br>
prenom: <INPUT TYPE=text NAME="prenom" REQUIRED><br>
nom: <INPUT TYPE=text NAME="nom" REQUIRED><br>
date de naissance: <INPUT TYPE=date NAME="datenaiss" REQUIRED><br>
Sexe: <INPUT TYPE=radio NAME="sexe" VALUE="homme" SELECTED> homme <br>
<INPUT TYPE=radio NAME="sexe" VALUE="femme" > femme <br>

<?php
$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=index.php'>");
//récupération des pays
$query = "SELECT * FROM Pays";
if ($debug) echo $query."<br>";
$result = pg_query($connect,$query);
$error = pg_last_error($connect);
if ($error != "")
	die($error);
echo 'Choix du pays:<SELECT name="pays">';
while($row=pg_fetch_array($result)) //choix du pays
{
  echo '<option value="'.$row[0].'">'.$row[0].'</option>';
}
echo "</SELECT><br>";

pg_close($connect);
?>



email:<INPUT TYPE=text NAME="email" REQUIRED><br>
mot de passe: <INPUT TYPE=PASSWORD NAME="password"><br>
confirmer le mot de passe: <INPUT TYPE=PASSWORD NAME="password2"><br>
titre du profil : <INPUT TYPE='text' NAME="titre_profil"><br>
confidentialité:<br>
    Privé <INPUT Type='radio' Name='confidentialite' VALUE=true /><br/>
		Public <INPUT Type='radio' Name='confidentialite' VALUE=false /><br/>
<INPUT TYPE=SUBMIT NAME="envoyer"><br>
</FORM>
<a href="connexion.php">Déjà inscrit ? Connectez-vous</a>

<?php
}?>
</BODY>


</HTML>
