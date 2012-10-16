<?php
require_once('headers.php');
require_once('global_functions.php');
require_once('extract_top.php');

$type = $_GET['type'];
switch ($type) {
	case "tivaa":
		$modtitle = "TIVAA";	
	break;
	case "rolmre_cantine":
		$modtitle = "ROLMRE CANTINE";	
	break;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$compte_div.$jquery.$jqueryui.$message_div.$graburljs ?>
		
		
		<script type="text/javascript" src="js/jquery.ui.datepicker-fr.js"></script>
	<script type="text/javascript">
            $(document).ready(function() {
		$('#db').datepicker({inline: true,minDate: "-1Y",maxDate: "0"});
		
		$('#df').datepicker({inline: true,minDate: "-1Y",maxDate: "0"});
                showCompte(<?php echo '"' . $arCompte[0] . '", "' . $arCompte[1] . '", "' . $arCompte[2] . '"' ?>);
		
		if(gup('type')=="tivaa"){
			$("#div_rol").hide();
			$("#rol").val('00');
		}
		
		//////jqueryui buttons/////
		$( "input:submit,input:button,button" ).button();
            });
                
            function submit(){
                var db = $("#db").val();
                var df = $("#df").val();
		var rol = $("#rol").val();
                var sql_db = reversedate(db);
                var sql_df = reversedate(df);

		if(rol==''){
			message("Veuillez entrer un num\351ro de ROLMRE!")
		}else{
			if(db=='' || df==''){
				message("Veuillez entrer une date de d\351but et de fin!")
			}else{
				window.location="extract_<?php print $_GET['type']; ?>.php?sql_db="+sql_db+"&sql_df="+sql_df+"&db="+db+"&df="+df+"&rol="+rol;
			}
		}
                
                
            }
            
            function reversedate(d){
                var ar = d.split("/");
                return ar[2]+"-"+ar[1]+"-"+ar[0];
            }
	</script>
	</head>
	<body>
		<? include_once('menu.php'); ?>
		<div name="message" id="message" ></div>
		<div name="compte_div" id="compte_div"></div>
		<br/><br/>
		<h1>Module d'extraction <? print $modtitle; ?></h1><br/><br/>
		<div name="version" id="version">version <?php echo VERSION ?></div>
		<div name="div_rol" id="div_rol">
			<table>
				<tr>
					<th>Num&eacute;ro du ROLMRE</th>
				</tr>
				<tr>
					<td style="text-align:center;">
						<input id="rol" type="text" size="2" maxlength="2" />
					</td>
				</tr>
			</table>
		</div>
		<table>
			<tr>
			    <th>Date D&eacute;part (incluse)</th><th>Date Fin (incluse)</th><th>Extraire</th>
			</tr>
                        <tr>
                                <td>
                                    <form id="placeholder" method="get" action="#">
                                        <input id="db" type="text" readonly />
                                    </form>
                                </td>
                                <td>
                                    <form id="placeholder" method="get" action="#">
                                        <input id="df" type="text" readonly />
                                    </form>
                                </td>
                                <td>
                                    <button type="button" onclick="javascript:submit();">Soumettre</button>
                                </td>
                        </tr>
		</table>
	</body>
</html>