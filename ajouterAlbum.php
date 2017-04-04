<HTML>
<BODY>


<?php
include('config.php');
//connexion à la BDD

session_start();

if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");

$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=ajouterAlbum.php'>");

if (isset($_POST["envoyer"]))
{
  // Vérifications habituelles

  if (!isset($_POST['titre']) || !isset($_POST['legende']) || !isset($_POST['vignette']))
  {
    die("Erreur : tous les champs ne sont pas définis. Contactez l'administrateur !");
  }
  if (empty($_POST['titre']) || empty($_POST['legende']) || empty($_POST['vignette']))
    die("Remplissez tous les champs ! <meta http-equiv=Refresh content='5; URL=ajouterAlbum.php'>");

    // l'album n'est pas obligatoire, donc si rien n'est rentré, on met "NULL"
  if (empty($_POST["album"]))
    $album = 'NULL';
  else
    $album = $_POST['album'];

    // Affectation des valeurs
  $titre = $_POST["titre"];
  $legende = $_POST["legende"];
  $vignette = $_POST["vignette"];

  if ($debug)
  {
    echo "Titre : $titre<br>";
    echo "Légende : $legende<br>";
    echo "Num vignette : $vignette<br>";
  }
  // Auto-incrément
  $nouvelID = maxTable("Album", "id_album", $connect);
  $nouvelID += 1;
  if ($debug)
    echo "Nouvel ID : $nouvelID <br>";



  // Ajout de la photo
  $query = "INSERT INTO Album (id_album, titre, legende, proprietaire, vignette) VALUES ($nouvelID, '$titre', '$legende', '".$_SESSION["pseudo"]."', $vignette)";
  if ($debug)
    echo "Query = $query<br>";
  pg_query($connect,$query);
  $error = pg_last_error($connect);
  if ($error != "")
    die($error);

  if (!empty($vignette))
  {
    $query = "UPDATE Photo SET album = $nouvelID WHERE Photo.id_photo = $vignette";
    if ($debug)
      echo "Query = $query<br>";
    $result = pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);
  }
  //echo "<meta http-equiv=Refresh content='0; URL=ajouterAlbum.php'>";

}
else
{
 ?>
  <h1>Ajout d'un album à un profil</h1>
  <form action="ajouterAlbum.php" method="post">
    <label>Titre</label><input type="text" name="titre"><br>
    <label>Légende</label><input type="text" name="legende"><br>
    <label>Vignette</label><SELECT name="vignette" size="1">
  <?php

    // Liste déroulante des photos de la personne, pour la mettre en vignette

    // Récupération de la liste des photos
    $user = $_SESSION["pseudo"];
    $query = "SELECT id_photo, titre FROM Photo INNER JOIN Objet_Aimable ON id_photo = id_objet WHERE proprietaire = '$user' AND Photo.album IS NULL";
    if ($debug)
      echo "<br>$query<br>";
    $result= pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);
    while ($row = pg_fetch_array($result))
    {
      echo "<OPTION Value=".$row["id_photo"].">".$row["titre"]."</OPTION>";
    }//*/
    echo "<OPTION Value='NULL'>Pas de vignette pour le moment</OPTION>"

       ?><br>
  <input type="submit" name="envoyer" VALUE ="Ajouter!"><br>
  </form>

<?php }

pg_close($connect); ?>
</body>
</html>
