<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>PHP-WOL</title>
		<link rel="icon" type="image/png" href="img/favicon.png" />
		<link type="text/css" href="scripts/jquery-ui/css/sunny/jquery-ui-1.8.16.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="scripts/jquery-ui/js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="scripts/jquery-ui/js/jquery-ui-1.8.16.custom.min.js"></script>
		<script src="scripts/jquery.tmpl.min.js"></script>
		
		<link type="text/css" href="styles/style.css" rel="stylesheet" />
		<link type="text/css" href="styles/menu.css" rel="stylesheet" />
		
		<script type="text/javascript" src="scripts/script.js"></script>
		<script type="text/javascript" src="scripts/farms.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/groups.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/hosts.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/users.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/logs.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/schedule.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/settings.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/invit.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/stats.js" defer="defer"></script>
		<script type="text/javascript" src="scripts/mailing.js" defer="defer"></script>
	</head>
	<body>

	<div id="allPage">
		<header> </header>
		<div id="messages"></div>
		<div id="content">
<?php
$USER = Wol_User::getInstance();
?>
		<div id="connect-form" title="Login to PHP WOL">
			<p class="validateTips"> </p>
			<form>
				<fieldset>
					<p>	
						<label for="name">Name</label>
						<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all"/>
					</p>
					<p>
						<label for="password">Password</label>
						<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
					</p>
				</fieldset>
			</form>
		</div>
