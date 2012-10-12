<?php
require_once('checksession.php'); //already includes config.php

//###############################procedures#####################################
if (!empty($_SESSION['client'])) {
                $arCompte = getCompteDisplay();
                $arCompte = preg_split("/,/", $arCompte);
        }


//###############################variables######################################


//#################################building forms################################


//#################################functions#####################################

?>
