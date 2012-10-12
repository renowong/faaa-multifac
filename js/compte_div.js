function showCompte(content, id, type){
	var div = $('#compte_div');
	if (content.length > 0){
		//alert(id);
		switch (type) {
			case "client":
			div.html("<a href='clients.php?hideerrors=1&edit=" + id + "' class='compte'>Client : " + content + "</a><a href='retrieveid.php?form=clients&closeaccount=1'><img class='close' src='img/close.png' /></a>");
			break;

			case "mandataire":
			div.html("<a href='mandataires.php?hideerrors=1&edit=" + id + "' class='compte'>Mandataire : " + content + "</a><a href='retrieveid.php?form=mandataires&closeaccount=1'><img class='close' src='img/close.png' /></a>");
			break;
		}
		div.show();
	} else {
		div.empty();
		div.hide();
	}
}
