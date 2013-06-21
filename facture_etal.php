<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('facture_etal_top.php');

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$jquery.$jqueryui.$message_div.$graburljs.$compte_div ?>

		<script type="text/javascript">
		$(document).ready(function() {		
			$('#txt_quantite').keypress(function(event) {
				return /[0-9]/.test(String.fromCharCode(event.which));
			});

			$( "#dialog-confirm" ).hide();
			
			$("#box_periode2").change(function(){
				var selection2 = $("#box_periode2").get(0).selectedIndex;
				var selection = $("#box_periode").get(0).selectedIndex;
				selection2 -= 1;
				if (selection>=selection2){
					message("P\351riode invalide");
					$("#box_periode2")[0].selectedIndex = 0;
				}
			});
		});
		
		var fdata=new Array();
		var arTypes = new Array(<?php echo $jsarType ?>);
		var arMontantFCP = new Array(<?php echo $jsarMontantFCP ?>);
		var arMontantEURO = new Array(<?php echo $jsarMontantEURO ?>);
		var arUnite = new Array(<?php echo $jsarUnite ?>);
		var clientid = <?php echo $arCompte[1]?>;
		var success = gup("success");
		
	
		function showinfo(arrayid){
			arrayid--;
			if(success>0) {
				message("Facture provisoire cr&eacute;&eacute;e avec succ&egrave;s.");
				success=0;
			} else {
				message(arTypes[arrayid] + " (" + arMontantFCP[arrayid] + "FCP/" + arUnite[arrayid] + " &eacute;quivalent " + arMontantEURO[arrayid] + "&euro;/" + arUnite[arrayid]+")");
		}
		}

		function addtoarray(){
			var type = $("#box_type").val();
			var quant = $("#txt_quantite").val();
			var pricefcp = arMontantFCP[type-1];

			if(!$("#txt_quantite").val()==''){
				type = eval(type);
				if(quant>0) fdata.push(type+'#'+quant+'#'+pricefcp);	
				//alert(fdata[0]);
				showdetails();
			}
			else {
			message("Veuillez remplir une quantit\351");
			}
		}

		function showdetails(){
			var content;
			var type;
			var quant;	
			var total;
			var gtotal=0;
			var split;
			content = "<table><tr><th>D&eacute;signation</th><th>Prix</th><th>Quantit&eacute;</th><th>Total</th><th>Validation</th></tr>";
			for (i=0;i<fdata.length;i++) {
				split=fdata[i].split('#');
				type = split[0]; //example 1#45, take only 1
				quant = split[1];
				designation = arTypes[type-1];
				price = arMontantFCP[type-1];
				total = eval(quant*price);
				gtotal += total;
				content += "<tr><td>"+designation+"</td><td>"+price+" FCP</td><td class='right'>"+quant+"</td><td class='right'>"+total+" FCP</td><td class='center'><a href='javascript:suppdetail("+i+");'><img src='img/error_button.png' alt='supprimer' width='16' height='16' border='0'/></a></td></tr>";
			}
			content += "<tr><td colspan=3></td><td>"+gtotal+" FCP</td><td class='center'><a href='javascript:fconfirm();'><img src='img/checked.png' alt='valider' width='16' height='16' border='0'/></a></td></tr>";
			content += "</table>";
			$("#details").html(content);
			$("#txt_quantite").val('');
			$("#txt_quantite").focus();
			//alert(content);
		}
		
		function search_array(ar,value){
			for (var i = 0; i < ar.length; i++){
				if(ar[i] == value)
				return i;
			}	
			return -1;
		}

		function suppdetail(id){
			fdata.splice(id,1);
			showdetails();
		}

		function fconfirm(){
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
	
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			height:150,
			modal: true,
			buttons: {
				"Cr\351er": function() {
					var flat = fdata.toString();
					flat = flat.replace(/,/g,"$");
					submit_facture(flat,clientid);
					$( this ).dialog( "close" );
				},
				"Annuler": function() {
					$( this ).dialog( "close" );
				}
			}
		});
		}
		
		function submit_facture(fdata,clientid){
		var period = $("#box_periode").val();
		var period2 = $("#box_periode2").val();
		if(period2.length>0){period += " - "+period2;};
		
		$.get("facture_etal_submit.php",{fdata:fdata,clientid:clientid,period:period},
		      function(data){
			readResponse(data);
		      },"xml");
              //alert(data);
              //});
		}
	    
		function readResponse(data){
			responseXml = data;
			xmlDoc = responseXml.documentElement;
			factureid = xmlDoc.getElementsByTagName("facturetempid")[0].firstChild.data;
			window.location="facture_etal.php?success="+factureid;
		}


		function init(){
			showinfo(1);
			showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
		}
	</script>
	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div id="dialog-confirm" title="Demande de confirmation">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Cr&eacute;er le devis ?</p>
		</div>
		<div id="message" ></div>
		<div id="version">version <?php echo VERSION ?></div>
		<div id="invalidation"><?php echo $InValidationList ?></div>
		<div id="compte_div"></div>
		
		<br/><br/>
		<!-- Form -->
		<h1><?php echo $legend ?> <a href="delibs/<?php echo $deliblink ?>.pdf" target="_blank">(D&eacute;lib&eacute;ration <?php echo $deliblink ?>)</a> et <a href="delibs/<?php echo $deliblink2 ?>.pdf" target="_blank">(D&eacute;lib&eacute;ration <?php echo $deliblink2 ?>)</a></h1>

		<table id="tblcantine" class="tblform">
			<tbody>
				<tr>
					<td colspan="3">
						<!--Periode-->
                                                <label for="box_periode">P&eacute;riode</label>
                                                <select name="box_periode" id="box_periode">
                                                        <?php echo buildOptionsPeriod(1) ?>
                                                </select>
						<select name="box_periode2" id="box_periode2">
							<option value="" selected="selected">Pas de seconde p&eacute;riode</option>
                                                        <?php echo buildOptionsPeriod(0) ?>
                                                </select>
					</td>
				</tr>
				<tr>
					<td>
						<!--Type-->
						<label for="box_type">Type</label>
						<select name="box_type" id="box_type" onchange="showinfo(this.value)">
							<?php buildOptionsType() ?>
						</select>
					</td>
					<td>
						<!--Quantité-->
						<label for="txt_quantite">Quantit&eacute;</label>
						<input type="text" maxlength="2" name="txt_quantite" id="txt_quantite" />
					</td>
					<td>
						<img src="img/plus.png" alt="plussign" class="mousehand" onClick="addtoarray();" />
					</td>
				</tr>
			</tbody>
		</table>
		<br/>
		<div id="details"></div>
	</body>
</html>

