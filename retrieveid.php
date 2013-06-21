<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('retrieveid_top.php');
require_once('retrieveid_class.php');

switch ($_GET['form']){
	case "clients":
		$example = "NOM, PRENOM, DATE DE NAISSANCE, (ID)";
		$label = "Client";
	break;

	case "mandataires":
		$example = "NOM, PRENOM, ID TRESOR";
		$label = "Mandataire";
	break;

	case "enfants":
		$example = "NOM, PRENOM, ECOLE, CLASSE";
		$label = "Enfant";
	break;
}

if (isset($_POST['box_search'])) {
	echo $_POST['box_search'];
	$retriever = new retrieveid();
	$id = $retriever->id($_POST['box_search'],$_GET['form']);
	if ($id > 0) {
	
	$redirectto = $_GET['form'];
	if($redirectto=="enfants") $redirectto="clients";
	$form = $redirectto.'.php'; //get form type to search
	$url = $form . "?";
	header('location:'.$url.'edit='.$id.'&hideerrors=1');
	//echo $form;
	} else {
	$message = "Client Inexistant!";
	}
}
?>

<!DOCTYPE html>

<html lang="fr">
	<head>
	<?php echo $title.$icon.$charset.$nocache.$defaultcss.$chromecss.$graburljs.$compte_div.$jquery.$jqueryui.$message_div ?>
		<link rel="stylesheet" href="chosen/chosen.css" />
		<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
		
		<script type="text/javascript">
		$(document).ready(function() {
			var reaffect = gup('reaffect');
			if (reaffect) {
				$("#active_xml").prop("disabled","disabled");
				$("#submitbutton").val("R\351affecter");
				$("#submitbutton").click(function(){
					if($("#box_search").val()==''){
						message("Veuillez entrer une recherche");
						return false;
					}else{
						var reaffecttype = $("#reaffecttype").val();
						var reaffectfrom = $("#reaffectfrom").val();
						var reaffectto = $("#box_search").val();
						var fullname = $("#reaffectfromfullname").val();
						
						switch(reaffecttype){
							case 'kid':
								typetext = "de l'enfant ?";
							break;
							case 'factures':
								typetext = "des factures ?";
							break;
						}

						var text = "Accepter la r&eacute;affectation "+typetext;
						
						$("#modaltext").html(text);
						$( "#dialog:ui-dialog" ).dialog( "destroy" );
						$( "#dialog-confirm" ).dialog({
							resizable: false,
							height:150,
							modal: true,
							buttons: {
								"R\351affecter": function() {
									$.post("reaffect.php",{type:reaffecttype,from:reaffectfrom,to:reaffectto},
										function(data){
											//alert(data);
											window.location="clients.php?edit="+reaffectfrom+"&hideerrors=1";
										});
									$( this ).dialog( "close" );
								},
								"Annuler": function() {
									$( this ).dialog( "close" );
								}
							}
						});
						
						return false;
					}
						
				});
				$( "#warning" ).show();
				$( "#label_compte_desactive" ).hide();
				$( "#active_xml" ).hide();
			}else{
				$( "#warning" ).hide();
			}
				
			$( "#dialog-confirm" ).hide();
			
			$( "input:submit,input:button,button" ).button();
			///////chosen//////		
				load_list('1');
				
				$("#active_xml").change(function(){
					if($("#active_xml").prop("checked")){
						load_list('0');	
					}else{
						load_list('1');	
					}
				})
			});
		
		function load_list(active){
			var form = gup('form');
			
			$.post("retrieveid_list.php", { form:form , active:active })
			.done(function(data) {
				$("#box_search").empty();
				$("#box_search").append("<option value=''></option>");
				$("#box_search").append(data);
				$("#box_search").chosen();
				$("#box_search").trigger("liszt:updated");
				
			});
		}
		
		
		function init() {
			showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
		}
		</script>

	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div id="dialog-confirm" title="Demande de confirmation">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div id="modaltext"></div></p>
		</div>
		<div id="message"><? echo $message ?></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<br/><br/>
		<div id="warning" style="margin:auto;width:600px;background-color:yellow;border:red solid 10px;padding:10px;">
			<?php
			switch ($_GET['type']){
				case "kid":
					$warningmsg = "des enfants !";
				break;
				case "factures":
					$warningmsg = "des factures !";
				break;
			}
			print "<img src='img/warning.png' alt='warning'/> <h1>ATTENTION, vous &ecirc;tes en r&eacute;affection $warningmsg</h1>";
			?>
		</div>
		<!-- Suggestions -->
		<p>Veuillez entrer votre recherche avec les &eacute;l&eacute;ments suivants : "<? echo $example ?>"</p>
		<small style="float:right; visibility:hidden;">Hidden ID Field: <input type="text" id="suggestid" value="" style="font-size: 10px; width: 20px;" disabled="disabled" /></small>
		<br/>

		<br/>

			<form method="POST" action="<? echo $_SERVER['PHP_SELF'] . "?form=" . $_GET['form'] ?>" style="width:100%;text-align:center;">
				<input type="checkbox" id="active_xml" />
				<label for="active_xml" id="label_compte_desactive">Voir comptes d&eacute;sactiv&eacute;s</label>
				<br/>
				<label for="box_search"><? echo $label ?></label>
				<select name="box_search" id="box_search" data-placeholder="S&eacute;lectionner un compte" class="chzn-select" tabindex="2" style="width:450px;"></select>
				<input type="submit" id="submitbutton" value="Ouvrir le dossier" />
				<input type="hidden" name="reaffect" id="reaffect" value="<? echo $_GET['reaffect']; ?>"/>
				<input type="hidden" name="reaffecttype" id="reaffecttype" value="<? echo $_GET['type']; ?>"/>
				<input type="hidden" name="reaffectfrom" id="reaffectfrom" value="<? echo $arCompte[1]; ?>"/>
				<input type="hidden" name="reaffectfromfullname" id="reaffectfromfullname" value="<? echo $arCompte[0]; ?>"/>
			</form>

	</body>
</html>
