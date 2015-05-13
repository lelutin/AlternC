<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
  <head>
   <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Bootstrap, a sleek, intuitive, and powerful mobile first front-end framework for faster and easier web development.">
<meta name="keywords" content="HTML, CSS, JS, JavaScript, framework, bootstrap, front-end, frontend, web development">
<meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">

<title>AlternC<?php if ($title) { echo " - ".$title; } ?></title>

<!-- Bootstrap core CSS -->

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">

<link rel="stylesheet" href="assets/css/docs.min.css">

<!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
<script src="assets/js/ie-emulation-modes-warning.js"></script>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- Favicons -->
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="icon" href="/favicon.ico">

  </head>
  <body>


    <!-- Docs page layout -->
    <div class="bs-docs-header" id="content">
      <div class="container">
			 <div style="float: left; padding-right: 30px">
			 <a href="/"><img src="logo.png" alt="AlternC" /></a>
			 </div>
        <h1><a href="/">AlternC</a></h1>
      <p>Suite logicielle de gestion de serveur web & mail</p>
			 <?php if ($title) { echo "<p>".$title."</p>"; } ?>
      </div>
    </div>

    <div class="container bs-docs-container">

      <div class="row">
        <div class="col-md-9" role="main">
          <div class="bs-docs-section">

