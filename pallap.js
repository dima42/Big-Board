// JavaScript Document
var msgr;
var timeout;
var session_timeout = 18000;

function handleResponse(e) {
	if (msgr.readyState == 4 && msgr.status == 200) {
		$("#UrgentMessage").value = msgr.responseText;
		$("#UrgentMessage").style.color = "#000000";
	}
}

function handlePuzzle(e) {
	if (msgr.readyState == 4 && msgr.status == 200) {
		//so easiest thing to do is just refresh the page
		if (msgr.responseText == "E-42") {
			$("#UrgentMessage").value = "A puzzle with this title already exists. This page will refresh momentarily.";
			//location.replace("index.php");
		} else {
			$("#UrgentMessage").value = msgr.responseText;
		    //location.replace("index.php");
		}
	}
}

function handlePuzzleAddition(e) {
	if (msgr.readyState == 4 && msgr.status == 200) {
		//so easiest thing to do is just refresh the page
		if(msgr.responseText == "E-42") {
			$("#UrgentMessage").value = "A puzzle with this title already exists. This page will refresh momentarily.";
			var start = new Date().getTime();
        	while (new Date().getTime() < start + 5000);
			// location.replace("index.php");
		} else {
			$("#UrgentMessage").value = msgr.responseText;
	 		// location.reload(true);
		}
	} else {
		$("#new_puzzle_save").innerHTML = "Saving...please be patient.";
		d = new Date();
		n = d.getTime()%6;
		if (n == 1) {
			$("#new_puzzle_save").innerHTML = "Saving....please be patient.";
		} else if (n == 2) {
			$("#new_puzzle_save").innerHTML = "Saving....please be understanding.";
		} else if (n == 3) {
			$("#new_puzzle_save").innerHTML = "Saving....please be zen-like.";
		} else if (n == 4) {
			$("#new_puzzle_save").innerHTML = "Saving....please be zennish";
		} else if (n == 5) {
			$("#new_puzzle_save").innerHTML = "Saving.....please be lenient.";
		} else {
			$("#new_puzzle_save").innerHTML = "Saving.....please be excellent.";
		}
	}
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

		original_class = $("#puzzle"+puzzleID)[0].className.split(" ")[0];

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
		} else {
			// we are entering an answer
			msgr.open("GET","ajax_handler.php?f=ANS&uid="+userID+"&ttl="+encodeURIComponent(puzzleName)+"&pid="+puzzleID+"&ans="+elem.value.toUpperCase(),true);
			msgr.send();
			urlForSlack = "https://palindrome-tools.herokuapp.com/update?type=solution&text="+encodeURIComponent(puzzleName+" has been solved. The answer was "+elem.value.toUpperCase())+".&id="+puzzleID;

			status = original_class + " solved";
			actual_answer = elem.value.toUpperCase();
		}

		// we'll do this twice, once for the cells, and once for the answer fields
		updated_puzzles = $("#puzzle" + puzzleID);
		for (i=0; i < updated_puzzles.length; i++) {
			updated_puzzles[i].className = status;
		}

		// we'll do this twice, once for the cells, and once for the answer fields
		updated_puzzles = $("[name=puzans_" + puzzleID + "]");
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

function delete_puzzle(puzzleID) {
	// we need to do a number of things here. First is, check to see if there is an actual change.
	if (!$("#areyousure").checked) {
		alert ("You must check off the are you sure box.");
		return;
	}

	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handlePuzzle;
	msgr.open("GET","ajax_handler.php?f=DPZ&pid="+puzzleID,true);
	msgr.send();
}

function add_update(elem, code, userID) {
	// This is straightforward...send update into database and that's it.
	msgr = new XMLHttpRequest();
	msgr.onreadystatechange = handleResponse;
	msgr.open("GET","ajax_handler.php?f=NWS&uid="+userID+"&code="+code+"&news="+encodeURIComponent(elem.value),true);
	msgr.send();
	// there is no update to this
}

function idle_hands() {
  // if (timeout) {
  //   clearTimeout(timeout);
  // }
  // timeout = setTimeout("location.reload();", session_timeout);
}
