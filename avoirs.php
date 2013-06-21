<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('checksession.php');

if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}
	
	
$cUser = unserialize($_SESSION['user']);


############functions###############
function buildFacturesPayeesTable($id){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `".DB."`.`factures_cantine` WHERE `factures_cantine`.`reglement` = '1' AND `factures_cantine`.`idclient` = $id ORDER BY `idfacture` DESC LIMIT 10";
	//echo $query;
        $result = $mysqli->query($query);
        $list = "<select id='slt_facture' name='slt_facture'>";
        
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $list .= "<option value='CANT".$row['idfacture']."'>Facture cantine ".$row['communeid']." du ".reverse_date_to_normal($row['datefacture']).", règlement de ".$row['montantfcp']." FCP (".$row['montanteuro']."&euro;) le ".reverse_date_to_normal($row['datereglement'])."</option>";
        }
        
        $list .= "</select>";
        $mysqli->close();
        return $list;
}

?>
<!DOCTYPE html>

<html lang="fr">
	<head>

	<script type="text/javascript">
		$(document).ready(function() {
                //////key checks//////
				
                    $('#txt_montant').keypress(function(event) {
                            if(event.which=='0'||event.which=='8') return true;
                            return /^[0-9]+$/.test(String.fromCharCode(event.which));
                    });
                    
                });
                
                $("#form_avoir").submit(function(){
                            return false;
                    })

		function submit_avoir(){
		if($("#txt_montant").val()=="" || $("#obs").val()=="" || $("#slt_facture").val()==null){
			alert("Veuillez compl\351ter le formulaire.");	
		}else{
		var userid = $("#cuser").val();
		var facturecode = $("#slt_facture").val();
		var montant = $("#txt_montant").val();
		var obs = $("#obs").val();
                var client = <?php echo $arCompte[1] ?>;
		      
		$.post("avoir_submit.php",{userid:userid,facturecode:facturecode,montant:montant,obs:obs,client:client},
		       function(data){
				window.location = "clients.php?hideerrors=1&success=1&edit="+client;		
				});
		}
		}
	    
	</script>

	</head>
	<body>
		<div id="message" ></div>
		<h1>Module de demande d'avoir</h1>
		
		<form id="form_avoir" name="form_avoir">
                <input type="hidden" id="cuser" name="cuser" value="<? print $cUser->userid(); ?>" />
                Avoir pour le client <b><?php echo $arCompte[0] ?></b><br /><br />
                Lier l&apos;avoir &agrave; la facture :
                <?php 
		    echo buildFacturesPayeesTable($arCompte[1]);
		?><br /><br />
                Montant de l&apos;avoir en FCP: <input type='text' maxlength='6' id='txt_montant' name='txt_montant' size='8' style='text-align:right;'/><br /><br />
                Observations : <textarea id="obs" name="obs" style="resize:none;width:300px;vertical-align:middle;"></textarea><br /><br />
		<br />
		<input type="reset" onclick="div_avoir_close();" value="Annuler" /> <button onclick="submit_avoir();">Soumettre pour validation</button>
		</form>
                
	</body>
</html>

