<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('clients_top.php');

(isset($_GET['edit']) ? $edit="&edit=".$_GET['edit'] : $edit="");

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$clientjs.$retrieve_rues_js.$graburljs.$jquery.$jqueryui.$message_div.$compte_div.$kids_valid ?>
		<link rel="stylesheet" href="chosen/chosen.css" />
		<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/jquery.ui.datepicker-fr.js"></script>
		<script type="text/javascript">
		<? print "var listclasses = ".json_encode(buildOptionsClasses()).";\n"; ?>
		/////////////////////////////////////jquery start here /////////////////////////////////
			$(document).ready(function() {
				
			checkexist($('#txt_DateNaissance').val(), $('#txt_Nom').val(), $('#txt_Prenom').val());
			showSubmitResult();
			showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
				
				var edit = gup('edit');
				$("#opaquediv").hide();
				$('.reject').hide();
				$('#historique').hide();
				$('#avoirs').hide();
				$('#showreject').hide();
				$("#slt_status_periode").hide();
				if (edit>0){
					$('#divhistorique').show();
					$('#divavoirs').show();
					$('#divconjoint').load('conjoint.php?id='+edit, function(){
						$("#box_conjoint").chosen();
						$("button").button();
					});
					$('#divconjoint').show();
					
				}else{
					$('#divhistorique').hide();
					$('#divavoirs').hide();
					$('#accounttoggle').hide();
					$('#divconjoint').hide();
				}
				
				$('#showreject').click( function() {
					$('.reject').toggle();
					if ($('#showreject').text()=='Afficher les rejets'){
                                                $('#showreject').button('option','label','Cacher les rejets');
                                        }else{
                                                $('#showreject').button('option','label','Afficher les rejets');
                                        }
				});	

				$('#togglehistory').click( function() {
					$('#historique').slideToggle();
					if ($('#togglehistory').text()=='Afficher'){
						$('#togglehistory').button('option','label','Cacher');
						$('#showreject').show();
					}else{
						$('#togglehistory').button('option','label','Afficher');
						$('#showreject').hide();
					}
					scrolldown();
                                });
				
				$('#toggleavoirs').click( function() {
					$('#avoirs').slideToggle();
					if ($('#toggleavoirs').text()=='Afficher'){
						$('#toggleavoirs').button('option','label','Cacher');
					}else{
						$('#toggleavoirs').button('option','label','Afficher');
					}
					scrolldown();
                                })
			
				$('#toggleaccount').click( function() {
					$('#compte').slideToggle();
					if ($('#toggleaccount').text()=='Afficher'){
                                                $('#toggleaccount').button('option','label','Cacher');
                                        }else{
                                                $('#toggleaccount').button('option','label','Afficher');
                                        }
                                });
				
				$('#list_enfants').load('list_kids.php?id='+edit);
				
				
				//////key checks//////
				
				$('#txt_Nom').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_NomMarital').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_Prenom').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
					//return /^[a-zA-Z\u00C0-\u00D6\u00D9-\u00F6\u00F9-\u00FD\-\ ]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_Prenom2').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
					//return /^[a-zA-Z\u00C0-\u00D6\u00D9-\u00F6\u00F9-\u00FD\-\ ]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_IDTresor').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					if ($("#txt_IDTresor").val().length == 4){
					$("#txt_IDTresor").val($("#txt_IDTresor").val() + "-");
					}
					
					return /^[0-9\-]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_CPS').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[0-9]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_Fax').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					if ($("#txt_Fax").val().length == 2 || $("#txt_Fax").val().length == 5 || $("#txt_Fax").val().length == 8){
					$("#txt_Fax").val($("#txt_Fax").val() + "-");
					}
					return /^[0-9]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_Telephone').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					if ($("#txt_Telephone").val().length == 2 || $("#txt_Telephone").val().length == 5 || $("#txt_Telephone").val().length == 8){
					$("#txt_Telephone").val($("#txt_Telephone").val() + "-");
					}
					return /^[0-9]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_RIB').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					if ($("#txt_RIB").val().length == 5 || $("#txt_RIB").val().length == 11|| $("#txt_RIB").val().length == 23){
					$("#txt_RIB").val($("#txt_RIB").val() + "-");
					}
					return /^[0-9A-Za-z]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_cps_enfant').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[0-9]+$/.test(String.fromCharCode(event.which));
				});
							
				$('#txt_nom_enfant').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
					//return /^[a-zA-Z\u00C0-\u00D6\u00D9-\u00F6\u00F9-\u00FD\-\ ]+$/.test(String.fromCharCode(event.which));
				});
				
				$('#txt_prenom_enfant').keypress(function(event) {
					if(event.which=='0'||event.which=='8') return true;
					return /^[a-zA-Z\-\ \']+$/.test(String.fromCharCode(event.which));
					//return /^[a-zA-Z\u00C0-\u00D6\u00D9-\u00F6\u00F9-\u00FD\-\ \']+$/.test(String.fromCharCode(event.which));
				});
				
				//changement temporaire pour autoriser les enfants en tant que parents
				//if(edit==""|edit=="0"){
						//$('#txt_DateNaissance').datepicker({inline: true,changeMonth: true,changeYear: true,minDate: "-70Y",maxDate: "-18Y",yearRange: "-70:-18"});
						$('#txt_DateNaissance').datepicker({inline: true,changeMonth: true,changeYear: true,minDate: "-70Y",maxDate: "0",yearRange: "-70:+0"});
				//}
				
				$('#txt_dn_enfant').datepicker({inline: true,changeMonth: true,changeYear: true,minDate: "-18Y",maxDate: "0",yearRange: "-18:+0"});
				$('#txt_entree_enfant').datepicker({inline: true,changeMonth: true,changeYear: true});
				$('#txt_sortie_enfant').datepicker({inline: true,changeMonth: true,changeYear: true});
			
				//////jqueryui buttons/////
				$( "input:submit,input:button,button" ).button();
				
				load_classes($("#slt_ecole_enfant").val(),'slt_classe_enfant');
				
				///////session info for the kids form//////////////
				$("#slt_classe_enfant").val('<? print $_SESSION['values']['slt_enfantClasse']; ?>');
				$("#slt_sexe_enfant").val('<? print $_SESSION['values']['slt_enfantSexe']; ?>');
			});
			


		function showSubmitResult(){
			var success = gup('success');
			var edit = gup('edit');
			var exist = gup('exist');
			if (success=='1') {
				if (edit>0) {
					message("Compte mis &agrave; jour avec succ&egrave;s");
				} else {
					message("Nouvel administr&eacute; ajout&eacute; avec succ&egrave;s");
				}
			} else if (success=='0') {
				if (edit>0) {
					message("Echec dans la mise &agrave; jour du compte");
				} else {
					message("Echec dans l'ajout du nouvel administr&eacute;");
				}
				if (exist){message("Doublon d&eacute;tect&eacute;");}
			}
			
		}
		
		function editkid(id){
			//alert("editkid");
			//$("#chk_actif_enfant").prop("disabled", false);				
			    $.post("kids_functions.php", { query: "select",id: id },
				function(data) {
					//alert("Data Loaded: " + data);
					var xml = data,
					xmlDoc = $.parseXML(xml),
					$xml = $(xmlDoc),
					$child = $xml.find("child");
					
					var id = $child.attr("id");
					var nom = $child.find("nom").text();
					var prenom = $child.find("prenom").text();
					var dn = $child.find("dn").text();
					var sexe = $child.find("sexe").text();
					var cps = $child.find("cps").text();
					var status = $child.find("status").text();
					var status_expires = $child.find("status_expires").text();
					var status_periode = $child.find("status_periode").text();
					var ecole = $child.find("ecole").text();
					var classe = $child.find("classe").text();
					var entree = $child.find("entree").text();
					var sortie = $child.find("sortie").text();
					var active = $child.find("active").text();
					var dest = $child.find("destinataire").text();
					$("#slt_ecole_enfant").val(ecole);
					load_classes($("#slt_ecole_enfant").val(),'slt_classe_enfant');
					
					$("#txt_nom_enfant").val(nom);
					$("#txt_prenom_enfant").val(prenom);
					dn = dn.split("-");
					$("#txt_dn_enfant").val(dn[2]+"/"+dn[1]+"/"+dn[0]);
					$("#txt_cps_enfant").val(cps);
					//alert(classe);
					$("#slt_classe_enfant").val(classe);
					entree = entree.split("-");
					$("#txt_entree_enfant").val(entree[2]+"/"+entree[1]+"/"+entree[0]);
					sortie = sortie.split("-");
					$("#txt_sortie_enfant").val(sortie[2]+"/"+sortie[1]+"/"+sortie[0]);
					$("#slt_sexe_enfant").val(sexe);
					$("#slt_status_enfant").val(status);
					
					//determiner si le status est expire
					var dateParts = status_expires.split("-");
					var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
					var now = new Date();
					//alert(status_periode);
					if(now>jsDate){
						$("#slt_status_periode").val(0);
					}else{
						$("#slt_status_periode").val(status_periode);
					}
					//fin
					switch_periode(status);
					//if($("#slt_status_periode").val()>0){$("#slt_status_periode").show();}
					
					if(active=='1'){
						$("#chk_actif_enfant").prop("checked", true);
					}else{
						$("#chk_actif_enfant").prop("checked", false);
					}
					if(dest=='1'){
						$("#chk_dest_enfant").prop("checked", true);
					}else{
						$("#chk_dest_enfant").prop("checked", false);
					}
					$("#id_enfant").val(id);
					$("#btn_add_enfant").button("option", "label","Mettre \340 jour");
				});
		}
		
		function reaffectkid(id){
			window.location = "retrieveid.php?form=clients&reaffect=true&type=kid";
		}
		
		function reaffectfactures(id){
			window.location = "retrieveid.php?form=clients&reaffect=true&type=factures";
		}
		
		function load_classes(id,select){
				//alert(id);
				$("#"+select).empty();
				var list = listclasses[id].split(",");
				for (var i = 0; i < list.length; i++) {
					$("#"+select).append("<option value='"+list[i]+"'>"+list[i]+"</option>");
				}
		}
		
		function resetenfant(){
				var edit = gup("edit");
				window.location = "clients.php?edit="+edit+"&reset=1";
		}
		
		function scrolldown(){
			var n = $(document).height();
			$('html, body').animate({ scrollTop: n },'50');
		}
		
		function scrollup(){
			window.scrollTo(0, 0);
		}
		
		function div_avoir(p){
			scrollup();
			$("#div_avoir").load(p);
			$("#opaquediv").show();
		}
		
		function div_avoir_close(){
			$("#div_avoir").empty();
			$("#opaquediv").hide();
		}
		function affect_conjoint(){
			var conjointid = $("#box_conjoint").val();
			var id = gup('edit');
			
			$.get("conjoint_set.php",{id:id,conjointid:conjointid},
			function(data){
				$('#divconjoint').empty();
				$('#divconjoint').load('conjoint.php?id='+id, function(){
					$("#box_conjoint").chosen();
					$("button").button();
				});
			});

		}
		
		function switch_periode(value){
			if(value=='15'||value=='16'){
				$("#slt_status_periode").show();
			}else{
				$("#slt_status_periode").hide();
				$("#slt_status_periode")[0].selectedIndex = 0;
			}
		}
		
		function reset_date(input){
			//alert(input);
			$("#"+input).val("");
		}
		</script>
	</head>
	<body>
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<br/><br/>
		<!-- Form -->
		<h1><?php echo $legend ?></h1>
		<div id="accounttoggle">
			<button id="toggleaccount">Cacher</button>                                                  
		</div>
        <div id="compte">
		<table>
			<tr>
				<td>
					<form name="frmRegistration" action="clients_validate.php?validationType=php<? echo $edit ?>" method="POST">
						<table id="tblclient" class="tblform">
							<tbody>
								<tr><th colspan="2">Donn&eacute;es du compte</th></tr>
								<tr>
									<td title="Option d&eacute;sactiv&eacute;e lorsque des factures sont en cours ou la liste des enfants n'est pas vide.">
										<!--Status-->
										<label for="chk_status">Compte Actif</label>
										<input type="checkbox" name="chk_status" id="chk_status" value="1" <? if($_SESSION['values']['chk_status']) echo 'checked="checked"'; ?> <? echo $actiflock ?>/>
										<div>
										<?
										if($svc=="FTR"||$svc=="REG"||$svc=="INF"){
											echo $totalfactures;
										}
										?>
										</div>
									</td>
									<td>
										<!--Civilite-->
										<label for="box_Civilite">Civilit&eacute;<span class="red">*</span></label><br />
										<select class="input" name="box_Civilite" id="box_Civilite" onchange="validate(this.value, this.id);">
											<?php buildOptions($civiliteOptions, $_SESSION['values']['box_Civilite']) ?>
										</select><br/>
										<span id="box_CiviliteFailed" class="<?php echo $_SESSION['errors']['Civilite'] ?> red">
											Veuillez s&eacute;lectionner une civilit&eacute;.
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Nom Patronimyque-->
										<label for="txt_Nom">Nom<span class="red">*</span></label><br />
										<input class="uppercase" type="text" name="txt_Nom" id="txt_Nom" value="<?php echo $_SESSION['values']['txt_Nom'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_NomFailed" class="<?php echo $_SESSION['errors']['Nom'] ?> red"><br/>
										Veuillez entrer un nom valide.
										</span>
									</td>
									<td>
										<!--Nom Marital-->
										<label for="txt_NomMarital">Nom Marital</label><br />
										<input class="uppercase" type="text" name="txt_NomMarital" id="txt_NomMarital" value="<?php echo $_SESSION['values']['txt_NomMarital'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_NomMaritalFailed" class="<?php echo $_SESSION['errors']['NomMarital'] ?> red"><br/>
											Veuillez entrer un nom marital valide.
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Prenom-->
										<label for="txt_Prenom">Pr&eacute;nom<span class="red">*</span></label><br />
										<input class="uppercase" type="text" name="txt_Prenom" id="txt_Prenom" value="<?php echo $_SESSION['values']['txt_Prenom'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_PrenomFailed" class="<?php echo $_SESSION['errors']['Prenom'] ?> red"><br/>
											Veuillez entrer un pr&eacute;nom valide.
										</span>
									</td>
									<td>
										<!--Prenom2-->
										<label for="txt_Prenom2">Pr&eacute;nom 2</label><br />
										<input class="uppercase" type="text" name="txt_Prenom2" id="txt_Prenom2" value="<?php echo $_SESSION['values']['txt_Prenom2'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_Prenom2Failed" class="<?php echo $_SESSION['errors']['Prenom2'] ?> red"><br/>
											Veuillez entrer un deuxi&egrave;me pr&eacute;nom valide.
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Date de Naissance-->
										<label for="txt_DateNaissance">Date de Naissance<span class="red">*</span></label><br />
										<input type="text" name="txt_DateNaissance" id="txt_DateNaissance" value="<?php echo $_SESSION['values']['txt_DateNaissance'] ?>" size="20" maxlength="10" onBlur="checkexist(this.value, document.getElementById('txt_Nom').value, document.getElementById('txt_Prenom').value);" readonly />
										<span id="txt_DateNaissanceFailed" class="<?php echo $_SESSION['errors']['DateNaissance'] ?> red"><br/>
											Veuillez entrer une date valide.
										</span>
									</td>
									<td>
										<!--LieuNaissance-->
										<label for="txt_LieuNaissance">Lieu de Naissance</label><br />
										<input class="uppercase" type="text" name="txt_LieuNaissance" id="txt_LieuNaissance" value="<?php echo $_SESSION['values']['txt_LieuNaissance'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_LieuNaissanceFailed" class="<?php echo $_SESSION['errors']['LieuNaissance'] ?> red"><br/>
											Veuillez entrer un lieu valide.
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--ID Tresor-->
										<label for="txt_IDTresor">ID Tr&eacute;sor</label><br />
										<input type="text" name="txt_IDTresor" id="txt_IDTresor" value="<?php echo $_SESSION['values']['txt_IDTresor'] ?>" size="20" maxlength="7" onBlur="validate(this.value, this.id);" />
										<span id="txt_IDTresorFailed" class="<?php echo $_SESSION['errors']['IDTresor'] ?> red"><br/>
											Veuillez entrer un identifiant tr&eacute;sor valide (ex: 1234-12).
										</span>
									</td>
									<td>
										<!--Email-->
										<label for="txt_Email">Email</label><br />
										<input class="lowercase" type="text" name="txt_Email" id="txt_Email" value="<?php echo $_SESSION['values']['txt_Email'] ?>" size="20" maxlength="50" onBlur="validate(this.value, this.id);" />
										<span id="txt_EmailFailed" class="<?php echo $_SESSION['errors']['Email'] ?> red"><br/>
											Veuillez entrer un email valide (ex: monsieur@mail.pf).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--CPS-->
										<label for="txt_CPS">CPS</label><br />
										<input type="text" name="txt_CPS" id="txt_CPS" value="<?php echo $_SESSION['values']['txt_CPS'] ?>" size="20" maxlength="7" onBlur="validate(this.value, this.id);" />
										<span id="txt_CPSFailed" class="<?php echo $_SESSION['errors']['CPS'] ?> red"><br/>
											Veuillez entrer un num&eacute;ro CPS valide (ex: 1234567).
										</span>
									</td>
									<td>
										<!--Telephone-->
										<label for="txt_Telephone">T&eacute;l&eacute;phone</label><br />
										<input type="text" name="txt_Telephone" id="txt_Telephone" value="<?php echo $_SESSION['values']['txt_Telephone'] ?>" size="20" maxlength="11" onBlur="validate(this.value, this.id);" />
										<span id="txt_TelephoneFailed" class="<?php echo $_SESSION['errors']['Telephone'] ?> red"><br/>
											Veuillez entrer un num&eacute;ro t&eacute;l&eacute;phone valide (ex: 80-09-67).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Fax-->
										<label for="txt_Fax">Fax</label><br />
										<input type="text" name="txt_Fax" id="txt_Fax" value="<?php echo $_SESSION['values']['txt_Fax'] ?>" size="20" maxlength="11" onBlur="validate(this.value, this.id);" />
										<span id="txt_FaxFailed" class="<?php echo $_SESSION['errors']['Fax'] ?> red"><br/>
											Veuillez entrer un num&eacute;ro de fax valide (ex: 80-09-67).
										</span>
									</td>
									<td>
										<!--BP-->
										<label for="txt_BP">B.P.</label><br />
										<input type="text" name="txt_BP" id="txt_BP" value="<?php echo $_SESSION['values']['txt_BP'] ?>" size="20" maxlength="6" onBlur="validate(this.value, this.id);" />
										<span id="txt_BPFailed" class="<?php echo $_SESSION['errors']['BP'] ?> red"><br/>
											Veuillez entrer une bo&icirc;te postale valide (ex: 60002).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--CP-->
										<label for="txt_CP">Code Postal</label><br />
										<input class="uppercase" type="text" name="txt_CP" id="txt_CP" value="<?php echo $_SESSION['values']['txt_CP'] ?>" size="20" maxlength="5" onBlur="validate(this.value, this.id);retrieve_rue(this.value);" />
										<span id="txt_CPFailed" class="<?php echo $_SESSION['errors']['CP'] ?> red"><br/>
											Veuillez entrer un code postal valide (ex: 98702).
										</span>
									</td>
									<td>
										<!--Ville-->
										<label for="txt_Ville">Ville</label><br />
										<input class="uppercase" type="text" name="txt_Ville" id="txt_Ville" value="<?php echo stripslashes($_SESSION['values']['txt_Ville']) ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_VilleFailed" class="<?php echo $_SESSION['errors']['Ville'] ?> red"><br/>
										Veuillez entrer une ville valide (ex: FAAA).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Commune-->
										<label for="txt_Commune">Commune / D&eacute;partement</label><br />
										<input class="uppercase" type="text" name="txt_Commune" id="txt_Commune" value="<?php echo stripslashes($_SESSION['values']['txt_Commune']) ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_CommuneFailed" class="<?php echo $_SESSION['errors']['Commune'] ?> red"><br/>
											Veuillez entrer une commune valide (ex: FAAA).
										</span>
									</td>
									<td>
										<!--Pays-->
										<label for="txt_Pays">Pays</label><br />
										<input class="uppercase" type="text" name="txt_Pays" id="txt_Pays" value="<?php echo $_SESSION['values']['txt_Pays'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_PaysFailed" class="<?php echo $_SESSION['errors']['Pays'] ?> red"><br/>
											Veuillez entrer un pays valide (ex: TAHITI).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Aroa-->
										<label for="txt_Aroa">N&deg; + Aroa<span class="red">*</span></label><br />
										<input class="uppercase" type="text" name="txt_Aroa" id="txt_Aroa" value="<?php echo stripslashes($_SESSION['values']['txt_Aroa']) ?>" size="20" maxlength="35" onBlur="validate(this.value, this.id);" />
										<span id="txt_AroaFailed" class="<?php echo $_SESSION['errors']['Aroa'] ?> red"><br/>
											Veuillez entrer une rue valide.
										</span>
									</td>
									<td>
										<!--Quartier-->
										<label for="txt_Quartier">Quartier<span class="red">*</span></label><br />
										<input class="uppercase" type="text" name="txt_Quartier" id="txt_Quartier" value="<?php echo $_SESSION['values']['txt_Quartier'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_QuartierFailed" class="<?php echo $_SESSION['errors']['Quartier'] ?> red"><br/>
											Veuillez entrer un quartier valide.
										</span>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<!--RIB-->
										<label for="txt_RIB">R.I.B.</label>
										<input type="text" class="uppercase" name="txt_RIB" id="txt_RIB" size="40" maxlength="26" value="<?php echo $_SESSION['values']['txt_RIB'] ?>" onBlur="validate(this.value, this.id);" />
										<span id="txt_RIBFailed" class="<?php echo $_SESSION['errors']['RIB'] ?> red"><br/>
                                                                                        Veuillez entrer un R.I.B. valide.
                                                                                </span>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<!--Obs-->
										<label for="txt_obs">Observations</label><br />
										<textarea style="height:100px;width:340px;resize:none;" id="txt_obs" name="txt_obs"><?php echo $_SESSION['values']['txt_obs'] ?></textarea>
									</td>
								</tr>	
								<tr>
									<td colspan="2" style="text-align:right;">
										<!--Reset-->
										<input class="submit" type="button" onClick="window.location='<?php print $_SERVER['PHP_SELF'] ?>?reset=1&edit=<?php print $_GET['edit'] ?>';" value="Annuler" name="raz" />
										<!--Submit-->
										<input class="submit" type="submit" value="Valider" name="submit" />
									</td>
								</tr>
							</tbody>
						</table>
						<p class="legend"><span class="red">*</span> Champs Obligatoires</p>
					</form>
				</td>
				<?php echo buildAnnexesTable() ?>
			</tr>
		</table>
		</div>
	<?php
		if($svc=="FTR"||$svc=="REG"||$svc=="INF"){
			echo "<div id='divhistorique'>".
			"<h1>Status des factures li&eacute;es au compte</h1> <button id='togglehistory'>Afficher</button> <button id='showreject'>Afficher les rejets</button>".
			"<div id='historique'>";
			if (isset($_GET['edit'])) echo buildFacturesEnCoursTable($_GET['edit'],$ar_f_c);
			echo "<br/></div></div>";
		}
	?>

		<!-- obsolete
		<div id="divavoirs">
			<h1>Avoirs</h1> <button id="toggleavoirs">Afficher</button> <button onclick="div_avoir('avoirs.php');">Ajouter</button>	
			<div id="avoirs">
				<?php 
				//if (isset($_GET['edit'])) echo buildAvoirsTable($_GET['edit']);
				 ?>
			<br/>
			</div>
		</div>
		//-->
		
		<br/><br/>
		<div id="opaquediv" style="position:absolute;top:0px;width:100%;height:100%;z-index:20;">
			<div style="background-color:white;" id="div_avoir" ></div>
		</div>
	</body>
</html>

