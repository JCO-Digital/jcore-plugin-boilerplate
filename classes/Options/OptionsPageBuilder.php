<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName Squiz.Commenting.ClassComment.Missing

namespace JcoreBroiler\Options;

/**
 * Class OptionsPageBuilder, used to build options pages.
 *
 * Usage:
 * $optionsPage = (new OptionsPageBuilder())
 * ->set_menu_title('Tahti Backend')
 * ->set_page_title('Tahti Backend')
 * ->set_menu_slug('tahti-backend')
 * ->set_capability('manage_options')
 * ->set_template('options-page.twig')
 * ->build();
 *
 * @package JcoreBroiler\Options
 */
class OptionsPageBuilder {
	/**
	 * The menu title to be used for the page.
	 *
	 * @var string|null
	 */
	private ?string $menu_title;

	/**
	 * The title to be used for the page.
	 *
	 * @var string|null
	 */
	private ?string $page_title;

	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 *
	 * @var string|null
	 */
	private ?string $menu_slug;

	/**
	 * A capability required for this menu to be displayed to the user.
	 *
	 * @var string|null
	 */
	private ?string $capability;

	/**
	 * The template to be used for the page.
	 *
	 * @var string|null
	 */
	private ?string $template;

	/**
	 * The settings fields to be used for the page.
	 *
	 * @var array
	 */
	private array $settings_fields = array();

	/**
	 * The position of the page in the menu.
	 *
	 * @var int
	 */
	private int $position = 99;

	/**
	 * The icon to be used for the page.
	 *
	 * @var string
	 */
	private string $icon_url = '';

	/**
	 * The assets to be used for the page.
	 *
	 * @var array|null
	 */
	private ?array $assets;


	/**
	 * Sets the menu title to the options page currently being built.
	 *
	 * @param string $menu_title The menu title to be used for the page.
	 *
	 * @return OptionsPageBuilder
	 */
	final public function set_menu_title( string $menu_title ): OptionsPageBuilder {
		$this->menu_title = $menu_title;
		return $this;
	}

	/**
	 * Sets the page title to the options page currently being built.
	 *
	 * @param string $page_title The title to be used for the page.
	 *
	 * @return OptionsPageBuilder
	 */
	final public function set_page_title( string $page_title ): OptionsPageBuilder {
		$this->page_title = $page_title;
		return $this;
	}

	/**
	 * Sets the menu slug to the options page currently being built.
	 *
	 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu).
	 *
	 * @return $this
	 */
	final public function set_menu_slug( string $menu_slug ): OptionsPageBuilder {
		$this->menu_slug = $menu_slug;
		return $this;
	}

	/**
	 * Sets the required capability to the options page currently being built.
	 *
	 * @param string $capability A capability required for this menu to be displayed to the user.
	 *
	 * @return $this
	 *@see https://wordpress.org/support/article/roles-and-capabilities/
	 */
	final public function set_capability( string $capability ): OptionsPageBuilder {
		$this->capability = $capability;
		return $this;
	}

	/**
	 * Sets the template to the options page currently being built.
	 *
	 * @param string $template The name of the template. (Should be in the views folder).
	 *
	 * @return OptionsPageBuilder
	 */
	final public function set_template( string $template ): OptionsPageBuilder {
		$this->template = $template;
		return $this;
	}

	/**
	 * Adds a setting field to the options page currently being built.
	 *
	 * @param string $option_name The name of the option.
	 * @param string $option_label The label of the option.
	 * @param string $callback The callback function to sanitize the option.
	 *
	 * @return OptionsPageBuilder
	 */
	final public function add_setting_field( $option_name, $option_label, $callback = 'sanitize_text_field' ): OptionsPageBuilder {
		$this->settings_fields[] = array(
			'option_name'       => sanitize_title( $option_name ),
			'option_label'      => $option_label,
			'sanitize_callback' => $callback,
		);
		return $this;
	}

	/**
	 * Adds a stylesheet to the options page currently being built.
	 *
	 * @param array $css_arr The name of the css file to include.
	 *
	 * @return OptionsPageBuilder
	 */
	final public function add_stylesheet( array $css_arr ): OptionsPageBuilder {
		$this->assets['css'][] = $css_arr;
		return $this;
	}

	/**
	 * Adds a script to the options page currently being built.
	 *
	 * @param array $js_arr The name of the js file to include.
	 *
	 * @return OptionsPageBuilder
	 */
	final public function add_script( array $js_arr ): OptionsPageBuilder {
		$this->assets['js'][] = $js_arr;
		return $this;
	}

	/**
	 * Set the menu position of the options page currently being built.
	 *
	 * @param int $position The position of the page in the menu.
	 *
	 * @return OptionsPageBuilder
	 */
	final public function set_position( int $position ): OptionsPageBuilder {
		$this->position = $position;
		return $this;
	}

	/**
	 * Set the icon url of the options page currently being built.
	 *
	 * @param string $icon_url The icon URL or Dashicons name to use.
	 *
	 * @return OptionsPageBuilder
	 */
	final public function set_icon_url( string $icon_url ): OptionsPageBuilder {
		$this->icon_url = $icon_url;
		return $this;
	}

	/**
	 * Handles the creation of the options page class.
	 *
	 * @return OptionsPage
	 */
	final public function build(): OptionsPage {
		$options_page = new OptionsPage(
			$this->menu_title,
			$this->page_title,
			$this->menu_slug,
			$this->capability,
			$this->template,
			$this->settings_fields,
			$this->position,
			$this->icon_url,
			$this->assets
		);
		$options_page->register();
		return $options_page;
	}
}
