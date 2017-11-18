<!-- PHP Wrapper - 500 Server Error --><html>
<head>
	<title>P&A Magazine: Puzzles for the Fun Side of the Brain</title>
	<link rel=stylesheet href="utilities/gb.css" type="text/css"/>
	<style>P, div, TR, TD { font-family: Arial;} </style>
</head>

<body>

<p>Looking for issue</p>
<?
  echo "URL: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."<br>\n";
  $fixer = "checksuexec ".escapeshellarg($_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI']);
  echo `$fixer`;
?>


</BODY></HTML>