<HTML>
<HEAD>

<meta charset=utf-8>
<TITLE>
Bienvenue sur Instalol
</TITLE>
</HEAD>
<BODY>
<?php
include('config.php');
session_start();

if (!isset($_SESSION["pseudo"]))
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");
if ($_SESSION["pseudo"] == "")
  die("<meta http-equiv=Refresh content='0; URL=index.php' target='_top'>");

echo "<H1>";
echo $_SESSION['pseudo'];
echo ", Bienvenue sur INSTALOL</H1>";

?>






</BODY>

</HTML>
