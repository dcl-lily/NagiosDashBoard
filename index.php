<?php 
    $refreshvalue = 30; //value in seconds to refresh page
    $pagetitle = "TMCI-监控展示页面";

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title><?php echo($pagetitle); ?></title>
        <script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/nagios.js"></script>
		<link rel="stylesheet" href="css/nagios.css" type="text/css" />
    </head>
    <body onload="bodyload()">
	<div style="width:100%; ">
	<img src="images/tmci_logo_1.png" />
	</div>
	<hr>
	<div id="nagios_placeholder"></div>
    <div class="nagios_statusbar">
	<div style="float:right;"><img src="images/nec_logo_1.jpg" /></div>
        <div class="nagios_statusbar_item">
            <div id="timestamp_wrap"></div>
        </div>
        <div class="nagios_statusbar_item">
            <div id="loading"></div>
            <p id="refreshing"><span id="refreshing_countdown"><?php print $refreshvalue; ?></span> 秒后刷新</p>
        </div>
    </div>
    </body>
</html>
