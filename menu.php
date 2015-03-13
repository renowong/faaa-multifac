<?php
require_once('config.php');
//echo $_SESSION['user'];
//###############################procedures#####################################
//if (!empty($_SESSION['client'])) {
//                $arCompte = getCompteDisplay();
//                $arCompte = preg_split("/,/", $arCompte);
//        }
//##############################end procedures#################################

$cUser = unserialize($_SESSION['user']);
$admin = $cUser->userisadmin();
$validator = $cUser->userisvalidator();
($admin ? $readonly = '' : $readonly = 'readonly ');
($admin ? $lock = '' : $lock = 'blue');
$svc = $cUser->userservice();
//print_r($cUser);
$menu;
//echo $arCompte[2]; ///echo type de client
$menu = "<div class='chromestyle' id='chromemenu'>".
"<ul>".
"<li><a href='#' data='dropmenu0'>Accueil</a></li>".
"<li><a href='#' data='dropmenu1'>Nouveau</a></li>".
"<li><a href='#' data='dropmenu2'>Comptes</a></li>";
if (!empty($_SESSION['client'])&&($svc=="FTR"||$svc=="REG"||$svc=="INF")) $menu .="<li><a href='#' data='dropmenu3'>Facturation</a></li>";
if ($svc=="REG"||$admin) $menu .="<li><a href='#' data='dropmenu4'>R&egrave;glement</a></li>";
if ($svc=="REG"||$admin) $menu .= "<li><a href='#' data='dropmenu5'>Extractions</a></li>";
$menu .= "<li><a href='#' data='dropmenu6'>Administration</a></li>".
"</ul>".
"</div>";


//<!--0st drop down menu -->
$menu .= "<div id='dropmenu0' class='dropmenudiv' style='width: 210px;'>";
if ($svc=="FTR"||$admin) $menu .= "<a href='validation_required.php?validlist=1'>Liste des factures en attente</a>";
if ($validator||$admin) $menu .= "<a href='validation_required.php'>Liste des validations factures</a>";
if ($validator||$admin) $menu .= "<a href='validation_required_avoirs.php'>Liste des validations avoirs</a>";
$menu .= "<a href='main.php'>Accueil</a>".
"<a href='index.php'>Quitter</a>".
"</div>";
//
////<!--1st drop down menu -->
$menu .= "<div id='dropmenu1' class='dropmenudiv' style='width: 150px;'>".
"<a href='clients.php?reset=1'>Client</a>".
"<a href='mandataires.php?reset=1'>Mandataire</a>".
"<a href='lieux.php?reset=1'>Lieux</a>".
"</div>";


//<!--2nd drop down menu -->
$menu .= "<div id='dropmenu2' class='dropmenudiv' style='width: 150px;'>".
"<a href='retrieveid.php?form=clients'>Client</a>".
"<a href='retrieveid.php?form=mandataires'>Mandataire</a>".
"<a href='retrieveid.php?form=enfants'>Enfants</a>".
"</div>";

//<!--3rd drop down menu -->
$menu .= "<div id='dropmenu3' class='dropmenudiv' style='width: 150px;'>";
if ($arCompte[2]=="client") $menu .= "<a href='facture_cantine.php'>Cantine</a>";
if ($arCompte[2]=="mandataire") $menu .= "<a href='facture_etal.php'>Place et Etal</a>";
if ($arCompte[2]=="client" || $arCompte[2]=="mandataire") $menu .= "<a href='facture_amarrage.php'>Amarrage</a>";
$menu .= "</div>";

//<!--4rd drop down menu -->
$menu .= "<div id='dropmenu4' class='dropmenudiv' style='width: 150px;'>";
if ($arCompte[2]=="client" || $arCompte[2]=="mandataire") $menu .= "<a href='compte_paiement.php'>En attente</a>";
$menu .= "<a href='impayes.php'>Impay&eacute;s</a>";
$menu .= "<a href='paiement_comptant.php?id=0&amp;type=repas'>Tickets cantine</a>";
$menu .= "</div>";

//<!--5rd drop down menu -->
$menu .= "<div id='dropmenu5' class='dropmenudiv' style='width: 180px;'>".
"<a href='extract.php?type=tivaa'>TIVAA</a>".
"<a href='extract.php?type=rolmre_cantine'>ROLME CANTINE</a>";
$menu .= "</div>";

//<!--6rd drop down menu -->
$menu .= "<div id='dropmenu6' class='dropmenudiv' style='width: 180px;'>";
if ($admin) $menu .= "<a href='compte.php?reset=1'>Nouveau Compte</a>";
$menu .= "<a href='compte.php?modif=0'>Modification de Compte</a>";
if ($validator||$admin) $menu .= "<a href='migrate.php'>Migrations</a><a href='ecole_global_search.php'>Facturation Globale</a>".
"<a href='cf_cps.php'>CF CPS</a>".
"<a href='rol_ssbp.php'>Suppression ROL des sans BP</a>";
$menu .= "</div>";


$menu .= "<script type='text/javascript'>cssdropdown.startchrome('chromemenu')</script>";

echo $menu;
?>
