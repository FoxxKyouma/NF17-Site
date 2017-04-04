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
	if (!isset($_POST['commentaire']))
	{
	die("Erreur : commentaire vide");
	}

if (!isset($_POST["commentaire"]))
  die("<meta http-equiv=Refresh content='0; URL=commenter.php'>");
if (!isset($_POST["tag1"]))
  die("<meta http-equiv=Refresh content='0; URL=commenter.php'>");
if (!isset($_POST["tag2"]))
  die("<meta http-equiv=Refresh content='0; URL=commenter.php'>");
if (!isset($_POST["tag3"]))
  die("<meta http-equiv=Refresh content='0; URL=commenter.php'>");
if (!isset($_POST["tag4"]))
  die("<meta http-equiv=Refresh content='0; URL=commenter.php'>");
if (!isset($_POST["tag5"]))
  die("<meta http-equiv=Refresh content='0; URL=commenter.php'>");

 //valeurs a envoyer
 $com = $_POST["commentaire"];
 $tag1 = $_POST["tag1"];
 $tag2 = $_POST["tag2"];
 $tag3 = $_POST["tag3"];
 $tag4 = $_POST["tag4"];
 $tag5 = $_POST["tag5"];
 $tag = array($tag1,$tag2,$tag3,$tag4,$tag5);
 $id_photo = $_POST['id_photo'];


 if($debug)
 {
	echo "Commentaire : $com<br>";
	echo " tags : $tag1 $tag2 $tag3 $tag4 $tag5";
 }
 //incrementation de l'id 
 $idcom = maxTable("objet_aimable", "id_objet");
 $idcom +=1;
  if ($debug)
  {
    echo "Nouvel ID : $idcom <br>";
  }
  
$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=commenter.php'>");

 



  
// Ajout dans objet_aimable
$query = "INSERT INTO Objet_aimable (id_objet, proprietaire) VALUES ($idcom,'".$_SESSION["pseudo"]."')";  
pg_query($connect,$query);
//Ajout dans Commentaire
$query = "INSERT INTO Commentaire  (id_com, photo, texte) VALUES ($idcom, $id_photo, '$com')";
if ($debug) echo "Query = $query<br>";
pg_query($connect,$query);
if ($debug)
    echo "Query = $query<br>";

// Ajout dans Tags (en verifiant doublons) et Assoc_tagcommentaire
foreach ($tag as $value)
{	echo "Verification du tag";
	$query ="SELECT Tag.texte FROM Tag WHERE texte='$value'";
	$result = pg_query($connect,$query);
	if (pg_num_rows ($result) == 0) {
		echo "Tag valide <br>";
		$query = "INSERT INTO Tag (texte) VALUES ('$value')";
		pg_query($connect,$query);
	
	}
	
	else echo "Tag deja existant <br>";
		$query = "INSERT INTO Assoc_tagcommentaire (id_com,tag) VALUES ($idcom,'$value')";
		pg_query($connect,$query);
}
}

$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=commenter.php'>");
$query = "SELECT Photo.url, Photo.id_photo FROM Photo WHERE Photo.id_photo = '".$_POST['id_photo']."'";
$result = pg_query($connect,$query);
$row = pg_fetch_array($result);
echo "<figure> <img src='".$row["url"]."'width=50%><br><br>";
?>
					<form action="liker.php" method="post">
					<input type="hidden" name='id_objet' value = <?php echo $row["id_photo"]; ?> >
					<input type ="hidden" name ='id_photo' value = <?php echo $_POST['id_photo'];?> >
					<input type="hidden" name='nature' value = '1' >
					<input type="submit" name="liker" VALUE ="Like">
					</form>
					<form action="liker.php" method="post">
					<input type="hidden" name='id_objet' value = <?php echo $row["id_photo"]; ?> >
					<input type ="hidden" name ='id_photo' value = <?php echo $_POST['id_photo'];?> >
					<input type="hidden" name='nature' value = '-1' >
					<input type="submit" name="liker" VALUE ="Dislike">
					</form><br>
					<?php
$query = "SELECT Aimer.nature FROM Aimer WHERE Aimer.objet ='".$_POST['id_photo']."'";
$result=pg_query($connect,$query);
$score=0;
while ($row = pg_fetch_array($result))
	{
		$score+=$row[0];
	}
echo "Score de la photo : ".$score."<br>";
//Triple jointure pour recuperer les commentaires, leurs proprietaires, leurs likes et leurs tags
$query = "SELECT Commentaire.texte, Assoc_tagcommentaire.tag,  Objet_aimable.proprietaire, Aimer.nature, Commentaire.id_com FROM Commentaire LEFT JOIN Assoc_tagcommentaire ON  Assoc_tagcommentaire.id_com=Commentaire.id_com JOIN Objet_aimable ON Objet_aimable.id_objet=Commentaire.id_com LEFT JOIN Aimer ON Commentaire.id_com=Aimer.objet  WHERE Commentaire.photo = '".$_POST['id_photo']."'";
//$query = "SELECT texte FROM Commentaire WHERE photo = '".$_POST['id_photo']."'";
$result=pg_query($connect,$query);
    $error = pg_last_error($connect);
	if ($error != "")
        die($error);
$pre="";
$score = 0;
echo "Commentaires : <br>";
	while ($row = pg_fetch_array($result))
		 {
			 //verification que les tags n'appartiennent pas au com precedent
			 if ($row[0]==$pre)
			 {  $score+=$row[3];
				echo $row[1]." "; 
			 }	
			 else 
			 {
				if ($pre<>"")
				{
					echo "<br>score de ce commentaire : ".$score;
					?>
					<form action="liker.php" method="post">
					<input type="hidden" name='id_objet' value = <?php echo $id; ?> >
					<input type ="hidden" name ='id_photo' value = <?php echo $_POST['id_photo']; ?> >
					<input type="hidden" name='nature' value = '1' >
					<input type="submit" name="liker" VALUE ="Like">
					</form>
					<form action="liker.php" method="post">
					<input type="hidden" name='id_objet' value = <?php echo $id; ?> > 
					<input type ="hidden" name ='id_photo' value = <?php echo $_POST['id_photo']; ?> >
					<input type="hidden" name='nature' value = '-1' >
					<input type="submit" name="liker" VALUE ="Dislike">
					</form><br>
					<?php
					$score=0+$row[3];
					$id = $row[4];
					echo "<br><br>".$row[2]." : ".$row[0]."<br>";
					echo "tags : ".$row[1];
				}
				else
				{	$id = $row[4];
					$score=$row[3];
					echo "<br><br>".$row[2]." : ".$row[0]."<br>";
					echo "tags : ".$row[1];
				}
			 
			 }
			 
			 $pre=$row[0];
			 
		 }
	echo "<br>score de ce commentaire : ".$score."<br><br>";
	?>
					<form action="liker.php" method="post">
					<input type="hidden" name='id_objet' value = <?php echo $id; ?> >
					<input type ="hidden" name ='id_photo' value = <?php echo $_POST['id_photo']; ?> >
					<input type="hidden" name='nature' value = '1' >
					<input type="submit" name="liker" VALUE ="Like">
					</form>
					<form action="liker.php" method="post">
					<input type="hidden" name='id_objet' value = <?php echo $id; ?> > 
					<input type ="hidden" name ='id_photo' value = <?php echo $_POST['id_photo']; ?> >
					<input type="hidden" name='nature' value = '-1' >
					<input type="submit" name="liker" VALUE ="Dislike">
					</form><br>
					<?php
 $id_photo = $_POST['id_photo'];
If ($debug) echo "Id_photo : ".$id_photo ;
?>

<h1>Ajouter un commentaire</h1>
  <form action="commenter.php" method="post">
    <label>Commentaire</label><input type="text" name="commentaire"><br>
    <label>Tags</label> <input type="text" name="tag1">
						<input type="text" name="tag2">
						<input type="text" name="tag3">
						<input type="text" name="tag4">
						<input type="text" name="tag5">
	<input type="hidden" name='id_photo' value = <?php echo $id_photo; ?> >
	<input type="submit" name="envoyer" VALUE ="Envoyer"><br>
  </form>
</BODY>
</HTML>