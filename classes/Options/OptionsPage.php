<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\Options;

use Timber\Timber;

/**
 * Class for creating an options page.
 *
 * Usage:
 *  $optionsPage = (new OptionsPageBuilder())
 *  ->setMenuTitle('Tahti Backend')
 *  ->setPageTitle('Tahti Backend')
 *  ->setMenuSlug('tahti-backend')
 *  ->setCapability('manage_options')
 *  ->setTemplate('options-page.twig')
 */
class OptionsPage {
	/**
	 * The menu title to be used for the page.
	 *
	 * @var string
	 */
	private string $menu_title;

	/**
	 * The title to be used for the page.
	 *
	 * @var string
	 */
	private string $page_title;

	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 *
	 * @var string
	 */
	private string $menu_slug;

	/**
	 * A capability required for this menu to be displayed to the user.
	 *
	 * @var string
	 */
	private string $capability;

	/**
	 * The template to be used for the page.
	 *
	 * @var string
	 */
	private string $template;

	/**
	 * The settings fields to be used for the page.
	 *
	 * @var array
	 */
	private array $settings_fields;

	/**
	 * The position in the menu order this one should appear.
	 *
	 * @var int
	 */
	private int $position;
	/**
	 * The URL or Dashicons name to the icon to be used for this menu.
	 *
	 * @var string
	 */
	private string $icon_url;

	/**
	 * The assets to be used for the page.
	 *
	 * @var array
	 */
	private array $assets;

	/**
	 * The parent menu slug.
	 *
	 * @var string
	 */
	private string $parent;

	/**
	 * Initializes the options page.
	 *
	 * @param string $parent The parent menu slug.
	 * @param string $menu_title The menu title to be used for the page.
	 * @param string $page_title The title to be used for the page.
	 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu).
	 * @param string $capability A capability required for this menu to be displayed to the user.
	 * @param string $template The template to be used for the page.
	 * @param array  $settings_fields The settings fields to be used for the page.
	 * @param int    $position The position in the menu order this one should appear.
	 * @param string $icon_url The URL or Dashicons name to the icon to be used for this menu.
	 * @param array  $assets The assets to be used for the page.
	 */
	public function __construct(
		string $parent_slug,
		string $menu_title,
		string $page_title,
		string $menu_slug,
		string $capability,
		string $template,
		array $settings_fields,
		int $position = 99,
		string $icon_url = '',
		array $assets = array() ) {
		$this->parent          = $parent_slug;
		$this->menu_title      = $menu_title;
		$this->page_title      = $page_title;
		$this->menu_slug       = $menu_slug;
		$this->capability      = $capability;
		$this->template        = $template;
		$this->settings_fields = $settings_fields;
		$this->position        = $position;
		$this->icon_url        = $icon_url;
		$this->assets          = $assets;
	}

	/**
	 * Gets all the options for this page.
	 *
	 * @return array
	 */
	private function get_options(): array {
		$options = array();
		foreach ( $this->settings_fields as $field ) {
			$option_name             = $field['option_name'];
			$options[ $option_name ] = array(
				'value' => Option::get( $option_name, '' ),
				'label' => $field['option_label'],
			);
		}
		return $options;
	}

	/**
	 * Handles validation and saving of the options.
	 *
	 * @param array $input The input data.
	 *
	 * @return array
	 */
	final protected function validate_options( array $input ): array {
		$validated_options = array();
		foreach ( $this->settings_fields as $field ) {
			$option_name = $field['option_name'];

			// Validate and sanitize the input data as needed.
			if ( isset( $input[ $option_name ] ) ) {
				if ( $field['sanitize_callback'] && is_callable( $field['sanitize_callback'] ) ) {
					$value = call_user_func( $field['sanitize_callback'], ( $input[ $option_name ] ) );
				} else {
					$value = sanitize_text_field( $input[ $option_name ] );
				}
				$validated_options[ $option_name ] = $value;
			}
		}

		// Save the validated options.
		foreach ( $validated_options as $option_name => $value ) {
			Option::set( $option_name, $value );
		}

		return $validated_options;
	}

	/**
	 * Registers the options page.
	 *
	 * @return void
	 */
	final public function register(): void {
		add_action( 'admin_menu', array( $this, 'registers_option_page' ) );
		add_action( 'admin_init', array( $this, 'save_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 10, 1 );
	}

	/**
	 * Handles the registration of the page.
	 *
	 * @return void
	 */
	final public function registers_option_page(): void {
		if ( empty( $this->parent ) ) {
			add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				array( $this, 'render_page' ),
				$this->icon_url,
				$this->position
			);
		} else {
			add_submenu_page(
				$this->parent,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				array( $this, 'render_page' ),
				$this->position
			);
		}
	}

	/**
	 * Enqueues the assets for the page.
	 *
	 * @param string $hook The current admin page.
	 *
	 * @return void
	 */
	final public function enqueue_assets( string $hook ): void {
		if ( ! str_contains( $hook, $this->menu_slug ) ) {
			return;
		}

		if ( isset( $this->assets['js'] ) && is_array( $this->assets['js'] ) ) {
			foreach ( $this->assets['js'] as $asset ) {
				wp_enqueue_script(
					$asset['handle'],
					$asset['src'],
					$asset['deps'],
					$asset['ver'],
					$asset['in_footer']
				);
			}
		}

		if ( isset( $this->assets['css'] ) && is_array( $this->assets['css'] ) ) {
			foreach ( $this->assets['css'] as $asset ) {
				wp_enqueue_style(
					$asset['handle'],
					$asset['src'],
					$asset['deps'] ?? array(),
					$asset['ver'] ?? '',
					$asset['media'] ?? 'all'
				);
			}
		}
	}


	/**
	 * Renders the options page.
	 *
	 * @return void
	 */
	final public function render_page(): void {
		$options = $this->get_options();

		$action_url = add_query_arg(
			array(
				'page'                      => $this->menu_slug,
				"{$this->menu_slug}_action" => 1,
			),
			admin_url( 'admin.php' )
		);

		$api_nonce = wp_create_nonce( 'wp_rest' );

		/**
		 * Filters the context for the options page.
		 * This hook can be used to add additional data to the context.
		 *
		 * @param array $context The context for the options page.
		 * @param OptionsPage $options_page The options page object.
		 */
		$context = apply_filters(
			'timber_context_' . $this->menu_slug,
			array(
				'page_title' => $this->page_title,
				'menu_title' => $this->menu_title,
				'menu_slug'  => $this->menu_slug,
				'action_url' => $action_url,
				'options'    => $options,
				'errors'     => get_settings_errors(),
				'api_nonce'  => $api_nonce,
				'success'    => isset( $_GET['success'] ),
			),
			$this
		);

		Timber::render(
			$this->template,
			$context
		);
	}

	/**
	 * Handles saving the options.
	 *
	 * @return void
	 */
	final public function save_options(): void {
		// This is the URL we will redirect the user to after saving the options.
		$url = add_query_arg(
			array(
				'page' => $this->menu_slug,
			),
			admin_url( 'admin.php' )
		);

		/**
		 * Fires before the options are saved.
		 * This hook can be used to handle certain actions before we handle them and attempt to save the options
		 *
		 * @param string $url The url to redirect to after saving the options.
		 * @param OptionsPage $options_page The options page object.
		 */
		do_action( 'save_action_' . $this->menu_slug, $url, $this );

		if ( ! isset( $_GET[ "{$this->menu_slug}_action" ] ) ) {
			return;
		}
		if ( isset( $_POST[ "{$this->menu_slug}_nonce" ] )
			&& wp_verify_nonce( $_POST[ "{$this->menu_slug}_nonce" ], $this->menu_slug ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$input = wp_unslash( $_POST );

			// Validate and sanitize the input data as needed.
			$validate_options = $this->validate_options( $input );

			// Save the validated options.
			foreach ( $validate_options as $option_name => $value ) {
				Option::set( $option_name, $value );
			}

			$url = add_query_arg(
				array(
					'success' => true,
				),
				$url
			);

			// Redirect to avoid form resubmission.
			wp_safe_redirect( $url );
			exit;
		}
	}

	/**
	 * Get the menu slug, which is used for many things.
	 *
	 * @return string
	 */
	final public function get_menu_slug(): string {
		return $this->menu_slug;
	}
}
