<html>
<body>
<?php
// Si on n'a pas demandé de profil d'un certain pseudo, on rejette
session_start();
if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");



include('config.php');

$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=accueil.php'>");

if (!isset($_POST["suivie"]))
  die("Tous les champs ne sont pas définis !");

// On vérifie qu'on n'est pas déjà ami
$query="SELECT COUNT(*) FROM Ami WHERE suiveur = '".$_SESSION['pseudo']."' AND suivie = '".$_POST['suivie']."'";
  if($debug){
    echo $query."<br>";
}
$result = pg_query($connect,$query);
$error = pg_last_error($connect);
if ($error != "")
  die($error);

$nombreAmitiesExistantes = pg_fetch_array($result);
$nombreAmitiesExistantes = $nombreAmitiesExistantes[0];
if ($nombreAmitiesExistantes != 0)
{
  die ("Erreur : vous avez déjà cette personne en ami ! <meta http-equiv=Refresh content='5; URL=consultDemandeAmi.php'>");
}

// Ajout de la demande d'ami
	$query="INSERT INTO Demande_Ami (suiveur, suivie) VALUES( '".$_SESSION['pseudo']."', '".$_POST['suivie']."')";
		if($debug){
	    echo $query."<br>";
	}
	pg_query($connect,$query);
  $error = pg_last_error($connect);
  if ($error != "")
    die($error);
  //echo "<meta http-equiv=Refresh content='0; URL=consultDemandeAmi.php'>";

pg_close($connect);
?>
</body>
</html>
