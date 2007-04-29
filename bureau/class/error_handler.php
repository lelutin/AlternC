<?php

$customErrorHandler = true;
$phpErrorList = array();

function phpErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
	$errfile = str_replace("\\", "/", $errfile);
	$errorType = array (
		E_ERROR           => "Erreur",
		E_WARNING         => "Alerte",
		E_PARSE           => "Erreur d'analyse",
		E_NOTICE          => "Note",
		E_CORE_ERROR      => "Core Error",
		E_CORE_WARNING    => "Core Warning",
		E_COMPILE_ERROR   => "Compile Error",
		E_COMPILE_WARNING => "Compile Warning",
		E_USER_ERROR      => "Erreur spécifique",
		E_USER_WARNING    => "Alerte spécifique",
		E_USER_NOTICE     => "Note spécifique",
	);

	$GLOBALS["phpErrorList"][] = array ($errorType[$errno], $errstr, $errfile, $errline);
}

function displayPhpError($message = "Script OK")
{
	global $customErrorHandler, $phpErrorList;
	if ($customErrorHandler === false)
		return;
	$cd = dirname(__FILE__);
	$cd = str_replace("\\", "/", $cd);
	$errmsg = "";
	$c = count($phpErrorList);
	if ($c == 0)
	{
		echo "<div style=\"position: absolute; top: 0px; right: 0px; font-size: 11px; font-family: Verdana, Arial, Helvetiva, Sans-Serif; border: 5px solid green; padding: 3px; background-color: lightgreen; color: black;\">";
		echo $message;
		echo "</div>";
		return;
	}
	$errmsg = "Erreur" . ($c > 1 ? "s" : "") . ": <span style=\"font-weight: bold;\">" . $c . "</span><br />\n";
	foreach ($phpErrorList AS $error)
	{
		$error[2] = str_replace($cd, "", $error[2]);
		if (substr($error[2], 0, 1) == "/")
			$error[2] = substr($error[2], 1);
		$errmsg .= "<b>" . $error[0] . "</b>: <span style=\"color: red;\">" . $error[1] . "</span> dans le fichier <span style=\"font-weight: bold;\">" . $error[2] . "</span> à la ligne <span style=\"font-weight: bold;\">" . $error[3] . "</span><br />\n";
	}
	echo "<div style=\"position: absolute; font-size: 11px; font-family: Verdana, Arial, Helvetiva, Sans-Serif; top: 0px; right: 0px; border: 5px solid red; padding: 3px; background-color: white; color: black;\">\n";
	echo $errmsg;
	echo "</div>\n";

}

if ($customErrorHandler)
	set_error_handler("phpErrorHandler");

?>