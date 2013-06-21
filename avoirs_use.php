<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('checksession.php');

if (!empty($_SESSION['client'])) {
		$arCompte = getCompteDisplay();
		$arCompte = preg_split("/,/", $arCompte);
	}
	
	
$idavoir = $_GET["idavoir"];
$avoir = $_GET["avoir"];
$cUser = unserialize($_SESSION['user']);

############functions###############
function buildFacturesEnCoursTable($id){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT * FROM `factures_cantine` WHERE `acceptation` = '1' AND `reglement` = '0' AND `avoir` = '0' AND `idclient` = $id ORDER BY `idfacture` DESC LIMIT 10";
	//echo $query;
        $result = $mysqli->query($query);
        $list = "<select id='slt_facture' name='slt_facture'>";
        
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $list .= "<option value='CANT".$row['idfacture']."'>Facture cantine ".$row['communeid']." du ".reverse_date_to_normal($row['datefacture']).", montant de ".$row['montantfcp']." FCP (".$row['montanteuro']."&euro;)</option>";
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

		function apply_avoir(){
                    if($("#txt_montant").val()=="" || $("#obs").val()=="" || $("#slt_facture").val()==null){
                            alert("Pas d'application possible.");	
                    }else{
                    var avoirid = $("#idavoir").val();
                    var facturecode = $("#slt_facture").val();
                    var montant = <?php echo $avoir ?>;
                    var client = <?php echo $arCompte[1] ?>;
                    
                    $.post("avoir_apply.php",{avoirid:avoirid,facturecode:facturecode,montant:montant},
                           function(data){
                                    window.location = "clients.php?hideerrors=1&success=1&edit="+client;
                           });
                    
                    }
		}
	    
	</script>

	</head>
	<body>
		<div id="message" ></div>
		<h1>Module d'application d'un avoir</h1>
		
		<form id="form_avoir" name="form_avoir">
                Avoir d'un montant de <b><?php echo $avoir ?> FCP</b><br /><br />
                Appliquer l&apos;avoir &agrave; la facture :
                <?php 
		    echo buildFacturesEnCoursTable($arCompte[1]);
		?>
		<br /><br />
		<input type="hidden" id="idavoir" name="idavoir" value="<?php echo $idavoir; ?>" />
		<input type="reset" onclick="div_avoir_close();" value="Annuler" /> <button onclick="apply_avoir();">Valider</button>
		</form>
                
	</body>
</html>

