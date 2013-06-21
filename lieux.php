<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('lieux_top.php');


(isset($_GET['edit']) ? $edit="&edit=".$_GET['edit'] : $edit="");

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$graburljs.$compte_div.$lieuxjs.$jquery.$jqueryui.$message_div ?>
        <link rel="stylesheet" href="chosen/chosen.css" />
        <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
        
		<script type="text/javascript">
		$(document).ready(function() {
			//////jqueryui buttons/////
			$( "input:submit,input:button,button" ).button();
            $("#box_Proprietaire").chosen();
            $("#box_Locataire").chosen();
            $("#box_Mandataire").chosen();
            $("#box_Quartier").chosen();
            $("#box_Servitude").chosen();
		});
		function showSubmitResult(){
			var success = gup('success');
			var edit = gup('edit');
			if (success=='1') {
				if (edit>0) {
					message("Compte mis &agrave; jour avec succ&egrave;s");
				} else {
					message("Nouveau lieu ajout&eacute; avec succ&egrave;s");
				}
			} else if (success=='0') {
				if (edit>0) {
					message("Echec dans la mise &agrave; jour du compte");
				} else {
					message("Echec dans l'ajout du nouveau lieu");
				}
			}
		}

			function addOption(select){
				if (select==null) b=0;
				var objDropdown = document.getElementById('box_Facturer');
				var objProprietaire = document.getElementById('box_Proprietaire');
				var objMandataire = document.getElementById('box_Mandataire');
				var objLocataire = document.getElementById('box_Locataire');
				var indexProprietaire;
				var indexLocataire;
				var indexMandataire;
				var textProprietaire;
				var textMandataire;
				var textLocataire;

				indexProprietaire = objProprietaire.selectedIndex;
				indexMandataire = objMandataire.selectedIndex;
				indexLocataire = objLocataire.selectedIndex;

				textProprietaire = objProprietaire.options[indexProprietaire].text;
				textMandataire = objMandataire.options[indexMandataire].text;
				textLocataire = objLocataire.options[indexLocataire].text;

				objDropdown.length = 0;
				 var objOption = new Option("[S\xE9lectionner]",0);
				 objDropdown.options[objDropdown.length] = objOption;

				if (indexProprietaire > 0){
					var objOption = new Option(textProprietaire,objProprietaire.value);
					objDropdown.options[objDropdown.length] = objOption;
				}
				if (indexMandataire > 0){
					var objOption = new Option(textMandataire,objMandataire.value);
					objDropdown.options[objDropdown.length] = objOption;
				}
				if (indexLocataire > 0){
					var objOption = new Option(textLocataire,objLocataire.value);
					objDropdown.options[objDropdown.length] = objOption;
				}

				for (n=1;n<4;n=n+1) {
					if(objDropdown.options[n].value == select) objDropdown.selectedIndex = n;
				}
			}

			function init(){
				showSubmitResult();
				showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
				addOption(<?php echo $_SESSION['values']['txt_Facturer'] ?>);
			}
						
			function checksurface(){
				var box_cat;
				var txt_surf;
				var surf;
				box_cat = document.getElementById('box_Categorie');
				txt_surf = document.getElementById('txt_Surface');
				surf = txt_surf.value;
				if(box_cat.selectedIndex==2 && eval(surf>=100)){alert('Attention, la valeur entr\xE9e est sup\xE9rieure \xE0 la cat\xE9gorie. Celle-ci sera r\xE9affect\xE9e automatiquement');box_cat.selectedIndex=3;};
				if(box_cat.selectedIndex==3 && eval(surf<100)){alert('Attention, la valeur entr\xE9e est inf\xE9rieure \xE0 la cat\xE9gorie. Celle-ci sera r\xE9affect\xE9e automatiquement');box_cat.selectedIndex=2;};
				
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
		<table>
			<tr>
				<td>
					<form name="frmRegistration" id="frmRegistration" action="lieux_validate.php?validationType=php<? echo $edit ?>" method="POST">
						<table id="tbllieu" class="tblform">
							<tbody>
								<tr>
									<td>
										<!--Status-->
										<label for="chk_status">Lieu Actif</label>
										<input class="input" type="checkbox" name="chk_status" id="chk_status" value="1" <? if($_SESSION['values']['chk_status']) echo 'checked="checked"'; ?> />
									</td>
									<td>
										<label for="chk_principal">R&eacute;sidence Principale</label>
										<input class="input" type="checkbox" name="chk_principal" id="chk_principal" value="1" <? if($_SESSION['values']['chk_principal']) echo 'checked="checked"'; ?> />
									</td>
								</tr>
								<tr>
									<td>
										<!--Proprietaire-->
										<label for="box_Proprietaire">Propri&eacute;taire<span class="red">*</span></label><br />
										<select name="box_Proprietaire" id="box_Proprietaire" onchange="validate(this.value, this.id);addOption();" data-placeholder="S&eacute;lectionner un propri&eacute;taire" class="chzn-select" tabindex="2">
											<option value=""></option>
											<?php buildOptionsPersonnes($_SESSION['values']['box_Proprietaire']) ?>
										</select><br/>
										<span id="box_ProprietaireFailed" class="<?php echo $_SESSION['errors']['Proprietaire'] ?> red">
											Veuillez s&eacute;lectionner un propri&eacute;taire.
										</span>
									</td>
									<td>
										<!--Mandataire-->
										<label for="box_Mandataire">Mandataire</label><br />
										<select class="input" name="box_Mandataire" id="box_Mandataire" onchange="addOption();" data-placeholder="S&eacute;lectionner un mandataire" class="chzn-select" tabindex="2">
											<option value=""></option>
                                            <option value="">Aucun</option>
                                            <?php buildOptionsMandataires($_SESSION['values']['box_Mandataire']) ?>
										</select>
									</td>
								</tr>
								<tr>
									<td>
										<!--Locataire-->
										<label for="txt_Locataire">Locataire</label><br />
										<select class="input" name="box_Locataire" id="box_Locataire" onchange="addOption();" data-placeholder="S&eacute;lectionner un locataire" class="chzn-select" tabindex="2">
											<option value=""></option>
                                            <option value="">Aucun</option>
                                            <?php buildOptionsPersonnes($_SESSION['values']['box_Locataire']) ?>
										</select>
									</td>
									<td>
										<!--Facturer-->
										<label for="box_Facturer">Facturer<span class="red">*</span></label><br />
										<select class="input" name="box_Facturer" id="box_Facturer" onchange="validate(this.value, this.id);">
										</select><br/>
										<span id="box_FacturerFailed" class="<?php echo $_SESSION['errors']['Facturer'] ?> red">
											Veuillez choisir une personne &agrave; facturer.
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Categorie-->
										<label for="box_Categorie">Cat&eacute;gorie<span class="red">*</span></label><br />
										<select name="box_Categorie" id="box_Categorie" onchange="validate(this.value, this.id);checksurface();">
											<?php buildOptionsCategories($_SESSION['values']['box_Categorie']) ?>
										</select><br/>
										<span id="box_CategorieFailed" class="<?php echo $_SESSION['errors']['Categorie'] ?> red">
											Veuillez choisir une cat&eacute;gorie.
										</span>
									</td>
									<td>
										<!--Nom du lieu-->
										<label for="txt_Nomlieu">Nom du Lieu</label><br />
										<input class="capitalize" type="text" name="txt_Nomlieu" id="txt_Nomlieu" value="<?php echo $_SESSION['values']['txt_Nomlieu'] ?>" size="20" maxlength="20" onBlur="validate(this.value, this.id);" />
										<span id="txt_NomlieuFailed" class="<?php echo $_SESSION['errors']['Nomlieu'] ?>">
											Veuillez entrer un nom de lieu valide (ex: Diva Nui).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Surface-->
										<label for="txt_Surface">Surface du local</label><br />
										<input class="lowercase" type="text" name="txt_Surface" id="txt_Surface" value="<?php echo $_SESSION['values']['txt_Surface'] ?>" size="20" maxlength="5" onBlur="validate(this.value, this.id);checksurface();" />
										<span id="txt_SurfaceFailed" class="<?php echo $_SESSION['errors']['Surface'] ?>">
											Veuillez entrer une surface valide (ex: 50).
										</span>
									</td>
									<td>
										<!--N. Maison-->
										<label for="txt_Nmaison">Num&eacute;ro</label><br />
										<input type="text" name="txt_Nmaison" id="txt_Nmaison" value="<?php echo $_SESSION['values']['txt_Nmaison'] ?>" size="20" maxlength="5" onBlur="validate(this.value, this.id);" />
										<span id="txt_NmaisonFailed" class="<?php echo $_SESSION['errors']['Nmaison'] ?>">
											Veuillez entrer un num&eacute;ro valide (ex: 80).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Nom Servitude-->
										<label for="box_Servitude">Servitude<span class="red">*</span></label><br />
										<select name="box_Servitude" id="box_Servitude" onchange="validate(this.value, this.id);" data-placeholder="S&eacute;lectionner une servitude" class="chzn-select" tabindex="2">
											<option value=""></option>
                                            <?php buildOptionsServitudes($_SESSION['values']['box_Servitude']) ?>
										</select><br/>
										<span id="box_ServitudeFailed" class="<?php echo $_SESSION['errors']['Servitude'] ?> red">
											Veuillez entrer un nom de servitude valide (ex: Pamatai).
										</span>
									</td>
									<td>
										<!--Quartier-->
										<label for="box_Quartier">Quartier<span class="red">*</span></label><br />
										<select name="box_Quartier" id="box_Quartier" onchange="validate(this.value, this.id);" data-placeholder="S&eacute;lectionner un quartier" class="chzn-select" tabindex="2">
											<option value=""></option>
                                            <?php buildOptionsQuartiers($_SESSION['values']['box_Quartier']) ?>
										</select><br/>
                                        <span id="box_QuartierFailed" class="<?php echo $_SESSION['errors']['Quartier'] ?> red">
											Veuillez choisir un quartier (ex: Tavararo).
										</span>
									</td>
								</tr>
								<tr>
									<td>
										<!--Compteur-->
										<label for="txt_Compteur">N. Compteur d'eau</label><br />
										<input class="uppercase" type="text" name="txt_Compteur" id="txt_Compteur" value="<?php echo stripslashes($_SESSION['values']['txt_Compteur']) ?>" size="20" maxlength="20" />
									</td>
									<td>
										<!--EDT-->
										<label for="txt_EDT">N. Compteur EDT</label><br />
										<input type="text" name="txt_EDT" id="txt_EDT" value="<?php echo $_SESSION['values']['txt_EDT'] ?>" size="20" maxlength="20" />
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<!--Observations-->
										<label for="txt_Observations">Observations</label><br />
										<textarea name="txt_Observations" id="txt_Observations" rows="4" cols="60"><?php echo $_SESSION['values']['txt_Observations'] ?></textarea>
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
			</tr>
		</table>
	</body>
</html>
