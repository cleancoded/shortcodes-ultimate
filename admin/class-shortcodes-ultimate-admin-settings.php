<?php

/**
 * The Settings menu component.
 *
 * @since        5.0.0
 *
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/admin
 */
final class Shortcodes_Ultimate_Admin_Settings extends Shortcodes_Ultimate_Admin {

	/**
	 * The plugin settings data.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      array     $plugin_settings   The plugin settings data.
	 */
	private $plugin_settings;

	/**
	 * The slug of the settings page.
	 *
	 * @since    5.0.0
	 * @access   private
	 * @var      array     $settings_page   The slug of the settings page.
	 */
	private $settings_page;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  5.0.0
	 * @param string  $plugin_file    The path of the main plugin file
	 * @param string  $plugin_version The current version of the plugin
	 */
	public function __construct( $plugin_file, $plugin_version ) {

		parent::__construct( $plugin_file, $plugin_version );

		$this->plugin_settings = array();
		$this->settings_page   = 'shortcodes-ultimate-settings';

	}

	/**
	 * Add menu page.
	 *
	 * @since   5.0.0
	 */
	public function admin_menu() {

		/**
		 * Submenu: Settings
		 * admin.php?page=shortcodes-ultimate-settings
		 */
		$this->add_submenu_page(
			'shortcodes-ultimate',
			__( 'Settings', 'shortcodes-ultimate' ),
			__( 'Settings', 'shortcodes-ultimate' ),
			$this->get_capability(),
			$this->settings_page,
			array( $this, 'the_menu_page' )
		);

	}

	/**
	 * Register plugin settings.
	 *
	 * @since  5.0.0
	 */
	public function register_settings() {

		/**
		 * Add default settings section.
		 */
		add_settings_section(
			'shortcodes_ultimate_settings_general',
			false,
			array( $this, 'do_settings_section' ),
			$this->settings_page
		);

		/**
		 * Register plugin settings.
		 */
		foreach ( $this->get_plugin_settings() as $setting ) {

			add_settings_field(
				$setting['id'],
				$setting['title'],
				array( $this, 'do_settings_field' ),
				$this->settings_page,
				'shortcodes_ultimate_settings_general',
				$setting
			);

			if ( ! isset( $setting['sanitize'] ) ) {
				$setting['sanitize'] = false;
			}

			register_setting( $this->settings_page, $setting['id'], $setting['sanitize'] );

		}

	}

	/**
	 * Display specified settings section.
	 *
	 * @param mixed   $args Settings section data.
	 * @since  5.0.0
	 */
	public function do_settings_section( $args ) {

		$template = str_replace( 'shortcodes_ultimate_settings_', '', $args['id'] );

		$this->the_template( 'settings/sections/' . $template, $args );

	}

	/**
	 * Display specified settings field.
	 *
	 * @param mixed   $args The field data.
	 * @since  5.0.0
	 */
	public function do_settings_field( $args ) {
		$this->the_template( 'settings/fields/' . $args['type'], $args );
	}

	/**
	 * Add help tab and set help sidebar at Add-ons page.
	 *
	 * @since  5.0.0
	 * @param WP_Screen $screen WP_Screen instance.
	 */
	public function add_help_tab( $screen ) {

		if ( ! $this->is_component_page() ) {
			return;
		}

		$screen->add_help_tab( array(
				'id'      => 'shortcodes-ultimate-settings',
				'title'   => __( 'Plugin settings', 'shortcodes-ultimate' ),
				'content' => $this->get_template( 'help/settings' ),
			) );

		$screen->set_help_sidebar( $this->get_template( 'help/sidebar' ) );

	}

	/**
	 * Callback function to sanitize checkbox value.
	 *
	 * @since  5.0.0
	 * @param mixed   $value String 'on' or null.
	 * @return string        Sanitized checkbox value ('on' or empty string '').
	 */
	public function sanitize_checkbox( $value ) {

		$value = ( ! empty( $value ) && $value === 'on' ) ? 'on' : '';

		return $value;

	}

	/**
	 * Retrieve the plugin settings data.
	 *
	 * @since    5.0.0
	 * @access   protected
	 * @return  array The plugin settings data.
	 */
	protected function get_plugin_settings() {

		if ( ! count( $this->plugin_settings ) ) {

			$this->plugin_settings[] = array(
				'id'          => 'su_option_custom-formatting',
				'type'        => 'checkbox',
				'sanitize'    => array( $this, 'sanitize_checkbox' ),
				'title'       => __( 'Custom formatting', 'shortcodes-ultimate' ),
				'description' => __( 'Enable this option if you face any problems with formatting of nested shortcodes.', 'shortcodes-ultimate' ),
			);

			$this->plugin_settings[] = array(
				'id'          => 'su_option_skip',
				'type'        => 'checkbox',
				'sanitize'    => array( $this, 'sanitize_checkbox' ),
				'title'       => __( 'Skip default settings', 'shortcodes-ultimate' ),
				'description' => __( 'Enable this option if you don\'t want the inserted shortcode to contain any settings that were not changed by you. As a result, inserted shortcodes will be much shorter.', 'shortcodes-ultimate' ),
			);

			$this->plugin_settings[] = array(
				'id'          => 'su_option_prefix',
				'type'        => 'text',
				'sanitize'    => 'sanitize_html_class',
				'title'       => __( 'Shortcodes prefix', 'shortcodes-ultimate' ),
				'description' => __( 'This prefix will be used in shortcode names. For example: set <code>MY_</code> prefix and shortcodes will look like <code>[MY_button]</code>. Please note that this setting does not change shortcodes that have been inserted earlier. Change this setting very carefully.', 'shortcodes-ultimate' ),
			);

			$this->plugin_settings[] = array(
				'id'          => 'su_option_custom-css',
				'type'        => 'css',
				'sanitize'    => 'wp_strip_all_tags',
				'title'       => __( 'Custom CSS code', 'shortcodes-ultimate' ),
				'description' => __( 'In this field you can write your custom CSS code for shortcodes. These styles will have higher priority compared to original styles of shortcodes. You can use variables in your CSS code. These variables will be replaced by respective values.', 'shortcodes-ultimate' ),
			);

		}

		return apply_filters( 'su/admin/settings', $this->plugin_settings );

	}

}
