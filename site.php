<HTML>
<HEAD>
<meta charset=utf-8>
<TITLE> Instalol</title>
</head>

<?php
session_start();
if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php'>");
?>
<FRAMESET cols="20%,60%,20%">
<FRAME SRC="menu.php"; NAME="menu">
<FRAME SRC="accueil.php"; NAME="contenu">
<FRAME SRC="consultDemandeAmi.php"; NAME="demande">
</FRAMESET>
</HTML>
