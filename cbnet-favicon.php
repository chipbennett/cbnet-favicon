<?php
/*
 * Plugin Name:   cbnet Favicon
 * Plugin URI:    http://www.chipbennett.net/wordpress/plugins/cbnet-favicon/
 * Description:   Add a Favicon to your site. No bells or whistles; simply upload a (ICO, PNG, or GIF) file.
 * Version:       3.1
 * Author:        chipbennett
 * Author URI:    http://www.chipbennett.net/
 *
 * License:       GNU General Public License, v2 (or newer)
 * License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * Version 3.0 and later of this Plugin: Copyright (C) 2012 Chip Bennett,
 * Released under the GNU General Public License, version 2.0 (or newer)
 * 
 * Previous versions of this Plugin were derived from MaxBlogPress Favicon plugin, version 2.0.9, 
 * Copyright (C) 2007 www.maxblogpress.com, released under the GNU General Public License.
 */
 
 /**
 * Load Plugin textdomain
 */
function cbnetfavicon_load_textdomain() {
	load_plugin_textdomain( 'cbnetfavicon', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}
// Load Plugin textdomain
add_action( 'plugins_loaded', 'cbnetfavicon_load_textdomain' );


/**
 * Globalize Plugin options
 */
global $cbnetfavicon_options;
$cbnetfavicon_options = cbnetfavicon_get_options();

/**
 * Get Plugin option
 */
function cbnetfavicon_get_options() {
	return wp_parse_args( get_option( 'plugin_cbnetfavicon_options', array() ), cbnetfavicon_get_option_defaults() );
}

/**
 * Get option defaults
 */
function cbnetfavicon_get_option_defaults() {
	return apply_filters( 'cbnetfavicon_option_defaults', array( 'favicon' => array( 'file' => '', 'url' => '', 'width' => 0, 'height' => 0, 'type' => 'ico' ) ) );
}

/** 
 * Enqueue favicon
 */
function cbnetfavicon_enqueue_favicon() {
	// Globalize Plugin options
	global $cbnetfavicon_options;
	$url = $cbnetfavicon_options['favicon']['url'];
	$type = $cbnetfavicon_options['favicon']['type'];
	// Only do something if Favicon is set
	if ( '' != $url ) {
		echo '<link rel="icon" type="' . $type . '" href="' . $url . '">';
	}
}

/**
 * If WordPress 4.3+, and no cbnet Favicon is set,
 * direct user to core Site Icon feature
 */
if ( function_exists( 'has_site_icon' ) && '' == $cbnetfavicon_options['favicon']['url'] ) {

	/**
	 * Display admin notice
	 */
	function cbnet_favicon_admin_notice_no_favicon_use_site_icon() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php _e( 'WordPress 4.3 added core support for favicons, via the Site Icon feature.', 'cbnet_favicon' ); ?>
				<?php _e( 'Since you have not yet configured a site icon using the cbnet Favicon Plugin, you will need to use the core feature, rather than the Plugin, to configure your favicon.', 'cbnet_favicon' ); ?>
			</p>
			</p>
				<?php _e( 'Configure the core Site Icon feature via:', 'cbnet_favicon' ); ?>
				<strong>
				<?php 
				printf( 
					 '%1$s %5$s %2$s %5$s %3$s %5$s %4$s', 
					__( 'Appearance', 'cbnet_favicon' ), 
					__( 'Customize', 'cbnet_favicon' ), 
					__( 'Site Identity', 'cbnet_favicon' ), 
					__( 'Site Icon', 'cbnet_favicon' ), 
					__( '->', 'cbnet_favicon' ) 
				); 
				?>
				</strong>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'cbnet_favicon_admin_notice_no_favicon_use_site_icon' );

}

/**
 * If WordPress 4.3+, and cbnet Favicon is set,
 * direct user to migrate to core Site Icon feature
 */
else if ( function_exists( 'has_site_icon' ) && '' != $cbnetfavicon_options['favicon']['url'] ) {

	/** 
	 * If cbnet Favicon is set, but core Site Icon is not set, 
	 * output cbnet Favicon in the template
	 */
	if ( ! has_site_icon() ) {
		add_action( 'wp_head', 'cbnetfavicon_enqueue_favicon' );
	}

	/**
	 * Display admin notice
	 */
	function cbnet_favicon_admin_notice_favicon_use_site_icon() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php _e( 'WordPress 4.3 added core support for favicons, via the Site Icon feature.', 'cbnet_favicon' ); ?>
				<?php _e( 'Since you have configured a site icon using the cbnet Favicon Plugin, the image you used is stored in your Media Library.', 'cbnet_favicon' ); ?>
				<?php _e( 'You can use the same image to configure the core Site Icon feature.', 'cbnet_favicon' ); ?>
				<?php _e( 'Once you configure the core Site Icon feature, the cbnet Favicon display will be automatically disabled.', 'cbnet_favicon' ); ?>
			</p>
			</p>
				<?php _e( 'Configure the core Site Icon feature via:', 'cbnet_favicon' ); ?>
				<strong>
				<?php 
				printf( 
					 '%1$s %5$s %2$s %5$s %3$s %5$s %4$s', 
					__( 'Appearance', 'cbnet_favicon' ), 
					__( 'Customize', 'cbnet_favicon' ), 
					__( 'Site Identity', 'cbnet_favicon' ), 
					__( 'Site Icon', 'cbnet_favicon' ), 
					__( '->', 'cbnet_favicon' ) 
				); 
				?>
				</strong>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'cbnet_favicon_admin_notice_favicon_use_site_icon' );

}

/**
 * Otherwise, if WordPress < 4.3, 
 * continue as normal, but recommend user update
 */
else {

	/** 
	 * If Favicon is set, output it in the template
	 */
	add_action( 'wp_head', 'cbnetfavicon_enqueue_favicon' );

	/**
	 * Display admin notice
	 */
	function cbnet_favicon_admin_notice_update_wordpress() {
		?>
		<div class="notice notice-warning is-dismissible">
			<p>
				<?php _e( 'WordPress 4.3 added core support for favicons, via the Site Icon feature.', 'cbnet_favicon' ); ?>
				<?php _e( 'Please update to the latest version of WordPress.', 'cbnet_favicon' ); ?>
				<?php _e( 'This Plugin will continue to function, but or security reasons, this Plugin is not supported for outdated versions of WordPress.', 'cbnet_favicon' ); ?>
			</p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'cbnet_favicon_admin_notice_update_wordpress' );


	/**
	 * Register Plugin settings
	 * 
	 * Register and add settings fields
	 * for Plugin settings
	 */
	function cbnetfavicon_register_settings() {

		/**
		* Register Favicon setting
		* 
		* Registers Favicon setting as
		* part of core General settings
		*/
		register_setting( 'plugin_cbnetfavicon_options', 'plugin_cbnetfavicon_options', 'cbnetfavicon_validate_settings' );
		
		
		/**
		 * Add Favicon setting section
		 */
		add_settings_section( 'favicon', __( 'Favicon', 'cbnetfavicon' ), 'cbnetfavicon_settings_section_favicon', 'cbnetfavicon-settings' );
		
		
		/**
		 * Favicon section markup
		 */
		function cbnetfavicon_settings_section_favicon() {		
			?>
			<p>
			<?php _e( 'Upload an image to use as the site Favicon.', 'cbnetfavicon' ); ?> 
			<?php _e( 'Favicon file type must be one of ICO, PNG, JPEG, or GIF.', 'cbnetfavicon' ); ?> 
			<?php _e( 'Use supported images only.', 'cbnetfavicon' ); ?> 
			<?php _e( 'Image will be used as-is.', 'cbnetfavicon' ); ?>
			</p>
			<?php
		}


		/**
		* Add Favicon setting field
		* 
		* Adds Favicon setting field to 
		* Settings -> General
		*/
		add_settings_field( 'cbnetfavicon', '<label for="favicon">' . __( 'Favicon' , 'cbnetfavicon' ).'</label>', 'cbnetfavicon_form_field_markup', 'cbnetfavicon-settings', 'favicon' );

		
		/**
		 * Favicon setting form markup
		 */
		function cbnetfavicon_form_field_markup() {
			global $cbnetfavicon_options;
			$favicon = $cbnetfavicon_options['favicon'];
			$option_file = $favicon['file'];
			$option_url = $favicon['url'];
			?>
			<input type="file" name="favicon_file" />
			<?php if ( '' != $option_url ) { ?>
				<span><img src="<?php echo esc_url( $option_url ); ?>" class="cbnet-favicon-settings-page-image" /></span>
			<?php } ?>
			<input type="hidden" name="plugin_cbnetfavicon_options[favicon]" value="<?php echo esc_url( $option_url ); ?>" />
			<?php
		}


	}
	add_action( 'admin_init', 'cbnetfavicon_register_settings' );

	/**
	 * Validate Settings
	 *
	 * Callback to validate Plugin settings
	 */
	function cbnetfavicon_validate_settings( $input ) {
		if ( isset( $input['reset'] ) ) {
			$valid_input = cbnetfavicon_get_option_defaults();
		} else {
			global $cbnetfavicon_options;
			$is_valid_favicon = ( isset( $_FILES['favicon_file'] ) && in_array( $_FILES['favicon_file']['type'], array( 'image/ico', 'image/png', 'image/jpeg', 'image/gif' ) ) ? true : false );
			if ( ! $is_valid_favicon ) { add_settings_error( 'favicon', 'favicon', __( 'Favicon file type must be one of ICO, PNG, JPEG, or GIF.', 'cbnetfavicon' ) ); }
			$valid_input['favicon'] = ( $is_valid_favicon ? cbnetfavicon_image_upload( 'favicon', $input ) : $cbnetfavicon_options['favicon'] );
		}
		// Return $input
		return $valid_input;
	}

	/**
	 * Setup the Plugin Admin Settings Page
	 * 
	 * Add "Favicon" link to the "Settings" menu
	 * 
	 * @uses	cbnetfavicon_get_settings_page_cap()	defined in \functions\wordpress-hooks.php
	 */
	function cbnetfavicon_add_settings_page() {
		// Globalize Plugin options page
		global $cbnetfavicon_settings_page;
		// Add Plugin options page
		$cbnetfavicon_settings_page = add_options_page(
			// $page_title
			// Name displayed in HTML title tag
			__( 'cbnet Favicon Options', 'cbnetfavicon' ), 
			// $menu_title
			// Name displayed in the Admin Menu
			__( 'Favicon', 'cbnetfavicon' ), 
			// $capability
			// User capability required to access page
			'manage_options', 
			// $menu_slug
			// String to append to URL after "Plugins.php"
			'cbnetfavicon-settings', 
			// $callback
			// Function to define settings page markup
			'cbnetfavicon_admin_options_page'
		);
		// Load contextual help
		//add_action( 'load-' . $cbnetfavicon_settings_page, 'cbnetfavicon_settings_page_contextual_help' );
	}
	// Load the Admin Options page
	add_action( 'admin_menu', 'cbnetfavicon_add_settings_page' );


	/**
	 * cbnetfavicon Plugin Settings Page Markup
	 * 
	 */
	function cbnetfavicon_admin_options_page() { 
		// Define the page section
		$settings_section = 'cbnetfavicon-settings';
		?>

		<div class="wrap">
			<form action="options.php" method="post" enctype="multipart/form-data">
			<?php 
				// Implement settings field security, nonces, etc.
				settings_fields('plugin_cbnetfavicon_options');
				// Output each settings section, and each
				// Settings field in each section
				do_settings_sections( $settings_section );
			?>
				<?php submit_button( __( 'Save Settings', 'cbnetfavicon' ), 'primary', 'plugin_cbnetfavicon_options[submit]', false ); ?>
				<?php submit_button( __( 'Reset Defaults', 'cbnetfavicon' ), 'secondary', 'plugin_cbnetfavicon_options[reset]', false ); ?>
			</form>
		</div>
	<?php 
	}


	/**
	 * File Upload Handler
	 */
	function cbnetfavicon_image_upload( $the_file, $input ) {
		$file = $_FILES['favicon_file'];
		$name = $file['name'];
		$type = $file['type'];
		$tmp_name = $file['tmp_name'];
		$size = $file['size'];
		if ( '' != $name ) {
			$filesize = getimagesize( $tmp_name );
			$upload = wp_handle_upload( $file, array( 'test_form' => false ) );
			$upload['width'] = $filesize[0];
			$upload['height'] = $filesize[1];
			$upload['type'] = $type;
		} else {
			global $cbnetfavicon_options;
			$upload = $cbnetfavicon_options[$the_file];
		}
		//return $upload;
		return $upload;
	}

}