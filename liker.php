<HTML>
<BODY>


<?php
include('config.php');

session_start();

if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");
$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=liker.php'>");
$id = $_POST["id_objet"];
$nature = $_POST["nature"];
$pseudo = $_SESSION["pseudo"];
$id_photo = $_POST['id_photo'];
$query = "SELECT * FROM Aimer WHERE Aimer.utilisateur = '$pseudo' AND Aimer.objet = '$id'" ;
$result = pg_query($connect,$query);
if (pg_num_rows ($result) == 0) {
		$query = "INSERT INTO Aimer (objet, utilisateur, nature) VALUES ($id, '$pseudo', $nature)";
		pg_query ($connect,$query);
		echo "like ajoute";
}
else echo "Vous avez deja like ou dislike cet objet";
?> 
                <td>
                <form action='commenter.php' method="post">
                <input type="hidden" name='id_photo' value=<?php echo $id_photo; ?> >
                <input type="submit" name='commenter' value='Retour aux commentaires'>
                </form>
                </td>
                <?php	
?>
</BODY>
</HTML>