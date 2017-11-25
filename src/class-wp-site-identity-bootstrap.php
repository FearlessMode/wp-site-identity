<?php
/**
 * WP_Site_Identity_Bootstrap class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Plugin bootstrap class to register all necessary content.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Bootstrap {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity $plugin Plugin instance.
	 */
	public function __construct( WP_Site_Identity $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Action to register the plugin's settings.
	 *
	 * @since 1.0.0
	 */
	public function action_register_settings() {
		$registry = $this->plugin->services()->get( 'setting_registry' );
		$factory  = $registry->factory();

		$owner_data = $factory->create_aggregate_setting( 'owner_data', array(
			'title'        => __( 'Owner Data', 'wp-site-identity' ),
			'description'  => __( 'Data about the owner of the website.', 'wp-site-identity' ),
			'show_in_rest' => true,
		) );

		$owner_data->factory()->create_setting( 'type', array(
			'title'        => __( 'Type', 'wp-site-identity' ),
			'description'  => __( 'Whether the owner is an organization or an individual.', 'wp-site-identity' ),
			'type'         => 'string',
			'default'      => 'individual',
			'show_in_rest' => true,
			'choices'      => array(
				'individual'   => __( 'Individual', 'wp-site-identity' ),
				'organization' => __( 'Organization', 'wp-site-identity' ),
			),
		) )->register();

		$owner_data->factory()->create_setting( 'first_name', array(
			'title'        => __( 'First Name', 'wp-site-identity' ),
			'description'  => __( 'The owner&#8217;s first name.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'last_name', array(
			'title'        => __( 'Last Name', 'wp-site-identity' ),
			'description'  => __( 'The owner&#8217;s last name.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'organization_name', array(
			'title'        => __( 'Organization Name', 'wp-site-identity' ),
			'description'  => __( 'The organization&#8217;s name.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'organization_legal_name', array(
			'title'        => __( 'Organization Legal Name', 'wp-site-identity' ),
			'description'  => __( 'The organization&#8217;s full legal name as registered.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_line_1', array(
			'title'        => __( 'Address Line 1', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_line_2', array(
			'title'        => __( 'Address Line 2 (optional)', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_city', array(
			'title'        => __( 'City', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_zip', array(
			'title'        => __( 'Zip / Postal Code', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_state', array(
			'title'        => __( 'State', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_state_abbrev', array(
			'title'        => __( 'State (Abbrev.)', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_country', array(
			'title'        => __( 'Country', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_country_abbrev', array(
			'title'        => __( 'Country (Abbrev.)', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$address_placeholders_string = implode( ', ', array(
			'{line_1}',
			'{line_2}',
			'{city}',
			'{zip}',
			'{state}',
			'{state_abbrev}',
			'{country}',
			'{country_abbrev}',
		) );

		$address_single_default = _x( '{line_1} {line_2}, {city}, {state_abbrev} {zip}', 'single line address template', 'wp-site-identity' );
		$address_multi_default  = str_replace( '<br />', PHP_EOL, _x( '{line_1}<br />{line_2}<br />{city}, {state_abbrev} {zip}', 'multiple lines address template', 'wp-site-identity' ) );

		$owner_data->factory()->create_setting( 'address_format_single', array(
			'title'        => __( 'Address Format (Single Line)', 'wp-site-identity' ),
			/* translators: %s: comma-separated list of placeholders */
			'description'  => sprintf( __( 'The address format as a single line. Allowed placeholders are: %s', 'wp_site-identity' ), $address_placeholders_string ),
			'type'         => 'string',
			'default'      => $address_single_default,
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_format_multi', array(
			'title'        => __( 'Address Format (Multiple Lines)', 'wp-site-identity' ),
			/* translators: %s: comma-separated list of placeholders */
			'description'  => sprintf( __( 'The address format as multiple lines. Allowed placeholders are: %s', 'wp_site-identity' ), $address_placeholders_string ),
			'type'         => 'string',
			'default'      => $address_multi_default,
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'email', array(
			'title'        => __( 'Email Address', 'wp-site-identity' ),
			'type'         => 'string',
			'default'      => get_option( 'admin_email' ),
			'show_in_rest' => true,
			'format'       => 'email',
		) )->register();

		$owner_data->factory()->create_setting( 'website', array(
			'title'        => __( 'Website URL', 'wp-site-identity' ),
			'type'         => 'string',
			'default'      => home_url(),
			'show_in_rest' => true,
			'format'       => 'uri',
		) )->register();

		$owner_data->factory()->create_setting( 'phone', array(
			'title'        => __( 'Phone Number (Machine Readable)', 'wp-site-identity' ),
			'description'  => __( 'The contact phone number, in machine readable format (for example <code>+1555123456</code>).', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'phone_human', array(
			'title'        => __( 'Phone Number (Human Readable)', 'wp-site-identity' ),
			'description'  => __( 'The contact phone number, in human readable format (for example <code>(555) 123 456</code>).', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->register();

		$appearance = $factory->create_aggregate_setting( 'appearance', array(
			'title'        => __( 'Appearance', 'wp-site-identity' ),
			'description'  => __( 'Appearance information representing the brand.', 'wp-site-identity' ),
			'show_in_rest' => true,
		) );

		// TODO: Register appearance sub settings.
		$appearance->register();

		/**
		 * Fires when additional settings for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Site_Identity_Setting_Registry $registry Setting registry instance.
		 */
		do_action( 'wp_site_identity_register_settings', $registry );
	}

	/**
	 * Action to register the plugin's shortcodes.
	 *
	 * @since 1.0.0
	 */
	public function action_register_shortcodes() {
		$registry = $this->plugin->services()->get( 'shortcode_registry' );
		$factory  = $registry->factory();

		foreach ( $this->plugin->services()->get( 'setting_registry' )->get_all_settings() as $aggregate_setting ) {
			if ( ! is_a( $aggregate_setting, 'WP_Site_Identity_Setting_Registry' ) ) {
				continue;
			}

			$icon = 'appearance' === $aggregate_setting->get_name() ? 'dashicons-admin-appearance' : 'dashicons-admin-users';

			foreach ( $aggregate_setting->get_all_settings() as $setting ) {
				$setting_name  = $setting->get_name();
				$setting_title = $setting->get_title();

				switch ( $setting_name ) {
					case 'address_format_single':
						$setting_name  = 'address_single';
						$setting_title = __( 'Address (Single Line)', 'wp-site-identity' );
						break;
					case 'address_format_multi':
						$setting_name  = 'address_multi';
						$setting_title = __( 'Address (Multiple Lines)', 'wp-site-identity' );
						break;
				}

				$callback_name = 'shortcode_callback_' . $aggregate_setting->get_name() . '_setting_' . $setting_name;

				$factory->create_shortcode( $setting_name, array( $this, $callback_name ), array(
					'label'         => $setting_title,
					'listItemImage' => $icon,
				) )->register();
			}
		}
	}

	/**
	 * Magic call method.
	 *
	 * Acts as a dynamic shortcode render callback.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method Method name.
	 * @param array  $args   Method arguments.
	 * @return mixed Method results.
	 */
	public function __call( $method, $args ) {
		$is_shortcode_callback = preg_match( '/^shortcode_callback_([a-z_]+)_setting_([a-z_]+)$/', $method, $matches );

		if ( $is_shortcode_callback ) {
			$aggregate_setting_name = $matches[1];
			$setting_name           = $matches[2];

			$data = call_user_func( array( $this->plugin, $aggregate_setting_name ) );

			return $data->get_as_html( $setting_name );
		}
	}

	/**
	 * Action to register the plugin's settings page in the admin.
	 *
	 * @since 1.0.0
	 */
	public function action_register_settings_page() {
		$registry = $this->plugin->services()->get( 'admin_page_registry' );
		$factory  = $registry->factory();

		$factory->create_admin_submenu_page( 'settings', array(
			'title'            => __( 'Site Identity Settings', 'wp-site-identity' ),
			'capability'       => 'manage_options',
			'render_callback'  => array( $this, 'action_render_settings_page' ),
			'handle_callback'  => array( $this, 'action_handle_settings_page' ),
			'enqueue_callback' => null,
			'menu_title'       => __( 'Site Identity', 'wp-site-identity' ),
			'parent_slug'      => 'options-general.php',
		) )->register();

		/**
		 * Fires when additional admin pages for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Site_Identity_Admin_Page_Registry $registry Admin page registry instance.
		 */
		do_action( 'wp_site_identity_register_admin_pages', $registry );
	}

	/**
	 * Action to handle a request to the plugin's settings page.
	 *
	 * @since 1.0.0
	 */
	public function action_handle_settings_page() {
		$setting_registry = $this->plugin->services()->get( 'setting_registry' );

		$factory = $this->plugin->services()->get( 'settings_form_registry' )->factory();

		$owner_data_form = $factory->create_form( $setting_registry->get_setting( 'owner_data' ) );
		$owner_data_form->set_defaults();

		$owner_data_sections = array(
			array(
				'slug'   => 'basic',
				'title'  => __( 'Basic Information', 'wp-site-identity' ),
				'fields' => array(
					'type',
					'first_name',
					'last_name',
					'organization_name',
					'organization_legal_name',
				),
			),
			array(
				'slug'   => 'address',
				'title'  => __( 'Address', 'wp-site-identity' ),
				'fields' => array(
					'address_line_1',
					'address_line_2',
					'address_city',
					'address_zip',
					'address_state',
					'address_state_abbrev',
					'address_country',
					'address_country_abbrev',
					'address_format_single',
					'address_format_multi',
				),
			),
			array(
				'slug'   => 'contact',
				'title'  => __( 'Contact Data', 'wp-site-identity' ),
				'fields' => array(
					'email',
					'website',
					'phone',
					'phone_human',
				),
			),
		);

		$section_factory = $owner_data_form->get_section_registry()->factory();
		$field_registry  = $owner_data_form->get_field_registry();

		foreach ( $owner_data_sections as $owner_data_section ) {
			$section_factory->create_section( $owner_data_section['slug'], array(
				'title' => $owner_data_section['title'],
			) )->register();

			foreach ( $owner_data_section['fields'] as $owner_data_field_slug ) {
				$field_registry->get_field( $owner_data_field_slug )->set_section_slug( $owner_data_section['slug'] );
			}
		}

		$field_registry->get_field( 'address_zip' )->set_css_classes( array() );
		$field_registry->get_field( 'address_format_multi' )->set_extra_attrs( array(
			'rows' => 4,
		) );

		foreach ( array( 'address_state_abbrev', 'address_country_abbrev' ) as $owner_data_field_slug ) {
			$field_registry->get_field( $owner_data_field_slug )->set_css_classes( array( 'small-text' ) );
		}

		foreach ( array( 'address_format_single', 'address_format_multi' ) as $owner_data_field_slug ) {
			$field_registry->get_field( $owner_data_field_slug )->set_css_classes( array( 'large-text', 'code' ) );
		}

		$owner_data_form->register();

		$appearance_form = $factory->create_form( $setting_registry->get_setting( 'appearance' ) );
		$appearance_form->set_defaults();

		// TODO: Add appearance settings sections and fields.
		$appearance_form->register();
	}

	/**
	 * Action to render the plugin's settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Admin_Page $admin_page Admin page object.
	 */
	public function action_render_settings_page( $admin_page ) {
		if ( ! is_a( $admin_page, 'WP_Site_Identity_Admin_Submenu_Page' ) || 'options-general.php' !== $admin_page->get_parent_slug() ) {
			require ABSPATH . 'wp-admin/options-head.php';
		}

		$settings_forms = $this->plugin->services()->get( 'settings_form_registry' )->get_all_forms();

		$current_slug = null;

		if ( ! empty( $settings_forms ) ) {
			// @codingStandardsIgnoreStart
			if ( isset( $_GET['tab'] ) && isset( $settings_forms[ $_GET['tab'] ] ) ) {
				$current_slug = $_GET['tab'];
			} else {
				$current_slug = key( $settings_forms );
			}
			// @codingStandardsIgnoreEnd
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( $admin_page->get_title() ); ?></h1>

			<?php if ( ! empty( $settings_forms ) ) : ?>
				<?php if ( count( $settings_forms ) > 1 ) : ?>
					<h2 class="nav-tab-wrapper" style="margin-bottom:1em;">
						<?php foreach ( $settings_forms as $slug => $settings_form ) : ?>
							<a class="<?php echo esc_attr( $slug === $current_slug ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>" href="<?php echo esc_url( add_query_arg( 'tab', $slug, $admin_page->get_url() ) ); ?>">
								<?php echo esc_html( $settings_form->get_setting_registry()->get_title() ); ?>
							</a>
						<?php endforeach; ?>
					</h2>
				<?php else : ?>
					<h2 class="screen-reader-text">
						<?php echo esc_html( $settings_forms[ $current_slug ]->get_setting_registry()->get_title() ); ?>
					</h2>
				<?php endif; ?>

				<?php $settings_forms[ $current_slug ]->render(); ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
