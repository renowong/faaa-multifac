<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('validation_required_top.php');
$cUser = unserialize($_SESSION['user']);
$userid = $cUser->userid();
?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$compte_div.$jquery.$jqueryui.$message_div.$graburljs ?>
	
	
	<script type="text/javascript">
	$(document).ready(function(){

	var validated = gup("validlist");
	$("#list_validation").load("avoir_validate_list.php");
	$( "#dialog-form" ).hide();
	});
	
	function fconfirm(text,avoirid){
	
	$( "#dialog:ui-dialog" ).dialog( "destroy" );
	
		$( "#dialog-form" ).dialog({
			height: 200,
			width: 500,
			modal: true,
			buttons: {
				"Confirmer le rejet": function() {
				if($("#commentaire").val()==''){
					message("Erreur, vous n'avez pas entr\351 de commentaire");
				}else{
					validateavoir(avoirid,0,$("#commentaire").val());
					$("#commentaire").val('');
					$( this ).dialog( "close" );
				}
				},
				Cancel: function() {
					$("#commentaire").val('');
					$( this ).dialog( "close" );
				}
			},
		});
	}
	
	function validate(avoirid,valid){
		if(valid) {
			validateavoir(avoirid,1,'Valid&eacute;');
		}else{
			fconfirm("Veuillez entrer un commentaire (obligatoire).",avoirid);
		}
		$("#list_validation").empty();
		$("#list_validation").load("avoir_validate_list.php");
	}
	
	function validateavoir(avoirid, acceptation, comment){
		var userid = '<? print $userid; ?>';
	    $.post('avoirs_validate.php',{avoirid:avoirid,acceptation:acceptation,comment:comment,userid:userid},
		   function(data){
			$("#list_validation").empty();
			$("#list_validation").load("avoir_validate_list.php");
		   });
	}
	
	function init(){
		showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
	}

	</script>

	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<br/>
		<h1>Module de validation des avoirs</h1><br/>
		
		<div id="list_validation" style="height:600px;"></div>
		
		
<div id="dialog-form" title="Rejeter l'avoir">
	<p class="validateTips">Le commentaire est obligatoire.</p>

	<form>
	<fieldset>
		<label for="commentaire">Commentaire</label>
		<input type="text" name="commentaire" id="commentaire" size="50" />
	</fieldset>
	</form>
</div>
		
	</body>
</html>

