<?php
/**
 * CM Settings Callbacks simplifies the WordPress Settings API by providing you with a field callback function that you can pass your arguments to.
 *
 * @author Carlo Manf
 * @link http://carlomanf.id.au/products/cm-settings-callbacks/
 * @version 1.1
 */

/**
 * Output a settings field or fieldset
 *
 * @param string $setting     The name of the setting
 * @param string $field       The name of the field within the setting
 * @param string $type        The type of field (text, textarea, checkbox, editor)
 * @param string $label       The label beside the checkbox (if checkbox)
 * @param string $description The description below the field
 * @param array  $filters     The names of the functions to which to pass the values before outputting, in order (if text value(s))
 * @param int    $rows        The number of rows in the textarea or editor
 * @param array  $fieldset    The names of the fields in the fieldset, without the common prefix
 * @param string $prefix      A common prefix for the field names, if a fieldset
 * @param array  $labelset    The names of the labels in the fieldset
 */
function cm_settings_field_callback( $args ) {
	extract( wp_parse_args( $args, array(
		'setting' => '',
		'field' => '',
		'type' => 'text',
		'label' => '',
		'description' => '',
		'filters' => array(),
		'rows' => 8,
		'fieldset' => array(),
		'prefix' => '',
		'labelset' => array()
	) ) );

	$options = get_option( $setting );

	if ( empty( $fieldset ) )
		$fields = array( $field );
	else {
		$fields = $fieldset;
		echo '<fieldset>';
	}

	foreach ( $fields as $key => $field_without_prefix ) {
		$field = $prefix . $field_without_prefix;

		if ( !empty( $labelset ) ) {
			$label = $labelset[ $key ];

			if ( isset( $did_one ) && true === $did_one )
				echo '<br>';
		}
		$did_one = true;

		//* Format for the name and ID attributes
		$name = sprintf( '%s[%s]', $setting, $field );

		//* Handle checkboxes
		if ( 'checkbox' === $type ) {
			$checked = checked( true, isset( $options[ $field ] ), false );

			if ( !empty( $description ) && empty( $fieldset ) )
				echo '<fieldset>';

			printf( '<label for="%1$s"><input type="%2$s" id="%1$s" name="%1$s" value="1"%3$s> %4$s</label>', $name, $type, $checked, $label );

			if ( !empty( $description ) && empty( $fieldset ) )
				printf( '<p class="description">%s</p></fieldset>', $description );
		}
		else {
			//* Add and apply the filters
			if ( !empty( $filters ) )
				foreach( $filters as $filter )
					add_filter( $name, $filter );

			$value = isset( $options[ $field ] ) ? apply_filters( $name, $options[ $field ] ) : '';

			//* Handle single line text fields
			if ( 'text' === $type ) {
				if ( !empty( $label ) )
					printf( '<label for="%s">', $name );

				printf( '<input class="regular-text" type="%2$s" id="%1$s" name="%1$s" value="%3$s">', $name, $type, $value );

				if ( !empty( $label ) )
					printf( ' %s</label>', $label );
			}

			//* Handle rich text editors
			if ( 'editor' === $type )
				wp_editor( $value, sprintf( '%s_%s', $setting, $field ), array( 'textarea_name' => $name, 'textarea_rows' => $rows ) );

			//* Add the description if there's one
			if ( !empty( $description ) )
				printf( '<p class="description">%s</p>', $description );
		}
	}

	if ( !empty( $fieldset ) ) {
		if ( !empty( $description ) )
			printf( '<p class="description">%s</p>', $description );

		echo '</fieldset>';
	}
}
