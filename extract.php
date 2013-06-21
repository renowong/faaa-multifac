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
<!DOCTYPE html>

<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$chromecss.$compte_div.$jquery.$jqueryui.$message_div.$graburljs ?>
		
		
		<script type="text/javascript" src="js/jquery.ui.datepicker-fr.js"></script>
	<script type="text/javascript">
            $(document).ready(function() {
		switch(gup('type')){
			case 'tivaa':
			break;
			default:
			var ar_rol = getrol(gup('type'));
		}
		
		
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
	    
	    function list_rol(){
		var output="<h1>Liste des ROL pr&eacute;c&eacute;dents</h1><br/>";
		
		for (var i in window.ar_rol) {
			output += "<a href='"+window.ar_rol[i].filename+"' target='_blank'>ROL du "+window.ar_rol[i].from+" au "+window.ar_rol[i].to+"</a></br></br>";
		}
		$("#list_rol").empty();
		$("#list_rol").html(output);
	    }
	    
	    function getrol(cat){
			$.post("main_functions.php",{cat:cat},function(data){
				ar_rol = jQuery.parseJSON(data); //global variable
				list_rol();
			});
		}
	    
	    function inrange_date(db,df,dates_array){
		var inrange = false;       
		var parse_db = Date.parse(db);
		var parse_df = Date.parse(df);
		
		for (var i in window.ar_rol) {
			//alert(window.ar_rol[i].from);
			var ar1 = Date.parse(window.ar_rol[i].from);
			var ar2 = Date.parse(window.ar_rol[i].to);
				
			while(parse_db<=parse_df){
				inrange = ((ar1 <= parse_db) && (parse_db <= ar2));
				//alert(parse_db+"//ar1="+ar1+"//ar2="+ar2+" "+inrange);
				if(inrange==true){
					parse_db = parse_df+1; //get out of the while!
				}else{
					parse_db += 86400000; //add one day
				}
			}
			if(inrange==true){
				break; //get out of the loop!
			}else{
				parse_db = Date.parse(db);
			}
		}

		
		return inrange;
	    }
	    
            function submit(){
		//alert(window.ar_rol[0].from);
		
                var db = $("#db").val();
                var df = $("#df").val();
		var rol = $("#rol").val();
                var sql_db = reversedate(db);
                var sql_df = reversedate(df);
		
		if(Date.parse(sql_db)>Date.parse(sql_df)){
			message("Les dates choisies sont incorrectes!")
		}else{
			if(inrange_date(sql_db,sql_df,window.ar_rol)){
				message("Les dates choisies entrecoupent un pr\351c\351dent ROLMRE!")
			}else{
				if(rol==''){
					message("Veuillez entrer un num\351ro de ROLMRE!")
				}else{
					if(db=='' || df==''){
						message("Veuillez entrer une date de d\351but et de fin!")
					}else{
						if(gup('type')!=='tivaa') $("#submitbt").prop("disabled",true);
						window.location="extract_<?php print $_GET['type']; ?>.php?sql_db="+sql_db+"&sql_df="+sql_df+"&db="+db+"&df="+df+"&rol="+rol;
					}
				}
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
		<div id="message" ></div>
		<div id="compte_div"></div>
		<br/><br/>
		<h1>Module d'extraction <? print $modtitle; ?></h1><br/><br/>
		<div id="version">version <?php echo VERSION ?></div>
		<div id="div_rol">
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
			</br>
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
                                    <button id="submitbt" type="button" onclick="javascript:submit();">Soumettre</button>
                                </td>
                        </tr>
		</table>
		<hr />
		<div id="list_rol"></div>
	</body>
</html>