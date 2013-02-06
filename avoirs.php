<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('checksession.php');

if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}
	
	
$cUser = unserialize($_SESSION['user']);
$admin = $cUser->userisadmin();

############functions###############
function buildFacturesPayeesTable($id){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`factures_cantine` WHERE `factures_cantine`.`reglement` = '1' AND `factures_cantine`.`idclient` = $id ORDER BY `idfacture` DESC LIMIT 5";
	//echo $query;
        $result = $mysqli->query($query);
        $list = "<select id='slt_facture' name='slt_facture'>";
        
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $list .= "<option value='CANT".$row['idfacture']."'>Facture cantine ".$row['communeid']." du ".reverse_date_to_normal($row['datefacture']).", règlement de ".$row['montantfcp']." FCP (".$row['montanteuro']."euros) le ".reverse_date_to_normal($row['datereglement'])."</option>";
        }
        
        $list .= "</select>";
        $mysqli->close();
        return $list;
}



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
	<?php echo $jquery.$jqueryui.$compte_div ?>
	<script type="text/javascript">
		$(document).ready(function() {

                    
                });
                
                $("#form_avoir").submit(function(){
                        if($("#txt_montant").val()=="" || $("#obs").val()==""){
                            alert("Veuillez compl\351ter le formulaire.");
                            return false;
                        }else{
                            return true;
                        }
                    })

	</script>

	</head>
	<body>
		<div name="message" id="message" ></div>
		<h1>Module de demande d'avoir</h1>
		
		<form id="form_avoir" name="form_avoir" action="avoir_upload.php" method="post">
                Avoir pour le client <b><?php echo $arCompte[0] ?></b><br /><br />
                Lier l&apos;avoir &agrave; la facture :
                <?php 
		    echo buildFacturesPayeesTable($arCompte[1]);
		?><br /><br />
                Montant de l&apos;avoir en FCP: <input type='text' maxlength='6' id='txt_montant' name='txt_montant' size='8' style='text-align:right;'/><br /><br />
                Observations : <textarea id="obs" name="obs" style="resize:none;width:300px;vertical-align:middle;"></textarea><br /><br />
		<br />
		<input type="reset" onclick="div_avoir_close();" value="Annuler" /> <input type="submit" name="submit" value="Soumettre pour validation" />
		</form>
                
	</body>
</html>

