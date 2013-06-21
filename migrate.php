<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('migrate_top.php');
?>
<!DOCTYPE html>

<html lang="fr">
	<head>
	<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$jquery.$jqueryui.$message_div.$compte_div ?>
	<script type="text/javascript">
	<? print "var listclasses = ".json_encode(buildOptionsClasses()).";\n"; ?>

	$(document).ready(function(){
	$('.check:button').hide();
	
		$('.check:button').toggle(function(){
		    $('input:checkbox').prop('checked','checked');
		    $(this).text('Tout D\351cocher')
		},function(){
		    $('input:checkbox').prop('checked','');
		    $(this).text('Tout Cocher');        
		});
		
		$("#history").load('migrate_history.php');
		
		load_classes($("#e_amigrer").val(),'c_amigrer');
		load_classes($("#e_migrer").val(),'c_migrer');
	    init();
	});
	
	function load_classes(id,select){
		//alert(id);
		$("#"+select).empty();
		var list = listclasses[id].split(",");
		for (var i = 0; i < list.length; i++) {
			$("#"+select).append("<option value='"+list[i]+"'>"+list[i]+"</option>");
		}
	}
	
	function undo(from_ecole,from_classe,to_ecole,to_classe){
		$("#e_amigrer option[value="+to_ecole+"]").prop('selected', 'selected');
		load_classes($("#e_amigrer").val(),'c_amigrer');
		$("#c_amigrer option[value="+to_classe+"]").prop('selected', 'selected');
		//$("#c_amigrer").val(to_classe);
		$("#e_migrer option:[value="+from_ecole+"]").prop('selected', 'selected');
		load_classes($("#e_migrer").val(),'c_migrer');
		$("#c_migrer option[value="+from_classe+"]").prop('selected', 'selected');
		//$("#c_migrer").val(from_classe);
		charger('true');
	}
	
	function charger(){
		var ecole = $("#e_amigrer").val();
		var classe = $("#c_amigrer").val();
		
		$.post("migrate_functions.php",{ecole:ecole,classe:classe,type:"load"},
		function(data){
			if(data.length>46){
				$("#results").html(data);
				$('input:checkbox').prop('checked','');
				$('.check:button').text('Tout Cocher');  
				$('.check:button').show();
				message("");
			}else{
				$("#results").html('');
				$('.check:button').hide();
				message("Pas de r\351sultats!");
			}
		});
		

	}
	
	function migrer(){
		//alert("migration");
		var ecole = $("#e_migrer").val();
		var classe = $("#c_migrer").val();
		var fecole = $("#e_amigrer").val();
		var fclasse = $("#c_amigrer").val();
		var selected = new Array();
		$('#results input:checked').each(function() {
		    selected.push($(this).attr('name'));
		});
		//alert(selected.length);
		if(selected.length==0){
			message("Veuillez cocher au moins un \351l\350ve!");
		}else{
			$.post("migrate_functions.php",{fecole:fecole,fclasse:fclasse,ecole:ecole,classe:classe,ids:selected,type:"migrate"},
			function(data){
				//alert(data);
				$("#results").empty();
				message("Migration effectu\351e");
				$("#history").load('migrate_history.php');
				$('.check:button').hide();
			});
		}
		
	}
	
	function init(){
		showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
	}
	</script>
	</head>
	<body>
		<? include_once('menu.php'); ?>
		<div id="message" ></div>
		<div id="compte_div"></div>
		<div id="version">version <?php echo VERSION ?></div>
		<br/>
		<table>
			<tr>
				<td>
					<h1>Historique</h1>
					<p class="italique">Cliquez pour revenir en arri&egrave;re</p>
					<div id="history" </div>
				</td>
				<td>
					<h1>A Migrer ---</h1>
					S&eacute;lection &eacute;cole : <select name="e_amigrer" id="e_amigrer" onchange="javascript:load_classes(this.value,'c_amigrer');"><? echo buildOptionsSchools(); ?></select><br/>
					Classe : <select name="c_amigrer" id="c_amigrer"></select>
					<!--<input type="text" size="5" maxlength="5" name="c_amigrer" id="c_amigrer"/>-->
					<button onclick="charger('false');">Charger</button>
				</td>
				<td>
					<h1>Migrer vers ---</h1>
					S&eacute;lection &eacute;cole : <select name="e_migrer" id="e_migrer" onchange="javascript:load_classes(this.value,'c_migrer');"><? echo buildOptionsSchools(); ?></select><br/>
					Classe : <select name="c_migrer" id="c_migrer"></select>
					<!--<input type="text" size="5" maxlength="5" name="c_migrer" id="c_migrer"/>-->
					<button onclick="migrer();">Migrer</button>
				</td>
			</tr>
		</table><hr/>
		<button class="check">Tout Cocher</button>
		<div id="results"/>
		<div id="version">version <?php echo VERSION ?></div>
	</body>
</html>

