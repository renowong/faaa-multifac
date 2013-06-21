<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('compte_top.php');
?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$nocache.$defaultcss.$chromecss.$graburljs.$compte_div.$compte_valid.$jquery.$jqueryui.$message_div ?>
		<script type="text/javascript">
		$(document).ready(function() {
			//////jqueryui buttons/////
			$( "input:submit,input:button,button" ).button();
		});
		function confirmpwd(pwd2){
			var pwd = $('#txt_Password').val();
			if (pwd==pwd2){
				$('#txt_Password2Failed').removeClass("error");
				return true;
			} else {
				$('#txt_Password2Failed').addClass("error");
				return false;
			}
		}

		function showSubmitResult(){
			var success = gup('success');
			var update = gup('update');
			//alert(success);
			if (success=='1') {
			if (update>0) {
				message("Compte mis &agrave; jour avec succ&egrave;s");
			} else {
				message("Nouvel agent ajout&eacute; avec succ&egrave;s");
			}
			} else if (success=='0') {
				if (update>0) {
					message("Echec dans la mise &agrave; jour du compte");
				} else {
					message("Echec dans l'ajout du nouvel agent");
				}
			}
		}

			function jumpto(id){
				window.location = 'compte.php?modif=' + id;
			}

			function init(){
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
		<h1><?php echo $legend ?></h1><br/>
		<?php if ($admin) buildComptes() ?>
		<table>
			<tr>
				<td>
					<form name="frmRegistration" action="compte_validate.php?validationType=php" method="POST">
						<input type="hidden" name="userid" value="<?php echo $userid ?>" />
						<table id="tblcompte" class="tblform">
							<tbody>
								<tr>
									<td>
										<!--Nom Patronimyque-->
										<label for="txt_Nom">Nom<span class="red">*</span></label><br />
										<input class="uppercase <?php echo $lock ?>" type="text" name="txt_Nom" id="txt_Nom" value="<?php echo $_SESSION['values']['txt_Nom'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" <?php echo $readonly ?> autocomplete="off"/>
										<span id="txt_NomFailed" class="<?php echo $_SESSION['errors']['Nom'] ?>">Veuillez entrer un nom valide.</span>
									</td>
									<td>
										<!--Prenom-->
										<label for="txt_Prenom">Pr&eacute;nom<span class="red">*</span></label><br />
										<input class="capitalize <?php echo $lock ?>" type="text" name="txt_Prenom" id="txt_Prenom" value="<?php echo $_SESSION['values']['txt_Prenom'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" <?php echo $readonly ?> autocomplete="off"/>
										<span id="txt_PrenomFailed" class="<?php echo $_SESSION['errors']['Prenom'] ?>">Veuillez entrer un pr&eacute;nom valide.</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Login-->
										<label for="txt_Login">Nom d'utilisateur<span class="red">*</span></label><br />
										<input class="lowercase <?php echo $lock ?>" type="text" name="txt_Login" id="txt_Login" value="<?php echo $_SESSION['values']['txt_Login'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" <?php echo $readonly ?> autocomplete="off"/>
										<span id="txt_LoginFailed" class="<?php echo $_SESSION['errors']['Login'] ?>">Veuillez entrer un nom utilisateur valide (minimum 4 lettres).</span>
									</td>
									<td>
										<!--Service-->
										<label for="box_Service">Service<span class="red">*</span></label><br />
										<select class="<?php echo $lock ?>" name="box_Service" id="box_Service" onchange="validate(this.value, this.id);">
											<?php buildOptions($serviceOptions, $_SESSION['values']['box_Service'], $readonly) ?>
										</select>
										<span id="box_ServiceFailed" class="<?php echo $_SESSION['errors']['Service'] ?>">
											Veuillez s&eacute;lectionner un service.
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Password-->
										<label for="txt_Password">Mot de passe<span class="red">*</span></label><br />
										<input class="lowercase" type="password" name="txt_Password" id="txt_Password" value="<?php echo $_SESSION['values']['txt_Password'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" autocomplete="off"/>
										<span id="txt_PasswordFailed" class="<?php echo $_SESSION['errors']['Password'] ?>">Veuillez entrer un Mot de Passe valide (min 4 lettres).</span>
									</td>
									<td>
										<!--Password2-->
										<label for="txt_Password2">M.D.P. (confirmation)<span class="red">*</span></label><br />
										<input class="lowercase" type="password" name="txt_Password2" id="txt_Password2" value="<?php echo $_SESSION['values']['txt_Password2'] ?>" size="20" maxlength="20" onBlur="confirmpwd(this.value);" autocomplete="off"/>
										<span id="txt_Password2Failed" class="<?php echo $_SESSION['errors']['Password2'] ?>">Les mots de passes ne sont pas identiques.</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Admin-->
										<?php
										(($admin) ? $isDisabled = '' : $isDisabled = 'onClick="return false;" ');
										($_SESSION['values']['chk_isAdmin']==1 ? $isChecked = 'checked ' : $isChecked = '');
										echo '<label for="chk_Admin">Est Administrateur?</label>';
										echo '<input type="checkbox" name="chk_Admin" id="chk_Admin" value="1" '.$isDisabled.$isChecked.'/>';
										?>
									</td>
									<td>
										<!--Disabled-->
										<?php
										(($admin) ? $isDisabled = '' : $isDisabled = 'onClick="return false;" ');
										($_SESSION['values']['chk_isActive']==1 ? $isChecked = 'checked ' : $isChecked = '');
										echo '<label for="chk_Active">Compte est Activ&eacute;?</label>';
										echo '<input type="checkbox" name="chk_Active" id="chk_Active" value="1" '.$isDisabled.$isChecked.'/>';
										?>
									</td>
								</tr>
								<tr>
                                    <td>
										<!--Validator-->
										<?php
										(($admin) ? $isDisabled = '' : $isDisabled = 'onClick="return false;" ');
										($_SESSION['values']['chk_isValidator']==1 ? $isChecked = 'checked ' : $isChecked = '');
										echo '<label for="chk_Validator">Valideur?</label>';
										echo '<input type="checkbox" name="chk_Validator" id="chk_Validator" value="1" '.$isDisabled.$isChecked.'/>';
										?>
									</td>
									<td class="right">
										<!--Reset-->
										<input class="submit" type="button" onClick="window.location='<?php echo $_SERVER['PHP_SELF'] ?>?reset=1';" value="Annuler" name="raz"<?php echo $readonly ?>/>
										<!--Submit-->
										<input class="submit" type="submit" onClick="return confirmpwd(document.getElementById('txt_Password2').value);" value="Valider" name="submit" />
									</td>
								</tr>
							</tbody>
						</table>
					</form>
					<p class="blue legend">Champs Bloqu&eacute;s</p><p class="legend"><span class="red">*</span> Champs Obligatoires</p>
				</td>
			</tr>
		</table>
	</body>
</html>

