<?php
include_once('config.php');
$id = $_GET["id"];

$output = '<table id="tblconjoint" class="tblform" style="width:100%;">'.
				'	<thead>'.
				'		<tr>'.
				'			<th>Conjoint</th>'.
				'		</tr>'.
				'	</thead>'.
				'	<tbody>'.
				'		<tr>'.
				'			<td><label>Sélectionner un conjoint</label><br />'.
                                '<select name="box_conjoint" id="box_conjoint" onchange="validate(this.value, this.id);addOption();" data-placeholder="Affecter &agrave; vide pour supprimer le conjoint" class="chzn-select" tabindex="2">'.
				'<option value=""></option>';
				$output .=  buildOptionsPersonnes();
				$output .= '</select> <button onclick=affect_conjoint();>Affecter</button>'.
                                '</td>'.
				'		</tr>'.	
				'		<tr>'.
				'			<td><label>Conjoint actuel</label><br/>';
                                $output .=  find_conjoint($id);
                                $output .= '</td>'.
				'		</tr>'.
				'	</tbody>'.
				'</table><br/>';
                                

print $output;

// functions ////////////

function buildOptionsPersonnes() {
	$mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
	$query = "SELECT `clientid`, `clientnom`, `clientprenom`, `clientdatenaissance` FROM `clients` WHERE `clientstatus`='1' ORDER BY `clientnom`";
	$result = $mysqli->query($query);

	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	    $output .= '<option value="' . $row['clientid'] . '">' . strtoupper($row['clientnom']) . " " . strtoupper($row['clientprenom']) . " - " . date("d/m/Y",strtotime($row['clientdatenaissance'])) . '</option>';
	}
	$mysqli->close();
        return $output;
}

function find_conjoint($id){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $conjointid = find_conjointid($id);
        $query = "SELECT `clientid`, `clientnom`, `clientprenom`, `clientdatenaissance`,`clientid` FROM `clients` WHERE `clientid`='$conjointid'";
        $result = $mysqli->query($query);

        $row = $result->fetch_array(MYSQLI_ASSOC);
	if($result->num_rows>0) $output = "<a href='clients.php?hideerrors=1&edit=".$row['clientid']."'>".strtoupper($row['clientnom'])." ".strtoupper($row['clientprenom'])." - ".date("d/m/Y",strtotime($row['clientdatenaissance']))."</a>";
	$mysqli->close();
	return $output;
}

function find_conjointid($clientid){
        $mysqli = new mysqli(DBSERVER, DBUSER, DBPWD, DB);
        $query = "SELECT `conjointid` FROM `clients` WHERE `clientid`='$clientid'";
        $result = $mysqli->query($query);

        $row = $result->fetch_array(MYSQLI_ASSOC);
	$output = $row['conjointid'];
	$mysqli->close();
	return $output;
}
?>