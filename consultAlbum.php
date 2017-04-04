<html>
<body>
<?php

session_start();

if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");

include('config.php');

$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=accueil.php'>");

$query = "SELECT Photo.id_photo, Photo.titre, Photo.url, Photo.legende FROM Photo,Album WHERE Album.id_album=Photo.album AND Album.id_album=".$_POST['id_album'].""; //requête pour récupérer les photos contenus dans l'album

if($debug)
{
    echo $query."<br>";
}

$result=pg_query($connect,$query);
$error = pg_last_error($connect);

if ($error != "")
        die($error);
        
$arr = pg_fetch_all($result);//lit toutes les lignes d'un résultat renvoie NULL si pas de tuples

//s'il existe des photos, on les affiche en lui proposant de les afficher
if($arr)
{      
            echo "<table border=0> <tr> <th> Titre </th> <th> Légende </th> </tr>";
            while ($row = pg_fetch_array($result))
            {
                echo "<tr> <td>".$row["titre"]."</td> <td>".$row["legende"]."</td> <td>";
                ?> 
                <td>
                <form action='commenter.php' method="post">
                <input type="hidden" name='id_photo' value=<?php echo $row["id_photo"]; ?> >
                <input type="submit" name='commenter' value='Commenter'>
                </form>
                </td>
                </tr>
                
                <?php
            }
            echo "</table>";
        
}
else
    echo "Il n'existe aucune photo dans cet album.";

pg_close($connect);

?>
</body>
</html>
