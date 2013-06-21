<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('checksession.php');

if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}
	
	
$cUser = unserialize($_SESSION['user']);
$admin = $cUser->userisadmin();
?>
<!DOCTYPE html>

<html lang="fr">
	<head>
	<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$jquery.$jqueryui.$message_div.$compte_div ?>
	<script type="text/javascript">
		$(document).ready(function() {
			showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
		});

	</script>

	</head>
	<body>
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<h1>Module d'importation des CF CPS</h1>
		
		<form target="_blank" action="upload_cps_cf.php" method="post" enctype="multipart/form-data">
		<label for="file">Fichier :</label>
		<input type="file" name="file" id="file" /> 
		<br />
		<input type="submit" name="submit" value="Submit" />
		</form>
	</body>
</html>

