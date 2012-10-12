
function centerbox(id,top,left) {
	if (typeof top == 'undefined' ) top = true;
	if (typeof left == 'undefined' ) left = true;

	var box = document.getElementById(id);
	var userw = window.outerWidth;
	var userh = window.outerHeight;
	var y;
	var x;
	
	box.style.position="absolute";
	
	//y = (userh/2)-(box.offsetHeight); //not high enough
	y = "25%";
	x = (userw/2)-(box.offsetWidth/2);
	
	if(left) {box.style.left = x;}
	if(top) {box.style.top = y;}

}