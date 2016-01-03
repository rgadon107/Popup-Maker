<?php
/**
 * Condition
 *
 * @package     PUM
 * @subpackage  Classes/PUM_Condition
 * @copyright   Copyright (c) 2016, Daniel Iser
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PUM_Condition extends PUM_Fields {

	public $id;

	public $labels = array();

	public $field_prefix = 'popup_conditions';

	public $field_name_format = '{$prefix}[][][{$field}]';

	/**
	 * @var string
	 */
	public $templ_value_format = '{$field}';

	public $group = 'general';

	/**
	 * Sets the $id of the Condition and returns the parent __construct()
	 *
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$this->id = $args['id'];

		if ( ! empty( $args['labels'] ) ) {
			$this->set_labels( $args['labels'] );
		}

		if ( ! empty( $args['group'] ) ) {
			$this->group = $args['group'];
		}

		return parent::__construct( $args );
	}

	public function get_id() {
		return $this->id;
	}

	public function set_labels( $labels = array() ) {
		$this->labels = wp_parse_args( $labels, array(
			'name' => __( 'Condition', 'popup-maker' ),
		) );
	}

	public function get_label( $key ) {
		return isset( $this->labels[ $key ] ) ? $this->labels[ $key ] : null;
	}

	public function get_labels() {
		return $this->labels;
	}

	public function get_field_name( $field ) {
		return str_replace(
			array(
				'{$prefix}',
				'{$section}',
				'{$field}'
			),
			array(
				$this->field_prefix,
				$field['section'] != 'general' ? "[{$field['section']}]" : '',
				$field['id']
			),
			$this->field_name_format
		);
	}

	/**
	 * @return array
	 */
	public function get_all_fields() {
		$all_fields = array();
		foreach ( $this->fields as $section => $fields ) {
			$all_fields = array_merge( $all_fields, $this->get_fields( $section ) );
		}

		return $all_fields;
	}

	public function get_templ_name( $args, $print = true ) {
		$name = str_replace(
			array(
				'{$prefix}',
				'{$section}',
				'{$field}'
			),
			array(
				$this->field_prefix,
				$args['section'] != 'general' ? ".{$args['section']}" : "",
				$args['id']
			),
			$this->templ_value_format
		);

		if ( $print ) {
			$name = "<%= $name %>";
		}

		return $name;
	}


	/**
	 * @param array $values
	 */
	function render_fields( $values = array() ) {
		foreach ( $this->get_all_fields() as $id => $args ) {
			$value = isset( $values[ $args['id'] ] ) ? $values[ $args['id'] ] : null;

			$this->render_field( $args, $value );
		}
	}

	/**
	 */
	public function render_templ_fields() {
		foreach ( $this->get_all_fields() as $id => $args ) {
			$this->render_templ_field( $args );
		}
	}

	public function field_before( $class = '' ) {
		?><div class="facet-col <?php esc_attr_e( $class ); ?>"><?php
	}

	public function field_after() {
		?></div><?php
	}

	/**
	 * Sanitize fields
	 *
	 * @param array $values
	 *
	 * @return string $input Sanitized value
	 * @internal param array $input The value inputted in the field
	 *
	 */
	function sanitize_fields( $values = array() ) {
		$sanitized_values = array();
		if ( isset( $values['target'] ) && $values['target'] == $this->get_id() ) {
			$sanitized_values['target'] = $this->get_id();
		}
		foreach ( $this->get_all_fields() as $id => $field ) {
			$value = isset( $values[ $field['id'] ] ) ? $values[ $field['id'] ] : null;
			$value = $this->sanitize_field( $field, $value );
			if ( ! is_null( $value ) ) {
				$sanitized_values[ $field['id'] ] = $value;
			}
		}

		return $sanitized_values;
	}

}
