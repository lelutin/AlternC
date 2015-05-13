<?php

// List here the supported languages :
$otherlang=array( 
		 "fr" => "Français",
		 "en" => "English"
		  );

$uri=trim($_SERVER["REQUEST_URI"],"/");

// auto lang redirect:
if (!$uri) {
  $lang="en";
  if (isset($_SERVER["ACCEPT_LANGUAGE"])) {
    $l=substr($_SERVER["ACCEPT_LANGUAGE"],0,2);
    if (isset($otherlang[$l])) {
      $lang=$l;
    }
  }
  header("Location: /".$lang);
  exit();
}

list($lang,$uri)=explode("/",$uri,2);


if (!isset($otherlang[$lang])) {
  header("HTTP/1.0 404 Not Found");
  echo "<h1>Lang not supported</h1>";
  exit();
}

unset($otherlang[$lang]);

// Now we spit the proper page:
switch ($uri) {
case "install":
  require_once("install.php");
  exit();
case "":
  require_once("home.php");
  exit();
default:
  header("HTTP/1.0 404 Not Found");
  echo "<h1>Page not found</h1>";
  exit();
}

