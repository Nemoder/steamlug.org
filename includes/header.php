<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name = 'description' content = "<?php echo $description; ?>" />
		<meta name = 'keywords' content = "<?php echo $keywords; ?>" />
		<title>SteamLUG <?php echo $pageTitle; ?></title>
		<link rel="stylesheet" href="css/style.css" type="text/css" />
		<script src="http://twitterjs.googlecode.com/svn/trunk/src/twitter.min.js" type="text/javascript"></script>

		<script>
			var serverTime = <?php echo microtime(true); ?>;
		</script>
		<!--<script type = "text/javascript" src="http://dfgc.jbushproductions.com/microtime2.php" async="async"></script>!-->
<?php
	if (isset($extraJS))
	{
		echo "\t\t<script type = 'text/javascript'>\n";
		echo $extraJS;
		echo "\t\t</script>\n";
	}
	if (isset($externalJS))
	{
		foreach ($externalJS as $js)
		{
			echo "\t\t<script type = 'text/javascript' src = '" . $js . "' async = 'async'></script>\n";
		}
	}
	if (isset($extraCSS))
	{
		echo "\t\t<style type = 'text/css'>\n";
		echo $extraCSS;
		echo "\t\t</style>\n";
	}
?>
	</head>
	<body>
<?php
	$newsPage = "";
	$chatPage1 = "";
	$groupPage = "";
	$gamingPage = "";
	$eventsPage = "";
	$serversPage = "";
	$mumblePage = "";
	$projectsPage = "";
	$aboutPage = "";

	if (strpos($_SERVER["SCRIPT_NAME"], "news.php"))
	{
		$newsPage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "chat.php"))
	{
		$chatPage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "group.php"))
	{
		$groupPage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "gaming.php"))
	{
		$gamingPage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "events.php"))
	{
		$gamingPage = "current";
		$eventsPage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "servers.php"))
	{
		$gamingPage = "current";
		$serversPage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "mumble.php"))
	{
		$gamingPage = "current";
		$mumblePage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "projects.php"))
	{
		$projectsPage = "current";
	}
	else if (strpos($_SERVER["SCRIPT_NAME"], "about.php"))
	{
		$aboutPage = "current";
	}
?>
		<nav>
			<ul>
				<li class = '<?php echo $newsPage; ?>'><a href = 'news'>News</a></li>
				<li class = '<?php echo $chatPage; ?>'><a href = 'chat'>Chat</a></li>
				<li class = '<?php echo $groupPage; ?>'><a href = 'http://steamcommunity.com/groups/steamlug/'>Group</a></li>
				<li class = '<?php echo $gamingPage; ?>'><a href = 'events'>Gaming</a><ul><li class = '<?php echo $eventsPage; ?>'><a href = 'events'>Events</a></li><li class = '<?php echo $serversPage; ?>'><a href = 'servers'>Servers</a></li><li class = '<?php echo $mumblePage; ?>'><a href = 'mumble'>Mumble</a></li></ul></li>
				<li class = '<?php echo $projectsPage; ?>'><a href = 'projects'>Projects</a></li>
				<li class = '<?php echo $aboutPage; ?>'><a href = 'about'>About</a></li>
			</ul>
		</nav>