<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('compte_paiement_top.php');

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$graburljs.$jquery.$jqueryui.$message_div.$compte_div ?>
	<script type="text/javascript">
	$(document).ready(function() {
		//$("[id^=chk_cps]").change(function(event){
		//	var id = event.target.id;
		//	var factureid = $("#"+id).val();
		//	
		//	//alert(id);
		//	if($("#"+id).prop("checked")){
		//		
		//		$.post("checkcps.php", { factureid:factureid, bool:"1" } );
		//		$("#tr"+factureid).addClass("purple");
		//		$("#tr"+factureid).prop("title","Facture b\351n\351ficiant d'une bourse");
		//	}else{
		//		$.post("checkcps.php", { factureid:factureid, bool:"0" } );
		//		$("#tr"+factureid).removeClass("purple");
		//		$("#tr"+factureid).prop("title","");
		//	}
		//});
	});
	
		function paiement(factureid,type){
			window.location.href="paiement_comptant.php?id="+factureid+"&type="+type;
		}

                function init(){
			showSubmitResult();
                        showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
			//alert("<?php echo $arCompte[1] ?>");
		}
		
		function showSubmitResult(){
			var success = gup('success');
			//alert(success);
			if (success>1) {
				message("Paiement effectu&eacute; avec succ&egrave;s.");
			} else if (success===0) {
				message("Echec du paiment, veuillez contacter votre administrateur");
			}
		}
	</script>

	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<br/><br/>
		<h1>Module de r&egrave;glement des factures</h1><br/><br/>
		<div id="version">version <?php echo VERSION ?></div>

		<table>
			<tr>
				<th>Type de Facture</th><th>Facture</th><th>PDF</th><th>Payer</th><th>Bourse</th>
				<? echo $listfacture_avalider ?>
			</tr>
		</table>
		<br /><br />
		<h1>Factures derni&egrave;rement r&eacute;gl&eacute;es</h1><br/><br/>
		<table>
			<tr>
				<th>Type de Facture</th><th>Facture</th><th>PDF</th><th>Re&ccedil;u</th>
				<? echo $listfacture_validees ?>
			</tr>
		</table>
		<br />
	</body>
</html>

