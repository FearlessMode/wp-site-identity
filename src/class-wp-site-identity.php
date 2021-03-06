<?php
/**
 * WP_Site_Identity class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Plugin main class.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity {

	/**
	 * Plugin main file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $main_file;

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $version;

	/**
	 * Service container.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Service_Container
	 */
	private $services;

	/**
	 * Plugin settings bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Settings
	 */
	private $bootstrap_settings;

	/**
	 * Plugin shortcodes bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Shortcodes
	 */
	private $bootstrap_shortcodes;

	/**
	 * Plugin admin pages bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Admin_Pages
	 */
	private $bootstrap_admin_pages;

	/**
	 * Plugin Customizer content bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Customizer
	 */
	private $bootstrap_customizer;

	/**
	 * Owner data access point.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Owner_Data
	 */
	private $owner_data;

	/**
	 * Appearance access point.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Data
	 */
	private $appearance;

	/**
	 * Constructor.
	 *
	 * Sets the plugin file and version and instantiates the service container.
	 *
	 * @since 1.0.0
	 *
	 * @param string $main_file Plugin main file.
	 * @param string $version   Plugin version.
	 */
	public function __construct( $main_file, $version ) {
		$this->main_file = $main_file;
		$this->version   = $version;
		$this->services  = new WP_Site_Identity_Service_Container();

		$this->bootstrap_settings    = new WP_Site_Identity_Bootstrap_Settings( $this );
		$this->bootstrap_shortcodes  = new WP_Site_Identity_Bootstrap_Shortcodes( $this );
		$this->bootstrap_admin_pages = new WP_Site_Identity_Bootstrap_Admin_Pages( $this );
		$this->bootstrap_customizer  = new WP_Site_Identity_Bootstrap_Customizer( $this );

		$this->register_services();
	}

	/**
	 * Gets the full path for a relative path within the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $relative_path Relative path to a plugin file or directory.
	 * @return string Full path.
	 */
	public function path( $relative_path ) {
		return path_join( plugin_dir_path( $this->main_file ), $relative_path );
	}

	/**
	 * Gets the full URL for a relative path within the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $relative_path Relative path to a plugin file or directory.
	 * @return string Full URL.
	 */
	public function url( $relative_path ) {
		return path_join( plugin_dir_url( $this->main_file ), $relative_path );
	}

	/**
	 * Gets the plugin version number.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin version number.
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Gets the plugin's service container.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Service_Container Service container instance.
	 */
	public function services() {
		return $this->services;
	}

	/**
	 * Gets the owner data access point.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Owner_Data Owner data access point.
	 */
	public function owner_data() {
		if ( ! isset( $this->owner_data ) ) {
			$aggregate_setting = $this->services->get( 'setting_registry' )->get_setting( 'owner_data' );

			$this->owner_data = new WP_Site_Identity_Owner_Data( 'wpsi_', $aggregate_setting );
		}
		return $this->owner_data;
	}

	/**
	 * Gets the appearance access point.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Data Appearance access point.
	 */
	public function appearance() {
		if ( ! isset( $this->appearance ) ) {
			$aggregate_setting = $this->services->get( 'setting_registry' )->get_setting( 'appearance' );

			$this->appearance = new WP_Site_Identity_Data( 'wpsi_', $aggregate_setting );
		}
		return $this->appearance;
	}

	/**
	 * Adds all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks() {
		add_action( 'init', array( $this->bootstrap_settings, 'action_init' ), 1, 0 );
		add_action( 'init', array( $this->bootstrap_shortcodes, 'action_init' ), 10, 0 );
		add_action( 'admin_menu', array( $this->bootstrap_admin_pages, 'action_admin_menu' ), 1, 0 );
		add_action( 'customize_register', array( $this->bootstrap_customizer, 'action_customize_register' ), 10, 1 );
		add_action( 'customize_preview_init', array( $this->bootstrap_customizer, 'action_customize_preview_init' ), 10, 0 );
	}

	/**
	 * Removes all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function remove_hooks() {
		remove_action( 'init', array( $this->bootstrap_settings, 'action_init' ), 1 );
		remove_action( 'init', array( $this->bootstrap_shortcodes, 'action_init' ), 10 );
		remove_action( 'admin_menu', array( $this->bootstrap_admin_pages, 'action_admin_menu' ), 1 );
		remove_action( 'customize_register', array( $this->bootstrap_customizer, 'action_customize_register' ), 10 );
		remove_action( 'customize_preview_init', array( $this->bootstrap_customizer, 'action_customize_preview_init' ), 10 );
	}

	/**
	 * Registers all services used by the plugin.
	 *
	 * @since 1.0.0
	 */
	private function register_services() {

		// Settings.
		$this->services->register( 'setting_feedback_handler', 'WP_Site_Identity_Setting_Feedback_Handler' );
		$this->services->register( 'setting_validator', 'WP_Site_Identity_Setting_Validator' );
		$this->services->register( 'setting_sanitizer', 'WP_Site_Identity_Setting_Sanitizer' );
		$this->services->register( 'setting_registry', 'WP_Site_Identity_Standard_Setting_Registry', array(
			'wpsi_',
			'site_identity',
			new WP_Site_Identity_Service_Reference( 'setting_feedback_handler' ),
			new WP_Site_Identity_Service_Reference( 'setting_validator' ),
			new WP_Site_Identity_Service_Reference( 'setting_sanitizer' ),
		) );

		// Admin pages.
		$this->services->register( 'admin_page_registry', 'WP_Site_Identity_Standard_Admin_Page_Registry', array(
			'wpsi_',
		) );

		// Settings forms.
		$this->services->register( 'settings_form_registry', 'WP_Site_Identity_Standard_Settings_Form_Registry' );

		// Shortcodes.
		$this->services->register( 'shortcode_registry', 'WP_Site_Identity_Standard_Shortcode_Registry', array(
			'wpsi_',
		) );
	}
}
