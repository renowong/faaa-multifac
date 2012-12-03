<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('facture_amarrage_top.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$jquery.$jqueryui.$message_div.$graburljs.$compte_div ?>

		<script type="text/javascript">
		$(document).ready(function() {		
			$('#txt_quantite').keypress(function(event) {
				return /[0-9]/.test(String.fromCharCode(event.which));
			});
			$('#txt_PY').keypress(function(event) {
				return /[0-9]/.test(String.fromCharCode(event.which));
			});

			$( "#dialog-confirm" ).hide();
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
				"Facturer": function() {
					
					var flat = fdata.toString();
					
					flat = flat.replace(/,/g,"$");
					//alert(clientid);
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
		var py = $("#txt_PY").val();
		var lieu = $("#txt_lieu").val();
		var nav = $("#txt_nav").val();
		//alert (period);
		$.get("facture_amarrage_submit.php",{fdata:fdata,clientid:clientid,period:period,py:py,lieu:lieu,nav:nav},
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
			window.location="facture_amarrage.php?success="+factureid;
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
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Valider la facturation ?</p>
		</div>
		<div name="message" id="message" ></div>
		<div name="version" id="version">version <?php echo VERSION ?></div>
		<div name="invalidation" id="invalidation"><?php echo $InValidationList ?></div>
		<div name="compte_div" id="compte_div"></div>
		
		<br/><br/>
		<!-- Form -->
		<h1><?php echo $legend ?> <a href="delibs/<?php echo $deliblink ?>.pdf" target="_blank">(D&eacute;lib&eacute;ration <?php echo $deliblink ?>)</a></h1>

		<table name="tblcantine" id="tblcantine" class="tblform">
			<tbody>
				<tr>
					<td>
						<!--Periode-->
                                                <label for="box_periode">P&eacute;riode</label>
                                                <select name="box_periode" id="box_periode">
                                                        <?php echo $PeriodeList ?>
                                                </select>
					</td>
					<td colspan="2">
						<!--Navire-->
						<label for="txt_nav">Navire</label>
						<input class="uppercase" type="text" maxlength="20" name="txt_nav" id="txt_nav" />
					</td>
				</tr>
				<tr>
					<td>
						<!--PY-->
						<label for="txt_PY">PY</label>
						<input type="text" maxlength="10" name="txt_PY" id="txt_PY" />
					</td>
					<td colspan="2">
						<!--Lieu-->
						<label for="txt_lieu">Emplacement</label>
						<input class="uppercase" type="text" maxlength="10" name="txt_lieu" id="txt_lieu" />
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
						<image src="img/plus.png" class="mousehand" onClick="addtoarray();" />
					</td>
				</tr>
			</tbody>
		</table>
		<br/>
		<div name="details" id="details" ></div>
	</body>
</html>

