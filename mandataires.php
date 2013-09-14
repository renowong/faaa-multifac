<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('mandataires_top.php');


(isset($_GET['edit']) ? $edit="&edit=".$_GET['edit'] : $edit="");

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$mandatairejs.$retrieve_rues_js.$graburljs.$compte_div.$jquery.$jqueryui.$message_div ?>

		<script type="text/javascript">
		
		$(document).ready(function() {
				var edit = gup('edit');
				$('.reject').hide();
				$('#historique').hide();
				$('#showreject').hide();
				if (edit>0){$('#divhistorique').show();}else{$('#divhistorique').hide();$('#accounttoggle').hide();}
				
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
                                });
			
				$('#toggleaccount').click( function() {
					$('#compte').slideToggle();
					if ($('#toggleaccount').text()=='Afficher'){
                                                $('#toggleaccount').button('option','label','Cacher');
                                        }else{
                                                $('#toggleaccount').button('option','label','Afficher');
                                        }
                                });
			
			
			//////key checks//////
			$('#txt_Nom').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
			});
						
			$('#txt_Prenom').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				return /^[a-zA-Z\-\ ]+$/.test(String.fromCharCode(event.which));
				//return /^[a-zA-Z\u00C0-\u00D6\u00D9-\u00F6\u00F9-\u00FD\-\ ]+$/.test(String.fromCharCode(event.which));
			});
			
			$('#txt_Notahiti').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				return /^[0-9a-zA-Z]+$/.test(String.fromCharCode(event.which));
			});
			
			$('#txt_IDTresor').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				if ($("#txt_IDTresor").val().length == 4){
				$("#txt_IDTresor").val($("#txt_IDTresor").val() + "-");
				}
				
				return /^[0-9\-]+$/.test(String.fromCharCode(event.which));
			});
			
			$('#txt_Telephone').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				if ($("#txt_Telephone").val().length == 2 || $("#txt_Telephone").val().length == 5 || $("#txt_Telephone").val().length == 8){
				$("#txt_Telephone").val($("#txt_Telephone").val() + "-");
				}
				return /^[0-9]+$/.test(String.fromCharCode(event.which));
			});
			
			$('#txt_Telephone2').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				if ($("#txt_Telephone2").val().length == 2 || $("#txt_Telephone2").val().length == 5 || $("#txt_Telephone2").val().length == 8){
				$("#txt_Telephone2").val($("#txt_Telephone2").val() + "-");
				}
				return /^[0-9]+$/.test(String.fromCharCode(event.which));
			});
			
			$('#txt_Fax').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				if ($("#txt_Fax").val().length == 2 || $("#txt_Fax").val().length == 5 || $("#txt_Fax").val().length == 8){
				$("#txt_Fax").val($("#txt_Fax").val() + "-");
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
			
			$('#txt_RC').keypress(function(event) {
				if(event.which=='0'||event.which=='8') return true;
				if ($("#txt_RC").val().length == 5){
				$("#txt_RC").val($("#txt_RC").val() + "-");
				}
				return /^[0-9a-zA-Z]+$/.test(String.fromCharCode(event.which));
			});
			
			//////jqueryui buttons/////
			$( "input:submit,input:button,button" ).button();
			
		});
		
		function showSubmitResult(){
			var success = gup('success');
			var edit = gup('edit');
			//alert(success);
			if (success=='1') {
				if (edit>0) {
					message("Compte mis &agrave; jour avec succ&egrave;s");
				} else {
					message("Nouveau mandataire ajout&eacute; avec succ&egrave;s");
				}
			} else if (success=='0') {
				if (edit>0) {
					message("Echec dans la mise &agrave; jour du compte");
				} else {
					message("Echec dans l'ajout du nouveau mandataire");
				}
			}
		}

		function init(){
			checkexist($('#txt_Nom').val(), $('#txt_Prenom').val());
			showSubmitResult();
			showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
		}
		</script>
	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<br/><br/>
		<h1><?php echo $legend ?></h1>
		<div id="accounttoggle">
			<button id="toggleaccount">Cacher</button>                                                  
		</div>
        <div id="compte">
		<table>
			<tr>
				<td>
					<form name="frmRegistration" action="mandataires_validate.php?validationType=php<? echo $edit ?>" method="POST">
						<table id="tblmandataire" class="tblform">
							<tbody>
								<tr>
									<td colspan="2">
										<!--Status-->
										<label for="chk_status">Compte Actif</label>
										<input class="input" type="checkbox" name="chk_status" id="chk_status" value="1" <? if($_SESSION['values']['chk_status']) echo 'checked="checked"'; ?> />
									</td>
								</tr>
								<tr>
                                                                        <td>
                                                                                <!--Prefix-->
                                                                                <label for="box_Prefix">Pr&eacute;fix<span class="red">*</span></label><br />
										<select class="input" name="box_Prefix" id="box_Prefix" onchange="validate(this.value, this.id);">
                                                                                	<?php buildOptions($prefixOptions, $_SESSION['values']['box_Prefix']) ?>
										</select>
                                                                                <span id="box_PrefixFailed" class="<?php echo $_SESSION['errors']['Prefix'] ?> red"><br/>
                                                                                        Veuillez s&eacute;lectionner un pr&eacute;fix.
                                                                                </span>

                                                                        </td>
                                                                        <td>
                                                                                <!--RS-->
                                                                                <label for="txt_RS">Raison Sociale<span class="red">*</span></label><br />
                                                                                <input class="uppercase" type="text" name="txt_RS" id="txt_RS" value="<?php echo $_SESSION['values']['txt_RS'] ?>" size="20" maxlength="25" onBlur="validate(this.value, this.id);" />
                                                                                <span id="txt_RSFailed" class="<?php echo $_SESSION['errors']['RS'] ?> red"><br/>
                                                                                        Veuillez entrer une raison sociale.
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
										<!--Prenom-->
										<label for="txt_Prenom">Pr&eacute;nom<span class="red">*</span></label><br />
										<input class="uppercase" type="text" name="txt_Prenom" id="txt_Prenom" value="<?php echo $_SESSION['values']['txt_Prenom'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);checkexist(document.getElementById('txt_Nom').value, this.value);" />
										<span id="txt_PrenomFailed" class="<?php echo $_SESSION['errors']['Prenom'] ?> red"><br/>
											Veuillez entrer un pr&eacute;nom valide.
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
                                                                                <!--NoTahiti-->
                                                                                <label for="txt_Notahiti">No. Tahiti<span class="red">*</span></label><br />
                                                                                <input type="text" name="txt_Notahiti" id="txt_Notahiti" value="<?php echo $_SESSION['values']['txt_Notahiti'] ?>" size="20" maxlength="6" onBlur="validate(this.value, this.id);" />
										<span id="txt_NotahitiFailed" class="<?php echo $_SESSION['errors']['Notahiti'] ?> red"><br/>
                                                                                        Veuillez entrer un No. Tahiti valide (ex: 343244).
                                                                                </span>
                                                                        </td>
                                                                        <td>
                                                                                <!--RC-->
                                                                                <label for="txt_RC">RC</label><br />
                                                                                <input class="uppercase" type="text" name="txt_RC" id="txt_RC" value="<?php echo $_SESSION['values']['txt_RC'] ?>" size="20" maxlength="7" onkeyup="buildrc(this.value, this.id);" onBlur="validate(this.value, this.id);" />
										<span id="txt_RCFailed" class="<?php echo $_SESSION['errors']['RC'] ?> red"><br/>
                                                                                        Veuillez entrer un No. RC valide (ex: 34324-A).
                                                                                </span>
                                                                        </td>
                                                                </tr>
								<tr>
									<td>
										<!--Telephone-->
										<label for="txt_Telephone">T&eacute;l&eacute;phone</label><br />
										<input type="text" name="txt_Telephone" id="txt_Telephone" value="<?php echo $_SESSION['values']['txt_Telephone'] ?>" size="20" maxlength="11" onBlur="validate(this.value, this.id);" />
										<span id="txt_TelephoneFailed" class="<?php echo $_SESSION['errors']['Telephone'] ?> red"><br/>
											Veuillez entrer un num&eacute;ro t&eacute;l&eacute;phone valide (ex: 80-09-67).
										</span>
									</td>
									<td>
										<!--Telephone Portable-->
										<label for="txt_Telephone2">T&eacute;l&eacute;phone Portable</label><br />
										<input type="text" name="txt_Telephone2" id="txt_Telephone2" value="<?php echo $_SESSION['values']['txt_Telephone2'] ?>" size="20" maxlength="11" onBlur="validate(this.value, this.id);" />
										<span id="txt_Telephone2Failed" class="<?php echo $_SESSION['errors']['Telephone2'] ?> red"><br/>
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
                                                                                <span id="txt_RIBFailed" class="<?php echo $_SESSION['errors']['RIB'] ?>">
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
										<input class="submit" type="button" onClick="window.location='<?php echo $_SERVER['PHP_SELF'] ?>?reset=1';" value="Annuler" name="raz" />
										<!--Submit-->
										<input class="submit" type="submit" value="Valider" name="submit" />
									</td>
								</tr>
							</tbody>
						</table>
						<p class="legend"><span class="red">*</span> Champs Obligatoires</p>
					</form>
				</td>
					<?php echo buildLieuTable() ?>
			</tr>
		 </table>
		</div>
		<div id="divhistorique">
			<h1>Historique des derni&egrave;res factures</h1> <button id="togglehistory">Afficher</button> <button id="showreject">Afficher les rejets</button>	
			<div id="historique">
				<?php 
				if (isset($_GET['edit'])) echo buildFacturesEnCoursTable($_GET['edit'],$ar_f_m);
				 ?>
			<br/>
			</div>
		</div>	
	</body>
</html>

