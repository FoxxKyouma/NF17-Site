<html>
<body>
<?php
include('config.php');
/*
Pour une recherche depuis une autre page:
Faire un formulaire avec 'pseudo', 'nom' et 'prenom' définis (même si ça doit être en hidden)
*/
session_start();
if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");
?>
<h1>Rechercher un profil</h1>

<form action="rechercherProfil.php" method="post">
  Pseudo:<input type="text" name="pseudo" <?php if(isset($_POST["pseudo"])) echo "Value='".$_POST["pseudo"]."'"; ?> ><br>
  Nom:<input type="text" name="nom" <?php if(isset($_POST["nom"])) echo "Value='".$_POST["nom"]."'"; ?> ><br>
  Prénom:<input type="text" name="prenom" <?php if(isset($_POST["prenom"])) echo "Value='".$_POST["prenom"]."'"; ?> ><br>
  <input type="submit" name="rechercher" value="Rechercher">
</form>

<?php

if (isset($_POST["pseudo"]) && isset($_POST["nom"]) && isset($_POST["prenom"]))
{
  if (!empty($_POST["pseudo"]) || !empty($_POST["nom"]) || !empty($_POST["prenom"]))
  {
  $connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=accueil.php'>");

  // Création de la requete
  $query = "SELECT pseudo, prenom, nom FROM Utilisateur WHERE ";

  if (!empty($_POST["pseudo"]))
  {
    // UPPER permet de ne pas se soucier de la casse
      $query .= "UPPER(pseudo) LIKE UPPER('".$_POST["pseudo"]."')";
      if (!empty($_POST["nom"]) || !empty($_POST["prenom"]))
      {
        $query .= " AND ";
      }
  }
  if (!empty($_POST["prenom"]))
  {
      $query .= "UPPER(prenom) = UPPER('".$_POST["prenom"]."')";
      if (!empty($_POST["nom"]))
      {
        $query .= " AND ";
      }
  }
  if (!empty($_POST["nom"]))
  {
      $query .= "UPPER(nom) = UPPER('".$_POST["nom"]."')";
  }

  if ($debug)
    echo "Query : ".$query."<br>";
  // Requete
  $result = pg_query($connect,$query);
  $error = pg_last_error($connect);
  if ($error != "")
    die($error);
  // Afficage sous forme de table
  echo "<table border=1>";
  echo "<tr><th>pseudo</th><th>Nom</th><th>Prénom</th></tr>";
  while ($row = pg_fetch_array($result))
  {
    echo "<tr><td>".$row["pseudo"]."</td><td>".$row["nom"]."</td><td>".$row["prenom"]."</td><td>";
    ?>
    <form action="consultProfil.php" method="post">
      <input type="hidden" name='pseudo' value='<?php echo $row['pseudo']; ?>'>
      <input type="submit" name='acceder' value='Voir Profil'>
    </form>
    <?php
    echo "</td></tr>";
  }
  echo "</table>";

	pg_close($connect);
}
}


?>


</body>
</html>
