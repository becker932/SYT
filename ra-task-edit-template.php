<?php
/*
Template Name: ra-task-edit-template
*/
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>

<?php wp_head(); ?>

</head>
<body >

<div id="body"  class="clearfix" style="height:100%; vertical-align: middle;">

<?php 
	echo do_shortcode ("[gravityform id='4' ajax='true' title='false' tabindex='30' field_values='syt-ra-postid=".urldecode(get_query_var('syt-dra-postid'))."&syt-ra-hazard=".urldecode(get_query_var('syt-dra-hazard'))."&syt-ra-risk=".urldecode(get_query_var('syt-dra-risk'))."&syt-ra-atRisk=".urldecode(get_query_var('syt-dra-atRisk'))."&syt-ra-currentControls=".urldecode(get_query_var('syt-dra-currentControls'))."&syt-ra-standard=".urldecode(get_query_var('syt-dra-standard'))."&syt-ra-recommendations=".urldecode(get_query_var('syt-dra-recommendations'))."&syt-ra-standardMet=".urldecode(get_query_var('syt-dra-standardMet'))."&syt-ra-actionSwitch=".urldecode(get_query_var('syt-dra-actionSwitch'))."&syt-ra-actioned=".urldecode(get_query_var('syt-dra-actioned'))."&syt-ra-actionedby=".urldecode(get_query_var('syt-dra-actionedby'))."']");
?>

</div>
</body>
</html>
<!--/body -->
