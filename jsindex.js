
function workExp (display) {
	
	var divs = document.getElementsByClassName("allWorkExp");
	
	console.log(divs);
	console.log(display);

	for(var i = 0; i < divs.length; i++)
	{
		divs[i].style.display = display;	
	}
	
}

// function checkForm() {
// 	[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}
// }
