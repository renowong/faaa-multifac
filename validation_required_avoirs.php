<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('validation_required_top.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$compte_div.$jquery.$jqueryui.$message_div.$graburljs ?>
	
	
	<script type="text/javascript">
	$(document).ready(function(){

	var validated = gup("validlist");
	$("#list_validation").load("avoir_validate_list.php");
	$( "#dialog-form" ).hide();
	});
	
	function fconfirm(type,text,fid){
	
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
					validatefacture(type,fid,0,$("#commentaire").val());
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
	
	function validate(type,factureid,valid){
		if(valid) {
			validatefacture(type,factureid,1,'En attente de r&egrave;glement');
		}else{
			fconfirm(type,"Veuillez entrer un commentaire (obligatoire).",factureid);
		}
		$("#list_validation").empty();
		$("#list_validation").load("facture_validate_list.php");
	}
	
	function validatefacture(type,factureid, acceptation, comment){
	    $.post('factures_validate.php',{type:type,factureid:factureid,acceptation:acceptation,comment:comment},
		   function(data){
			$("#list_validation").empty();
			$("#list_validation").load("facture_validate_list.php");
		   });
	}
	
	function filter(type){
		var validated = gup("validlist");
		$("#list_validation").empty();
		$("#list_validation").load("facture_validate_list.php?validlist="+validated+"&type="+type);
	}

	function init(){
		showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
	}

	</script>

	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div name="message" id="message" ></div>
		<div name="compte_div" id="compte_div"></div>
		<div name="version" id="version">version <?php echo VERSION ?></div>
		<br/>
		<h1>Module de validation des avoirs</h1><br/>
		
		<div id="list_validation" name="list_validation" style="height:600px;"></div>
		
		
<div id="dialog-form" title="Rejeter la facture">
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

