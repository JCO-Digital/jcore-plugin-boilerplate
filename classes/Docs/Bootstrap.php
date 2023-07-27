<?php

namespace JcoreBroiler\Docs;

use JcoreBroiler\Options\OptionsPageBuilder;
use Twig\Extra\Markdown\DefaultMarkdown;
use Timber\Timber;
use Twig\Environment;
use Twig\Extra\Markdown\MarkdownExtension;
use Twig\Extra\Markdown\MarkdownRuntime;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Registers the docs loader.
 *
 * @package JcoreBroiler\Docs
 */
class Bootstrap {

	/**
	 * Initializes the Docs class.
	 * You can use OptionsPageBuilder to build options pages here or just leave it as it is.
	 *
	 * @param string $parent_slug The parent page.
	 *
	 * @return void
	 * @see OptionsPageBuilder
	 */
	public static function init( string $parent_slug = 'plugins.php' ): void {
		self::timber_init();
		add_filter( 'timber/twig', array( static::class, 'add_markdown_to_twig' ) );
		self::register_docs_page( $parent_slug );
	}

	/**
	 * Adds markdown to twig.
	 *
	 * @param Environment $twig The twig environment.
	 * @return Environment
	 */
	public static function add_markdown_to_twig( $twig ) {
		$twig->addExtension( new MarkdownExtension() );
		$twig->addRuntimeLoader(
			new class() implements RuntimeLoaderInterface {
				public function load( $class ) {
					if ( MarkdownRuntime::class === $class ) {
						return new MarkdownRuntime( new DefaultMarkdown() );
					}
				}
			}
		);
		return $twig;
	}

	/**
	 * Initializes Timber.
	 *
	 * @return void
	 */
	private static function timber_init(): void {
		Timber::$locations = array(
			BROILER_PLUGIN_PATH . '/views',
			BROILER_PLUGIN_PATH . '/docs',
		);
	}

	/**
	 * Registers Documentation page.
	 *
	 * @param string $parent_slug The parent page.
	 * @return void
	 */
	private static function register_docs_page( string $parent_slug ): void {
		$options_page = ( new OptionsPageBuilder() )
			->set_page_title( 'Docs' )
			->set_menu_title( 'JCORE-Plugin Docs' )
			->set_menu_slug( 'jcore-broiler-docs' )
			->set_parent_slug( $parent_slug )
			->set_capability( 'edit_posts' )
			->set_icon_url( 'dashicons-media-document' )
			->set_position( 100 )
			->set_template( 'docs.twig' )
			->add_script(
				array(
					'handle'    => 'tailwindcss',
					'src'       => 'https://cdn.tailwindcss.com?plugins=typography',
					'deps'      => array(),
					'ver'       => false,
					'in_footer' => false,
				)
			)
			->add_script(
				array(
					'handle'    => 'alpinejs',
					'src'       => 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js',
					'deps'      => array(),
					'ver'       => '',
					'in_footer' => true,
				)
			)
			->add_stylesheet(
				array(
					'handle' => 'highlightjs-css',
					'src'    => 'https://cdn.jsdelivr.net/npm/highlight.js@11.8.0/styles/idea.min.css',
					'deps'   => array(),
					'ver'    => false,
					'media'  => 'all',
				)
			)
			->add_script(
				array(
					'handle'    => 'highlightjs-js',
					'src'       => 'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.7.0/build/highlight.min.js',
					'deps'      => array(),
					'ver'       => '',
					'in_footer' => false,
				)
			)
			->build();

		add_filter( 'timber_context_' . $options_page->get_menu_slug(), array( static::class, 'add_docs_file_to_context' ) );
	}

	/**
	 * Adds the docs file to the context.
	 *
	 * @param array $context The context.
	 * @return array
	 */
	public static function add_docs_file_to_context( array $context ): array {
		$context['docs_file'] = self::build_docs_array();
		return $context;
	}

	/**
	 * Builds the docs array.
	 *
	 * @return array
	 */
	private static function build_docs_array(): array {
		$docs_file = BROILER_PLUGIN_PATH . '/docs/docs.json';
		if ( ! is_readable( $docs_file ) ) {
			return array();
		}
		$docs_json = wp_json_file_decode( $docs_file );
		if ( ! is_array( $docs_json ) ) {
			return array();
		}
		$docs = array();
		foreach ( $docs_json as $document ) {
			if ( ! is_readable( BROILER_PLUGIN_PATH . '/docs/' . $document->file ) ) {
				continue;
			}
			$docs[] = array(
				'title'   => $document->title,
				'slug'    => sanitize_title( $document->title ) . '-' . md5( $document->title ),
				'content' => Timber::compile(
					array( 'docs-wrapper.twig' ),
					array( 'document_file' => $document->file )
				),
			);
		}
		return $docs;
	}
}
