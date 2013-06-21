<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('ecole_global_search_top.php');

?>
<!DOCTYPE html>

<html lang="fr">
	<head>
	<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$jquery.$jqueryui.$message_div.$compte_div.$graburljs ?>
	
	

	<script type="text/javascript">
	<? print "var listclasses = ".json_encode(buildOptionsClasses()).";\n"; ?>
	
	//values for the progress bar//
	/**/var percent;	 //////
	/**/var p;		 /////
	/////////////////////////////
	
	$(document).ready(function(){
	$('.check:button').hide();
	$("#progress").hide();
	$("#opaquediv").hide();
	
		$('.check:button').toggle(function(){
		    $('input:checkbox').prop('checked','checked');
		    $(this).text('Tout D\351cocher')
		},function(){
		    $('input:checkbox').prop('checked','');
		    $(this).text('Tout Cocher');        
		});
		
		load_classes($("#slt_ecole").val(),'slt_classe');
		
		//$("#confirm").hide();
		$("#btn_facturer").prop('disabled','true')
		init();
		
		$("#confirm").draggable();

		var pdf = gup('pdf');
		if(pdf=='0'){message("Pas de sortie en PDF");}
	});
	
	function fconfirm(text,f){
		$("#opaquediv").show();
		$("#confirm-text").empty();
		$("#confirm-text").html("<p>"+text+"</p>");
		$("#confirm").show();
		$("#confirm button").click(function(){
			if($(this).val()=='true'){
				eval(f+'('+$("#chk_print0").is(':checked')+')');
			}
			$("#confirm").hide();
			$("#opaquediv").hide();
			$("#confirm button").off('click');
		});
		
	}
	
	
	
	function charger(){
		var ecole = $("#slt_ecole").val();
		var classe = $("#slt_classe").val();
		
		$.post("ecole_global_search_functions.php",{ecole:ecole,classe:classe,type:"load"},
		function(data){
                    //alert(data);
			if(data.length>46){
				$("#results").html(data);
				$('input:checkbox').prop('checked','');
				$('.check:button').text('Tout Cocher');  
				$('.check:button').show();
				message("");
				$("#btn_facturer").prop('disabled','')
			}else{
				$("#results").html('');
				$('.check:button').hide();
				message("Pas de r\351sultats!");
			}
		});
		

	}
	
	function load_classes(id,select){
		//alert(id);
		$("#"+select).empty();
		var list = listclasses[id].split(",");
		for (var i = 0; i < list.length; i++) {
			$("#"+select).append("<option value='"+list[i]+"'>"+list[i]+"</option>");
		}
	}
	
	function facturer(print0){
		var selected = new Array();
		$('#results input:checked').each(function() {
		    selected.push($(this).attr('name'));
		});
		//alert(selected.length);
		var period = $("#box_periode").val();
		if(selected.length==0){
			message("Veuillez cocher au moins un \351l\350ve!");
		}else{
			$.post("ecole_global_search_functions.php",{ids:selected,type:"gfacture",print0:print0,period:period},
			function(data){
				percent = 0;p = self.setInterval("progress()",1000);
				genzip(data);
				$("#results").empty();
				message("Facturation en cours...Veuillez patienter pour le t\351l\351chargement...");
				$('.check:button').hide();
				$("#btn_facturer").prop('disabled','true');
			});
		}
	}
	
	function progress(){
		if(percent==100){
			self.clearInterval(p);
			$("#progress").hide();
			$("#opaquediv").hide();
		} else {
			$("#opaquediv").show();
			$("#progress").show();
			percent += 10;
			$("#progressbar").progressbar({
				value:percent
			});
		}
	}
	
	function genzip(ids){
		var ar_ids = ids.split(",");
		for(i=0;i<ar_ids.length;i++){
			$.get("createpdf.php",{idfacture:ar_ids[i],zip:"1",type:"cantine"});
			//alert(ar_ids[i]);
		}
		var t=setTimeout("window.open('zippdf.php','_parent')",10000);
		var t=setTimeout("load_ziplist()",12000);
	}

	function load_ziplist(){
		$("#ziplist").empty();
		$("#ziplist").load("ecole_global_search_ziplist.php");
		$("#seeall").empty();
		$("#seeall").load("ecole_global_search_ziplist.php?seeall=1");
		$("#seeall").hide();
	}
	
	function seemore(){
		$("#seeall").slideDown();
	}
	
	function close_seemore(){
		$("#seeall").slideUp();
	}
	
	function init(){
		showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
		load_ziplist();
	}
	</script>
	</head>
	<body>
		<? include_once('menu.php'); ?>
		<div id="message"></div>
		<div id="seeall"></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<br/>
		<table>
			<tr>
				<td>
					<h1>Derni&egrave;res &eacute;ditions</h1>
					<div id="ziplist" />
				</td>
				<td>
					<h1>Recherche par Ecole</h1>
					S&eacute;lection &eacute;cole : <select name="slt_ecole" id="slt_ecole" onchange="javascript:load_classes(this.value,'slt_classe');"><? echo buildOptionsSchools(); ?></select><br/>
					Classe : <select name="slt_classe" id="slt_classe"></select><button onclick="charger('false');">Charger</button>
				</td>
				<td><h1>P&eacute;riode</h1>
					<select name="box_periode" id="box_periode">
                        <?php echo buildOptionsPeriod(0); ?>
                    </select>
				</td>
				<td>
					<h1>Actions</h1>
					<button name="btn_facturer" id="btn_facturer" onclick="fconfirm('Etes vous certain(e) de vouloir valider? Cette action est irr\351versible!','facturer');" style="background-color:red;">Facturer</button>
				</td>
			</tr>
		</table><hr/>
		<button class="check">Tout Cocher</button>
		<div id="opaquediv" style="position:absolute;top:0px;width:100%;height:100%;z-index:20;">
			<div id="confirm" class="ui-widget-content">
			<div id="confirm-text"></div>
			<input type="checkbox" name="chk_print0" id="chk_print0" /> Cocher pour imprimer les factures &agrave; 0 Frs.<br/><br/>
			<button value="false">Non</button> <button value="true">Oui</button>
		</div>
				<!-- Progressbar -->
		<div id="progress" name="progress">
		<h2>Progression</h2>
		<div id="progressbar"></div>
		</div>
		</div>
		
		
		<div id="results"></div>
		<div id="version">version <?php echo VERSION ?></div>
	
	</body>
</html>