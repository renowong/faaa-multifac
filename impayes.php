<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('impayes_top.php');


$cUser = unserialize($_SESSION['user']);
$login = $cUser->userlogin();
//print $login;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$compte_div.$jquery.$jqueryui.$message_div.$graburljs ?>
	
	<link rel="stylesheet" href="chosen/chosen.css" />
	<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){

	$("#list_validation").load("impayes_list.php?type=all&range=0");
	$( "#dialog-form" ).hide();
	
	$("#box_search").chosen();
	});
	
    	
	
	function filter(type){
            var range = $("#slt_range").val();
		$(".chzn-select").val('').trigger("liszt:updated");
		$("#list_validation").empty();
		$("#list_validation").load("impayes_list.php?type="+type+"&range="+range);
	}
        
        function range(range){
            var type = $("#slt_filter").val();
		$(".chzn-select").val('').trigger("liszt:updated");
		$("#list_validation").empty();
		$("#list_validation").load("impayes_list.php?range="+range+"&type="+type);
	}

	function filter_byclient(){
		$("#slt_filter")[0].selectedIndex = 0;
                $("#slt_range")[0].selectedIndex = 0;
		var client = $("#box_search").val();
		$("#list_validation").empty();
		$("#list_validation").load("impayes_list.php?client="+client);
	}

	function init(){
		showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
	}

	</script>

	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div name="message" id="message" ></div>
		<div name="compte_div" id="compte_div"></div>
		<div name="version" id="version">version <?php echo VERSION ?></div>
		<br/>
		<h1>Module des impay&eacute;s</h1><br/>
		
		Filtre par type : <select id="slt_filter" onchange="javascript:filter(this.value);">
			<option value="all">Tout</option>
			<option value="cantine">Cantine</option>
			<option value="etal">Place et Etal</option>
			<option value="amarrage">Amarrage</option>
		</select> Filtre par date : <select id="slt_range" onchange="javascript:range(this.value);">
                        <option value="0">Tout</option>
			<option value="1">1 mois</option>
			<option value="2">2 mois</option>
			<option value="3">3 mois et plus</option>
		</select><br/><br/>
				
				<label for="box_search"><? echo $label ?></label>
				
				<select name="box_search" id="box_search" data-placeholder="S&eacute;lectionner un compte" class="chzn-select" tabindex="2" style="width:450px;">
					<option value=""></option>
					<?php buildOptionsPersonnes($_GET['form']); ?>
				</select>
				<button onclick="filter_byclient();">Filtrer par client</button>
		<br/><br/>
		
		<div id="list_validation" name="list_validation" style="height:600px;"></div>
		
		
	</body>
</html>