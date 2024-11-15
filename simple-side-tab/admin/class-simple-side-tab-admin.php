<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Simple_Side_Tab_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Plugin settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $settings    Plugin settings.
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $settings ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->settings = $settings;
	}




    /**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// return if not plugin settings page 
		if ( ! $this->is_settings_page() ) {
			return;
		}

        // add the WordPress color picker css file
		wp_enqueue_style( 'wp-color-picker' );

		// include custom CSS for our settings page
        wp_enqueue_style( $this->plugin_name, SIMPLE_SIDE_TAB_URI . '/admin/css/simple-side-tab-admin.css', array(), $this->version, 'all' );
	}




    /**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// return if not plugin settings page 
		if ( ! $this->is_settings_page() ) {
			return;
		}

		// include our custom jQuery file with WordPress Color Picker dependency
		wp_enqueue_script( $this->plugin_name, SIMPLE_SIDE_TAB_URI . '/admin/js/simple-side-tab-admin.js', array( 'wp-color-picker' ), $this->version, false );
	}




    // action function to add a new submenu under Settings
    public function admin_menu() {

		add_options_page(__('Simple Side Tab Option Settings', 'simple-side-tab' ), 'Simple Side Tab', 'manage_options', SIMPLE_SIDE_TAB_OPTIONS_PAGE, array( $this, 'render_settings_page') );
    }




    // Use Settings API to whitelist options
    public function settings_api_init() {

        register_setting(
			'rum_sst_option_group',
			'rum_sst_plugin_options',
			[
				'sanitize_callback' => [ $this, 'sanitize_plugin_options' ]
			]
		);
    }




    // Build array of links for rendering in installed plugins list
    public function plugin_actions( $links ) {

        $settings = array( 'settings' => '<a href="options-general.php?page='.SIMPLE_SIDE_TAB_OPTIONS_PAGE.'">' . __('Settings', 'simple-side-tab') . '</a>' );
        $support  = array( 'support'  => '<a href="https://wordpress.org/support/plugin/simple-side-tab/" target="_blank">' . __('Support', 'simple-side-tab') . '</a>' );
        $actions  = array_merge( $settings, $support, $links );

        return $actions;
    }




	// Display and plugin settings page
	public function render_settings_page() {

		require_once SIMPLE_SIDE_TAB_DIR . '/admin/partials/settings-page.php';
	}




	public function is_settings_page() {

		// get the current screen
		$screen = get_current_screen();

		// check to see if we are on our settings page
		if ( $screen->id == SIMPLE_SIDE_TAB_SETTINGS_PAGE_ID ) {
			return true;
		}
	}




	public function require_fields_notice() {

		// return if not plugin settings page 
		if ( ! $this->is_settings_page() ) {
			return;
		}

		if ( ! $this->settings->is_renderable() ) {
			echo '<div class="error notice">';
			echo '	<p>' . __( 'Your tab will not display without the required fields.', 'simple-side-tab' ) . '</p>';
			echo '</div>';
		}
	}




    /**
     * Sanitize the plugin options.
     *
     * @param array $input The unsanitized options input.
     * @return array The sanitized options.
     */
    public function sanitize_plugin_options( $input ) {
        $sanitized = [];

		$valid_fonts = [
			'Arial, sans-serif',
			'Georgia, serif',
			'"Helvetica Neue", Helvetica, sans-serif',
			'"Lucida Sans Unicode", "Lucida Grande", sans-serif',
			'Tahoma, sans-serif',
			'"Trebuchet MS", sans-serif',
			'Verdana, sans-serif'
		];

		$valid_positions = [ 'left', 'right' ];

		if ( isset( $input['text_for_tab'] ) ) {
            $sanitized['text_for_tab'] = sanitize_text_field( $input['text_for_tab'] );
        }

        if ( isset( $input['tab_url'] ) ) {
            $sanitized['tab_url'] = sanitize_url( $input['tab_url'] );
        }

		if ( isset( $input['font_family'] ) && in_array( $input['font_family'], $valid_fonts, true ) ) {
			$sanitized['font_family'] = $input['font_family'];
		} else {
			$sanitized['font_family'] = 'Arial, sans-serif';
		}

		if ( isset( $input['font_weight_bold'] ) && $input['font_weight_bold'] === '1' ) {
			$sanitized['font_weight_bold'] = '1';
		}

		if ( isset( $input['text_shadow'] ) && $input['text_shadow'] === '1' ) {
			$sanitized['text_shadow'] = '1';
		}

		if ( isset( $input['target_blank'] ) && $input['target_blank'] === '1' ) {
			$sanitized['target_blank'] = '1';
		}

		if ( isset( $input['left_right'] ) && in_array( $input['left_right'], $valid_positions, true ) ) {
			$sanitized['left_right'] = $input['left_right'];
		} else {
			$sanitized['left_right'] = 'left';
		}

		if ( isset( $input['pixels_from_top'] ) && is_numeric( $input['pixels_from_top'] ) && absint( $input['pixels_from_top'] ) > 0 ) {
			$sanitized['pixels_from_top'] = absint( $input['pixels_from_top'] );
		} else {
			$sanitized['pixels_from_top'] = 350;
		}

		if ( isset( $input['text_color'] ) ) {
			$sanitized['text_color'] = sanitize_hex_color( $input['text_color'] );
			if ( ! $sanitized['text_color'] ) {
				$sanitized['text_color'] = '#ffffff';
			}
		} else {
			$sanitized['text_color'] = '#ffffff';
		}

		if ( isset( $input['tab_color'] ) ) {
			$sanitized['tab_color'] = sanitize_hex_color( $input['tab_color'] );
			if ( ! $sanitized['tab_color'] ) {
				$sanitized['tab_color'] = '#a0244e';
			}
		} else {
			$sanitized['tab_color'] = '#a0244e';
		}

		if ( isset( $input['hover_color'] ) ) {
			$sanitized['hover_color'] = sanitize_hex_color( $input['hover_color'] );
			if ( ! $sanitized['hover_color'] ) {
				$sanitized['hover_color'] = '#a4a4a4';
			}
		} else {
			$sanitized['hover_color'] = '#a4a4a4';
		}

		return $sanitized;
    }
}
