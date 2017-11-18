// JavaScript Document
var msgr;
var timeout;
var session_timeout = 18000;
var mit_url_str = "http://www.20000puzzles.com/puzzle/";
var mit_url_str_lng = mit_url_str.length;

function handleResponse(e) {
	if (msgr.readyState == 4 && msgr.status == 200) {
		document.getElementById("UrgentMessage").value = msgr.responseText;
		document.getElementById("UrgentMessage").style.color = "#000000";
	}
}

function handlePuzzle(e) {
	if (msgr.readyState == 4 && msgr.status == 200) {
		//so easiest thing to do is just refresh the page
		if(msgr.responseText == "E-42") {
			document.getElementById("UrgentMessage").value = "A puzzle with this title already exists. This page will refresh momentarily.";
			//location.replace("index.php");
		} else {
			document.getElementById("UrgentMessage").value = msgr.responseText;
		    //location.replace("index.php");
		}
	}
}

function handlePuzzleAddition(e) {
	if (msgr.readyState == 4 && msgr.status == 200) {
		//so easiest thing to do is just refresh the page
		if(msgr.responseText == "E-42") {
			document.getElementById("UrgentMessage").value = "A puzzle with this title already exists. This page will refresh momentarily.";
			var start = new Date().getTime();
        	while (new Date().getTime() < start + 5000);
			location.replace("index.php");
		} else {
			document.getElementById("UrgentMessage").value = msgr.responseText;
	 		location.reload(true);
		}
	} else {
		document.getElementById("new_puzzle_save").innerHTML = "Saving...please be patient.";
		d = new Date();
		n = d.getTime()%6;
		if (n == 1) {
			document.getElementById("new_puzzle_save").innerHTML = "Saving....please be patient.";
		} else if (n == 2) {
			document.getElementById("new_puzzle_save").innerHTML = "Saving....please be understanding.";
		} else if (n == 3) {
			document.getElementById("new_puzzle_save").innerHTML = "Saving....please be zen-like.";
		} else if (n == 4) {
			document.getElementById("new_puzzle_save").innerHTML = "Saving....please be zennish";
		} else if (n == 5) {
			document.getElementById("new_puzzle_save").innerHTML = "Saving.....please be lenient.";
		} else {
			document.getElementById("new_puzzle_save").innerHTML = "Saving.....please be excellent.";
		}
	}
}


function toggle_Puzzle_Checkout(puzzleID) {
	updated_puzzles = document.getElementsByName("puzchk_"+puzzleID);
	elem = updated_puzzles[0];
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	if (elem.src.indexOf("noton") == -1) {
		msgr.open("GET","ajax_handler.php?f=QWT&pid="+puzzleID+"&uid="+document.getElementById("userid").value,true);
		msgr.send();
		new_image = "noton.png"; adjustment = -1;
	} else {
		msgr.open("GET","ajax_handler.php?f=CHK&pid="+puzzleID+"&uid="+document.getElementById("userid").value,true);
		msgr.send();
		new_image = "onit.png"; adjustment = 1;
	}
	
	for (i=0; i < updated_puzzles.length; i++) {
		updated_puzzles[i].src = new_image;
	}
	
	updated_puzzles = document.getElementsByName("puzwrk_"+puzzleID);
	if (updated_puzzles[0].innerHTML == "" && adjustment == -1) {
		newTotal = ""
	} else {
		newTotal = (updated_puzzles[0].innerHTML*1)+adjustment;
	}
	for (i=0; i < updated_puzzles.length; i++) {
		if (newTotal == 0) { updated_puzzles[i].innerHTML =""; }
		else {updated_puzzles[i].innerHTML =newTotal}
	}
}

function toggle_instructions() {
	if (document.getElementById("instructions").style.display == "block") {
		document.getElementById("instructions").style.display = "none";
		document.getElementById("instruction_status").innerHTML = "show";
	} else {
		document.getElementById("instructions").style.display = "block";
		document.getElementById("instruction_status").innerHTML = "hide";
	}
}

function show_notes(notes) {
	// all this is showing the notes for a given puzzle
	document.getElementById("show_notes").style.display = "block";
	document.getElementById("show_notes").style.position = "absolute";
	document.getElementById("show_notes").style.left = event.pageX;
	document.getElementById("show_notes").style.top = event.pageY;
	document.getElementById("show_notes").innerHTML = notes+"<br>[Click to hide]";
	// there is no update to this
}

function hide_notes() {
	// all this is showing the notes for a given puzzle
	document.getElementById("show_notes").style.display = "none";
}

function editAnswer(elem, puzzleID, origAnswer, userID, puzzleName) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	urlForSlack = "";
	if (elem.value == origAnswer) {
		exit;
	} else {
		// if there is an answer in place, confirm that this answer should be deleted
		if (origAnswer > "") {
			wishyWashy = confirm("Are you sure you want to change the answer to this puzzle?");
			if (!wishyWashy) {
				exit;
			}
		}
		
		original_class = document.getElementsByName("puzzle_"+puzzleID)[0].className.split(" ")[0];
		
		msgr = new XMLHttpRequest();
		msgr.onreadystatechange = handleResponse;
		if (elem.value == "?") {
			// we are stuck on this puzzle. We are not changing the value, but the status.
			msgr.open("GET","ajax_handler.php?f=STT&uid="+userID+"&ttl="+encodeURIComponent(puzzleName)+"&pid="+puzzleID+"&stt=stuck",true);
			msgr.send();	
			status = original_class + " stuck";
			urlForSlack = "https://palindrome-tools.herokuapp.com/update?type=stuck&text="+encodeURIComponent("Everybody is stuck on "+puzzleName)+"&id="+puzzleID;
			actual_answer = "";
		} else if (elem.value == "!") {
			// we are stuck on this puzzle, but we are making it a priority.
			msgr.open("GET","ajax_handler.php?f=STT&uid="+userID+"&ttl="+encodeURIComponent(puzzleName)+"&pid="+puzzleID+"&stt=priority",true);
			msgr.send();	
			urlForSlack = "https://palindrome-tools.herokuapp.com/update?type=priority&text="+encodeURIComponent(puzzleName+" is now a priority")+"&id="+puzzleID;
			status = original_class + " priority";
			actual_answer = "";
		} else if (elem.value == "!!!") {
			// we are stuck on this puzzle, but we are making it a priority.
			msgr.open("GET","ajax_handler.php?f=STT&uid="+userID+"&ttl="+encodeURIComponent(puzzleName)+"&pid="+puzzleID+"&stt=featured",true);
			msgr.send();	
			urlForSlack = "https://palindrome-tools.herokuapp.com/update?type=feature&text="+encodeURIComponent(puzzleName+" is now the featured puzzle")+"&id="+puzzleID;
			status = original_class + " featured";
			actual_answer = "";
		} else if (elem.value == "" || elem.value == ".") {
			// we are deleting the answer to this puzzle
			msgr.open("GET","ajax_handler.php?f=STT&uid="+userID+"&ttl="+encodeURIComponent(puzzleName)+"&pid="+puzzleID+"&stt=open",true);
			msgr.send();	
			status = original_class + " open";
			actual_answer = "";
		} else if (elem.value.indexOf(mit_url_str)==0) {
			quicksave_new_puzzle(elem.value, puzzleID);
		} else {
			// we are entering an answer	
			msgr.open("GET","ajax_handler.php?f=ANS&uid="+userID+"&ttl="+encodeURIComponent(puzzleName)+"&pid="+puzzleID+"&ans="+elem.value.toUpperCase(),true);
			msgr.send();
			urlForSlack = "https://palindrome-tools.herokuapp.com/update?type=solution&text="+encodeURIComponent(puzzleName+" has been solved. The answer was "+elem.value.toUpperCase())+".&id="+puzzleID;

			status = original_class + " solved";
			actual_answer = elem.value.toUpperCase();
		}

		// we'll do this twice, once for the cells, and once for the answer fields
		updated_puzzles = document.getElementsByName("puzzle_"+puzzleID);
		for (i=0; i < updated_puzzles.length; i++) {
			updated_puzzles[i].className = status;
		}
		
		// we'll do this twice, once for the cells, and once for the answer fields
		updated_puzzles = document.getElementsByName("puzans_"+puzzleID);
		for (i=0; i < updated_puzzles.length; i++) {
			updated_puzzles[i].value = actual_answer;
		}
		
		if (urlForSlack > "") {
			updateSlack(urlForSlack);
		}
	}
}

function updateSlack(uri) {
	var msgr2 = new XMLHttpRequest();
	msgr2.onreadystatechange = handleResponse;
	msgr2.open("GET",uri,true);
	msgr2.send();	
	//alert(uri);
}

function new_link(elem, puzzleID) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	new_link = elem.value;
	
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	msgr.open("GET","ajax_handler.php?f=NPL&pid="+puzzleID+"&link="+encodeURIComponent(new_link),true);
	msgr.send();	
	
	// we'll do this twice, once for the cells, and once for the answer fields
	updated_puzzles = document.getElementsByName("puzurllink_"+puzzleID);
	for (i=0; i < updated_puzzles.length; i++) {
		updated_puzzles[i].href = new_link;
	}
}

function new_sprd(elem, puzzleID) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	new_link = elem.value;
	
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	msgr.open("GET","ajax_handler.php?f=NPS&pid="+puzzleID+"&link="+encodeURIComponent(new_link),true);
	msgr.send();	
	
	// we'll do this twice, once for the cells, and once for the answer fields
	updated_puzzles = document.getElementsByName("puzsprlink_"+puzzleID);
	for (i=0; i < updated_puzzles.length; i++) {
		updated_puzzles[i].href = new_link;
	}
}

function new_name(elem, puzzleID) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	new_link = elem.value;
	
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	msgr.open("GET","ajax_handler.php?f=NPN&pid="+puzzleID+"&ttl="+encodeURIComponent(new_link),true);
	msgr.send();	
	
	// we'll do this twice, once for the cells, and once for the answer fields
	updated_puzzles = document.getElementsByName("puzttl_"+puzzleID);
	for (i=0; i < updated_puzzles.length; i++) {
		updated_puzzles[i].href = new_link;
	}
}

function upd_notes(elem, puzzleID) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	new_notes = elem.value;
	
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	msgr.open("GET","ajax_handler.php?f=UNS&pid="+puzzleID+"&nts="+encodeURIComponent(new_notes),true);
	msgr.send();	
	
	// we'll do this twice, once for the cells, and once for the answer fields
	//updated_puzzles = document.getElementsByName("puzsprlink_"+puzzleID);
	//for (i=0; i < updated_puzzles.length; i++) {
		//updated_puzzles[i].href = new_notes;
	//}
}

function delete_puzzle(puzzleID) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	if (!document.getElementById("areyousure").checked) {
		alert ("You must check off the are you sure box.");
		return;
	}
	
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handlePuzzle;
	msgr.open("GET","ajax_handler.php?f=DPZ&pid="+puzzleID,true);
	msgr.send();
}

function change_parent(elem, puzzleID, metaID) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	if (elem.checked) {
		fnct = "APM";
	} else {
		fnct = "RPM";
	}
	
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	msgr.open("GET","ajax_handler.php?f="+fnct+"&pid="+puzzleID+"&mid="+metaID,true);
	msgr.send();	
	
	// there is no update to this
}

function add_update(elem, code, userID) {
	// This is straightforward...send update into database and that's it.
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	msgr.open("GET","ajax_handler.php?f=NWS&uid="+userID+"&code="+code+"&news="+encodeURIComponent(elem.value),true);
	msgr.send();	
	// there is no update to this
}

function show_puzzle_input(type, metaID, userID) {
	//alert(event.pageX+"; "+event.pageY);
	if (type=="M") { 
		n_p_type = "meta";
	} else {
		if (metaID == 0) {
			n_p_type = "puzzle";
		} else {
			n_p_type="puzzle in meta";
		}
	}
	
	// type should be either P or M. If M, then there is no metaID needed
	document.getElementById("new_puzzle_input").style.display = "block";
	document.getElementById("new_puzzle_input").style.position = "absolute";
	document.getElementById("new_puzzle_input").style.left = event.pageX;
	document.getElementById("new_puzzle_input").style.top = event.pageY;
	document.getElementById("new_puzzle_hidden_uid").value = userID;
	document.getElementById("new_puzzle_hidden_type").value = type;
	document.getElementById("new_puzzle_hidden_parent").value = metaID;
	document.getElementById("new_puzzle_type").innerHTML = n_p_type;
	document.getElementById("new_puzzle_title").select();
	// there is no update to this
}

function abort_addition() {
	document.getElementById("new_puzzle_input").style.display = "none";	
}

function save_new_puzzle() {
	// one thing we need to do is do a better job indicating saving is in progress
    document.getElementById("new_puzzle_save").innerHTML = "Saving...please be patient";

	uid = document.getElementById("new_puzzle_hidden_uid").value;
    new_ttl = document.getElementById("new_puzzle_title").value;
    new_url = document.getElementById("new_puzzle_url").value;
	if (new_url == "URL") { new_url = ""};
    new_typ = document.getElementById("new_puzzle_hidden_type").value;
    new_par = document.getElementById("new_puzzle_hidden_parent").value;
   
	// This is straightforward...send update into database and that's it.
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handlePuzzleAddition;
	if (new_typ == "M") {
		msgr.open("GET","ajax_handler.php?f=ANM&uid="+uid+"&ttl="+encodeURIComponent(new_ttl)+"&url="+encodeURIComponent(new_url),true);
	} else {
		if (new_par == 0) {
			msgr.open("GET","ajax_handler.php?f=ANP&uid="+uid+"&ttl="+encodeURIComponent(new_ttl)+"&url="+encodeURIComponent(new_url),true);
		} else {
			msgr.open("GET","ajax_handler.php?f=APIM&uid="+uid+"&par="+new_par+"&ttl="+encodeURIComponent(new_ttl)+"&url="+encodeURIComponent(new_url),true);
		}
	}
	msgr.send();	
}

function quicksave_new_puzzle(puzzle_url, metapuzzle_id) {
	uid = document.getElementById("new_puzzle_hidden_uid").value;
    new_ttl = process_url(puzzle_url);
    new_url = puzzle_url;
    new_typ = "puzzle in meta";
    new_par = metapuzzle_id;
   
	// This is straightforward...send update into database and that's it.
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handlePuzzleAddition;
	msgr.open("GET","ajax_handler.php?f=APIM&uid="+uid+"&par="+new_par+"&ttl="+encodeURIComponent(new_ttl)+"&url="+encodeURIComponent(new_url),true);
	msgr.send();
}

function promote_puzzle(puzzleID) {
	// Promoting a puzzle is pretty easy...all you have to do is add a relationship to itself
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handlePuzzle;
	msgr.open("GET","ajax_handler.php?f=PRO&pid="+puzzleID,true);
	msgr.send();
}

function idle_hands() {
  if (timeout) {
    clearTimeout(timeout);
  }
  timeout = setTimeout("location.reload();", session_timeout);
}

function process_url(url_string) {
	var puzzle_string;
	if (url_string.indexOf(mit_url_str) == 0) {
		url_string = url_string.replace(mit_url_str,"");
		url_string = url_string.substr(0,url_string.indexOf("/"));
		puzzle_string = process_string(url_string);
	} else {
		puzzle_string = "Puzzle not found";
	}
	return puzzle_string;
}

function process_string(fodder) {
	var result = "";
	var start_of_word = 1;
	for (i = 0;  i < fodder.length; i++) {
		var individ = fodder.toUpperCase().charCodeAt(i);
		if (individ > 64 && individ < 90) {
			if (start_of_word == 1) {
				start_of_word = 0;
				result = result + fodder.toUpperCase().charAt(i);
			} else {
				result = result + fodder.toLowerCase().charAt(i);
			}
		} else {
			if (individ == 47) {
				return result;
			}
			start_of_word = 1;
			result = result+" ";
		}
	}
	return result;
}