<?php
	
	$result = "";
		
if ($_POST['f']) {
	$words_to_add = $_POST['words_to_add'];
	if ($words_to_add != "")  {
		$result = processWords($words_to_add);
	} else {
		$result = "No words entered";
	}
}
	print <<<_FULLTEXT_
<html>
<head>
<title>Word Manipulation</title>
</head>

<body>
_FULLTEXT_;

print "<P>$result</P>";

	print <<<_HTML_
<FORM ACTION="add_words.php" METHOD="POST"><input type="hidden" name="f" value="SubmitWords">
<P>Words</p>
<p><textarea name="words_to_add" cols="100" rows="20"></textarea></p>
<input type=Submit name=Submit value=Submit>
</FORM>

</body>
</html>
_HTML_;

function processWords($words) {
	connectToDB();
	$word_array = explode("\r\n",$words);
	$query = "INSERT INTO words_tbl VALUES ";
	
	// for each word in word_array
	for ($i = 0, $total_words = count($word_array); $i < $total_words; $i++) {
		$word = $word_array[$i];
		$word_nosp = ""; $word_bank=""; $word_alpha=""; $word_rev = ""; $word_morse="";$word_cons="";
		$word_ltrs = str_split($word);
		$alpha_letters = array(26);
		for ($j=0, $total_letters = count($word_ltrs); $j < $total_letters; $j++) {
			$this_letter = strtoupper($word_ltrs[$j]);
			$letter_value = ord($this_letter);
			if ($letter_value > 64 && $letter_value < 91) {
				$word_nosp = $word_nosp.$this_letter; // simply make a letters-only string
				$word_rev = $this_letter.$word_rev; // reverse the string (letters only)
				if (!stripos("_AEIOUY",$this_letter)) {
					$word_cons = $word_cons.$this_letter; // make a consonant only string
					//print "Echo $word_cons";
				}
				$word_morse = $word_morse.getMorse($letter_value-65);
				
				$alpha_letters[$letter_value-64] = $alpha_letters[$letter_value-64] + 1; // for the letter bank and alpha order
			}
		}
		
		for ($k=1; $k<27; $k++) {
			if ($alpha_letters[$k] > 0) {
				$word_bank = $word_bank.chr($k+64);
				$word_alpha = $word_alpha.str_repeat(chr($k+64),$alpha_letters[$k]);
			}
		}
		
		$word_length = strlen($word_nosp);
		
		if ($i !=0 ) { $query = $query.", "; }
		$query = $query." ('$word_nosp', '$word_alpha', '$word_bank', '$word_cons', ".strlen($word_nosp).", '$word_rev', '$word_morse', '$word', 42) ";
	}
	
	$result = mysql_query($query);
	
	return "<P>Words added using query</P><code>".$query."</code><p>(".mysql_error().")</P>";	
}

function getMorse($value) {
	$morse_code = array(
					".-",	"-...",	"-.-.",	"-..",	".",
					"..-.",	"--.",	"....",	"..",	".---",
					"-.-",	".-..",	"--",	"-.",	"---",
					".--.",	"--.-",	".-.", "...",	"-",
					"..-",	"...-",	".--",	"-..-",	"-.--",
					"--.." );
					
	return $morse_code[$value];
}

function connectToDB() {
	$gConnection = mysql_connect('localhost', 'spandext_foggy', 'sm4rg4nA');
	if (!$gConnection) {
	}
	mysql_select_db('spandext_prd_words') or writeHeader('Could not select database');

	return; 
}
?>