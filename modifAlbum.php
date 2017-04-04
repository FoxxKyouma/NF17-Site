<HTML>
<HEAD>
<meta charset= utf-8>
<TITLE> Modification d'album </TITLE>
</HEAD>

<BODY>
<FORM method=POST action="modifAlbum.php">
<?php
include('config.php');
session_start();

//vérification session
if (!isset($_SESSION["pseudo"]))   die("<meta http-equiv=Refresh content='0; URL=index.php'>");
if ($_SESSION["pseudo"] == "")   die("<meta http-equiv=Refresh content='0; URL=index.php'>");

//connexion
$connect = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass") or die("Impossible de se connecter à la base de données <meta http-equiv=Refresh content='5; URL=modifAlbum.php'>");


// 1ere arrivée sur la page
if(!isset($_POST['envoyer']) && !isset($_POST['modif']))
{
//récupération des albums
$query= "SELECT id_album,titre FROM Album where proprietaire ='".$_SESSION["pseudo"]."'";
	if($debug)
		echo $query."<br>";
	$result= pg_query($connect,$query);
	$error= pg_last_error($connect);
	if ($error != "")
		die($error);

	//nombre d'album	
	$num = pg_num_rows($result);

	//test existence d'album
	if($num==0)
	{
		echo "Vous n'avez pas d'album<br>";
		echo "<a href='accueil.php' target='contenu'> Retour à la page d'accueil </a>";
		echo "<a href='ajouterAlbum.php' target='contenu'> Ajouter un nouvel album </a>";
    }
	else
		{//Formulaire
		echo '<FORM method=POST action="modifAlbum.php">';
		echo 'Choix de l\'album: <SELECT name="album">';
		while($row = pg_fetch_array($result)) //récupération des albums
			{
			echo '<option value="'.$row[0].'">'.$row[1].'</option>';
			}
		echo '</SELECT><br>';
		echo '<SELECT name="modification">';
		echo '<OPTION value="ajout"> ajout de photo </OPTION>';
		echo '<OPTION value="vignette"> ajout d\'une vignette </OPTION>';
		echo '</SELECT>';

	
		echo "<INPUT TYPE=SUBMIT NAME='envoyer' VALUE='envoyer'>";
		echo ' </FORM>';
		}

}

if(isset($_POST['envoyer']))
{
	$album = $_POST['album'];
	$modif = $_POST['modification'];
	if($debug)
		echo $album."<br>".$modif."<br>";

	//verification variable définies 1er formulaire

	if((!isset($album) || !isset($modif)) && isset($_POST['envoyer']))
		die("Erreur : tous les champs ne sont pas définis. Contactez l'administrateur !");


	//verification non vide 1er formulaire

	if((empty($album) || empty($modif)) && isset($_POST['envoyer']) )
		die("Remplissez tous les champs ! <meta http-equiv=Refresh content='5; URL=modifAlbum.php'>");

	// Si ajout photo !

	if($modif == 'ajout' || isset($_POST['modif'])) //vérification choix = ajout ou que formulaire de modification (ajout ou vignette rempli
	{
		if(!isset($_POST['modif']))
		{
			//récupération des photos de l'user qui n'ont pas d'album
			$query = "SELECT Photo.id_photo, titre,legende FROM Photo INNER JOIN Objet_Aimable ON Objet_Aimable.id_objet = Photo.id_photo where album IS NULL and proprietaire ='".$_SESSION['pseudo']."'";
			if($debug)
				echo $query."<br>";
			$result= pg_query($connect,$query);
			$error= pg_last_error($connect);
			if ($error != "")
				die($error);
		
			//nombre de photos récupérées
			$num = pg_num_rows($result);

			//test existence de photos n'appartenant à aucun album
			if($num==0)
				die("Erreur: Vous n'avez aucune photo qui n'appartient à aucun album<br>");
	

		//Formulaire choix photos à ajouter
		echo '<FORM method=POST action="modifAlbum.php">';
		echo "cochez les photos à ajouter:<br>";
		while($row = pg_fetch_array($result)) //récupération des photos

			//Formulaire choix photos à ajouter
			echo '<FORM method=POST action="modifAlbum.php">';
			echo "cochez les photos à ajouter:<br>";
			
			while($row = pg_fetch_array($result)) //récupération des albums

			{
				echo '<input type="checkbox" name="photo[]" value="'.$row[0].'">titre:'.$row[1].' legende:'.$row[2].'<br>';
			}

		echo '<input type="hidden" name ="album" value="'.$album.'">'; //récupération de l'id de l'album pour UPDATE
		echo "<INPUT TYPE=SUBMIT NAME='ajout'>";
		echo  '</FORM>';

			
			echo '<input type="hidden" name ="idalbum" value="'.$album.'">'; //récupération de l'id de l'album pour UPDATE
			echo "<INPUT TYPE=SUBMIT NAME='modif'>";
			echo  '</FORM>';
		}
		else
		{
			//récupération id album
			$idalbum = $_POST['idalbum'];
			$photo = $_POST['photo'];
			
			if(!isset($idalbum) || !isset($photo))
				die("tous les champs ne sont pas remplis");
			
			if($debug)
				echo $idalbum."<br>";
			
			//récupération toutes les photos cochées et ajout à l'album
			foreach($photo as $value)
			{
				if($debug)
				echo $value."<br>";
			
				//requête d'update
				$query=" UPDATE Photo SET album='".$idalbum."' WHERE id_photo='".$value."'";
				if($debug)
				echo $query."<br>";
				$result= pg_query($connect,$query);
				$error= pg_last_error($connect);
				if ($error != "")
				die($error);
				
			}
			die("Opération terminée vous allez être redirigé vers l'accueil <meta http-equiv=Refresh content='5; URL=accueil.php'>");
			
		}
	
	}
	if($modif == 'vignette')
	{
		//Ce script a pour but d'ajouter ou modifier la vignette d'un album
		//on vérifie d'abord si l'album est vide
		//récupération des photos appartenant
		$query = "SELECT Photo.id_photo, titre,legende FROM Photo where Photo.album='$album'";
		if($debug)
			echo $query."<br>";
		$result= pg_query($connect,$query);
		$error= pg_last_error($connect);
		if ($error != "")
			die($error);
		
		//nombre de photos récupérées
		$num = pg_num_rows($result);

		//test existence de photos
		if($num==0)
        {
            //l'utilisateur va pouvoir ajouter une vignette à partir de photos non issus de l'album.
            $album_vide=TRUE;
            $query = "SELECT Photo.id_photo, titre,legende FROM Photo, Objet_Aimable where Objet_Aimable.id_objet=Photo.id_photo and Photo.album IS NULL and Objet_Aimable.proprietaire='".$_SESSION['pseudo']."'"; 
            if($debug)
                echo $query."<br>";
            $result= pg_query($connect,$query);
            $error= pg_last_error($connect);
            if ($error != "")
                die($error);
            $arr=pg_fetch_all($result);
            if($arr==NULL)
                die("Erreur: Il n'existe actuellement aucune photo pour les mettre en vignette de l'album<br>");
            $vignette_actuelle=FALSE;
        }
        else //il existe des photos appartenant à l'album, on cherche quel est l'actuelle vignette
        {
            $album_vide=FALSE;
            $query="SELECT Photo.id_photo,Photo.titre,Photo.legende FROM Photo,Objet_Aimable,Album WHERE Album.id_album='".$_POST["album"]."' AND Album.vignette=Photo.id_photo";
            if($debug)
                echo $query."<br>";
            $result2= pg_query($connect,$query);
            $error= pg_last_error($connect);
            if ($error != "")
                die($error);
            $row2=pg_fetch_array($result2);
            $vignette_actuelle=$row2["id_photo"];
            echo "Vous vous apprêtez à modifier la vignette actuelle.<br>";
        }
        echo '<FORM method=POST action="ajouterPhoto.php">';
        echo "Choisissez la photo que vous voulez mettre en vignette de cet album :<br>";
        echo "<SELECT name='vignette_choisie'>";
        while($row = pg_fetch_array($result)) //récupération des photos
        {
            if(!$album_vide & $row["id_photo"]==$vignette_actuelle) //on met en évidence la photo qui est déjà la vignette
                echo '<option value="'.$row["id_photo"].'" SELECTED> titre:'.$row["titre"].' legende:'.$row["legende"].' (actuelle vignette) </option>';
            else
                echo '<option value="'.$row["id_photo"].'">titre:'.$row["titre"].' legende:'.$row["legende"].'</option>';
        }
        echo "</SELECT>";
        
        echo '<input type="hidden" name ="album" value="'.$album.'">'; //récupération de l'id de l'album pour UPDATE
        echo '<input type="hidden" name ="album_vide" value="'.$album_vide.'">'; 
        echo '<input type="hidden" name ="vignette_actuelle" value="'.$vignette_actuelle.'">';
        echo "<INPUT TYPE=SUBMIT NAME='ajout3' VALUE='ajouter'>";
        echo  '</FORM>';
		
    }
}
if(isset($_POST['ajout3']))
{
    if(!isset($_POST['vignette_choisie']))
        die("Vous avez oublié de choisir une vignette. <meta http-equiv=Refresh content='0; URL=modifAlbum.php'>");
    if ($_POST['vignette_choisie'] == "")   
        die("<meta http-equiv=Refresh content='0; URL=index.php'>");
    echo $_POST["vignette_actuelle"];
    echo "<br>".$_POST['vignette_choisie'];
    if($_POST["vignette_actuelle"]!=$_POST['vignette_choisie'])    
    {
        //mise à jour du champ vignette de l'album sélectionné en fonction de la photo précédemment choisie
        $query= "UPDATE Album  SET vignette=".$_POST['vignette_choisie']." WHERE id_album=".$_POST['album'];
        if($debug)
            echo $query."<br>";
        $result= pg_query($connect,$query);
        $error= pg_last_error($connect);
        if ($error != "")
            die($error);
            
        //si la photo qui a été choisie n'appartenait pas à l'album alors on met à jour le champ album de la photo en question
        if($_POST['album_vide'])
        {
            $query= "UPDATE Photo SET album=".$_POST['album']." WHERE id_photo=".$_POST['vignette_choisie'];
            if($debug)
                echo $query."<br>";
            $result= pg_query($connect,$query);
            $error= pg_last_error($connect);
            if ($error != "")
                die($error);
        }
        echo "<meta http-equiv=Refresh content='5; URL=accueil.php'>";
    }
    else
    {
        echo "Vous avez choisi la même vignette.";
        echo "<meta http-equiv=Refresh content='5; URL=accueil.php'>";
    }
}


pg_close($connect);

?>
</FORM>


</BODY>
</HTML>
