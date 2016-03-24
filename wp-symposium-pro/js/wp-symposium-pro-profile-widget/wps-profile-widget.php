<?php
     /**
     * Plugin Name: WP Symposium PRO Profile Widget
     * Plugin URI: http://www.wnccnwebdev.com
     * Description: A widget for displaying user profile information associated with WP Symposium Pro. The widget will allow you to display the user avatar in three different sizes, and user menu, display user friends and up to 6 additional shortcodes with tiles. No coding required.
     * Version: 14.8.3
     * Author: Michael Junker
     * Author URI: http://www.wnccnwebdev.com
     * License: Commercial. Cannot be reproduced, replicated or modified without the express permission of the author.
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     */
    /**
     * Register the widget
     */
    function wpspprofwid_load_widgets() {
        register_widget( 'WPS_PRO_Profile_Widget' );
    }
    add_action( 'widgets_init', 'wpspprofwid_load_widgets' );

    /**
     * Profile Widget class.
     */
    class WPS_PRO_Profile_Widget extends WP_Widget {

        /**
         * Widget setup.
         */
        public function __construct() {
            $widget_ops = array( 'description' => __('Add a custom Profile to your sidebar.') );
            parent::__construct( 'nav_menu1', __('WP Symposium PRO Profile'), $widget_ops );
        }

        // ********************** How to display Widget on Screen *******************************
        public function widget($args, $instance) {

            // THIS is where to ADD the code
            if ( ! is_user_logged_in() ) {
                /* ========================= Fifth Shortcode section start ==========================*/

                //Set Shortcode 5 title
                $sc5title = apply_filters( 'Shortcode 5 Title', $instance['sc5title'] );

                //Display Shortcode 5 title
                if ( ! empty( $sc5title ) )
                    echo $args['before_sc1title'] . "<h3>$sc5title</h3>" . $args['after_sc1title'];

                //Do Shortcode 5
                echo do_shortcode($instance['sc5']);

                /* ========================= Sixth Shortcode section start =============================*/

                //Set Shortcode 6 title
                $sc6title = apply_filters( 'Shortcode 6 Title', $instance['sc6title'] );

                //Display Shortcode 6 title
                if ( ! empty( $sc6title ) )
                    echo $args['before_sc1title'] . "<h3>$sc6title</h3>" . $args['after_sc1title'];

                //Do Shortcode 6
                echo do_shortcode($instance['sc6']);

                return;
            }

            // Set Title
            $title = apply_filters( 'widget_title', $instance['title'] );

            // Display before widget code
            echo $before_widget;

            //Display Title
            if ( ! empty( $title ) )
                echo $args['before_title'] . $title . $args['after_title'];

            /* ========================= Avatar image start ====================================*/

            if($instance['avtar_size']!='')
            {

                echo do_shortcode('[wps-avatar size="'.$instance['avtar_size'].'"]');
            }
            /* ========================= Avatar image end ====================================*/
            // Do Shortcodes

            echo do_shortcode('[wps-friends-add-button after="<br />"]');
            echo do_shortcode('[wps-mail-to-user after="<br />"]');

            // ====================== Menu Display ==================================

            //Set Menu title
            $mtitle = apply_filters( 'Menu Title', $instance['mtitle'] );

            //Display Title
            if ( ! empty( $mtitle ) )
                echo $args['before_mtitle'] . "<h3>$mtitle</h3>" . $args['after_mtitle'];

            // Get menu
            $nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

            if($nav_menu != '')
            {
                wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu ) );
            }

            /* ========================= Friends shortcode start ====================================*/

            if($instance['friends_shortcode']=='on')
            {
                echo "<h3>My Friends</h3>";
                echo do_shortcode('[wps-friends size="35" count=5]');
            }

            /* ========================= Friends Page URL start ==================================*/

            echo '<p><a href="'.$instance ['friends_url'].'">'.$instance['fptitle'].'</a></p>';

            /* ========================= First Shortcode section start ===================================*/
            //Set Shortcode 1 title
            $sc1title = apply_filters( 'Shortcode 1 Title', $instance['sc1title'] );

            //Display Shortcode 1 title
            if ( ! empty( $sc1title ) )
                echo $args['before_sc1title'] . "<h3>$sc1title</h3>" . $args['after_sc1title'];

            //Do Shortcode 1
            echo do_shortcode($instance['sc1']);

            /* ========================= Second Shortcode section start ===============================*/

            //Set Shortcode 2 title
            $sc2title = apply_filters( 'Shortcode 2 Title', $instance['sc2title'] );

            //Display Shortcode 2 title
            if ( ! empty( $sc2title ) )
                echo $args['before_sc1title'] . "<h3>$sc2title</h3>" . $args['after_sc1title'];

            //Do Shortcode 2
            echo do_shortcode($instance['sc2']);

            /* ========================= Third Shortcode section start =====================*/

            //Set Shortcode 3 title
            $sc3title = apply_filters( 'Shortcode 3 Title', $instance['sc3title'] );

            //Display Shortcode 3 title
            if ( ! empty( $sc3title ) )
                echo $args['before_sc1title'] . "<h3>$sc3title</h3>" . $args['after_sc1title'];

            //Do Shortcode 3
            echo do_shortcode($instance['sc3']);

            /* ========================= Fourth Shortcode section start ==========================*/

            //Set Shortcode 4 title
            $sc4title = apply_filters( 'Shortcode 4 Title', $instance['sc4title'] );

            //Display Shortcode 4 title
            if ( ! empty( $sc4title ) )
                echo $args['before_sc1title'] . "<h3>$sc4title</h3>" . $args['after_sc1title'];

            //Do Shortcode 4
            echo do_shortcode($instance['sc4']);

            /* ========================= Fifth Shortcode section start ==========================*/

            //Set Shortcode 5 title
            $sc5title = apply_filters( 'Shortcode 5 Title', $instance['sc5title'] );

            //Display Shortcode 5 title
            if ( ! empty( $sc5title ) )
                echo $args['before_sc1title'] . "<h3>$sc5title</h3>" . $args['after_sc1title'];

            //Do Shortcode 5
            echo do_shortcode($instance['sc5']);

            /* ========================= Sixth Shortcode section start =============================*/

            //Set Shortcode 6 title
            $sc6title = apply_filters( 'Shortcode 6 Title', $instance['sc6title'] );

            //Display Shortcode 6 title
            if ( ! empty( $sc6title ) )
                echo $args['before_sc1title'] . "<h3>$sc6title</h3>" . $args['after_sc1title'];

            //Do Shortcode 6
            echo do_shortcode($instance['sc6']);

            echo $args['after_widget'];

            // Display after widget code
            echo $after_widget;
        }

        // Update the widget settings.
        public function update( $new_instance, $old_instance ) {
            $instance['title'] = strip_tags( stripslashes($new_instance['title']) );
            $instance['avtar_size'] =  strip_tags( stripslashes($new_instance['avtar_size']) );
            $instance['mtitle'] = strip_tags( stripslashes($new_instance['mtitle']) );
            $instance['nav_menu'] = (int) $new_instance['nav_menu'];
            $instance['friends_shortcode'] =  $new_instance['friends_shortcode'];
            $instance['fptitle'] = strip_tags( stripslashes($new_instance['fptitle']) );
            $instance['friends_url'] = strip_tags( stripslashes($new_instance['friends_url']) );
            $instance['sc1title'] = strip_tags( stripslashes($new_instance['sc1title']) );
            $instance['sc1'] = strip_tags( stripslashes($new_instance['sc1']) );
            $instance['sc2title'] = strip_tags( stripslashes($new_instance['sc2title']) );
            $instance['sc2'] = strip_tags( stripslashes($new_instance['sc2']) );
            $instance['sc3title'] = strip_tags( stripslashes($new_instance['sc3title']) );
            $instance['sc3'] = strip_tags( stripslashes($new_instance['sc3']) );
            $instance['sc4title'] = strip_tags( stripslashes($new_instance['sc4title']) );
            $instance['sc4'] = strip_tags( stripslashes($new_instance['sc4']) );
            $instance['sc5title'] = strip_tags( stripslashes($new_instance['sc5title']) );
            $instance['sc5'] = strip_tags( stripslashes($new_instance['sc5']) );
            $instance['sc6title'] = strip_tags( stripslashes($new_instance['sc6title']) );
            $instance['sc6'] = strip_tags( stripslashes($new_instance['sc6']) );
            return $instance;
        }

        // =============== Displays widget in Admin Panel

        public function form( $instance ) {
            $title = isset( $instance['title'] ) ? $instance['title'] : '';
            $avtar_size = isset( $instance['avtar_size'] ) ? $instance['avtar_size'] : '';
            $mtitle = isset( $instance['mtitle'] ) ? $instance['mtitle'] : '';
            $nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
            $friends_shortcode = isset( $instance['friends_shortcode'] ) ? $instance['friends_shortcode'] : '';
            $fptitle = isset( $instance['fptitle'] ) ? $instance['fptitle'] : '';
            $friends_url = isset( $instance['friends_url'] ) ? $instance['friends_url'] : '';
            $sc1title = isset( $instance['sc1title'] ) ? $instance['sc1title'] : '';
            $sc1 = isset( $instance['sc1'] ) ? $instance['sc1'] : '';
            $sc2title = isset( $instance['sc2title'] ) ? $instance['sc2title'] : '';
            $sc2 = isset( $instance['sc2'] ) ? $instance['sc2'] : '';
            $sc3title = isset( $instance['sc3title'] ) ? $instance['sc3title'] : '';
            $sc3 = isset( $instance['sc3'] ) ? $instance['sc3'] : '';
            $sc4title = isset( $instance['sc4title'] ) ? $instance['sc4title'] : '';
            $sc4 = isset( $instance['sc4'] ) ? $instance['sc4'] : '';
            $sc5title = isset( $instance['sc5title'] ) ? $instance['sc5title'] : '';
            $sc5 = isset( $instance['sc5'] ) ? $instance['sc5'] : '';
            $sc6title = isset( $instance['sc6title'] ) ? $instance['sc6title'] : '';
            $sc6 = isset( $instance['sc6'] ) ? $instance['sc6'] : '';

            // Get menus
            $menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

            // If no menus exists, direct the user to go and create some.
            if ( !$menus ) {
                echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
                return;
            }
            // ============= Widget Selectio s and inputs
            ?>
            <p>
                <p><center>Visibility: Everything from here, including Shortcode 4 is visible to <div><strong>*** Logged IN users ONLY ***</strong></div></center></p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wpspprofwid' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
            </p>
            <p>
            <div><center>Select Avatar Size Below:</center></div>
            <label for="<?php echo $this->get_field_id( 'avtar_size' ); ?>"><?php _e( 'Avatar Size: ' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'avtar_size' ); ?>" name="<?php echo $this->get_field_name('avtar_size'); ?>">
                <option value=""> - No Avatar - </option>
                <?php
                    echo '<option value="300"'. selected( $avtar_size, '300', false ). '>300 x 300px</option>';
                    echo '<option value="250"'. selected( $avtar_size, '250', false ). '>250 x 250px</option>';
                    echo '<option value="200"'. selected( $avtar_size, '200', false ). '>200 x 200px</option>';
                    echo '<option value="150"'. selected( $avtar_size, '150', false ). '>150 x 150px</option>';
                    echo '<option value="100"'. selected( $avtar_size, '100', false ). '>100 x 100px</option>';
                ?>
            </select>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'mtitle' ); ?>"><?php _e( 'User Menu Title:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('mtitle'); ?>" name="<?php echo $this->get_field_name('mtitle'); ?>" value="<?php echo $mtitle; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'nav_menu' ); ?>"><?php _e( 'Set User Menu:' ); ?></label>
                <select id="<?php echo $this->get_field_id( 'nav_menu' ); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
                    <option value="">Select Menu</option>
                    <?php
                        foreach ( $menus as $menu ) {
                            echo '<option value="' . $menu->term_id . '"'
                                . selected( $nav_menu, $menu->term_id, false )
                                . '>'. $menu->name . '</option>';
                        }
                    ?>
                </select>
            </p>
            <p>
            <div><center>Check Box to Show User Friends</center></div>
            <label for="<?php echo $this->get_field_id( 'friends_shortcode' ); ?>"><?php _e( 'Show Friends: ' ); ?></label>
            <input class="checkbox" type="checkbox" <?php checked($friends_shortcode , 'on'); ?> id="<?php echo $this->get_field_id('friends_shortcode'); ?>" name="<?php echo $this->get_field_name('friends_shortcode'); ?>" />
            </p>
            <p>
            <label for="<?php echo $this->get_field_id( 'fptitle' ); ?>"><?php _e( 'Friend Page Label - ex. See All Friends' ); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('fptitle'); ?>" name="<?php echo $this->get_field_name('fptitle'); ?>" value="<?php echo $fptitle; ?>" />
            </p>
            <p>
            <p><center><div>Link for Friends Page</div><div>- Leave <strong>BLANK</strong> for no link. -</div></center></p>
                <label for="<?php echo $this->get_field_id( 'friends_url' ); ?>"><?php _e( 'URL to Friends Page:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('friends_url'); ?>" name="<?php echo $this->get_field_name('friends_url'); ?>" value="<?php echo $friends_url; ?>" />
            <p><center>Add Additional Shortcodes Below:</center></p>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc1title' ); ?>"><?php _e( 'Shortcode 1 Title:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('sc1title'); ?>" name="<?php echo $this->get_field_name('sc1title'); ?>" value="<?php echo $sc1title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc1' ); ?>"><?php _e( 'Shortcode 1:' ); ?></label>
                <textarea name="<?php echo $this->get_field_name('sc1'); ?>" id="<?php echo $this->get_field_id('sc1'); ?>" class="widefat"><?php echo $sc1; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc1title' ); ?>"><?php _e( 'Shortcode 2 Title:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('sc2title'); ?>" name="<?php echo $this->get_field_name('sc2title'); ?>" value="<?php echo $sc2title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc2' ); ?>"><?php _e( 'Shortcode 2:' ); ?></label>
                <textarea name="<?php echo $this->get_field_name('sc2'); ?>" id="<?php echo $this->get_field_id('sc2'); ?>" class="widefat"><?php echo $sc2; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc3title' ); ?>"><?php _e( 'Shortcode 3 Title:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('sc3title'); ?>" name="<?php echo $this->get_field_name('sc3title'); ?>" value="<?php echo $sc3title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc3' ); ?>"><?php _e( 'Shortcode 3:' ); ?></label>
                <textarea name="<?php echo $this->get_field_name('sc3'); ?>" id="<?php echo $this->get_field_id('sc3'); ?>" class="widefat"><?php echo $sc3; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc4title' ); ?>"><?php _e( 'Shortcode 4 Title:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('sc4title'); ?>" name="<?php echo $this->get_field_name('sc4title'); ?>" value="<?php echo $sc4title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc4' ); ?>"><?php _e( 'Shortcode 4:' ); ?></label>
                <textarea name="<?php echo $this->get_field_name('sc4'); ?>" id="<?php echo $this->get_field_id('sc4'); ?>" class="widefat"><?php echo $sc4; ?></textarea>
            </p>
            <p>
            <p><center><div>Visibility:</div><div>Shortcode 5 and 6 are visible at <div><strong>*** ALL TIMES ***</strong></div></div></center></p>
                <label for="<?php echo $this->get_field_id( 'sc5title' ); ?>"><?php _e( 'Shortcode 5 Title:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('sc5title'); ?>" name="<?php echo $this->get_field_name('sc5title'); ?>" value="<?php echo $sc5title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc5' ); ?>"><?php _e( 'Shortcode 5:' ); ?></label>
                <textarea name="<?php echo $this->get_field_name('sc5'); ?>" id="<?php echo $this->get_field_id('sc5'); ?>" class="widefat"><?php echo $sc5; ?></textarea>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc6title' ); ?>"><?php _e( 'Shortcode 6 Title:' ); ?></label>
                <input type="text" class="widefat" id="<?php echo $this->get_field_id('sc6title'); ?>" name="<?php echo $this->get_field_name('sc6title'); ?>" value="<?php echo $sc6title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'sc6' ); ?>"><?php _e( 'Shortcode 6:' ); ?></label>
                <textarea name="<?php echo $this->get_field_name('sc6'); ?>" id="<?php echo $this->get_field_id('sc6'); ?>" class="widefat"><?php echo $sc6; ?></textarea>
            </p>
        <?php
        }
    }

// ================ Auto updates

require_once ('wp_autoupdate.php');
$wptuts_plugin_current_version = '14.8.3';
$wptuts_plugin_remote_path = 'http://www.wnccnwebdev.com/wp-content/plugins/wp-symposium-pro-profile-widget/update.php';
$wptuts_plugin_slug = plugin_basename(__FILE__);
new __wps__wps_pro_profile_widget_auto_update ($wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug);
?>