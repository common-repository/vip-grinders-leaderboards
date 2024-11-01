<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    wp_die( sprintf(
        __( '%s should only be called when uninstalling the plugin.', 'vglb' ), __FILE__
    ) );
    exit;
}

$role = get_role( 'administrator' );

if ( ! empty( $role ) ) {
    delete_option('vglb_options');
}
