<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('paiement_comptant_top.php');

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$graburljs.$compte_div.$jquery.$jqueryui.$message_div ?>

		<script type="text/javascript" src="js/jquery.ui.datepicker-fr.js"></script>		
		<script type="text/javascript">
			/* Jquery */
			$(document).ready(function() {
				$('#txt_virement').datepicker({inline: true,minDate: "-1Y",maxDate: "0"});
				
				$('#txt_date_tresor').datepicker({inline: true,minDate: "-1Y",maxDate: "0"});
				
				showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
				
				$("#chq").hide();
				$("#vir").hide();
				$("#tsr").hide();
				$("#tpe").hide();
				$("#ech").hide();
				if (gup("type")=="repas"){
					$("#chk_echelon").prop("disabled",true);
					$("#box_Mode").empty();
					$("#box_Mode").append('<option value="num">Num&eacute;raire</option>');
					$("#box_Mode").append('<option value="chq">Ch&egrave;que</option>');
				}else{
					$("#mt").hide();
					$("#txt_payeur").val('<?php print $arCompte[0] ?>');
					$("#hid_payeur").val('<?php print $arCompte[0] ?>');
				}
				
				$( "#dialog-confirm" ).hide();
				
				//////jqueryui buttons/////
				$( "input:submit,input:button,button" ).button();
				
				//////key checks//////
				
				$('#txt_num_cheque').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[0-9]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_echelon').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[0-9]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_payeur').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_obs').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[0-9a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
				});
			});
			
			function fconfirm(){

				$( "#dialog:ui-dialog" ).dialog( "destroy" );
	
				$( "#dialog-confirm" ).dialog({
					resizable: false,
					height:150,
					modal: true,
					buttons: {
						"Payer": function() {
							submit_form();
							$( this ).dialog( "close" );
						},
						"Annuler": function() {
							$( this ).dialog( "close" );
						}
					}
				});
			}
			
			
			function submit_form(){
				
				var table = gup('type');
				var payeur = $("#txt_payeur").val();
				var type = $("#hid_type").val();
				var mode = $("#box_Mode").val();
				var echelonnage = $("#chk_echelon").prop("checked");
				var obs = $("#txt_obs").val();
				var num_chq = $("#txt_num_cheque").val();
				var organisme = $("#box_Organisme").val();
				var date_virement = $("#txt_virement").val();
				var date_tresor = $("#txt_date_tresor").val();
				var info_tresor = $("#txt_info_tresor").val();
				var montantech = $("#txt_echelon").val()*1;
				var montantmax = $("#montantmax").val()*1;
				var tpe = $("#txt_tpe").val();
				var id = gup('id');
				
				if (table=="repas"){
					var montantfcp = $("#txt_encaissement").val();
					var montanteuro = eval(montantfcp/119.332);
					var restearegler = montantfcp;
				}else{
					<? print "var details = ".json_encode(getAmount()).";\n"; ?>
					var montantfcp = details['montantfcp'];
					var montanteuro = details['montanteuro'];
					var restearegler = details['restearegler'];
				}
				
				if(payeur==''){
					message("Veuiller entrer le nom ou l'organisme payeur!");
					return false;
				}
				
				if(echelonnage && montantech=='') {
					message("Montant de l'\351chelon vide");
					return false;
				}
				
				if(mode=='0') {
					message("Veuillez s\351lectionner un mode de paiement");
					return false;
				}
				
				if(mode=='chq' && num_chq=='') {
					message("Veuillez entrer un num\351ro de ch\350que");
					return false;
				}
				if(mode=='vir' && date_virement=='') {
					message("Veuillez entrer une date de virement");
					return false;
				}
				if(mode=='tsr' && (date_tresor=='' || info_tresor=='')) {
					message("Veuillez entrer une date et information du tr\351sor");
					return false;
				}
				if(mode=='mnd' && obs=='') {
					message("Veuillez entrer une observation (obligatoire)");
					return false;
				}
				if(mode=='tpe' && tpe=='') {
					message("Veuillez entrer une information TPE");
					return false;
				}
			
				if(montantech>montantmax){
					message("Montant de l'\351chelon supp\351rieur au montant \340 r\351gler!");
					return false;
				}
				
				if(echelonnage) {echelonnage=1;}else{echelonnage=0;}
						
				$.get("paiement_comptant_submit.php",{id:id,payeur:payeur,type:type,mode:mode,echelonnage:echelonnage,montantech:montantech,obs:obs,numero_cheque:num_chq,organisme:organisme,date_virement:date_virement,date_tresor:date_tresor,info_tresor:info_tresor,tpe:tpe,montantfcp:montantfcp,montanteuro:montanteuro,restearegler:restearegler,table:table},
				      function(data){
					readResponse(data);
				});
			}
			
			function readResponse(data){
				responseXml = data;
				xmlDoc = responseXml.documentElement;
				var recuid = xmlDoc.getElementsByTagName("lastid")[0].firstChild.data;
				//alert(recuid);
				window.location="compte_paiement.php?success="+recuid;
			    }
			
			function selectmode(mode){
				//alert(mode);
				mode = "#"+mode;
				//first hide all
				$("#chq").hide();
				$("#vir").hide();
				$("#tsr").hide();
				$("#tpe").hide();
				$("#ech").hide();
				$("#chk_echelon").prop("checked", false);
				
				switch(mode){
					case "#22bc":
						$("#chk_echelon").prop("disabled", true);
						$("#txt_payeur").val("Commune de FAAA");
						$("#txt_payeur").prop("readonly", true);
					break;
					case "#22cf":
						$("#chk_echelon").prop("disabled", true);
						$("#txt_payeur").val("CPS");
						$("#txt_payeur").prop("readonly", true);
					break;
					case "#12cf":
						var ech = eval($("#montanttotal").val())/2;
						$("#chk_echelon").prop("checked", true);
						toggle_ech();
						$("#chk_echelon").prop("disabled", true);
						$("#txt_payeur").val("CPS");
						$("#txt_payeur").prop("readonly", true);
						$("#txt_echelon").val(ech);
					break;
					case "#12bc":
						var ech = eval($("#montanttotal").val())/2;
						$("#chk_echelon").prop("checked", true);
						toggle_ech();
						$("#chk_echelon").prop("disabled", true);
						$("#txt_payeur").val("Commune de FAAA");
						$("#txt_payeur").prop("readonly", true);
						$("#txt_echelon").val(ech);
					break;
					default:
						$("#chk_echelon").prop("disabled", false);
						$("#txt_payeur").prop("readonly", false);
						$("#txt_payeur").val($("#hid_payeur").val());
				}
				
				// then show selected
				$(mode).show();
				//alert(mode)
				//if(mode=="#anl") {$("#chk_echelon").prop("disabled","disabled");}else{$("#chk_echelon").prop("disabled","");};
			}
			
			function toggle_ech(){
				//alert($("#chk_echelon").attr("checked"));
				if($("#chk_echelon").attr("checked")=="checked"){
					$("#ech").show();
					$("#txt_echelon").val('');
				} else {
					$("#ech").hide();
					$("#txt_echelon").val('');
				}
			}
			
			function set_new_name(n){
				$("#hid_payeur").val(n);
			}
		</script>
	</head>
	<body>
		<? include_once('menu.php'); ?>
		<div id="dialog-confirm" title="Demande de confirmation">
		<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Valider le paiement ?</p>
		</div>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<br/><br/>
		<h1><?php echo $legend ?></h1>
		<table>
			<tr>
				<td>
					<table id="title">
						<tr>
							<td>
								<?php echo $info[0] ?>
							</td>
						</tr>
					</table>
					<table id="tblpaiement" class="tblform">
						<tbody>
							<tr> 
								<td colspan="2">
									<!--Payeur-->
									<label for="txt_payeur">Payeur</label><br />
									<input type="text" id="txt_payeur" name="txt_payeur" size="40" maxlength="20" value="" class="uppercase" onblur="set_new_name(this.value);" />
									<input type="hidden" id="hid_type" name="hid_type" value="<?php echo $info[1] ?>"/>
									<input type="hidden" id="hid_payeur" name="hid_payeur" />
								</td> 
							</tr>
							<tr>
								<td>
									<!--Mode-->
									<label for="box_Mode">Mode de paiement</label><br />
									<select class="input" name="box_Mode" id="box_Mode" onchange="selectmode(this.value);">
										<? print Modes(); ?>
									</select>
								</td>
								<td>
									<!--Echelonnage-->
									<label for="box_Mode">Echelonnage</label><br />
									<input type="checkbox" name="chk_echelon" id="chk_echelon" value="1" onclick="toggle_ech();" />
								</td>
							</tr>
							<tr id="chq">
								<td>
									<!--num cheque-->
									<label for="txt_num_cheque">Num&eacute;ro du Ch&egrave;que</label><br />
									<input type="text" name="txt_num_cheque" id="txt_num_cheque" value="" size="10" maxlength="8" />
								</td>
								<td>
									<!--Organisme-->
									<label for="box_Organisme">Organisme de paiement</label><br />
									<select class="input" name="box_Organisme" id="box_Organisme">
										<option value="BT">BT</option>
										<option value="BP">BP</option>
										<option value="CCP">CCP</option>
										<option value="CDC">CDC</option>
										<option value="SOC">SOC</option>
									</select>
								</td>
							</tr>
							<tr id="vir">
								<td colspan="2">
									<!--virement-->
									<label for="txt_virement">Date du virement</label><br />
									<form id="placeholder" method="get" action="#">
									<input type="text" name="txt_virement" id="txt_virement" value="" size="10" maxlength="8" READONLY />
									</form>
								</td>
							</tr>
							<tr id="tsr">
								<td>
									<!--Tresor date-->
									<label for="txt_date_tresor">Date</label><br />
									<input type="text" name="txt_date_tresor" id="txt_date_tresor" value="" size="10" maxlength="8" READONLY />
								</td>
								<td>
									<!--Info-->
									<label for="txt_info_tresor">Infos</label><br />
									<input type="text" name="txt_info_tresor" id="txt_info_tresor" value="" size="10" maxlength="8" />
								</td>
							</tr>
							<tr id="tpe">
								<td colspan="2">
									<!--TPE-->
									<label for="txt_tpe">TPE</label><br />
									<input type="text" name="txt_tpe" id="txt_tpe" value="" size="10" maxlength="8" />
								</td>
							</tr>
							<tr id="ech">
								<td colspan="2">
									<!--Echelonnage-->
									<label for="txt_echelon">Montant de l'&eacute;chelon</label><br />
									<input type="text" name="txt_echelon" id="txt_echelon" value="" size="10" maxlength="8" /> FCP
								</td>
							</tr>
							<tr id="mt">
								<td colspan="2">
									<!--Echelonnage-->
									<label for="txt_encaissement">Montant de l'encaissement</label><br />
									<input type="text" name="txt_encaissement" id="txt_encaissement" value="" size="10" maxlength="8" /> FCP
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<!--OBS-->
									<label for="txt_obs">Observations</label><br />
									<input type="text" id="txt_obs" name="txt_obs" size="50" maxlength="50" class="uppercase" />									</td>
								</td>
							</tr>
							<tr> 
								<td colspan="2" style="text-align:right;"> 
									<!--Submit--> 
									<input class="submit" type="submit" onclick="fconfirm();" value="Valider" name="submit" /> 
								</td> 
							</tr>
						</tbody>
					</table>
					<p class="legend"><span class="red">*</span> Champs Obligatoires</p>
				</td>
			</tr>
		</table>
	</body>
</html>
