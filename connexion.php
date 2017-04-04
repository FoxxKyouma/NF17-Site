<HTML>
<HEAD>
<TITLE> Connexion à Instalol </TITLE>
<META charset=utf-8>
</HEAD>


<?php
include('config.php');
if(isset($_POST['envoyer'])){

	$pseudo=$_POST['pseudo'];
	$password=$_POST['password'];

	//on affiche les valeurs
	if($debug){
	    echo $pseudo."<br>";
	    echo $password."<br>";
	}
	//vérification que les champs sont bien définis
	if(!isset($pseudo) || !isset($password)){
	    die("Erreur : tous les champs ne sont pas définis. Contactez l'administrateur !");
	}

	//connexion à la BDD
	$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=connexion.php'>");


	$query = "SELECT pseudo, password FROM utilisateur WHERE pseudo ='".$pseudo."'";
	//affichage de la requete
	if($debug){
	    echo $query."<br>";
	}

	$result= pg_query($connect,$query);
	$error = pg_last_error($connect);
	if ($error != "")
		die($error);
	$test = pg_num_rows($result); //compte le nombre de ligne avec le pseudo

	$row= pg_fetch_array($result);
  echo "pseudo : ".$row["pseudo"]."<br>";
  echo "password : ".$row["password"]."<br>";
	//vérification que les champs sont non-vides
	if(empty($password) or empty($pseudo)){
		die("Veuillez remplir tous les champs <meta http-equiv=Refresh content='5; URL=connexion.php'>");
	}

	//vérification du pseudo(existence d'une ligne avec ce pseudo)
	if($test==0){
		die("Ce pseudo n'existe pas <meta http-equiv=Refresh content='5; URL=connexion.php'>");
		}

	//vérification du mot de passe
  echo "<br>Password : ".$row["password"]."<br>";
	if($password != $row["password"]){
		die("password incorrect <meta http-equiv=Refresh content='5; URL=connexion.php'>");
		}


	// début de session
	session_start();
	$_SESSION['pseudo']=$pseudo;

	echo"Vous allez être redirigé vers le site<meta http-equiv=Refresh content='2; URL=site.php'>";



	pg_close($connect);
}
else{ //affichage du formulaire
?>

<FORM method=POST action="connexion.php">
<label>pseudo:</label><INPUT TYPE=text NAME="pseudo" REQUIRED><br>
<label>mot de passe:</label><INPUT TYPE=PASSWORD NAME="password"><br>
<INPUT TYPE=SUBMIT NAME="envoyer"><br>
</FORM>
<?php
}
?>
