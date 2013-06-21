<?php
session_start();
session_unset();
session_destroy();
include_once('config.php');
require_once('headers.php');

?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<?php echo $title.$icon.$charset.$defaultcss.$graburljs.$jquery.$jqueryui.$message_div ?>

		<script type="text/javascript">
			$(document).ready(function(){
			$("#login").keydown(function (e){
				if(e.keyCode == 13){
				    login();
				}
			})
			
			$("#password").keydown(function (e){
				if(e.keyCode == 13){
				    login();
				}
			})
					
				$( "#dialog-form" ).dialog({
					height: 320,
					width: 300,
					modal: false,
					resizable: false,
					buttons: {
						"Se connecter": function() {
							login();
						},
						RAZ: function() {
							reset();
						}
					},
					beforeclose : function() { window.close(); }
				});
				
				$("#login").val("");
			});
			
			function login(){
				var slogin = $("#login").val();
				var spassword = $("#password").val();
				$.get('svlogin.php',{login:slogin,password:spassword},
				function(data){
					response(data);
				},"xml");
			}
			
			function response(data){
				var xmlResponse;
				var xmlDocumentElement;
				var bSuccess;
				var sessionid;
				var userid;
			
				xmlResponse = data;
				xmlDocumentElement = xmlResponse.documentElement;
				bSuccess = xmlDocumentElement.getElementsByTagName("access")[0].firstChild.data;
				if(bSuccess=="OK") {
					sessionid = xmlDocumentElement.getElementsByTagName("sessionid")[0].firstChild.data;
					userid = xmlDocumentElement.getElementsByTagName("userid")[0].firstChild.data;
					document.location = "main.php?sessionid=" + sessionid + "&userid=" + userid;
				} else {
					message(bSuccess);
					$("#password").val("");
				}
			}
			
			function reset() {
				$("#login").val("");
				$("#password").val("");
				$("#login").focus();
			}

		function showMessage(){
			var expire = gup("expired");
			if (expire=='1') {message("Session expir&eacute;e, veuillez vous reconnecter.");}
		}
		</script>
	</head>
	<body onload="showMessage();$('#login').focus();">
		<div id="message" ></div>
		<div id="dialog-form" title="Multifacturation version <?php echo VERSION ?>">
		<img src="img/community-users-icon.png" alt="users-icon"/><br/><br/>
			<div style="text-align:right;">
				Utilisateur : <input type="text" name="login" id="login" value="" maxlength="10" size="20" /><br/><br/>
				Mot de passe : <input type="password" name="password" id="password" value="" maxlength="10" size="20" />
			</div>
			
		</div>

		<div id="html5" style="position:relative;top:50px;">
		    Application Certifi&eacute;e<br/><br/><img src="img/HTML5.png" alt="html5"/>
		</div>
		
	</body>
</html>
