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

if(!isset($_POST["pseudo"]))
{
    $_POST["pseudo"]=$_SESSION["pseudo"];
    // Si on a modifié le compte
    if (isset($_POST["modifier"]))
    {
      $query = "";
      if (isset($_POST["nom"]))
      {
        $query = "UPDATE Utilisateur SET nom = '".$_POST["nom"]."' WHERE pseudo = '".$_SESSION['pseudo']."';";
      }
      if (isset($_POST["prenom"]))
      {
        $query = "UPDATE Utilisateur SET prenom = '".$_POST["prenom"]."' WHERE pseudo = '".$_SESSION['pseudo']."';";
      }
      if (isset($_POST["email"]))
      {
        $query = "UPDATE Utilisateur SET email = '".$_POST["email"]."' WHERE pseudo = '".$_SESSION['pseudo']."';";
      }
      if (isset($_POST["date_naissance"]))
      {
        $query = "UPDATE Utilisateur SET date_naissance = '".$_POST["date_naissance"]."' WHERE pseudo = '".$_SESSION['pseudo']."';";
      }
      if (isset($_POST["sexe"]))
      {
        $query = "UPDATE Utilisateur SET sexe = '".$_POST["sexe"]."' WHERE pseudo = '".$_SESSION['pseudo']."';";
      }
      if (isset($_POST["pays"]))
      {
        $query = "UPDATE Utilisateur SET pays = '".$_POST["pays"]."' WHERE pseudo = '".$_SESSION['pseudo']."';";
      }
      if (isset($_POST["profil"]))
      {
        $query = "UPDATE Utilisateur SET prive = '".$_POST["profil"]."' WHERE pseudo = '".$_SESSION['pseudo']."';";
      }
      if($debug){
    	    echo $query."<br>";
    	}

    $result=pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);

    }




}







// Affichage des informations du profil
$query="SELECT titre_profil,prive FROM Utilisateur WHERE pseudo='".$_POST['pseudo']."'";

//affichage de la requete
	if($debug){
	    echo $query."<br>";
	}

$result=pg_query($connect,$query);
$error = pg_last_error($connect);
if ($error != "")
  die($error);
$row= pg_fetch_array($result);
if(!empty($row["titre_profil"])){echo "<h1 align=center >".$row["titre_profil"]."</h1>";}
echo "<h2 align=center> Profil de ".$_POST["pseudo"]."</h2> <br>";

// PARTIE PUBLIQUE / PRIVEE : à vérifier
$autorise = false;

// Si c'est notre profil, on peut le voir quand même
if ($_SESSION["pseudo"] == $_POST["pseudo"])
{
  $autorise = true;
}

// Si le profil est public, on peut y accéder
$prive = $row["prive"];

if (!$prive == 't')
    $autorise = true;



// Vérification du lien d'amitié avec l'utilisateur connecté
  $query = "SELECT COUNT(*) FROM Ami WHERE suiveur = '".$_SESSION["pseudo"]."' AND suivie = '".$_POST["pseudo"]."'";
  if ($debug)
    echo "Query = $query<br>";
  $result=pg_query($connect,$query);
  $error = pg_last_error($connect);
  if ($error != "")
    die($error);
  $row = pg_fetch_array($result);
  $ami = $row[0];//si le profil est public mais qu'il n'existe aucun lien d'amitié il faudra lui proposer par la suite
  if(!$autorise) $autorise= $ami;
  $suivi=$_POST["pseudo"];

if ((!$ami || !$autorise) & $_SESSION["pseudo"] != $_POST["pseudo"])
{
    //vérification si la personne n'a pas déjà été demandé en ami
    $query= "SELECT * FROM Demande_Ami WHERE suiveur = '".$_SESSION["pseudo"]."' AND suivie = '".$suivi."'";
    //affichage de la requete
	if($debug){
	    echo $query."<br>";
	}

    $result=pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
      die($error);
    $row=pg_fetch_array($result);

    if(empty($row))
    {
	//faire une demande en amitié
	echo "<form action='ajouterAmi.php' method='post'>";
	echo "<input type='hidden' name='suivie' value='".$suivi."'>";
    echo "<input type='submit' name='acceder' value='Demander en ami'>";
	echo "</form>";
	}
	else
        echo "Vous l'avez déjà demandé en ami.";
}

// Si on a une relation d'amitié avec le profil ou si c'est notre profil ou profil public
if ($autorise)
{
//affichage des informations sur le profil
    $query="SELECT nom,prenom,date_naissance,sexe,pays,email FROM Utilisateur WHERE pseudo='".$_POST['pseudo']."'";

    //affichage de la requete
    if($debug)
    {
        echo $query."<br>";
    }
    $result=pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
    die($error);
    $row= pg_fetch_array($result);

    // On offre la possibilité de modifier son profil
    if ($_POST["pseudo"] == $_SESSION["pseudo"])
    {
      echo "<h3>Informations sur le profil : </h3>";
      ?>
      <form action="consultProfil.php" method="post">
        <label>Nom : </label><input type="text" name="nom" value="<?php echo $row["nom"]; ?>"><input type="submit" name="modifier" value="Modifier">
      </form>
      <form action="consultProfil.php" method="post">
        <label>Prénom : </label><input type="text" name="prenom" value="<?php echo $row["prenom"]; ?>"><input type="submit" name="modifier" value="Modifier">
      </form>
      <form action="consultProfil.php" method="post">
        <label>Email : </label><input type="text" name="email" value="<?php echo $row["email"]; ?>"><input type="submit" name="modifier" value="Modifier">
      </form>
      <form action="consultProfil.php" method="post">
        <label>Sexe : </label><br>
        <label>Homme</label><input type="radio" name="sexe" value="homme" <?php if ($row['sexe'] == "homme") echo "CHECKED"; ?> ><br>
        <label>Femme</label><input type="radio" name="sexe" value="femme" <?php if ($row['sexe'] == "femme") echo "CHECKED"; ?> ><br><input type="submit" name="modifier" value="Modifier">
      </form>
      <form action="consultProfil.php" method="post">
        <label>Date de naissance : </label><input type="text" name="date_naissance" value="<?php echo $row["date_naissance"]; ?>"><input type="submit" name="modifier" value="Modifier">
      </form>
      <form action="consultProfil.php" method="post">
        <label>Pays: </label>
      <?php
      $query = "SELECT * FROM Pays";
      //if ($debug) echo $query."<br>";
      $result = pg_query($connect,$query);
      $error = pg_last_error($connect);
      if ($error != "")
      	die($error);
      echo '<SELECT name="pays">';
      while($row2=pg_fetch_array($result)) //choix du pays
      {
        echo '<option value="'.$row2[0].'"';
        if ($row['pays'] == $row2[0])
          echo " SELECTED ";
        echo '>'.$row2[0].'</option>';
      }
      echo "</SELECT>";
      ?>

      <input type="submit" name="modifier" value="Modifier">
    </form>
      <form action="consultProfil.php" method="post">
        <label>Profil : </label><br>
        <label>Privé</label><input type="radio" name="profil" value="t" <?php if ($prive == "t") echo "CHECKED"; ?> ><br>
        <label>Public</label><input type="radio" name="profil" value="f" <?php if ($prive == "f") echo "CHECKED"; ?> ><br><input type="submit" name="modifier" value="Modifier">
      </form>
      <?php
    }
    else {
      echo "<h3>Informations sur le profil : </h3>";
      echo "Nom : ".$row['nom']."<br>";
      echo "Prénom : ".$row['prenom']."<br>";
      echo "Email : ".$row['email']."<br>";
      echo "Sexe: ".$row['sexe']."<br>";
      echo "Date de naissance : ".$row['date_naissance']."<br>";
      echo "Pays : ".$row['pays']."<br>";

    if ($prive=='t')
        echo "Type de profil : privé <br>";
    else
        echo "Type de profil : public <br>";
}
    // Affichage des liens vers les albums non vides
    echo "<div id=Album>";
    echo "<h3>Albums</h3>";
    // Affichage sous forme de table
    $query="SELECT Album.id_album,Album.titre,Album.legende,Photo.url FROM Album INNER JOIN Photo ON Album.vignette=Photo.id_photo WHERE Album.proprietaire='".$_POST['pseudo']."'";

    //affichage de la requete
    if($debug){
            echo $query."<br>";
        }
    $result=pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
        die($error);
    $arr = pg_fetch_all($result);
    if($arr)
    {
        echo "Cliquez sur l'album : ";
        echo "<table border=1>";
        $row=pg_fetch_array($result);
        while($row)
        {
            echo "<tr>";
            $i=1;
            while($row & $i<=3)//affichage trois albums par ligne                   
            {
                    echo "<td> <figure> <img src='".$row["url"]."' alt='".$row["titre"]."' title='".$row["titre"]."' width=15%>";
                    echo "<figcaption>".$row["legende"];
                    ?>
                        <form action="consultAlbum.php" method="post">
                        <input type="hidden" name='id_album' value='<?php echo $row["id_album"]; ?>'>
                        <input type="submit" name='acceder' value='Voir Album'>
                        </form>
                        </figcaption> </figure>
                    <?php
                    echo "</td>";
                    $i++;
                    $row=pg_fetch_array($result);
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    else
        echo "Il existe aucun album à ce jour.";
        
    //Affichage des photos classées et non classées les plus récentes
    echo "</div>";
    echo "<h3>Photos :</h3>";
    $query="SELECT Photo.id_photo,Photo.titre,Photo.legende,url FROM Photo,Objet_Aimable WHERE Photo.id_photo=Objet_Aimable.id_objet AND proprietaire='".$_POST['pseudo']."' ORDER BY Photo.id_photo DESC";

    //affichage de la requete
    if($debug){
            echo $query."<br>";
        }
    $result=pg_query($connect,$query);
    $error = pg_last_error($connect);
    if ($error != "")
        die($error);
    $arr = pg_fetch_all($result);//lit toutes les lignes d'un résultat renvoie NULL si pas de tuples
    if($arr)
    {
        $row = pg_fetch_array($result);
        echo "<figure> <img src='".$row["url"]."' alt='".$row["titre"]."' title='".$row["titre"]."' width=20%>";
        ?>
            <figcaption> <?php echo $row["legende"]; ?> <br> 
            <form action='commenter.php' method="post">
            <input type="hidden" name='id_photo' value=<?php echo $row["id_photo"]; ?> >
            <input type="submit" name='commenter' value='Commenter'>
            </form>
            </figcaption> 
            </figure>
        <?php        
        if(pg_num_rows($result)>1)
        {
            echo "Autres photos disponibles : <br>";
            echo "<table border=1> <tr> <th> Titre </th> <th> Légende </th> <th> Lien </th> </tr>";
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
                <?php
            }
            echo "</table>";
        }
        
    }



}

	pg_close($connect);
?>
</body>
</html>
