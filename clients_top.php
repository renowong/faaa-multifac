<?php
require_once('checksession.php');

//###############################variables######################################

$civiliteOptions = array("0" => "[S&eacute;lectionner]",
						 "Mr" => "Monsieur",
						 "Mme" => "Madame",
						 "Mlle" => "Mademoiselle");


//###############################procedures#####################################
if (!isset($_SESSION['values']) || (isset($_GET['reset']) && $_GET['reset']==1)){resetvalues();reseterrors();}

//if (!isset($_SESSION['errors']) || (isset($_GET['reset']) && $_GET['reset']==1)){reseterrors();}


if (isset($_GET['edit']) && $_GET['edit'] > 0) {
	getData($_GET['edit']); //set client data into session variables (just for the form)
	setPersistData($_GET['edit']); //set client data into persistent variable (XML)
	$legend = "Edition du compte de ".$_SESSION['values']['box_Civilite']." ".strtoupper($_SESSION['values']['txt_Nom'])." ".strtoupper($_SESSION['values']['txt_Prenom']);
} else {
	$legend = "Cr&eacute;ation d'un compte client";
	$_SESSION['values']['chk_status'] = '1';
}

if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}



$facture_en_cours = getAllFactures($_GET['edit']);
$kids_list = getAllKids($_GET['edit']);

if($facture_en_cours>0){
	$actiflock = "style='visibility:hidden;'";
	$totalfactures = $facture_en_cours." facture(s) en cours. <a href='javascript:reaffectfactures();'><img src='img/reaffect.png' alt='reaffecter' style='vertical-align:middle;' title='Cliquez pour transf&eacute;rer les factures &agrave; un autre compte' /></a>";
}elseif($kids_list>0){
	$actiflock = "style='visibility:hidden;'";
}

//if ($_GET['resetkid']=='1') {resetvalues();resetenfantvalues();}

//#################################functions#####################################

function setPersistData($id){
	$_SESSION['client'] = '<?xml version="1.0" encoding="windows-1252" standalone="yes"?>'.
	'<compte>'.
	'<type>client</type>'.
	'<clientid>'.$id.'</clientid>'.
	'<status>'.$_SESSION['values']['chk_status'].'</status>'.
	'<civilite>'.$_SESSION['values']['box_Civilite'].'</civilite>'.
	'<nom>'.$_SESSION['values']['txt_Nom'].'</nom>'.
	'<nommarital>'.$_SESSION['values']['txt_NomMarital'].'</nommarital>'.
	'<prenom>'.$_SESSION['values']['txt_Prenom'].'</prenom>'.
	'<prenom2>'.$_SESSION['values']['txt_Prenom2'].'</prenom2>'.
	'<datenaissance>'.$_SESSION['values']['txt_DateNaissance'].'</datenaissance>'.
	'<lieunaissance>'.$_SESSION['values']['txt_LieuNaissance'].'</lieunaissance>'.
	'<idtresor>'.$_SESSION['values']['txt_IDTresor'].'</idtresor>'.
	'<email>'.$_SESSION['values']['txt_Email'].'</email>'.
	'<cps>'.$_SESSION['values']['txt_CPS'].'</cps>'.
	'<telephone>'.$_SESSION['values']['txt_Telephone'].'</telephone>'.
	'<telephone2></telephone2>'.
	'<fax>'.$_SESSION['values']['txt_Fax'].'</fax>'.
	'<bp>'.$_SESSION['values']['txt_BP'].'</bp>'.
	'<cp>'.$_SESSION['values']['txt_CP'].'</cp>'.
	'<ville>'.$_SESSION['values']['txt_Ville'].'</ville>'.
	'<commune>'.$_SESSION['values']['txt_Commune'].'</commune>'.
	'<pays>'.$_SESSION['values']['txt_Pays'].'</pays>'.
	'<aroa>'.$_SESSION['values']['txt_Aroa'].'</aroa>'.
	'<quartier>'.$_SESSION['values']['txt_Quartier'].'</quartier>'.
	'<rib>'.$_SESSION['values']['txt_RIB'].'</rib>'.
	'<obs>'.$_SESSION['values']['txt_obs'].'</obs>'.
	'</compte>';
}

function buildOptions($options, $selectedOption) {
	foreach ($options as $value => $text) {
		if ($value === $selectedOption) {
			echo '<option value="' . $value . '" selected="selected">' . $text . '</option>';
		} else {
			echo '<option value="' . $value . '">' . $text . '</option>';
		}
	}
}

function getData($id){
	$Mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`clients` WHERE `clients`.`clientid` = " . $id;

	$result = $Mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$_SESSION['values']['chk_status'] = $row['clientstatus'];
		$_SESSION['values']['box_Civilite'] = $row['clientcivilite'];
		$_SESSION['values']['txt_Nom'] = $row['clientnom'];
		$_SESSION['values']['txt_NomMarital'] = $row['clientnommarital'];
		$_SESSION['values']['txt_Prenom'] = $row['clientprenom'];
		$_SESSION['values']['txt_Prenom2'] = $row['clientprenom2'];
		$_SESSION['values']['txt_DateNaissance'] = date("d/m/Y", strtotime($row['clientdatenaissance']));
		$_SESSION['values']['txt_LieuNaissance'] = $row['clientlieunaissance'];
		$_SESSION['values']['txt_IDTresor'] = $row['clientidtresor'];
		$_SESSION['values']['txt_Email'] = $row['clientemail'];
		if($row['clientcps']==0) $row['clientcps']="";
		$_SESSION['values']['txt_CPS'] = $row['clientcps'];
		$_SESSION['values']['txt_Telephone'] = wordwrap($row['clienttelephone'],2,"-",true);
		$_SESSION['values']['txt_Fax'] = wordwrap($row['clientfax'],2,"-",true);
		$_SESSION['values']['txt_BP'] = $row['clientbp'];
		$_SESSION['values']['txt_CP'] = $row['clientcp'];
		$_SESSION['values']['txt_Ville'] = $row['clientville'];
		$_SESSION['values']['txt_Commune'] = $row['clientcommune'];
		$_SESSION['values']['txt_Pays'] = $row['clientpays'];
		$_SESSION['values']['txt_Aroa'] = $row['aroa'];
		$_SESSION['values']['txt_Quartier'] = $row['quartier'];
		$_SESSION['values']['txt_RIB'] = $row['clientrib'];
		$_SESSION['values']['txt_obs'] = $row['obs'];
	}
	if ($_GET['hideerrors']) reseterrors();
	$Mysqli->close();
}

function fixphone($n){
	return wordwrap($n,2,"-",true);
}


function reseterrors(){
	$_SESSION['errors']['Civilite'] = 'hidden';
	$_SESSION['errors']['Nom'] = 'hidden';
	$_SESSION['errors']['NomMarital'] = 'hidden';
	$_SESSION['errors']['Prenom'] = 'hidden';
	$_SESSION['errors']['Prenom2'] = 'hidden';
	$_SESSION['errors']['DateNaissance'] = 'hidden';
	$_SESSION['errors']['LieuNaissance'] = 'hidden';
	$_SESSION['errors']['IDTresor'] = 'hidden';
	$_SESSION['errors']['Email'] = 'hidden';
	$_SESSION['errors']['CPS'] = 'hidden';
	$_SESSION['errors']['Telephone'] = 'hidden';
	$_SESSION['errors']['Fax'] = 'hidden';
	$_SESSION['errors']['BP'] = 'hidden';
	$_SESSION['errors']['CP'] = 'hidden';
	$_SESSION['errors']['Ville'] = 'hidden';
	$_SESSION['errors']['Commune'] = 'hidden';
	$_SESSION['errors']['Pays'] = 'hidden';
	$_SESSION['errors']['Aroa'] = 'hidden';
	$_SESSION['errors']['Quartier'] = 'hidden';
	$_SESSION['errors']['RIB'] = 'hidden';
	$_SESSION['errors']['enfantNom'] = 'hidden';
	$_SESSION['errors']['enfantPrenom'] = 'hidden';
	$_SESSION['errors']['enfantDN'] = 'hidden';
	$_SESSION['errors']['enfantCPS'] = 'hidden';
	
}

function resetvalues(){
		$_SESSION['values']['chk_status'] = '1';
		$_SESSION['values']['box_Civilite'] = '';
		$_SESSION['values']['txt_Nom'] = '';
		$_SESSION['values']['txt_NomMarital'] = '';
		$_SESSION['values']['txt_Prenom'] = '';
		$_SESSION['values']['txt_Prenom2'] = '';
		$_SESSION['values']['txt_DateNaissance'] = '';
		$_SESSION['values']['txt_LieuNaissance'] = '';
		$_SESSION['values']['txt_IDTresor'] = '';
		$_SESSION['values']['txt_Email'] = '';
		$_SESSION['values']['txt_CPS'] = '';
		$_SESSION['values']['txt_Telephone'] = '';
		$_SESSION['values']['txt_Fax'] = '';
		$_SESSION['values']['txt_BP'] = '';
		$_SESSION['values']['txt_CP'] = '';
		$_SESSION['values']['txt_Ville'] = '';
		$_SESSION['values']['txt_Commune'] = '';
		$_SESSION['values']['txt_Pays'] = '';
		$_SESSION['values']['txt_Aroa'] = '';
		$_SESSION['values']['txt_Quartier'] = '';
		$_SESSION['values']['txt_RIB'] = '';
		$_SESSION['values']['txt_obs'] = '';
		$_SESSION['values']['txt_enfantNom'] = '';
		$_SESSION['values']['txt_enfantPrenom'] = '';
		$_SESSION['values']['txt_enfantDN'] = '';
		$_SESSION['values']['txt_enfantCPS'] = '';
		$_SESSION['values']['slt_enfantStatus'] = '';
		$_SESSION['values']['slt_enfantEcole'] = '';
		$_SESSION['values']['slt_enfantClasse'] = '';
		$_SESSION['values']['txt_enfant_entree'] = '';
		$_SESSION['values']['txt_enfant_sortie'] = '';
		$_SESSION['values']['slt_enfantSexe'] = '';
		$_SESSION['values']['hid_enfantid'] = '';
		//$_SESSION['values']['chk_enfantDest'] = '';
}

function getLieu($type){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD);

	$query = "SELECT `".DB."`.`lieux`.`lieuid`, `".DB."`.`lieux`.`lieustatus`, `".DB."`.`lieux`.`lieuprincipal`, `".DB."`.`tarifs_ramassage_om`.`Type`,".
			 " `".DBRUES."`.`rues`.`Rue`, `".DBRUES.
			 "`.`quartiers`.`Quartier` FROM `".DB."`.`lieux` INNER JOIN `".DBRUES.
			 "`.`rues` ON `lieux`.`lieuservitude` = `rues`.`IDRue` ".
			 "INNER JOIN `".DBRUES."`.`quartiers` ON `lieux`.`lieuquartier` = `quartiers`.`IDQuartier` ".
			 "INNER JOIN `".DB."`.`tarifs_ramassage_om` ON `lieux`.`lieucategorie` = `tarifs_ramassage_om`.`IDtarif` ".
			 "WHERE `lieux`.`$type` = ". $_GET['edit']." ORDER BY `".DB."`.`lieux`.`lieustatus` DESC";

	$output = ''; //defining the variable
	$result = $mysqli->query($query);

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		($row['lieustatus']==0 ? $class="class='crossed'" : $class="");
		($row['lieuprincipal']==1 ? $home="<img src='img/home.png' alt='home' title='R&eacute;sidence principale' /> " : $home="");
		$output .= $home."<a $class href='lieux.php?edit=".$row['lieuid']."&hideerrors=1'>".$row['Type']." &agrave; ".$row['Rue']." ".$row['Quartier']."</a><br/><br/>";
	}
	
	if($output=="") $output="<a href='lieux.php?reset=1'>Ajouter une r&eacute;sidence</a>";

	$mysqli->close();
	return $output;
}


function buildAnnexesTable(){
if (isset($_GET['edit']) && $_GET['edit'] > 0) {
	$output = '<td><div id="divconjoint"></div>'.
		'<form id="frmkids" name="frmkids" method="post" action="kids_validate.php?validationType=php">'.
		'<table id="tblenfants" class="tblform">'.
		'	<thead>'.
		'		<tr>'.
		'			<th colspan="2">Enfants</th>'.
		'		</tr>'.
		'	</thead>'.
		'	<tbody>'.
		'		<tr>'.
		'			<td colspan=\'2\'><input type="checkbox" name="chk_actif_enfant" id="chk_actif_enfant" checked /> <label>Actif</label></td>'.
		'		</tr>'.	
		'		<tr>'.
		'			<td><label>Nom</label><span class="red">*</span><br /><input type="text" size="20" maxlength="25" name="txt_nom_enfant" id="txt_nom_enfant" class="uppercase" onBlur="enf_validate(this.value, this.id);" value="'.$_SESSION['values']['txt_enfantNom'].'" /><br/><span id="txt_nom_enfantFailed" class="'.$_SESSION['errors']['enfantNom'].' red">Veuillez entrer un nom valide.</span></td>'.
		'			<td><label>Pr&eacute;nom</label><span class="red">*</span><br /><input type="text" size="20" maxlength="25" name="txt_prenom_enfant" id="txt_prenom_enfant" class="uppercase" onBlur="enf_validate(this.value, this.id);" value="'.$_SESSION['values']['txt_enfantPrenom'].'" /><br/><span id="txt_prenom_enfantFailed" class="'.$_SESSION['errors']['enfantPrenom'].' red">Veuillez entrer un pr&eacute;nom valide.</span></td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td><label>Date de Naissance</label><span class="red">*</span><br /><input type="text" size="10" maxlength="10" name="txt_dn_enfant" id="txt_dn_enfant" value="'.$_SESSION['values']['txt_enfantDN'].'" readonly /><br/><span id="txt_dn_enfantFailed" class="'.$_SESSION['errors']['enfantDN'].' red">Veuillez entrer une date valide.</span></td>'.
		'			<td><label>Sexe</label><span class="red">*</span><br /><select name="slt_sexe_enfant" id="slt_sexe_enfant" onChange="enf_validate(this.value, this.id);"><option value="M">M</option><option value="F">F</option></select></td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td><label>DN CPS</label><span class="red">*</span><br /><input type="text" size="7" maxlength="7" name="txt_cps_enfant" id="txt_cps_enfant" onBlur="enf_validate(this.value, this.id);" value="'.$_SESSION['values']['txt_enfantCPS'].'" /><br/><span id="txt_cps_enfantFailed" class="'.$_SESSION['errors']['enfantCPS'].' red">Veuillez entrer un num&eacute;ro CPS valide.</span></td>'.
		'			<td><label>Status</label><span class="red">*</span><br /><select name="slt_status_enfant" id="slt_status_enfant" onChange="switch_periode(this.value);">'.buildStatusCantine($_SESSION['values']['slt_enfantStatus']).'</select>'.
		'			<br/><select name="slt_status_periode" id="slt_status_periode">'.buildStatusPeriode($_SESSION['values']['slt_enfantPeriode']).'</select></td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td><label>Ecole</label><span class="red">*</span><br /><select name="slt_ecole_enfant" id="slt_ecole_enfant" onchange="load_classes(this.value,\'slt_classe_enfant\');enf_validate(this.value, this.id);">'.buildOptionsSchools($_SESSION['values']['slt_enfantEcole']).'</select></td>'.
		'			<td><label>Classe</label><span class="red">*</span><br /><select name="slt_classe_enfant" id="slt_classe_enfant" onChange="enf_validate(this.value, this.id);"></select></td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td><label>Date d\'entr&eacute;e</label><br /><input type="text" size="10" maxlength="10" name="txt_entree_enfant" id="txt_entree_enfant" value="'.$_SESSION['values']['txt_enfant_entree'].'" readonly /> <a href="javascript:reset_date(\'txt_entree_enfant\');"><img src="img/close.png" alt="close" style="vertical-align:middle;width:16px;height:16px;" /></a></td>'.
		'			<td><label>Date de sortie</label><br /><input type="text" size="10" maxlength="10" name="txt_sortie_enfant" id="txt_sortie_enfant" value="'.$_SESSION['values']['txt_enfant_sortie'].'" readonly /> <a href="javascript:reset_date(\'txt_sortie_enfant\');"><img src="img/close.png" alt="close" style="vertical-align:middle;width:16px;height:16px;" /></a></td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td colspan="2" style="text-align:right;"><input type="hidden" id="id_client_enfant" name="id_client_enfant" value="'.$_GET['edit'].'" /><input type="hidden" id="id_enfant" name="id_enfant" value="'.$_SESSION['values']['hid_enfantid'].'" />'.
		'			<button class="submit" type="reset" id="reset_enfant" name="reset_enfant" onclick="javascript:$(\'#btn_add_enfant\').button(\'option\',\'label\',\'Ajouter\');resetenfant();">RAZ</button> <button type="submit" class="submit" name="btn_add_enfant" id="btn_add_enfant">Ajouter</button></td>'.
		'		</tr>'.			
		'		<tr>'.
		'			<td colspan="2"><label>Liste des enfants</label>';
		if(getAllKids($_GET['edit'])>0) $output .='<a href="javascript:reaffectkid();"><img alt="reaffectation" style="vertical-align:middle;" src="img/reaffect.png" title="Cliquez pour une r&eacute;affection vers un autre parent"/></a></td>';
		$output .= '		</tr>'.
		'		<tr>'.
		'			<td colspan="2"><div id="list_enfants"></div></td>'.
		'		</tr>'.
		'	</tbody>'.
		'</table></form>'.
		'<table id="tbllieu" class="tblform" style="width:100%;">'.
		'	<thead>'.
		'		<tr>'.
		'			<th>Lieux</th>'.
		'		</tr>'.
		'	</thead>'.
		'	<tbody>'.
		'		<tr>'.
		'			<td><label>Propri&eacute;taire de</label></td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td>'.getLieu("lieuproprietaire").'</td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td><label>Locataire de</label></td>'.
		'		</tr>'.
		'		<tr>'.
		'			<td>'.getLieu("lieulocataire").'</td>'.
		'		</tr>'.
		'	</tbody>'.
		'</table>'.
		'</td>';
	return $output;
	}
}

function buildOptionsSchools($s){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT * FROM `'.DB.'`.`ecoles_faaa` ORDER BY nomecole';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if($row["ecoleid"]==$s){$select=" selected";}else{$select="";}
		$list .= "<option value='".$row["ecoleid"]."'$select>".$row["nomecole"]."</option>";
	}
	$mysqli->close();
	return $list;
}

function buildOptionsClasses(){
	$newarray = array();
	$returnarray = array();
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT `idecole`,`classe` FROM `'.DB.'`.`classes` ORDER BY `classe`';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$newarray[$row['idecole']] = $newarray[$row['idecole']].$row['classe'].',';
	}
	$mysqli->close();
	foreach($newarray as $key => $value){
		$returnarray[$key] = substr($newarray[$key],0,-1);
	}
	return $returnarray;
}

function buildStatusCantine($s){
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = 'SELECT * FROM `status_cantine` WHERE (`idstatus` BETWEEN 1 AND 7) OR (`idstatus` BETWEEN 15 AND 21)';
	$result = $mysqli->query($query);
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if($row["idstatus"]==$s){$select=" selected";}else{$select="";}
		$list .= "<option value='".$row["idstatus"]."'$select>".$row["status"]."</option>";
	}
	$mysqli->close();
	return $list;
}

function buildStatusPeriode($s){
	$thismonth = date("n");
	if($thismonth>3){$nextyear2periode=1;}else{$nextyear2periode=0;}
	if($thismonth>6){$nextyear3periode=1;}else{$nextyear3periode=0;}

	$ar_periode = array("Expir\351","P&eacute;riode : ao&ucirc;t &agrave; d&eacute;cembre ".date("Y"),"P&eacute;riode : janv &agrave; mars ".(date("Y")+$nextyear2periode)," P&eacute;riode : avril &agrave; juin ".(date("Y")+$nextyear3periode));
	for($i=0;$i<count($ar_periode);$i++){
		if($s==$i){$select=" selected";}else{$select="";}
		$list .= "<option value='".$i."'$select>".$ar_periode[$i]."</option>";
	}
	return $list;
}

function buildFacturesEnCoursTable($id,$ar_tables){
//print_r($ar_tables);
    
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$output = "<table class=\"tblform\"><tbody><tr><th>Type de Facture</th><th>Facture</th><th>PDF</th><th>Status</th><th>Commentaire</th></tr></tbody>";

foreach( $ar_tables as &$val ){

	$query = "SELECT * FROM `".$val['table']."` WHERE `idclient` = '$id' ORDER BY datefacture DESC, idfacture DESC limit 30";

        
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
		//$comment = $row["comment"];
		if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
		$comment = str_replace(" ; ","<br/>",$row["comment"]);
		if($row["validation"]==0) {$status="En cours de validation";$reject="";
		}else{
			if($row["acceptation"]==0){$status="Refus&eacute;e";$reject="reject";}else{$status="Valid&eacute;e";$reject="";}
		}
                if($val['title']=="cantine"){
				$output .= "<tbody class=\"$reject\"><tr><td>".$val['title']."<br/>".getEnfantPrenom($row['idfacture'])."</td><td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
			}else{
				$output .= "<tbody class=\"$reject\"><tr><td>".$val['title']."</td><td>Facture ".$row["communeid"]." du ".french_date($row["datefacture"])." montant de ";
				}
		$output .= trispace($row["montantfcp"]);
		$output .= " FCP (soit ".$row["montanteuro"]." &euro;)<br/>Obs : ".$row["obs"]."</td><td><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=".$val['link']."\" target=\"_blank\">$pdf</a></td><td>$status</td><td>$comment</td></tr></tbody>";
        }
    }  

	$output .= "</table>";
    $mysqli->close();
    return $output;

}

function buildAvoirsTable($id){
    
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$output = "<table class=\"tblform\"><tbody><tr><th>Montant</th><th>Reste</th><th>Status</th><th>Liaison</th><th>PDF</th>".
	"<th>Date</th><th>Obs</th><th>Valideur</th><th>Obs valideur</th><th>Action</th></tr></tbody>";
	
	$query = "SELECT `avoirs`.`idavoir`,`avoirs`.`montant`,`avoirs`.`reste`,`avoirs`.`validation`,`avoirs`.`acceptation`,`avoirs`.`idfacture`,`avoirs`.`date`,".
	"`avoirs`.`obs`,`avoirs`.`obs_valideur`,`factures_cantine`.`datefacture`,`factures_cantine`.`datefacture`,".
	"`factures_cantine`.`montantfcp`,`factures_cantine`.`montanteuro`,`factures_cantine`.`duplicata` FROM `avoirs` INNER JOIN `factures_cantine`".
	"ON `avoirs`.`idfacture`=`factures_cantine`.`idfacture` ".
	"WHERE `avoirs`.`idclient` = '$id' AND `avoirs`.`validation` = '0' ORDER BY date DESC, `idavoir` DESC limit 10";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
		$status="En cours de validation";
                $output .= "<tbody><td>".trispace($row['montant'])." FCP</td>";
		$output .= "<td>".trispace($row["reste"])." FCP</td><td>$status</td>";
		$output .= "<td>Facture du ".french_date($row["datefacture"])." - ";
		$output .= trispace($row["montantfcp"]);
		$output .= " FCP (soit ".$row["montanteuro"]." &euro;)</td><td><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=cantine\" target=\"_blank\">$pdf</a></td><td>".french_date($row["date"])."</td>";
		$output .= "<td>".$row["obs"]."</td><td>".$row["userlogin"]."</td><td>".$row["obs_valideur"]."</td><td></td></tbody>";
        }
	
	$query = "SELECT `avoirs`.`idavoir`,`avoirs`.`montant`,`avoirs`.`reste`,`avoirs`.`validation`,`avoirs`.`acceptation`,`avoirs`.`idfacture`,`avoirs`.`date`,".
	"`avoirs`.`obs`,`avoirs`.`obs_valideur`,`factures_cantine`.`datefacture`,`factures_cantine`.`datefacture`,".
	"`factures_cantine`.`montantfcp`,`factures_cantine`.`montanteuro`, `user`.`userlogin` FROM `avoirs` INNER JOIN `factures_cantine`".
	"ON `avoirs`.`idfacture`=`factures_cantine`.`idfacture` RIGHT JOIN `user` ON `avoirs`.`valideur_id`= `user`.`userid` ".
	"WHERE `avoirs`.`idclient` = '$id' ORDER BY date DESC, `idavoir` DESC limit 10";
        $result = $mysqli->query($query);
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
		if($row["duplicata"]=='0'){$pdf="<img src=\"img/opdf.png\" alt=\"original\" class=\"ico\">";}else{$pdf="<img src=\"img/dpdf.png\" alt=\"duplicata\" class=\"ico\">";}
		if($row["acceptation"]=='0'){$status="Refus&eacute;e";}else{$status="Valid&eacute;e";$reject="";}
		if($row["reste"]>'0' && $row["acceptation"]=='1'){$use="<button onclick=\"div_avoir('avoirs_use.php?idavoir=".$row["idavoir"]."&avoir=".$row["reste"]."');\">Utiliser</button>";}else{$use='';}
                $output .= "<tbody><td>".trispace($row['montant'])." FCP</td>";
		$output .= "<td>".trispace($row["reste"])." FCP</td><td>$status</td>";
		$output .= "<td>Facture du ".french_date($row["datefacture"])." - ";
		$output .= trispace($row["montantfcp"]);
		$output .= " FCP (soit ".$row["montanteuro"]." &euro;)</td><td><a href=\"createpdf.php?idfacture=".$row['idfacture']."&type=cantine\" target=\"_blank\">$pdf</a></td><td>".french_date($row["date"])."</td>";
		$output .= "<td>".$row["obs"]."</td><td>".$row["userlogin"]."</td><td>".$row["obs_valideur"]."</td><td>$use</td></tbody>";
        }
	$output .= "</table>";
    $mysqli->close();
    return $output;

}

function getAllFactures($id){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`factures_cantine` WHERE `factures_cantine`.`reglement` = 0 AND `factures_cantine`.`acceptation` = 1 AND `factures_cantine`.`idclient` = '$id'";
	//echo $query;
        $result = $mysqli->query($query);
	$count = $result->num_rows;
        $mysqli->close();
        return $count;
}

function getAllKids($id){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`enfants` WHERE `clientid` = $id";
        $result = $mysqli->query($query);
	$count = $result->num_rows;
        $mysqli->close();
        return $count;
}

function getEnfantPrenom($idfacture){
    $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
    $query = "SELECT `enfants`.`prenom` FROM `factures_cantine_details` INNER JOIN `enfants` ".
            " ON `factures_cantine_details`.`idenfant`=`enfants`.`enfantid` WHERE `factures_cantine_details`.`idfacture`='$idfacture'";
    $result = $mysqli->query($query);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $output = $row["prenom"];
    $mysqli->close();
    return $output;
}
?>
