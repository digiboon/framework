<?php

/**
 * Making WordPress theme development less of a bother.
 *
 * @version 2.5
 * @author Digital Baboon LLC <asko@digitalbaboon.com>
 * @link https://digitalbaboon.com/framework
 */

declare(strict_types=1);

class Framework
{
	private array $templatePartials = [];
	private array $templateHelpers = [];
	private array $templateData = [];

	/**
	 * E-mail sending helper.
	 *
	 * Parses the `body` template and replaces all
	 * occurrences of {key} with a key-value set in `data`.
	 * and sends an email to `to' with a subject from `subject`.
	 *
	 * @param string $template
	 * @param array $data Configuration.
	 *
	 * @return void
	 */
	public function sendEmail(string $template, array $data): void
	{
		if(empty($data['to']) || empty($data['subject'])) return;

		// Get to
		$to = $data['to'];

		// Get subject
		$subject = $data['subject'];

		// Set headers
		$headers = ['Content-Type: text/html; charset=UTF-8'];

		wp_mail($to, $subject, $this->emailTemplate($template, $data), $headers);
	}

	/**
	 * Creates an ajax action.
	 *
	 * Creates an ajax action with `name` and `callback`,
	 * where the `callback` gets called when the ajax action
	 * is invoked.
	 *
	 * @param string $name Name of the action (example: this_is_a_name).
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function ajaxAction(string $name, callable $callback): void
	{
		add_action('wp_ajax_nopriv_' . $name, function () use ($callback) {

			if ($callback instanceof Closure) {

				$callback();

			}

			wp_die();

		});

		add_action('wp_ajax_' . $name, function () use ($callback) {

			if ($callback instanceof Closure) {

				$callback();

			}

			wp_die();

		});
	}

	/**
	 * Return $_GET and $_POST item.
	 *
	 * @param string $key
	 *
	 * @return string|null
	 */
	public function input(string $key): ?string
	{
		if(!empty($_REQUEST[$key])) {

			return htmlspecialchars($_REQUEST[$key]);

		}

		return null;
	}

	/**
	 * Returns the current paged for use within queries.
	 *
	 * @return int
	 */
	public function paged(): int
	{
		add_action('wp', function() {

			if(get_query_var('paged')) {

				return get_query_var('paged');

			}

			return 1;

		});
	}

	/**
	 * Registers a post type.
	 *
	 * @param $id
	 * @param $type
	 * @param bool $singularName
	 * @param bool $pluralName
	 * @param array $supports
	 *
	 * @return void
	 */
	public function registerPostType(string $id, string $type, ?string $singularName = null, ?string $pluralName = null, array $supports = []): void
	{
		add_action('init', function() use ($id, $type, $singularName, $pluralName, $supports) {

			register_post_type($id, [
				'labels' => [
					'name' => _x($pluralName ? $pluralName : $id, 'framework'),
					'singular_name' => _x($singularName ? $singularName : $id, 'framework'),
					'menu_name' => _x($pluralName ? $pluralName : $id, 'framework'),
					'name_admin_bar' => _x($singularName ? $singularName : $id, 'framework'),
					'add_new' => _x('Add New', 'framework'),
					'add_new_item' => __('Add New ' . $singularName ? $singularName : $id, 'framework'),
					'new_item' => __('New ' . $singularName ? $singularName : $id, 'framework'),
					'edit_item' => __('Edit ' . $singularName ? $singularName : $id, 'framework'),
					'view_item' => __('View ' . $singularName ? $singularName : $id, 'framework'),
					'all_items' => __('All ' . $pluralName ? $pluralName : $id, 'framework'),
					'search_items' => __('Search ' . $pluralName ? $pluralName : $id, 'framework'),
					'parent_item_colon' => __('Parent ' . $pluralName ? $pluralName : $id . ':', 'framework'),
					'not_found' => __('No ' . strtolower($pluralName ? $pluralName : $id) . ' found.', 'framework'),
					'not_found_in_trash' => __('No ' . strtolower($pluralName ? $pluralName : $id) . ' found in Trash.', 'framework')
				],
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => ['slug' => $id],
				'capability_type' => $type,
				'has_archive' => true,
				'hierarchical' => false,
				'supports' => $supports
			]);

		});
	}

	/**
	 * Registers a custom taxonomy.
	 *
	 * @param $tax
	 * @param $object_type
	 * @param array $args
	 *
	 * @return void
	 */
	public function registerTaxonomy(string $tax, string $object_type, array $args = []): void
	{
		add_action('init', function() use ($tax, $object_type, $args) {

			register_taxonomy($tax, $object_type, $args);

		});
	}

	/**
	 * Initializes the theme with sensible defaults.
	 *
	 * Intializes the theme by running a series of things:
	 * - Enabling WordPress itself to deal with the page titles
	 * - Adds post thumbnails support
	 * - Enables HTML5
	 * - Automatic feed links
	 * - Framework JavaScript
	 *
	 * @param array $excluded Array of things to exclude
	 *
	 * @return void
	 */
	public function init(array $excluded = []): void
	{
		/**
		 * Let WordPress manage the document title.
		 */
		if (!in_array('title-tag', $excluded)) {

			add_theme_support('title-tag');

		}

		/**
		 * Enable support for Post Thumbnails on posts and pages.
		 */
		if (!in_array('post-thumbnails', $excluded)) {

			add_theme_support('post-thumbnails');

		}

		/**
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		if (!in_array('html5', $excluded)) {

			add_theme_support('html5', [
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			]);

		}

		/**
		 * Add automatic feed links
		 */
		if (!in_array('automatic-feed-links', $excluded)) {

			add_theme_support('automatic-feed-links');

		}

		/**
		 * Load Framework's JavaScript library for easy JS integration with back-end WP
		 */
		if (!in_array('framework-js', $excluded)) {

			add_action('wp_head', function () {

				echo '<script>const _frameworkAjaxURL = "' . admin_url('admin-ajax.php') . '";</script>';

			}, -1000);

			self::script('framework-js', plugin_dir_url(__FILE__) . 'framework.js');

		}
	}

	/**
	 * Creates a menu.
	 *
	 * Creates a navigation menu.
	 *
	 * @param string $id ID of the menu
	 * @param string $name Name of the menu
	 *
	 * @return void
	 */
	public function menu(string $id, string $name): void
	{
		add_action('init', function () use ($id, $name) {

			register_nav_menu($id, __($name, 'framework'));

		});
	}

	/**
	 * Requires a stylesheet.
	 *
	 * Requires a given `path` as stylesheet.
	 *
	 * @param string $id ID of the stylesheet.
	 * @param string $path Path to the stylesheet file.
	 *
	 * @return void
	 */
	public function style(string $id, string $path): void
	{
		add_action('wp_enqueue_scripts', function () use ($id, $path) {

			wp_enqueue_style($id, $path);

		});
	}

	/**
	 * Requires a script.
	 *
	 * Requires a given `path` as script.
	 *
	 * @param string $id ID of the stylesheet.
	 * @param string $path Path to the stylesheet file.
	 *
	 * @return void
	 */
	public function script(string $id, string $path): void
	{
		add_action('wp_enqueue_scripts', function () use ($id, $path) {

			wp_enqueue_script($id, $path);

		});
	}

	/**
	 * Callback for is_front_page.
	 *
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isFrontPage(callable $callback): void
	{
		add_action('wp', function () use ($callback) {

			if (is_front_page() && $callback instanceof Closure) {

				$callback();

			}

		});
	}

	/**
	 * Callback for is_home.
	 *
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isHome(callable $callback): void
	{
		add_action('wp', function () use ($callback) {

			if (is_home() && $callback instanceof Closure) {

				$callback();

			}

		});
	}

	/**
	 * Callback for is_page.
	 *
	 * @param string $id ID or slug of the page, otherwise '*'.
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isPage(string $id, callable $callback): void
	{
		add_action('wp', function () use ($id, $callback) {

			if ($id === '*') {

				if (is_page() && $callback instanceof Closure) {

					$callback();

				}

			} else {

				if (is_page($id) && $callback instanceof Closure) {

					$callback();

				}

			}

		});
	}

	/**
	 * Callback for is_single.
	 *
	 * @param string $id ID or slug of the post, otherwise '*'.
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isSingle(string $id, callable $callback): void
	{
		add_action('wp', function () use ($id, $callback) {

			if ($id === '*') {

				if (is_single() && $callback instanceof Closure) {

					$callback();

				}

			} else {

				if (is_single($id) && $callback instanceof Closure) {

					$callback();

				}

			}

		});
	}

	/**
	 * Callback for is_category.
	 *
	 * @param string $id ID or slug of the category, otherwise '*'.
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isCategory(string $id, callable $callback): void
	{
		add_action('wp', function () use ($id, $callback) {

			if ($id === '*') {

				if (is_category() && $callback instanceof Closure) {

					$callback();

				}

			} else {

				if (is_category($id) && $callback instanceof Closure) {

					$callback();

				}

			}

		});
	}

	/**
	 * Callback for is_tag.
	 *
	 * @param string $id ID or slug of the tag, otherwise '*'.
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isTag(string $id, callable $callback): void
	{
		add_action('wp', function () use ($id, $callback) {

			if ($id === '*') {

				if (is_tag() && $callback instanceof Closure) {

					$callback();

				}

			} else {

				if (is_tag($id) && $callback instanceof Closure) {

					$callback();

				}

			}

		});
	}

	/**
	 * Callback for is_archive.
	 *
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isArchive(callable $callback): void
	{
		add_action('wp', function () use ($callback) {

			if (is_archive() && $callback instanceof Closure) {

				$callback();

			}

		});
	}

	/**
	 * Callback for is_author.
	 *
	 * @param string $id ID or slug of the author, otherwise '*'.
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isAuthor(string $id, callable $callback): void
	{
		add_action('wp', function () use ($id, $callback) {

			if ($id === '*') {

				if (is_author() && $callback instanceof Closure) {

					$callback();

				}

			} else {

				if (is_author($id) && $callback instanceof Closure) {

					$callback();

				}

			}

		});
	}

	/**
	 * Callback for is_tax.
	 *
	 * @param string $tax
	 * @param string $id
	 * @param callable $callback
	 *
	 * @return void
	 */
	public function isTax(string $tax, string $id, callable $callback): void
	{
		add_action('wp', function () use ($tax, $id, $callback) {

			if ($tax === '*') {

				if(is_tax() && $callback instanceof Closure) {

					$callback();

				}

			} else {

				if($id === '*') {

					if(is_tax($tax) && $callback instanceof Closure) {

						$callback();

					}

				} else {

					if(is_tax($tax, $id) && $callback instanceof Closure) {

						$callback();

					}

				}

			}

		});
	}

	/**
	 * Callback for is_search.
	 *
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isSearch(callable $callback): void
	{
		add_action('wp', function () use ($callback) {

			if (is_author() && $callback instanceof Closure) {

				$callback();

			}

		});
	}

	/**
	 * Callback for is_404.
	 *
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function is404(callable $callback): void
	{
		add_action('wp', function () use ($callback) {

			if (is_404() && $callback instanceof Closure) {

				$callback();

			}

		});
	}

	/**
	 * Callback for is_user_logged_in.
	 *
	 * @param callable $callback Function to call.
	 *
	 * @return void
	 */
	public function isUserLoggedIn(callable $callback): void
	{
		add_action('wp', function () use ($callback) {

			if (is_user_logged_in() && $callback instanceof Closure) {

				$callback();

			}

		});
	}

	/**
	 * Returns the current queried object.
	 *
	 * @since 2.1
	 */
	public function current()
	{
		$obj = get_queried_object();

		if (isset($obj)) {

			return $obj;

		}

		return false;
	}

	/**
	 * Returns the post tags in a Handlebars friendly way.
	 *
	 * @param $id
	 *
	 * @return array
	 */
	private function getPostTags(int $id): array
	{
		$result = [];
		$tags = wp_get_post_terms($id, 'post_tag', ['fields' => 'all']);

		if ($tags) {

			foreach ($tags as $tag) {

				$result[$tag->term_id]['id'] = $tag->term_id;
				$result[$tag->term_id]['name'] = $tag->name;
				$result[$tag->term_id]['url'] = get_category_link($tag);

			}

		}

		return $result;
	}

	/**
	 * Handlebars friendly WP_Query wrapper.
	 *
	 * @param array $query WP_Query's query.
	 *
	 * @return array
	 */
	public function query(array $query): array
	{
		$result = [];
		$WPQuery = new WP_Query($query);

		if ($WPQuery->have_posts()) {

			while ($WPQuery->have_posts()) {

				$WPQuery->the_post();

				// set ID
				$result[get_the_ID()]['id'] = get_the_ID();

				// set title
				$result[get_the_ID()]['title'] = get_the_title();

				// set url
				$result[get_the_ID()]['url'] = get_the_permalink();

				// set date
				$result[get_the_ID()]['published_at'] = get_the_date();

				// set author
				$result[get_the_ID()]['author'] = get_the_author();

				// set image
				$result[get_the_ID()]['image'] = [
					'thumbnail' => get_the_post_thumbnail_url(get_the_ID()),
					'small' => get_the_post_thumbnail_url(get_the_ID(), 'small'),
					'medium' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
					'large' => get_the_post_thumbnail_url(get_the_ID(), 'large'),
					'full' => get_the_post_thumbnail_url(get_the_ID(), 'full'),
				];

				// set has_tag
				$result[get_the_ID()]['has_tag'] = has_tag();

				// set tags
				$result[get_the_ID()]['tags'] = $this->getPostTags(get_the_ID());

				// set content
				$result[get_the_ID()]['content'] = get_the_content();

				// set pages
				$result[get_the_ID()]['pages'] = $WPQuery->max_num_pages;

			}

		}

		return $result;
	}

	/**
	 * Get template from theme directory.
	 *
	 * Get template from theme directory for
	 * a give `name`.
	 *
	 * @param string $name Name of the template.
	 *
	 * @return bool|false|string
	 */
	private function getTemplate(string $name): string
	{
		$template = false;

		if (file_exists(get_template_directory() . '/templates/' . $name . '.hbs')) {

			$template = file_get_contents(get_template_directory() . '/templates/' . $name . '.hbs');

		}

		return $template;
	}

	/**
	 * Register template partials.
	 *
	 * @param array $partials Array of partials to register.
	 */
	public function registerTemplatePartials(array $partials = []): void
	{
		$this->templatePartials = $partials;
	}

	/**
	 * Sets template data for use within all templates.
	 *
	 * @param array $data
	 */
	public function setTemplateData(array $data = []): void
	{
		$this->templateData = $data;
	}

	/**
	 * Get template partials.
	 *
	 * Get all template partials.
	 *
	 * @return array
	 */
	private function getTemplatePartials(): array
	{
		$partials = [];

		foreach ($this->templatePartials as $partial) {

			$partials[$partial] = $this->getTemplate('partials/' . $partial);

		}

		return $partials;
	}

	/**
	 * Convert a echo-ing fn to return.
	 *
	 * Convert a echo-ing function to return instead.
	 *
	 * @param string $fn Function name.
	 * @param bool|array $args Arguments.
	 *
	 * @return false|string
	 */
	public function getWPTemplateFn(string $fn, $args = false): string
	{
		$callback = function () use ($fn, $args) {

			ob_start();

			if ($args) {

				call_user_func($fn, $args);

			} else {

				call_user_func($fn);

			}

			$out = ob_get_contents();

			ob_end_clean();

			return $out;

		};

		return $callback();
	}

	/**
	 * Return default template data.
	 *
	 * Return default template data for use
	 * within templates.
	 *
	 * @return array
	 */
	private function getTemplateDefaultData(): array
	{
		return [
			'wp_head' => $this->getWPTemplateFn('wp_head'),
			'wp_footer' => $this->getWPTemplateFn('wp_footer'),
			'body_class' => $this->getWPTemplateFn('body_class'),
			'language_attributes' => $this->getWPTemplateFn('language_attributes'),
		];
	}

	/**
	 * Register template helpers.
	 *
	 * @param array $helpers
	 *
	 * @return void
	 */
	public function registerTemplateHelpers(array $helpers = []): void
	{
		$this->templateHelpers = $helpers;
	}

	/**
	 * snake_case mirror of registerTemplateHelpers.
	 *
	 * @param array $helpers
	 *
	 * @return void
	 */
	public function register_template_helpers(array $helpers = []): void
	{
		$this->registerTemplateHelpers(($helpers));
	}

	/**
	 * Register template helpers.
	 *
	 * Register a variety of template helpers
	 * for use within templates.
	 *
	 * @return array
	 */
	private function getTemplateHelpers(): array
	{
		$helpers = [

			/**
			 * bloginfo helper.
			 *
			 * @param string $show Item to show.
			 */
			'info' => function (string $show) {

				return get_bloginfo($show);

			},

			/**
			 * wp_nav_menu helper.
			 *
			 * @param string $menu Menu to show.
			 */
			'menu' => function (string $menu) {

				$framework = new Framework();

				return $framework->getWPTemplateFn('wp_nav_menu', [
					'theme_location' => $menu,
					'container' => '',
				]);

			},

			/**
			 * get_post_meta helper
			 *
			 * @param string $id ID of the post
			 * @param string $key Key of the meta item.
			 */
			'meta' => function (string $id, string $key) {

				return get_post_meta($id, $key, true);

			},

			/**
			 * Date helper.
			 *
			 * @param string $format Format of the date string.
			 */
			'date' => function (string $format) {

				return date($format);

			},

			/**
			 * Pagination helper.
			 *
			 * @param $prev
			 * @param $next
			 * @param $pages
			 *
			 * @return mixed
			 */
			'pagination' => function($prev, $next, $pages) {

				return paginate_links([
					'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
					'total'        => $pages,
					'current'      => max( 1, get_query_var( 'paged' ) ),
					'format'       => '?paged=%#%',
					'show_all'     => false,
					'type'         => 'plain',
					'end_size'     => 2,
					'mid_size'     => 1,
					'prev_next'    => true,
					'prev_text'    => sprintf( '<i></i> %1$s', __( $prev, 'framework' ) ),
					'next_text'    => sprintf( '%1$s <i></i>', __( $next, 'framework' ) ),
					'add_args'     => false,
					'add_fragment' => '',
				]);

			}

		];

		return array_merge($helpers, $this->templateHelpers);
	}

	/**
	 * Prepare the template to be rendered. This is a clone of
	 * LightnCandy's deprecated `prepare` method.
	 *
	 * @param $php
	 * @param null $tmpDir
	 * @param bool $delete
	 *
	 * @return callable
	 */
	public static function prepareTemplate(string $php, ?string $tmpDir = null, bool $delete = true): callable
	{
		$php = "<?php $php ?>";

		if (!ini_get('allow_url_include') || !ini_get('allow_url_fopen')) {

			if (!is_string($tmpDir) || !is_dir($tmpDir)) {

				$tmpDir = sys_get_temp_dir();

			}

		}

		if ($tmpDir && is_dir($tmpDir)) {

			$fn = tempnam($tmpDir, 'lci_');

			if (!$fn) {

				error_log("Can not generate tmp file under $tmpDir!!\n");
				return false;

			}

			if (!file_put_contents($fn, $php)) {

				error_log("Can not include saved temp php code from $fn, you should add $tmpDir into open_basedir!!\n");
				return false;

			}

			$phpfunc = include($fn);

			if ($delete) {

				unlink($fn);

			}

			return $phpfunc;

		}

		return include('data://text/plain,' . urlencode($php));

	}

	/**
	 * Displays a template with a given name.
	 *
	 * Displays a template with a given name
	 * from the `templates` directory.
	 *
	 * @param string $name Name of the template.
	 * @param array $data Data passed to template.
	 *
	 * @return callable
	 */
	public function template(string $name, array $data = []): callable
	{
		if (file_exists(get_template_directory() . '/templates/' . $name . '.hbs')) {

			$template = file_get_contents(get_template_directory() . '/templates/' . $name . '.hbs');

			$phpStr = LightnCandy\LightnCandy::compile($template, [
				'flags' => LightnCandy\LightnCandy::FLAG_ERROR_EXCEPTION,
				'partials' => $this->getTemplatePartials(),
				'helpers' => $this->getTemplateHelpers(),
			]);

			$renderer = $this->prepareTemplate($phpStr);

			if ($renderer instanceof Closure) {

				echo $renderer(array_merge($data, $this->getTemplateDefaultData(), $this->templateData));

			}

			die();

		}
	}

	/**
	 * Returns the template code for an email.
	 *
	 * @param string $name
	 * @param array $data
	 *
	 * @return callable
	 */
	public function emailTemplate(string $name, array $data = []): string
	{
		if (file_exists(get_template_directory() . '/templates/emails/' . $name . '.hbs')) {

			$template = file_get_contents(get_template_directory() . '/templates/emails/' . $name . '.hbs');
			$phpStr = LightnCandy\LightnCandy::compile($template, [
				'flags' => LightnCandy\LightnCandy::FLAG_ERROR_EXCEPTION
			]);

			$renderer = $this->prepareTemplate($phpStr);

			if ($renderer instanceof Closure) {

				return $renderer(array_merge($data, $this->templateData));

			}

			die();

		}
	}

}