<?php
if (realpath (__FILE__) === realpath ($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");
?>

[s2Member-Pro-Stripe-Form level="*" ccaps="music,videos" desc="<?php echo esc_attr (_x ("Description and pricing details here.", "s2member-admin", "s2member")); ?>" cc="USD" custom="%%custom%%" ra="0.01" rp="1" rt="L" rr="BN" coupon="" accept_coupons="0" default_country_code="US" captcha="0" /]