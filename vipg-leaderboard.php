<?php

/**
 * Plugin Name:       VIP-Grinders Leaderboards
 * Plugin URI:        https://www.vip-grinders.com/vip-grinders-wordpress-plugin-for-affiliates/
 * Description:       Integrate VIP-Grinders leaderboards into your site.
 * Version:           1.6
 * Requires at least: 5.6.7
 * Requires PHP:      7.0
 * Author:            VIP-Grinders.com
 * Author URI:        https://www.vip-grinders.com
 * License:           GPLv2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vglb
 */

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define( 'VIPG_LEADERBOARD_DIR', plugin_dir_path( __FILE__ ) );

require_once( VIPG_LEADERBOARD_DIR . 'VIPG_Leaderboard.php' );
require_once( VIPG_LEADERBOARD_DIR . 'VIPG_LB_Setup.php' );
require_once( VIPG_LEADERBOARD_DIR . 'VIPG_LB_Help.php' );

VIPG_Leaderboard::init();


add_action('wp_ajax_nopriv_vglb_get_leaderboard_preview_wrap', 'vglb_get_leaderboard_preview_wrap');
add_action('wp_ajax_vglb_get_leaderboard_preview_wrap', 'vglb_get_leaderboard_preview_wrap');


function vglb_get_leaderboard_preview_wrap(){
    VIPG_LB_Setup::vglb_get_leaderboard_preview($_POST);
}