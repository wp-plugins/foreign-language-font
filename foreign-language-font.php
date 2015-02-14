<?php
/**
 * Plugin Name: Foreign Language Font
 * Description: Foreign Language Font allows you to display foreign language text in its own font.
 * Author: Carlo Manf
 * Plugin URI: http://carlomanf.id.au/products/foreign-language-font/
 * Author URI: http://carlomanf.id.au/products/foreign-language-font/
 * Version: 1.1
 */

//* Load the callback function
if ( !function_exists( 'cm_settings_field_callback' ) )
	require_once( 'lib/cm-settings-callbacks.php' );

//* Initialise settings section
add_action( 'admin_init', function() { //* flf_initialise_settings_section
	if ( false === get_option( 'flf' ) )
		add_option( 'flf' );

	add_settings_section(
		'flf', //* ID
		'Foreign Language Font', //* title to be displayed
		function() { //* callback
			echo '<p>If you would like to display foreign language text in its own font, enter the font link and font name below.</p>';
		},
		'reading' //* settings page to add to
	);

	add_settings_field(
		'url', //* ID
		'<label for="flf[url]">Font Link</label>', //* label
		'cm_settings_field_callback', //* callback
		'reading', //* settings page to add to
		'flf', //* section to add to
		array(
			'setting' => 'flf',
			'field' => 'url',
			'type' => 'text',
			'description' => 'Enter the link to the font stylesheet.',
			'filters' => array( 'esc_attr' )
		)
	);

	add_settings_field(
		'name', //* ID
		'<label for="flf[name]">Font Name</label>', //* label
		'cm_settings_field_callback', //* callback
		'reading', //* settings page to add to
		'flf', //* section to add to
		array(
			'setting' => 'flf',
			'field' => 'name',
			'type' => 'text',
			'description' => 'Enter the name of the font.',
			'filters' => array( 'esc_attr' )
		)
	);

	register_setting( 'reading', 'flf' );
} );

//* Enqueue the font stylesheet and plugin helper script
add_action( 'wp_enqueue_scripts', function() {
	$options = get_option( 'flf' );
	if ( empty( $options[ 'url' ] ) || empty( $options[ 'name' ] ) )
		return;

	wp_enqueue_style( 'foreign-language-font', esc_url( $options[ 'url' ] ) );

	wp_enqueue_script( 'foreign-language-font', plugins_url( 'foreign-language-font.js', __FILE__ ), array( 'jquery' ) );
	wp_localize_script( 'foreign-language-font', 'F', array( 'f' => esc_attr( $options[ 'name' ] ) ) );
}, 60 );
