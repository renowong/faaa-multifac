<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('impayes_top.php');


$cUser = unserialize($_SESSION['user']);
$login = $cUser->userlogin();
//print $login;

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$compte_div.$jquery.$jqueryui.$message_div.$graburljs ?>
	
	<link rel="stylesheet" href="chosen/chosen.css" />
	<script src="chosen/chosen.jquery.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function(){

	$("#list_validation").load("impayes_list.php?type=all&range=0");
	$( "#dialog-form" ).hide();
	$("#opaquediv").hide();
	$("#confirm").draggable();
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
	
	function tocsv(){
	    var ids_cantine = $("#ids_cantine").val();
	    var ids_etal = $("#ids_etal").val();
	    var ids_amarrage = $("#ids_amarrage").val();
	    
	    postwith("extract_impayes.php",{ids_cantine:ids_cantine, ids_etal:ids_etal, ids_amarrage:ids_amarrage});
	      
	    //window.location="extract_impayes.php?ids_cantine="+ids_cantine+"&ids_etal="+ids_etal+"&ids_amarrage="+ids_amarrage;
	}
	
	function postwith (to,p) {
	    var myForm = document.createElement("form");
	    myForm.method="post" ;
	    myForm.action = to ;
	    for (var k in p) {
	      var myInput = document.createElement("input") ;
	      myInput.setAttribute("name", k) ;
	      myInput.setAttribute("value", p[k]);
	      myForm.appendChild(myInput) ;
	    }
	    document.body.appendChild(myForm) ;
	    myForm.submit() ;
	    document.body.removeChild(myForm) ;
	}
	
	function relance_warning(lastdate,link){
	    if(lastdate!==""){
		fconfirm('Etes vous certain(e) de vouloir g\351n\351rer un nouveau courrier? Cette action est irr\351versible!',link);
	    }else{
		window.open(link, '_blank');
	    }
	}
	
	function fconfirm(text,link){
	    $("#opaquediv").show();
	    $("#confirm-text").empty();
	    $("#confirm-text").html("<p>"+text+"</p>");
	    $("#confirm").show();
	    $("#confirm button").click(function(){
		    if($(this).val()=='true'){
			window.open(link+'&force=1', '_blank');
		    }else{
			window.open(link, '_blank');
		    }
		    $("#confirm").hide();
		    $("#opaquediv").hide();
		    $("#confirm button").off('click');
	    });
	    
	}

	function init(){
		showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
	}

	</script>

	</head>
	<body onload="init();">
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
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
		
		<div id="opaquediv" style="position:absolute;top:0px;width:100%;height:100%;z-index:20;">
		    <div id="confirm" class="ui-widget-content">
			<div id="confirm-text"></div>
			<button value="false">Non</button> <button value="true">Oui</button>
		    </div>
		</div>
		
		<div id="list_validation" style="height:600px;"></div>
		
		
	</body>
</html>