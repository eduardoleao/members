<?php
/**
 * Handles the general settings view.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin\Settings\Views;

use Members\Util\Options;

class General extends View {

	public function name() {
		return 'general';
	}

	public function label() {
		return __( 'General' );
	}

	public function register() {

		// Get the current plugin settings w/o the defaults.
		$this->settings = get_option( 'members_settings' );

		// Register the setting.
		register_setting( 'members_settings', 'members_settings', [ $this, 'validateSettings' ] );
	}

	protected function addFields() {
		/* === Settings Sections === */

		// Add settings sections.
		add_settings_section( 'roles_caps',          esc_html__( 'Roles and Capabilities', 'members' ), [ $this, 'sectionRoleCaps' ], 'members-settings' );
		add_settings_section( 'content_permissions', esc_html__( 'Content Permissions',    'members' ), '__return_false',             'members-settings' );
		add_settings_section( 'sidebar_widgets',     esc_html__( 'Sidebar Widgets',        'members' ), '__return_false',             'members-settings' );
		add_settings_section( 'private_site',        esc_html__( 'Private Site',           'members' ), '__return_false',             'members-settings' );

		/* === Settings Fields === */

		// Role manager fields.
		$sections = [
			'roles_caps' => [
				'enable_role_manager' => [
					'label'    => __( 'Role Manager' ),
					'callback' => 'fieldEnableRoleManager'
				],
				'enable_multi_roles' => [
					'label'    => __( 'Multiple User Roles' ),
					'callback' => 'fieldEnableMultiRoles'
				],
				'explicit_denied_caps' => [
					'label'    => __( 'Capabilities' ),
					'callback' => 'fieldExplicitDeniedCaps'
				]
			],
			'content_permissions' => [
				'enable_content_permissions' => [
					'label'    => __( 'Enable Permissions', 'members' ),
					'callback' => 'fieldEnableContentPermissions'
				],
				'content_permissions_error' => [
					'label'    => __( 'Error Message', 'members' ),
					'callback' => 'fieldContentPermissionsError'
				]
			],
			'sidebar_widgets' => [
				'enable_content_permissions' => [
					'label'    => __( 'Enable Permissions', 'members' ),
					'callback' => 'fieldEnableContentPermissions'
				],
				'content_permissions_error' => [
					'label'    => __( 'Error Message', 'members' ),
					'callback' => 'fieldContentPermissionsError'
				]
			]
		];

		foreach ( $sections as $section => $fields ) {

			foreach ( $fields as $name => $args ) {
				add_settings_field(
					$name,
					$args['label'],
					[ $this, $args['callback'] ],
					'members-settings',
					$section
				);
			}
		}

	//	add_settings_field( 'enable_role_manager',  esc_html__( 'Role Manager',        'members' ), [ $this, 'fieldEnableRoleManager'  ], 'members-settings', 'roles_caps' );
	//	add_settings_field( 'enable_multi_roles',   esc_html__( 'Multiple User Roles', 'members' ), [ $this, 'fieldEnableMultiRoles'   ], 'members-settings', 'roles_caps' );
	//	add_settings_field( 'explicit_denied_caps', esc_html__( 'Capabilities',        'members' ), [ $this, 'fieldExplicitDeniedCaps' ], 'members-settings', 'roles_caps' );

		// Content permissions fields.
	//	add_settings_field( 'enable_content_permissions', esc_html__( 'Enable Permissions', 'members' ), [ $this, 'fieldEnableContentPermissions' ], 'members-settings', 'content_permissions' );
	//	add_settings_field( 'content_permissions_error',  esc_html__( 'Error Message',      'members' ), [ $this, 'fieldContentPermissionsError'  ], 'members-settings', 'content_permissions' );

		// Widgets fields.
	//	add_settings_field( 'widget_login', esc_html__( 'Login Widget', 'members' ), [ $this, 'fieldWidgetLogin' ], 'members-settings', 'sidebar_widgets' );
	//	add_settings_field( 'widget_users', esc_html__( 'Users Widget', 'members' ), [ $this, 'fieldWidgetUsers' ], 'members-settings', 'sidebar_widgets' );

		// Private site fields.
		add_settings_field( 'enable_private_site', esc_html__( 'Enable Private Site', 'members' ), [ $this, 'fieldEnablePrivateSite' ], 'members-settings', 'private_site' );
		add_settings_field( 'private_rest_api',    esc_html__( 'REST API',            'members' ), [ $this, 'fieldPrivateRestApi'    ], 'members-settings', 'private_site' );
		add_settings_field( 'enable_private_feed', esc_html__( 'Disable Feed',        'members' ), [ $this, 'fieldEnablePrivateFeed' ], 'members-settings', 'private_site' );
		add_settings_field( 'private_feed_error',  esc_html__( 'Feed Error Message',  'members' ), [ $this, 'fieldPrivateFeedError'  ], 'members-settings', 'private_site' );
	}

	public function boot() {

		$this->addFields();

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function enqueue() {
		wp_enqueue_script( 'members-settings' );
	}

	/**
	 * Validates the plugin settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array  $input
	 * @return array
	 */
	function validateSettings( $settings ) {

		// Validate true/false checkboxes.
		$settings['role_manager']         = ! empty( $settings['role_manager'] );
		$settings['explicit_denied_caps'] = ! empty( $settings['explicit_denied_caps'] );
		$settings['show_human_caps']      = ! empty( $settings['show_human_caps'] );
		$settings['multi_roles']          = ! empty( $settings['multi_roles'] );
		$settings['content_permissions']  = ! empty( $settings['content_permissions'] );
		$settings['login_form_widget']    = ! empty( $settings['login_form_widget'] );
		$settings['users_widget']         = ! empty( $settings['users_widget'] );
		$settings['private_blog']         = ! empty( $settings['private_blog'] );
		$settings['private_rest_api']     = ! empty( $settings['private_rest_api'] );
		$settings['private_feed']         = ! empty( $settings['private_feed'] );

		// Kill evil scripts.
		$settings['content_permissions_error'] = stripslashes( wp_filter_post_kses( addslashes( $settings['content_permissions_error'] ) ) );
		$settings['private_feed_error']        = stripslashes( wp_filter_post_kses( addslashes( $settings['private_feed_error']        ) ) );

		// Return the validated/sanitized settings.
		return $settings;
	}

	/**
	 * Role/Caps section callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function sectionRoleCaps() { ?>

		<p class="description">
			<?php esc_html_e( 'Your roles and capabilities will not revert back to their previous settings after deactivating or uninstalling this plugin, so use this feature wisely.', 'members' ); ?>
		</p>
	<?php }

	/**
	 * Role manager field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldEnableRoleManager() { ?>

		<label>
			<input type="checkbox" name="members_settings[role_manager]" value="true" <?php checked( Options::roleManagerEnabled() ); ?> />
			<?php esc_html_e( 'Enable the role manager.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Explicit denied caps field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldExplicitDeniedCaps() { ?>

		<fieldset>

			<p>
				<label>
					<input type="checkbox" name="members_settings[explicit_denied_caps]" value="true" <?php checked( Options::explicitlyDenyCaps() ); ?> />
					<?php esc_html_e( 'Denied capabilities should always overrule granted capabilities.', 'members' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" name="members_settings[show_human_caps]" value="true" <?php checked( Options::showHumanCaps() ); ?> />
					<?php esc_html_e( 'Show human-readable capabilities when possible.', 'members' ); ?>
				</label>
			</p>

		</fieldset>
	<?php }

	/**
	 * Multiple roles field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldEnableMultiRoles() { ?>

		<label>
			<input type="checkbox" name="members_settings[multi_roles]" value="true" <?php checked( Options::multipleUserRolesEnabled() ); ?> />
			<?php esc_html_e( 'Allow users to be assigned more than a single role.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable content permissions field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldEnableContentPermissions() { ?>

		<label>
			<input type="checkbox" name="members_settings[content_permissions]" value="true" <?php checked( Options::contentPermissionsEnabled() ); ?> />
			<?php esc_html_e( 'Enable the content permissions feature.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Content permissions error message field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldContentPermissionsError() {

		wp_editor(
			Options::setting( 'content_permissions_error' ),
			'members_settings_content_permissions_error',
			array(
				'textarea_name'    => 'members_settings[content_permissions_error]',
				'drag_drop_upload' => true,
				'editor_height'    => 250
			)
		);
	}

	/**
	 * Login widget field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldWidgetLogin() { ?>

		<label>
			<input type="checkbox" name="members_settings[login_form_widget]" value="true" <?php checked( Options::loginWidgetEnabled() ); ?> />
			<?php esc_html_e( 'Enable the login form widget.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Uers widget field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldWidgetUsers() { ?>

		<label>
			<input type="checkbox" name="members_settings[users_widget]" value="true" <?php checked( Options::usersWidgetEnabled() ); ?> />
			<?php esc_html_e( 'Enable the users widget.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable private site field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldEnablePrivateSite() { ?>

		<label>
			<input type="checkbox" name="members_settings[private_blog]" value="true" <?php checked( Options::isPrivateBlog() ); ?> />
			<?php esc_html_e( 'Redirect all logged-out users to the login page before allowing them to view the site.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable private REST API field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldPrivateRestApi() { ?>

		<label>
			<input type="checkbox" name="members_settings[private_rest_api]" value="true" <?php checked( Options::isPrivateRestApi() ); ?> />
			<?php esc_html_e( 'Require authentication for access to the REST API.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable private feed field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldEnablePrivateFeed() { ?>

		<label>
			<input type="checkbox" name="members_settings[private_feed]" value="true" <?php checked( Options::isPrivateFeed() ); ?> />
			<?php esc_html_e( 'Show error message for feed items.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Private feed error message field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function fieldPrivateFeedError() {

		wp_editor(
			Options::setting( 'private_feed_error' ),
			'members_settings_private_feed_error',
			array(
				'textarea_name'    => 'members_settings[private_feed_error]',
				'drag_drop_upload' => true,
				'editor_height'    => 250
			)
		);
	}

	/**
	 * Renders the settings page.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function template() { ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'members_settings' ); ?>
			<?php do_settings_sections( 'members-settings' ); ?>
			<?php submit_button( esc_attr__( 'Update Settings', 'members' ), 'primary' ); ?>
		</form>

	<?php }

	/**
	 * Adds help tabs.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function addHelpTabs() {

		// Get the current screen.
		$screen = get_current_screen();

		// Roles/Caps help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'roles-caps',
				'title'    => esc_html__( 'Role and Capabilities', 'members' ),
				'callback' => [ $this, 'help_tab_roles_caps' ]
			)
		);

		// Content Permissions help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'content-permissions',
				'title'    => esc_html__( 'Content Permissions', 'members' ),
				'callback' => [ $this, 'help_tab_content_permissions' ]
			)
		);

		// Widgets help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'sidebar-widgets',
				'title'    => esc_html__( 'Sidebar Widgets', 'members' ),
				'callback' => [ $this, 'help_tab_sidebar_widgets' ]
			)
		);

		// Private Site help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'private-site',
				'title'    => esc_html__( 'Private Site', 'members' ),
				'callback' => [ $this, 'help_tab_private_site' ]
			)
		);

		// Set the help sidebar.
		$screen->set_help_sidebar( members_get_help_sidebar_text() );
	}

	/**
	 * Displays the roles/caps help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_roles_caps() { ?>

		<p>
			<?php esc_html_e( 'The role manager allows you to manage roles on your site by giving you the ability to create, edit, and delete any role. Note that changes to roles do not change settings for the Members plugin. You are literally changing data in your WordPress database. This plugin feature merely provides an interface for you to make these changes.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'The multiple user roles feature allows you to assign more than one role to each user from the edit user screen.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Tick the checkbox for denied capabilities to always take precedence over granted capabilities when there is a conflict. This is only relevant when using multiple roles per user.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Tick the checkbox to show human-readable capabilities when possible. Note that custom capabilities and capabilities from third-party plugins will show the machine-readable capability name unless they are registered.', 'members' ); ?>
		</p>
	<?php }

	/**
	 * Displays the content permissions help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_content_permissions() { ?>

		<p>
			<?php printf( esc_html__( "The content permissions features adds a meta box to the edit post screen that allows you to grant permissions for who can read the post content based on the user's role. Only users of roles with the %s capability will be able to use this component.", 'members' ), '<code>restrict_content</code>' ); ?>
		</p>
	<?php }

	/**
	 * Displays the sidebar widgets help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_sidebar_widgets() { ?>

		<p>
			<?php esc_html_e( "The sidebar widgets feature adds additional widgets for use in your theme's sidebars.", 'members' ); ?>
		</p>
	<?php }

	/**
	 * Displays the private site help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_private_site() { ?>

		<p>
			<?php esc_html_e( 'The private site feature redirects all users who are not logged into the site to the login page, creating an entirely private site. You may also replace your feed content with a custom error message.', 'members' ); ?>
		</p>
	<?php }
}