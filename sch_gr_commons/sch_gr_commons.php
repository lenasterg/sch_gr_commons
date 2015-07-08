<?php
/*
   Plugin Name: Sch.gr commons
   Plugin URI: 
   Version: 0.1
   Author: nts on cti.gr
   Description: Embeds videos, etc. from Greek Schools Network
   Text Domain: sch_gr_commons
   License: GPLv3
  */


$sch_gr_comm_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function sch_gr_comm_noticePhpVersionWrong() {
    global $sch_gr_comm_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Sch.gr commoms" requires a newer version of PHP to be running.',  'sch_gr_commons').
            '<br/>' . __('Minimal version of PHP required: ', 'sch_gr_commons') . '<strong>' . $sch_gr_comm_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'sch_gr_commons') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function sch_gr_comm_PhpVersionCheck() {
    global $sch_gr_comm_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $sch_gr_comm_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'sch_gr_comm_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function sch_gr_comm_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('sch_gr_commons', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
sch_gr_comm_i18n_init();


// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (sch_gr_comm_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('sch_gr_commons_init.php');
    sch_gr_comm_init(__FILE__);
}



wp_embed_register_handler( 'vodnew', '/http:\/\/vod-new.sch.gr\/asset\/detail\/((\w+)\/(\w+))/', 'sch_gr_wp_embed_handler_vodnew' );

/**
 * Add mmpress.sch.gr support
 * @version 1, stergatu
 * @since 0.1
 */
function sch_gr_wp_embed_handler_vodnew( $matches, $attr, $url, $rawattr ) {
    $args = wp_parse_args( $args, wp_embed_defaults() );

    $width = $args['width'];
    $height = floor( $width * 260 / 450 );
    $embed = '<div align="center"><iframe src="' . sprintf(
		    'http://vod-new.sch.gr/asset/player/%1$s/%2$s', esc_attr( $matches[1] ), esc_attr( $matches[2] ) ) . '" width="' . $width . 'px" '
	    . 'height="' . $height . 'px" scrolling="no" frameborder="0" '
	    . 'allowfullscreen="" mozallowfullscreen="" webkitallowfullscreen=""></iframe>'
	    . '<br/><a href="' . $url . '">'.__('Watch it in vod-new.sch.gr','sch_gr_commons').'</a></div>';
    ;

    return apply_filters( 'sch_gr_wp_embed_vodnew', $embed, $matches, $attr, $url, $rawattr );
}


/**
 * Add mmpress.sch.gr support
 * @version 1, stergatu
 * @since 0.1
 */
wp_embed_register_handler( 'mmpressch', '/mmpres.sch.gr:4000\/(\w+)/i', 'sch_gr_wp_embed_handler_mmpressch' );

function sch_gr_wp_embed_handler_mmpressch( $matches, $attr, $url, $rawattr ) {
   $args = wp_parse_args( $args, wp_embed_defaults() );

    $width = $args['width'];
    $height = floor( $width * 402 / 485 );
    $embed = '<div align="center"><iframe allowtransparency="true" width="' . $width . '" height="' . $height . '" src="' . $url . '/?autostart=false" frameborder="0" allowfullscreen mozallowfullscreen="" webkitallowfullscreen=""></iframe>'
	    . '	<br/><a href="http://mmpres.sch.gr">'.__('Go to mmpres.sch.gr','sch_gr_commons').'</a></div>';

    return apply_filters( 'sch_gr_embed_mmpressch', $embed, $matches, $attr, $url, $rawattr );
}