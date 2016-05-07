<?php
// Before removing this file, please verify the PHP ini setting `auto_prepend_file` does not point to this.

if (file_exists('/home/sites/evolver/wp.evolver.co.uk/web/wp-content/plugins/wordfence/waf/bootstrap.php')) {
    define("WFWAF_LOG_PATH", '/home/sites/evolver/wp.evolver.co.uk/web/wp-content/wflogs/');
    include_once '/home/sites/evolver/wp.evolver.co.uk/web/wp-content/plugins/wordfence/waf/bootstrap.php';
}
?>