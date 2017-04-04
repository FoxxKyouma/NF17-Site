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

if(isset($_POST['suiveur']))
{
    //ajout dans la table Ami de la relation suiveur-suivi
    $query= "INSERT INTO Ami VALUES ('".$_POST["suiveur"]."', '".$_SESSION["pseudo"]."')";
    //affichage de la requete
	if($debug){
	    echo $query."<br>";
	}
    pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);
	//suppression de la table Demande_Ami
    $query= "DELETE FROM Demande_Ami WHERE suiveur='".$_POST["suiveur"]."' AND suivie='".$_SESSION["pseudo"]."'";
    //affichage de la requete
	if($debug){
	    echo $query."<br>";
	}
    pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);
    //on peut lui proposer d'être son ami s'il ne l'est pas :
    $query= "SELECT * FROM Demande_Ami WHERE suiveur = '".$_SESSION["pseudo"]."' AND suivie='".$_POST["suiveur"]."'";
    //affichage de la requete
	if($debug){
	    echo $query."<br>";
	}

    $result=pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);
    $arr =pg_fetch_all($result);
    if($arr)
    {
        echo "<form action='ajouterAmi.php' method='POST'>";
        echo "<input type='hidden' name='suivie' value='".$_POST["suiveur"]."'>";
        echo "<input type='submit' name='demander' value='demander en ami'>";
        echo "</form>";
    }
}
else
{
//vérification si la personne n'a pas déjà été demandé en ami
$query= "SELECT * FROM Demande_Ami WHERE suivie = '".$_SESSION["pseudo"]."'";
//affichage de la requete
	if($debug){
	    echo $query."<br>";
	}


$result=pg_query($connect,$query);
$error = pg_last_error($connect);
if ($error != "")
  die($error);
$arr = pg_fetch_all($result);

// Si on n'est pas déjà ami
if($arr)
    {
        echo "<form action='consultDemandeAmi.php' method=post>";
        echo "<table>";
        echo "<tr> <th> Pseudo </th> <th> Nom </th> <th> Prénom </th> </tr>";
        while ($row = pg_fetch_array($result))
        {
            $query="SELECT nom,prenom FROM Utilisateur WHERE pseudo = '".$row[0]."'";
            if($debug)
            {
                echo $query."<br>";
            }
            $result2=pg_query($connect,$query);
            $error = pg_last_error($connect);
            if ($error != "")
              die($error);
            $row2=pg_fetch_array($result2);

            echo "<tr><td>".$row[0]."</td><td>".$row2[0]."</td><td>".$row2[1]."</td>";
            echo "<td> <input type='hidden' name='suiveur' value='$row[0]' </td> </tr>";
            echo "<td> <input type='submit' name='ajouter' value='ajouter' </td> </tr>";
        }
        echo "</table> </form>";

	}
else
    echo "Pas de demande d'ami";
}

pg_close($connect);

?>
</body>
</html>
