<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('checksession.php');
require_once('checkyear.php');

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
			getinfo("validation");
			getinfo("facture");
			getinfo("client");
			getinfo("enfant");
			getinfo("saint");
		});
		
		function getinfo(cat){
			$.post("main_functions.php",{cat:cat},function(data){
				var obj = jQuery.parseJSON(data);
				switch(cat){
					case "validation":
					$("#validation_div").html("Nombre de validations en attente : <b>"+obj.num+"</b>");
					break;
					case "facture":
					$("#facture_div").html("Nombre de factures en attente de paiement : <b>"+obj.num+"</b>");
					break;
					case "client":
					$("#client_div").html("Nombre d'administr&eacute;s dans l'application : <b>"+obj.num+"</b>");
					break;
					case "enfant":
					$("#enfant_div").html("Nombre d'enfants dans l'application : <b>"+obj.num+"</b>");
					break;
					case "saint":
						var d=new Date();
						var today = d.getDate()+"/"+(d.getMonth()+1)+"/"+d.getFullYear();
						var saint = (obj[0].Fete);
						$("#thtitle").append(" Aujourd'hui le "+today+" f&ecirc;te de "+obj[0].Fete);
					break;
				}
			});
		}
	</script>

	</head>
	<body>
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<h1>
		Bienvenue dans votre logiciel de multifacturation.</h1>
		
		<table style="width:50em">
			<tr><th><div id="thtitle">Chiffres du moment -- </div></th></tr>
			<tr>
				<td>
					<div id="validation_div" style="padding:10px;"></div>
					<div id="facture_div" style="padding:10px;"></div>
					<div id="client_div" style="padding:10px;"></div>
					<div id="enfant_div" style="padding:10px;"></div>
				</td>
			</tr>
		</table>
	</body>
</html>

