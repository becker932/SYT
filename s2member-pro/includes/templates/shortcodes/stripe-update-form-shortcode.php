<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
?>

[s2Member-Pro-Stripe-Form update="1" desc="<?php echo esc_attr (_x ("Update your billing information.", "s2member-front", "s2member")); ?>" default_country_code="US" captcha="0" /]