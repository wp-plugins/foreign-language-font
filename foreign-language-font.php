<?php
/**
 * Plugin Name: Foreign Language Font
 * Description: Foreign Language Font allows you to display foreign language text in its own font.
 * Author: Carlo Manf
 * Author URI: http://carlomanf.id.au
 * Version: 1.2.0
 */

// Load the callback function
if ( !function_exists( 'sc_settings_field_callback' ) )
	require_once( 'settings-callbacks.php' );

// Register settings page
add_action( 'admin_menu', function() {
	add_options_page(
		'Foreign Language Font', // title of page
		'Fgn Language Font', // menu text
		'manage_options', // capability to view the page
		'flf', // ID
		function() { // callback
			echo '<div class="wrap">';
			printf( '<h2>%s</h2>', 'Foreign Language Font' );
			echo '<p>If you would like to display foreign language text in its own font, enter the font link and font name below.</p>';
			echo '<form method="post" action="options.php">';
			settings_fields( 'flf' );
			do_settings_sections( 'flf' );
			submit_button();
			echo '</form></div>';
		}
	);
} );

// Initialise settings section
add_action( 'admin_init', function() {
	if ( false === get_option( 'flf' ) )
		add_option( 'flf' );

	add_settings_section( 'flf', null, null, 'flf' );

	add_settings_field(
		'url', // ID
		'<label for="flf[url]">Font Link</label>', // label
		'sc_settings_field_callback', // callback
		'flf', // settings page to add to
		'flf', // section to add to
		array(
			'setting' => 'flf',
			'field' => 'url',
			'type' => 'text',
			'description' => 'Enter the link to the font stylesheet.',
			'filters' => array( 'esc_attr' )
		)
	);

	add_settings_field(
		'name', // ID
		'<label for="flf[name]">Font Name</label>', // label
		'sc_settings_field_callback', // callback
		'flf', // settings page to add to
		'flf', // section to add to
		array(
			'setting' => 'flf',
			'field' => 'name',
			'type' => 'text',
			'description' => 'Enter the name of the font.',
			'filters' => array( 'esc_attr' )
		)
	);

	register_setting( 'flf', 'flf' );
} );

// Enqueue the font stylesheet and plugin helper script
add_action( 'wp_enqueue_scripts', function() {
	$options = get_option( 'flf' );
	if ( empty( $options[ 'url' ] ) || empty( $options[ 'name' ] ) )
		return;

	wp_enqueue_style( 'foreign-language-font', esc_url( $options[ 'url' ] ) );

	wp_enqueue_script( 'foreign-language-font', plugins_url( 'foreign-language-font.js', __FILE__ ), array( 'jquery' ) );
	wp_localize_script( 'foreign-language-font', 'F', array( 'f' => esc_attr( $options[ 'name' ] ) ) );
}, 60 );
