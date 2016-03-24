<?php
/**
* File Download routines for s2Member.
*
* Copyright: © 2009-2011
* {@link http://www.websharks-inc.com/ WebSharks, Inc.}
* (coded in the USA)
*
* Released under the terms of the GNU General Public License.
* You should have received a copy of the GNU General Public License,
* along with this software. In the main directory, see: /licensing/
* If not, see: {@link http://www.gnu.org/licenses/}.
*
* @package s2Member\Files
* @since 3.5
*/
if(realpath(__FILE__) === realpath($_SERVER["SCRIPT_FILENAME"]))
	exit("Do not access this file directly.");

if(!class_exists("c_ws_plugin__s2member_files"))
	{
		/**
		* File Download routines for s2Member.
		*
		* @package s2Member\Files
		* @since 3.5
		*/
		class c_ws_plugin__s2member_files
			{
				/**
				* Handles Download Access permissions.
				*
				* @package s2Member\Files
				* @since 110524RC
				*
				* @attaches-to ``add_action("init");``
				* @also-called-by API Function {@link s2Member\API_Functions\s2member_file_download_url()}, w/ ``$create_file_download_url`` param.
				*
				* @param array $create_file_download_url Optional. If this function is called directly, we can pass arguments through this array.
				* 	Possible array elements: `file_download` *(required)*, `file_download_key`, `file_stream`, `file_inline`, `file_storage`, `file_remote`, `file_ssl`, `file_rewrite`, `file_rewrite_base`, `skip_confirmation`, `url_to_storage_source`, `count_against_user`, `check_user`.
				* @return null|str If called directly with ``$create_file_download_url``, returns a string with the URL, based on configuration.
				* 	Else, this function may exit script execution after serving a File Download.
				*/
				public static function check_file_download_access($create_file_download_url = FALSE)
					{
						if(is_array($create_file_download_url) || !empty($_GET["s2member_file_download"]))
							{
								return c_ws_plugin__s2member_files_in::check_file_download_access($create_file_download_url);
							}
					}
				/**
				* Generates a File Download URL for access to a file protected by s2Member.
				*
				* @package s2Member\Files
				* @since 110926
				*
				* @param array $config Required. This is an array of configuration options associated with permissions being checked against the current User/Member; and also the actual URL generated by this routine.
				* 	Possible ``$config`` array elements: `file_download` *(required)*, `file_download_key`, `file_stream`, `file_inline`, `file_storage`, `file_remote`, `file_ssl`, `file_rewrite`, `file_rewrite_base`, `skip_confirmation`, `url_to_storage_source`, `count_against_user`, `check_user`.
				* @param bool $get_streamer_array Optional. Defaults to `false`. If `true`, this function will return an array with the following elements: `streamer`, `file`, `url`. For further details, please review this section in your Dashboard: `s2Member -› Download Options -› JW Player & RTMP Protocol Examples`.
				* @return string A File Download URL string on success; or an array on success, with elements `streamer`, `file`, `url` when/if ``$get_streamer_array`` is true; else false on any type of failure.
				*
				* @see s2Member\API_Functions\s2member_file_download_url()
				*/
				public static function create_file_download_url($config = FALSE, $get_streamer_array = FALSE)
					{
						return c_ws_plugin__s2member_files_in::create_file_download_url($config, $get_streamer_array);
					}
				/**
				* Auto-configures an Amazon S3 Bucket's ACLs.
				*
				* @package s2Member\Files
				* @since 110926
				*
				* @return bool|array True on success, else array on failure.
				* 	Failure array will contain a failure `code`, and a failure `message`.
				*/
				public static function amazon_s3_auto_configure_acls()
					{
						return c_ws_plugin__s2member_files_in::amazon_s3_auto_configure_acls();
					}
				/**
				* Auto-configures Amazon CloudFront distros.
				*
				* @package s2Member\Files
				* @since 130209
				*
				* @return bool|array True on success, else array on failure.
				* 	Failure array will contain a failure `code`, and a failure `message`.
				*/
				public static function amazon_cf_auto_configure_distros()
					{
						return c_ws_plugin__s2member_files_in::amazon_cf_auto_configure_distros();
					}
				/**
				* Determines the max period (in days), for Download Access.
				*
				* @package s2Member\Files
				* @since 3.5
				*
				* @return int Number of days, where 0 means no access to files is allowed.
				* 	Will not return a value > `365`, because this routine also controls the age of download logs to archives.
				*
				* @deprecated Deprecated in v111029. This function is no longer used by s2Member.
				*/
				public static function max_download_period /* No longer used by s2Member. */()
					{
						do_action("ws_plugin__s2member_before_max_download_period", get_defined_vars());

						for($n = 0, $max = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
							if(!empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed"]))
								if(!empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed_days"]))
									if(($days = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed_days"]))
										$max = ($max < $days) ? $days : $max;

						return apply_filters("ws_plugin__s2member_max_download_period", (($max > 365) ? 365 : $max), get_defined_vars());
					}
				/**
				* Determines the minimum Level required for File Download Access.
				*
				* @package s2Member\Files
				* @since 3.5
				*
				* @return bool|int False if no access is allowed, else Level number (int) 0+.
				*/
				public static function min_level_4_downloads()
					{
						do_action("ws_plugin__s2member_before_min_level_4_downloads", get_defined_vars());

						for($n = 0, $min = false; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
							if(!empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed"]))
								if(!empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed_days"]))
									if(($min = $n) >= 0)
										break;

						return apply_filters("ws_plugin__s2member_min_level_4_downloads", ((is_int($min)) ? $min : false), get_defined_vars());
					}
				/**
				* Creates a File Download Key.
				*
				* Builds a hash of: ``date("Y-m-d") . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"] . $file``.
				*
				* @package s2Member\Files
				* @since 3.5
				*
				* @param string $file Location of your protected file, relative to the `/s2member-files/` directory.
				* 	In other words, just the name of the file *(i.e. `file.zip` )*.
				* @param string $directive Optional. One of `ip-forever|universal|cache-compatible`.
				* 	`ip-forever` = a Download Key that never expires, tied only to a specific file and IP address.
				* 	`universal` and/or `cache-compatible` = a Download Key which never expires, and is NOT tied to any specific User. Use at your own risk.
				* @return string A Download Key. MD5 hash, 32 characters, URL-safe.
				*/
				public static function file_download_key($file = FALSE, $directive = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_file_download_key", get_defined_vars());
						unset($__refs, $__v);

						$file = ($file && is_string($file) && ($file = trim($file, "/"))) ? $file : "";

						if($directive === "ip-forever" && c_ws_plugin__s2member_no_cache::no_cache_constants(true))
							$salt = $file.$_SERVER["REMOTE_ADDR"];

						else if($directive === "universal" || $directive === "cache-compatible" || $directive)
							$salt = /* Just the file name. This IS cacheable. */ $file;

						else if(c_ws_plugin__s2member_no_cache::no_cache_constants(true))
							$salt = date("Y-m-d").$_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"].$file;

						$key = (!empty($salt)) ? md5(c_ws_plugin__s2member_utils_encryption::xencrypt($salt, false, false)) : "";

						return apply_filters("ws_plugin__s2member_file_download_key", $key, get_defined_vars());
					}
				/**
				* Download details on a per-User basis.
				*
				* @package s2Member\Files
				* @since 3.5
				*
				* @param object $user Optional. A `WP_User` object. Defaults to the current User's object.
				* @param string $not_counting_this_particular_file Optional. If you want to exclude a particular file,
				* 	relative to the `/s2member-files/` directory, or relative to the root of your Amazon S3 Bucket *(when applicable)*.
				* @param array $user_log Optional. Prevents another database connection *(i.e. the User's log does not need to be pulled again)*.
				* @param array $user_arc Optional. Prevents another database connection *(i.e. the User's archive does not need to be pulled again)*.
				* @return array An array with the following elements... File Downloads allowed for this User: (int)`allowed`, Download Period for this User in days: (int)`allowed_days`, Files downloaded by this User in the current Period: (int)`currently`, log of all Files downloaded in the current Period, with file names/dates: (array)`log`, archive of all Files downloaded in prior Periods, with file names/dates: (array)`archive`.
				*
				* @note Calculations returned by this function do NOT include File Downloads that were accessed with an Advanced File Download Key.
				* @todo Make it possible for s2Member to keep a count of files downloaded with an Advanced Download Key.
				*/
				public static function user_downloads($user = FALSE, $not_counting_this_particular_file = FALSE, $user_log = FALSE, $user_arc = FALSE)
					{
						foreach(array_keys(get_defined_vars())as$__v)$__refs[$__v]=&$$__v;
						do_action("ws_plugin__s2member_before_user_downloads", get_defined_vars());
						unset($__refs, $__v);

						$allowed = $allowed_days = $currently = /* Initialize these to zero. */ 0;
						$log = $arc = /* Initialize these to a default empty array value. */ array();

						if((is_object($user) || is_object($user = (is_user_logged_in()) ? wp_get_current_user() : false)) && !empty($user->ID) && ($user_id = $user->ID))
							{
								for($n = 0; $n <= $GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["levels"]; $n++)
									{
										if /* Do they have access? */($user->has_cap("access_s2member_level".$n))
											{
												if(!empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed"]) && !empty($GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed_days"]))
													{
														$allowed = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed"];
														$allowed_days = $GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["level".$n."_file_downloads_allowed_days"];
													}
												if /* We can stop now, if this is their Role. */($user->has_cap("s2member_level".$n))
													break /* Break now. */;
											}
									}
								$log = (is_array($user_log)) ? $user_log : ((is_array($log = get_user_option("s2member_file_download_access_log", $user_id)) && $log !== array(false)) ? $log : array());
								$arc = (is_array($user_arc)) ? $user_arc : ((is_array($arc = get_user_option("s2member_file_download_access_arc", $user_id)) && $arc !== array(false)) ? $arc : array());

								foreach(($user_file_download_access_log = $log) as $user_file_download_access_log_entry_key => $user_file_download_access_log_entry)
									if(isset($user_file_download_access_log_entry["date"]) && strtotime($user_file_download_access_log_entry["date"]) >= strtotime("-".$allowed_days." days"))
										if(isset($user_file_download_access_log_entry["file"]) && $user_file_download_access_log_entry["file"] !== $not_counting_this_particular_file)
											$currently = $currently + 1;
							}

						return apply_filters("ws_plugin__s2member_user_downloads", array("allowed" => $allowed, "allowed_days" => $allowed_days, "currently" => $currently, "log" => $log, "archive" => $arc), get_defined_vars());
					}
				/**
				* Total downloads of a particular file; possibly by a particular User.
				*
				* @package s2Member\Files
				* @since 111026
				*
				* @param string $file Required. Location of the file, relative to the `/s2member-files/` directory, or relative to the root of your Amazon S3 Bucket *(when applicable)*.
				* @param string|int $user_id Optional. If specified, s2Member will return total downloads by a particular User/Member, instead of collectively *(i.e among all Users/Members)*.
				* @param bool $check_archives_too Optional. Defaults to true. When true, s2Member checks its File Download Archive too, instead of ONLY looking at Files downloaded in the current Period. Period is based on your Basic Download Restrictions setting of allowed days across various Levels of Membership, for each respective User/Member. Or, if ``$user_id`` is specified, based solely on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's current Membership Level.
				* @return int The total for this particular ``$file``, based on configuration of function arguments.
				*
				* @note Calculations returned by this function do NOT include File Downloads that were accessed with an Advanced File Download Key.
				* @todo Make it possible for s2Member to keep a count of files downloaded with an Advanced Download Key.
				*/
				public static function total_downloads_of($file = FALSE, $user_id = FALSE, $check_archives_too = TRUE)
					{
						global /* Global database object reference. */ $wpdb;

						if /* Was ``$file`` passed in properly? */($file && is_string($file))
							{
								if(is_array($results = $wpdb->get_results("SELECT `meta_key`, `meta_value` FROM `".$wpdb->usermeta."` WHERE ".((is_numeric($user_id)) ? "`user_id` = '".esc_sql($user_id)."' AND " : "")."(`meta_key` = '".$wpdb->prefix."s2member_file_download_access_log'".(($check_archives_too) ? " OR `meta_key` = '".$wpdb->prefix."s2member_file_download_access_arc'" : "").") AND `meta_value` REGEXP '.*\"file\";s:[0-9]+:\"".esc_sql($file)."\".*'")))
									{
										foreach($results as $r /* Go through the entire array of results found in the `REGEXP` database query above. */)
											if(is_array($la_entries = /* Unserialize the array. */ maybe_unserialize($r->meta_value)) && !empty($la_entries))

												foreach($la_entries as $la_entry /* Go through all of the entries in each result ``$r``; collecting `counter` values. */)
													if(!empty($la_entry["file"]) && $la_entry["file"] === $file && /* Back compatibility. Is `counter` even set? */ (!empty($la_entry["counter"]) || ($la_entry["counter"] = 1)))
														{
															$total = (!empty($total)) ? $total + (int)$la_entry["counter"] : (int)$la_entry["counter"];
															break /* Break now. No need to continue looping; ``$file`` found in these entries. */;
														}
									}
							}
						return (!empty($total)) ? $total : /* Else return zero by default. */ 0;
					}
				/**
				* Total unique downloads of a particular file; possibly by a particular User.
				*
				* @package s2Member\Files
				* @since 111026
				*
				* @param string $file Required. Location of the file, relative to the `/s2member-files/` directory, or relative to the root of your Amazon S3 Bucket *(when applicable)*.
				* @param string|int $user_id Optional. If specified, s2Member will return total downloads by a particular User/Member, instead of collectively *(i.e among all Users/Members)*.
				* @param bool $check_archives_too Optional. Defaults to true. When true, s2Member checks its File Download Archive too, instead of ONLY looking at Files downloaded in the current Period. Period is based on your Basic Download Restrictions setting of allowed days across various Levels of Membership, for each respective User/Member. Or, if ``$user_id`` is specified, based solely on a specific User's `allowed_days`, configured in your Basic Download Restrictions, at the User's current Membership Level.
				* @return int The total for this particular ``$file``, based on configuration of function arguments.
				*
				* @note Calculations returned by this function do NOT include File Downloads that were accessed with an Advanced File Download Key.
				* @todo Make it possible for s2Member to keep a count of files downloaded with an Advanced Download Key.
				*/
				public static function total_unique_downloads_of($file = FALSE, $user_id = FALSE, $check_archives_too = TRUE)
					{
						global /* Global database object reference. */ $wpdb;

						if /* Was ``$file`` passed in properly? */($file && is_string($file))
							{
								if(is_array($results = $wpdb->get_results("SELECT `meta_key`, `meta_value` FROM `".$wpdb->usermeta."` WHERE ".((is_numeric($user_id)) ? "`user_id` = '".esc_sql($user_id)."' AND " : "")."(`meta_key` = '".$wpdb->prefix."s2member_file_download_access_log'".(($check_archives_too) ? " OR `meta_key` = '".$wpdb->prefix."s2member_file_download_access_arc'" : "").") AND `meta_value` REGEXP '.*\"file\";s:[0-9]+:\"".esc_sql($file)."\".*'")))
									{
										foreach($results as $r /* Go through the entire array of results found in the `REGEXP` database query above. */)
											if(is_array($la_entries = /* Unserialize the array. */ maybe_unserialize($r->meta_value)) && !empty($la_entries))

												foreach($la_entries as $la_entry /* Go through all of the entries in each result ``$r``; collecting `counter` values. */)
													if(!empty($la_entry["file"]) && $la_entry["file"] === $file && /* Back compatibility. Is `counter` even set? */ (!empty($la_entry["counter"]) || ($la_entry["counter"] = 1)))
														{
															$total = (!empty($total)) ? /* Only count `1` here (i.e. unique downloads). */ $total + 1 : 1;
															break /* Break now. No need to continue looping; ``$file`` found in these entries. */;
														}
									}
							}
						return (!empty($total)) ? $total : /* Else return zero by default. */ 0;
					}
				/**
				* Checks for GZIP rules in root `.htaccess` file.
				*
				* @package s2Member\Files
				* @since 120212
				*
				* @return bool True if rules exist, else false.
				*/
				public static function no_gzip_rules_in_root_htaccess()
					{
						$start_line = /* Beginning line for this entry. */ "# BEGIN s2Member GZIP exclusions";
						$end_line = /* Identifying end line for this entry. */ "# END s2Member GZIP exclusions";
						$htaccess = /* Location of this `.htaccess` file. */ ABSPATH.".htaccess";

						if(file_exists($htaccess) && is_readable($htaccess) && ($htaccess_contents = file_get_contents($htaccess)) !== false && is_string($htaccess_contents = trim($htaccess_contents)))
							return preg_match("/".preg_quote($start_line, "/")."[\r\n]+.*?[\r\n]+".preg_quote($end_line, "/")."[\r\n]{0,2}/is", $htaccess_contents);

						return /* Default return `false`. */ false;
					}
				/**
				* Writes no GZIP rules into root `.htaccess` file.
				*
				* @package s2Member\Files
				* @since 120212
				*
				* @return bool True if successfull, else false on any type of failure.
				*/
				public static function write_no_gzip_into_root_htaccess()
					{
						if(c_ws_plugin__s2member_files::remove_no_gzip_from_root_htaccess() /* Must first be able to remove any existing entry. */)
							{
								$start_line = /* Beginning line for this entry. */ "# BEGIN s2Member GZIP exclusions";
								$end_line = /* Identifying end line for this entry. */ "# END s2Member GZIP exclusions";
								$htaccess = /* Location of this `.htaccess` file we need to write in. */ ABSPATH.".htaccess";
								$ideally_position_before = /* Ideally, we can position before this entry. */ "# BEGIN WordPress";

								$no_gzip = $start_line."\n".trim(c_ws_plugin__s2member_utilities::evl(file_get_contents($GLOBALS["WS_PLUGIN__"]["s2member"]["c"]["files_no_gzip_htaccess"])))."\n".$end_line;

								if(file_exists($htaccess) && is_readable($htaccess) && is_writable($htaccess) && ($htaccess_contents = file_get_contents($htaccess)) !== false && is_string($htaccess_contents = trim($htaccess_contents)))
									{
										if(stripos /* If we can position in the ideal location, that's awesome. Let's do that now. */($htaccess_contents, $ideally_position_before) !== false)
											$htaccess_contents = trim(str_ireplace($ideally_position_before, $no_gzip."\n\n".$ideally_position_before, $htaccess_contents));

										else // Else, let's put it at the very top of the file by default.
											$htaccess_contents = trim($no_gzip."\n\n".$htaccess_contents);

										return file_put_contents($htaccess, $htaccess_contents);
									}
								else if(!file_exists($htaccess) && is_writable(dirname($htaccess)))
									{
										return file_put_contents($htaccess, $no_gzip);
									}
							}
						return /* Default return `false`. */ false;
					}
				/**
				* Removes no GZIP rules in root `.htaccess` file.
				*
				* @package s2Member\Files
				* @since 120212
				*
				* @return bool True if successful, else false on any type of failure.
				*/
				public static function remove_no_gzip_from_root_htaccess()
					{
						$start_line = /* Beginning line for this entry. */ "# BEGIN s2Member GZIP exclusions";
						$end_line = /* Identifying end line for this entry. */ "# END s2Member GZIP exclusions";
						$htaccess = /* Location of this `.htaccess` file we need to write in. */ ABSPATH.".htaccess";

						if(file_exists($htaccess) && is_readable($htaccess) && is_writable($htaccess) && ($htaccess_contents = file_get_contents($htaccess)) !== false && is_string($htaccess_contents = trim($htaccess_contents)))
							{
								$htaccess_contents = trim(preg_replace("/".preg_quote($start_line, "/")."[\r\n]+.*?[\r\n]+".preg_quote($end_line, "/")."[\r\n]{0,2}/is", "", $htaccess_contents));

								return /* Check for `false`, because this could return `0` if the file is now empty. */ (file_put_contents($htaccess, $htaccess_contents) !== false);
							}
						else if(!file_exists($htaccess) /* Return `true` here, we're OK. */)
							{
								return true;
							}
						return /* Default return `false`. */ false;
					}
			}
	}
?>