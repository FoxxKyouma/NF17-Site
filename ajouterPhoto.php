<HTML>
<BODY>


<?php
include('config.php');

session_start();

if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");
if (isset($_POST["envoyer"]))
{
  // Vérifications habituelles
  if (!isset($_POST['titre']) || !isset($_POST['legende']) || !isset($_POST['url']) || !isset($_POST['album']))
  {
    die("Erreur : tous les champs ne sont pas définis. Contactez l'administrateur !");
  }
  if (empty($_POST['titre']) || empty($_POST['legende']) || empty($_POST['url']))
    die("Remplissez tous les champs ! <meta http-equiv=Refresh content='5; URL=ajouterPhoto.php'>");
    
$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=ajouterPhoto.php'>");
    
    // l'album n'est pas obligatoire, donc si rien n'est rentré, on met "NULL"
  if (empty($_POST["album"]))
    $album = 'NULL';
  else
    $album = $_POST['album'];

    // Affectation des valeurs
  $titre = $_POST["titre"];
  $legende = $_POST["legende"];
  $url = $_POST["url"];

  if ($debug)
  {
    echo "Titre : $titre<br>";
    echo "Légende : $legende<br>";
    echo "url : $url<br>";
    echo "Num album : $album<br>";
  }
  // Auto-incrément
  $nouvelID = maxTable("Objet_aimable", "id_objet", $connect);
  $error = pg_last_error($connect);
  if ($error != "")
    die($error);
  $nouvelID += 1;
  if ($debug)
    echo "Nouvel ID : $nouvelID <br>";

  

  // Ajout de l'objet dans Aimable
  $query = "INSERT INTO Objet_aimable (id_objet, proprietaire) VALUES ($nouvelID, '".$_SESSION["pseudo"]."')";
  if ($debug)
    echo "Query = $query<br>";
  $result = pg_query($connect,$query);
  $donnee = pg_fetch_array($result, 0, PGSQL_NUM);
  echo "$donnee<br>";
  $error = pg_last_error($connect);
  if ($error != "")
    die($error);

  // Ajout de la photo
  $query = "INSERT INTO Photo (id_photo, titre, legende, url, album) VALUES ($nouvelID, '$titre', '$legende', '$url', $album)";
  if ($debug)
    echo "Query = $query<br>";
  pg_query($connect,$query);
  $error = pg_last_error($connect);
  if ($error != "")
    die($error);
  echo "<meta http-equiv=Refresh content='0; URL=ajouterPhoto.php'>";
  pg_close($connect);
}
else
{

 ?>
  <h1>Ajout d'une photo à un profil</h1>
  <form action="ajouterPhoto.php" method="post">
    <label>Titre</label><input type="text" name="titre"><br>
    <label>Légende</label><input type="text" name="legende"><br>
    <label>URL</label><input type="text" name="url"><br>
    <label>Album</label><SELECT name="album" size="1">
  <?php

    // Liste déroulante des albums de la personne, pour ajouter la photo directement à un album
    $connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=ajouterPhoto.php'>");

    // Récupération de la liste des albums
    $user = $_SESSION["pseudo"];
    $query = "SELECT id_album, titre FROM Album WHERE proprietaire = '$user'";
    if ($debug)
      echo "<br>$query<br>";
    $result= pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);
    while ($row = pg_fetch_array($result))
    {
      echo "<OPTION Value=".$row["id_album"].">".$row["titre"]."</OPTION>";
    }
    echo "<OPTION Value='NULL'>Pas d'album</OPTION>";
    pg_close($connect);

       ?><br>
  <input type="submit" name="envoyer" VALUE ="Ajouter!"><br>
  </form>

<?php } ?>
</body>
</html>
