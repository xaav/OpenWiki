<? header('Content-type: text/html; charset=utf8'); ?>
<!DOCTYPE HTML>
<html>
<head>
	<title><?= htmlspecialchars($response['title']) ?></title>

	<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print">
	<!--[if lt IE 8]><link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    <link rel="stylesheet" href="css/main.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" type="text/css" href="js/wmd/wmd.css">
    <script type="text/javascript" src="js/wmd/wmd.js"></script>
    <script type="text/javascript" src="js/wmd/showdown.js"></script>
</head>
<body>
<div class="container">
