<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('validation_required_top.php');
$list = $_GET['validlist'];
if($list){$stitle="Factures en attente";}else{$stitle="Module de validation des factures";}

$cUser = unserialize($_SESSION['user']);
$login = $cUser->userlogin();
//print $login;

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$compte_div.$jquery.$jqueryui.$message_div.$graburljs ?>
	
	<link rel="stylesheet" href="chosen/chosen.css" />
	<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){

	var validated = gup("validlist");
	$("#list_validation").load("facture_validate_list.php?validlist="+validated);
	$( "#dialog-form" ).hide();
	
	$("#box_search").chosen();
	});
	
	function fconfirm(type,text,fid,validlist){
	
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
					validatefacture(type,fid,0,$("#commentaire").val(),validlist);
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
	
	function devalidate(type,factureid){
		fconfirm(type,"Veuillez entrer un commentaire (obligatoire).",factureid,1);
		//filter('all');
	}
	
	function validate(type,factureid,valid){
		if(valid) {
			validatefacture(type,factureid,1,'Valid&eacute;e');
		}else{
			fconfirm(type,"Veuillez entrer un commentaire (obligatoire).",factureid,0);
		}
		//$("#list_validation").empty();
		//$("#list_validation").load("facture_validate_list.php");
	}
	
	function validatefacture(type,factureid, acceptation, comment,validlist){
		comment += " - <? print $login; ?>"
	    $.post('factures_validate.php',{type:type,factureid:factureid,acceptation:acceptation,comment:comment},
		   function(data){
			$("#list_validation").empty();
			if(validlist){
				$("#list_validation").load("facture_validate_list.php?validlist=1");
			}else{
				$("#list_validation").load("facture_validate_list.php");
			}
		   });
	}
	
	function filter(type){
		$(".chzn-select").val('').trigger("liszt:updated");
		var validated = gup("validlist");
		$("#list_validation").empty();
		$("#list_validation").load("facture_validate_list.php?validlist="+validated+"&type="+type);
	}

	function filter_byclient(){
		$("#slt_filter")[0].selectedIndex = 0;
		var client = $("#box_search").val();
		var validated = gup("validlist");
		$("#list_validation").empty();
		$("#list_validation").load("facture_validate_list.php?validlist="+validated+"&client="+client);
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
		<h1><? print $stitle ?></h1><br/>
		
		Filtre : <select id="slt_filter" onchange="javascript:filter(this.value);">
			<option value="all">Tout</option>
			<option value="cantine">Cantine</option>
			<option value="etal">Place et Etal</option>
			<option value="amarrage">Amarrage</option>
		</select><br/><br/>
				
				<label for="box_search"><? echo $label ?></label>
				
				<select name="box_search" id="box_search" data-placeholder="S&eacute;lectionner un compte" class="chzn-select" tabindex="2" style="width:450px;">
					<option value=""></option>
					<?php buildOptionsPersonnes($_GET['form']); ?>
				</select>
				<button onclick="filter_byclient();">Filtrer par client</button>
		<br/><br/>
		
		<div id="list_validation" style="height:600px;"></div>
		

		
		
<div id="dialog-form" title="Rejeter la facture">
	<p class="validateTips">Le commentaire est obligatoire.</p>

	<form>
	<fieldset>
		<label for="commentaire">Commentaire</label>
		<input type="text" name="commentaire" id="commentaire" maxlength="80" size="50" />
	</fieldset>
	</form>
</div>
		
	</body>
</html>

