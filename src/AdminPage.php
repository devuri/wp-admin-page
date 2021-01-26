<?php
/**
 * The Main admin Class used the genereate the dmin pages
 *
 * @copyright 	Copyright © 2020 Uriel Wilson.
 * @package   	AdminPage
 * @version   	3.1.4
 * @license   	GPL-2.0
 * @author    	Uriel Wilson
 * @link      	https://github.com/devuri/wp-admin-page/
 */

namespace WPAdminPage;

use WPAdminPage\FormHelper as Form;

if ( ! defined( 'ABSPATH' ) ) exit;


abstract class AdminPage {

	/**
	 * Class version
	 */
	const ADMINVERSION = '3.3.0';

	/**
	 * Get the current plugin dir path
	 * set this in the $main_menu array
	 *
	 * @var $plugin_path.
	 */
	private $plugin_path;


	/**
	 * $page_title
	 *
	 * (Required) The text to be displayed in the title tags of the page when the menu is selected.
	 *
	 * @var string
	 */
	private $page_title;

	/**
	 * $menu_title
	 *
	 * (string) (Required) The text to be used for the menu.
	 *
	 * @var string
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 */
	private $menu_title;

	/**
	 * $capability
	 *
	 * (string) (Required) The capability required for this menu to be displayed to the user.
	 *
	 * @var string
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 */
	private $capability;

	/**
	 * $menu_slug
	 *
	 * (string) (Required) The slug name to refer to this menu by.
	 * Should be unique for this menu page and only include lowercase alphanumeric,
	 * dashes, and underscores characters to be compatible with sanitize_key().
	 *
	 * @var string
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 */
	private $menu_slug;

	/**
	 * $function
	 *
	 * (callable) (Optional) The function to be called
	 * to output the content for this page.Default value: ''
	 *
	 * @var string
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 */
	private $function;

	/**
	 * $icon_url
	 *
	 * (string) (Optional) The URL to the icon to be used for this menu.
	 * Pass a base64-encoded SVG using a data URI,
	 * which will be colored to match the color scheme.
	 * This should begin with 'data:image/svg+xml;base64,'.
	 * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-chart-pie'.
	 * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
	 * Default value: ''
	 *
	 * @var string
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 */
	private $icon_url;

	/**
	 * $position
	 *
	 * (int) (Optional) The position in the menu order this item should appear.
	 * Default value: null
	 *
	 * @var int
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 */
	private $position;

	/**
	 * $prefix
	 *
	 * Main menu prefix used to add prefix for page=$prefix-menu-slug.
	 *
	 * @var string
	 */
	private $prefix;

	/**
	 * $submenu_args
	 *
	 * @var array submenu_args
	 * @link https://developer.wordpress.org/reference/functions/add_submenu_page/
	 */
	private $submenu_args;

	/**
	 * Menu color
	 *
	 * @var string
	 */
	private $mcolor = '#0071A1';

	/**
	 * Initialization
	 *
	 * @param array $main_menu Main menu.
	 * @param array $submenu_items submenu items.
	 * @since 1.0
	 */
	public function __construct(
		array $main_menu,
		array $submenu_items = array()
	) {

		// User defined.
		$args = $main_menu;

		/**
	 	 * Default params
	 	 *
	 	 * @link https://developer.wordpress.org/reference/functions/wp_parse_args/
	 	 */
		$default = array();
		$default['id']          = 'no-ID-provided';
		$default['pro']         = false;
		$default['mcolor']      = $this->mcolor;
		$default['page_title']  = 'Page Title';
		$default['menu_title']  = 'Title';
		$default['capability']  = 'manage_options';
		$default['menu_slug']   = null;
		$default['function']    = array( $this, 'menu_callback' );
		$default['icon_url']    = null;
		$default['position']    = null;
		$default['prefix']      = null;
		$default['plugin_path'] = plugin_dir_path( __FILE__ );
		$args = wp_parse_args( $args, $default );

		// define menu vars.
		$this->mcolor       = $args['mcolor'];
		$this->page_title   = $args['page_title'];
		$this->menu_title   = $args['menu_title'];
		$this->capability   = $args['capability'];
		$this->menu_slug    = $args['menu_slug'];
		$this->function     = array( $this, 'menu_callback' );
		$this->icon_url     = $args['icon_url'];
		$this->position     = $args['position'];
		$this->prefix       = $args['prefix'];
		$this->plugin_path  = $args['plugin_path'];

		// submenu.
		$this->submenu_args = $submenu_items;

		// actions.
		$this->wp_actions();

	}

	/**
	 * Lets load our actions
	 *
	 * @return void
	 */
	public function wp_actions() {
		// ok lets create the menu.
		add_action( 'admin_menu', array( $this, 'build_menu' ) );

		// styles_admin.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_styles' ) );

		// footer_separator.
		add_action( 'swa_footer', array( $this, 'footer_separator' ) );
	}

	/**
	 * Menu_args
	 *
	 * @return array get the menu args
	 */
	public function args() {
	  	$menu_args = array(
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->menu_slug,
			$this->function,
			$this->icon_url,
			$this->position,
			$this->prefix,
	  	);
	  	return $menu_args;
	}

	/**
	 * Load the FormHelper class
	 *
	 * @return Form
	 */
	public function form() {
		return new Form();
	}

	/**
	 * Whats the version
	 *
	 * @return string
	 */
	public function admin_gui_version() {
		if ( current_user_can( 'manage_options' ) ) {
			return self::ADMINVERSION;
		}
	}

	/**
	 * Admin  path
	 *
	 * @return string
	 */
	public function admin_path() {
		return $this->plugin_path . 'pages/';
	}


	/**
	 * Styles on header action
	 *
	 * Simple CSS Styles
	 */
	public function admin_page_styles() {
		wp_enqueue_style( 'wll-admin-style', plugin_dir_url( __FILE__ ) . 'assets/wll-admin.css', array(), self::ADMINVERSION, 'all' );
	}

	/**
	 * Footer action
	 *
	 * Add hr tag to the footer section
	 */
	public function footer_separator() {
		echo '<hr/>';
	}

	/**
	 * Get the Page
	 *
	 * @return string
	 * @since 1.0
	 */
	public function page_title() {
		/**
		 * WP page vars
		 *
		 * @link https://developer.wordpress.org/reference/functions/get_current_screen/
		 */
		$screen = get_current_screen();

		// get specific page name.
	  	$id_page_name = explode( '_', $screen->id );
		$current_page = $id_page_name[2];
		$current_page = sanitize_text_field( $current_page );
		return $current_page;
	}

	/**
	 * The Callback
	 *
	 * @since 1.0
	 */
	public function menu_callback() {
		$this->admin_page();
	}

	/**
	 * Get the page name
	 *
	 * @return string
	 */
	public function page_name() {
		$pagefile = str_replace( $this->prefix . '-', '', $this->page_title() );
		return $pagefile;
	}

	/**
	 * Load the admin page header
	 *
	 * @param  string $access .
	 * @return void
	 */
	public function header( $access = 'manage_options' ) {
		$access = 'manage_options';
		$header = plugin_dir_path( __FILE__ ) . 'pages/header.admin.php';
		$this->require_page( $header );
	}

	/**
	 * Load the admin page header
	 *
	 * @return void
	 */
	public function footer() {
		$footer = plugin_dir_path( __FILE__ ) . 'pages/footer.admin.php';
	  	$this->require_page( $footer );
	}

	/**
	 * Load the admin page
	 *
	 * @since 1.0
	 * @param string $spage the admin page name.
	 * @return string
	 */
	public function load_admin_page( $spage = null ) {

		if ( is_null( $spage ) ) {
			$admin_path = $this->admin_path();
		} else {
			$admin_path = $spage;
		}

		$admin_file = $admin_path . $this->menu_slug() . '/' . $this->page_name() . '.admin.php';
		return $admin_file;
	}

	/**
	 * Admin Page
	 *
	 * @since 1.0
	 * @return void
	 */
	public function admin_page() {
		// Setup the pages.
	  	$this->header();
	  	$this->require_page( $this->load_admin_page() );
	  	$this->footer();
	}

	/**
	 * File location error
	 *
	 * @param  string $adminfile .
	 */
	public function require_page( $adminfile ) {
	  	if ( file_exists( $adminfile ) ) {
			require_once $adminfile;
	  	} else {
			$file_location_error = '<h1>Menu file location error : Experiencing Technical Issues, Please Contact Admin</h1>';

			if ( current_user_can( 'manage_options' ) ) {
				$file_location_error  = '<h2>Please check file location, Page Does not Exist</h2>';
				$file_location_error .= '<span class="alert-danger">' . $adminfile . '</span> location of file was not found ';
			}
				// User feedback.
				echo $file_location_error;
		}
	}

	/**
	 * Admin Submenu
	 *
	 * @since 2.0
	 */
	public function submenu_page() {

		// This is a submenu.
		$this->admin_submenu = true;

		// Setup the pages.
		$this->header();
		$this->require_page( $this->load_admin_page() );
		$this->footer();
	}

	/**
	 * Admin Only Callback
	 *
	 * @since 2.0
	 */
	public function adminonly_callback() {
		$this->submenu_page( $this->page_title() );
	}

	/**
	 * Output for the dynamic tabs
	 *
	 * @since 1.0
	 * @link https://developer.wordpress.org/reference/functions/__/
	 */
	public function tab_menu() {

		echo '<h2 style="border: unset; " class="wll-admin nav-tab-wrapper wp-clearfix">';
		foreach ( $this->submenu_args as $key => $submenu_item ) {

			// First item is always admin only.
			if ( 0 === $key ) {
				$submenu_access = 'manage_options';
			} else {
				$submenu_access = $this->submenu_val( $submenu_item, 'capability' );
			}

			// Check if user has access for the menu.
			if ( current_user_can( $submenu_access ) ) {
				// Slugs.
				if ( 0 === $key ) {
					$submenu_slug = $this->menu_slug;
				} else {
					$submenu_slug = sanitize_title( $this->prefix . '-' . $this->submenu_val( $submenu_item, 'name' ) );
				}

				// Build out the sub menu items.
				if ( $submenu_slug === $this->page_title() ) {
					echo '<a href="' . admin_url( '/admin.php?page=' . strtolower( $submenu_slug ) . '') . '" style="color:' . $this->mcolor . '" class="wll-admin-tab nav-tab-active">' . ucwords( $this->submenu_val( $submenu_item, 'name' ) ) . '</a>';
				} else {
					echo '<a href="' . admin_url( '/admin.php?page=' . strtolower( $submenu_slug ) . '') . '" style="color:' . $this->mcolor . '" class="wll-admin-tab">' . ucwords( $this->submenu_val( $submenu_item, 'name' ) ) . '</a>';
				}
			}
		}
	  	echo '</h2>';
	}

	/**
	 * Menu_slug
	 *
	 * Get the menu slug without the $prefix
	 *
	 * @return string
	 */
	public function menu_slug() {
		$slug = str_replace( $this->prefix . '-', '', $this->menu_slug );
		return $slug;
	}

	/**
	 * Add Header Action
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function admin_header() {
		do_action( 'swa_head' );
	}

	/**
	 * Add Footer Action
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function admin_footer() {
		do_action( 'swa_footer' );
	}

	/**
	 * Admin Page Title
	 *
	 * @since 1.0
	 * @return string
	 */
	public function menu_title() {
		$menu_title = '<h2 class="wll-admin-dashicons-before ';
		$menu_title .= $this->icon_url;
		$menu_title .= '">';
		$menu_title .= '<span class="wll-admin-title">';
		$menu_title .= $this->page_title;
		$menu_title .= '</span>';
		$menu_title .= '</h2>';
		return $menu_title;
	}

	/**
	 * Main Menu
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_menu_page/
	 * @since 1.0
	 */
	public function build_menu() {

		// Prefix the slug to avoid any conflicts.
		$this->menu_slug = $this->prefix . '-' . $this->menu_slug;

		// Main Menu.
		add_menu_page(
			$this->page_title,
			$this->menu_title,
			$this->capability,
			$this->menu_slug,
			array( $this, 'menu_callback' ),
			$this->icon_url,
			$this->position
		);

		/**
		 * The admin submenu section
		 *
		 * Here we build out the admin menus submenu items
		 * for item 0 we will set the same slug as the main item
		 *
		 * @link https://developer.wordpress.org/reference/functions/add_submenu_page/
		 * @link https://developer.wordpress.org/reference/functions/__/
		 */
		foreach ( $this->submenu_args as $key => $submenu_item ) {

			// Access.
			if ( 0 === $key ) {
				$submenu_access = 'manage_options';
			} else {
				$submenu_access = $this->submenu_val( $submenu_item, 'capability' );
			}

			// Slugs .
			if ( 0 === $key ) {
				$submenu_slug = $this->menu_slug;
			} else {
				$submenu_slug = sanitize_title( $this->prefix . '-' . $this->submenu_val( $submenu_item, 'name' ) );
			}

			// Submenu page dir path.
			$spage = $this->submenu_val( $submenu_item, 'page_path' );

			add_submenu_page(
				$this->submenu_val( $submenu_item, 'parent' ),
				ucfirst( $this->submenu_val( $submenu_item, 'name' ) ),
				ucwords( $this->submenu_val( $submenu_item, 'name' ) ),
				$submenu_access,
				$submenu_slug,
				function() use ( $spage ) {
					$this->header();
					$this->require_page( $this->load_admin_page( $spage ) );
					$this->footer();
				}
			);
		}
	}

	/**
	 * Return the correct submenu value
	 *
	 * @param mixed  $val .
	 * @param string $item .
	 * @return string
	 */
	public function submenu_val( $val, $item = 'name' ) {

		/**
		 * Default args
		 */
		$default = array();
		$default['name']       = $val;
		$default['parent']     = $this->menu_slug;
		$default['capability'] = $this->capability;
		$default['page_path']  = null;
		$params = wp_parse_args( $val, $default );

		return $params[ $item ];

	}

} //class
