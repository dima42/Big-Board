<?
function writeStylesheet() {
	print <<<__HTML__
<style type="text/css">
#new_puzzle_input {
	display:none;
	text-align: center;
	width: 250px; 
	height: 100px;
	padding-top: 3px;
	border: 1px solid #CCCCCC;
	background: #E1EAFA;	
}

#instructions {
	display:none;
}

#show_notes {
	display:none;
	text-align: center;
	width: 240px; 
	height: 45px;
	padding-top: 3px;
	border: 1px solid #CCCCCC;
	background: #E1EAFA;	
}

#UrgentMessage {
	color: #FF6600;
	background: #FFD1B2;
}

.metaTitle {
	font-family: Verdana, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
}
.MetaRound, H1, A, P, H2, DIV,SPAN, INPUT, TD, TH {
	font-family: Verdana, Helvetica, sans-serif;
	font-size: 11px;
}

a, a:link, a:visited {
	color: #000000;
	text-decoration: underline;
}

a:hover, a:active {
	color: #000000;
	text-decoration: none;
}

.clickable { 	
	cursor: hand;
	cursor: pointer;
}

.pastNews { font-size: 10px; font-style: italic; }

.puzzle {
	font-family: Verdana, Helvetica, sans-serif;
	font-size: 11px;
}

.solved { background-color: #F6F6F6; }
.open { background-color: #FFE1CE; }
.stuck { background-color: #FBAA74; }
.priority { color: #FFFFFF; background-color: #FF6600; }
.featured { background-color: #66FF66; }

.priority a, .priority a:link, .priority a:visited { color: #FFFFFF }
.priority a:hover, .priority a:active { color: #FFFFFF; text-decoration: none; }

.fake_button {
	color: black !important; 
	font-weight: normal !important;
	background-color: #EEEEEE;
	border: 1px solid #999999;
	padding: 2px 2px;
	text-decoration: none;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
}

a.fake_button, a.fake_button:link, a.fake_button:visited, a.fake_button:hover, a.fake_button:active { color: black !important; text-decoration: none; }

.fake_button:hover {
	background-color: #FBAA74;
	cursor: hand;
	cursor: pointer;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
}
</style>
__HTML__;
}
?>