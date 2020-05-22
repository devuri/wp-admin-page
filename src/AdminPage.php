<?php

namespace WPAdminPage;
use WPAdminPage\FormHelper as Form;

  /**
   * --------------------------------------------------------------------------
   * @copyright 	Copyright © 2020 Uriel Wilson.
   * @package   	AdminPage
   * @version   	1.1.5
   * @license   	GPL-2.0
   * @author    	Uriel Wilson
   * @link      	https://github.com/devuri/wp-admin-page/
   * --------------------------------------------------------------------------
   */
  if (!defined('ABSPATH')) exit;


if (!class_exists('WPAdminPage\AdminPage')) {
  abstract class AdminPage {

    /**
     * class version
     */
    const ADMINVERSION = '1.1.5';

    /**
     * get the current plugin dir path
     * set this in the $main_menu array
     * @var [type]
     */
    private $plugin_path;


    /**
     * $page_title
     *
     * (Required) The text to be displayed in the title tags of the page when the menu is selected.
     * @var string
     */
    private $page_title;

    /**
     * $menu_title
     *
     * (string) (Required) The text to be used for the menu.
     * @var string
     * @link https://developer.wordpress.org/reference/functions/add_menu_page/
     */
    private $menu_title;

    /**
     * $capability
     *
     * (string) (Required) The capability required for this menu to be displayed to the user.
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
     * @var string
     * @link https://developer.wordpress.org/reference/functions/add_menu_page/
     */
    private $menu_slug;

    /**
     * $function
     *
     * (callable) (Optional) The function to be called
     * to output the content for this page.Default value: ''
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
     * @var string
     * @link https://developer.wordpress.org/reference/functions/add_menu_page/
     */
    private $icon_url;

    /**
     * $position
     *
     * (int) (Optional) The position in the menu order this item should appear.
     * Default value: null
     * @var int
     * @link https://developer.wordpress.org/reference/functions/add_menu_page/
     */
    private $position;

    /**
     * $prefix
     *
     * main menu prefix used to add prefix for page=$prefix-menu-slug
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
     * Stand alone Submenu for settings (options-general.php)
     *
     * Setup a seperate admin only menu without any sub menus
     * @link https://developer.wordpress.org/reference/functions/add_submenu_page/
     *
     * @var array $settings_args List of settings items
     * @var string $parent_slugs the parent page, defaults to WordPress Settings Menu
     * @var string $admin_only_capability who can access, defaults to Admin user Role
     * @var string $admin_submenu The admin menu
     * @since 1.0
     */
    private $settings_args;
    private $parent_slug  = 'options-general.php';
    private $admin_only_capability  = 'manage_options';
    private $admin_submenu;

    private $mcolor = '#0071A1';

    /**
     * To hide the submenu link from a top level menu
     * @var boolean
     */
    private $display_submenulink = true;

    /**
     * Initialization
     *
     * @param array $main_menu     Main menu
     * @param array $submenu_items submenu items
     * @param array $admin_only special admin only menu
     * @since 1.0
     */
    public function __construct(
        array $main_menu,
        array $submenu_items = array(),
        array $admin_only = array()
    ) {
      /**
       * add the color scheme
       * use defualt if not defined
       * @var [type]
       */
       if ( count($main_menu) > 9 ) {
        $this->mcolor = array_shift($main_menu);
       }
      $this->page_title   = $main_menu[0];
      $this->menu_title   = $main_menu[1];
      $this->capability   = $main_menu[2];
      $this->menu_slug    = $main_menu[3];
      $this->function     = array( $this, 'menu_callback' );
      $this->icon_url     = $main_menu[5];
      $this->position     = $main_menu[6];
      $this->prefix       = $main_menu[7];
      $this->plugin_path  = $main_menu[8];
      // show submenu link
      if (isset($main_menu[9])) {
        $this->display_submenulink  = $main_menu[9];
      }

      // submenu
      $this->submenu_args = $submenu_items;

      // Admin Only Settings Menu
      $this->settings_args = $admin_only;

      // actions
      $this->wp_actions();

    }

    /**
     * lets load our actions
     * @return
     */
    public function wp_actions(){
      // ok lets create the menu
      add_action( 'admin_menu',array( $this, 'build_menu' ) );

      // styles_admin
      add_action( 'admin_enqueue_scripts',array( $this, 'admin_page_styles') );

      // footer_separator
      add_action( 'swa_footer',array( $this, 'footer_separator' ) );
    }

    /**
     * menu_args
     *
     * @return array get the menu args
     */
    public function args(){
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
     * @return [type] [description]
     */
    public function form(){
  	  $form_helper = new Form();
      return $form_helper;
    }

    /**
     * get the instance
     * @return object
     */
    public function instance(){
      return new self($this->args(),$this->submenu_args,$this->settings_args);
    }

    /**
     * admin  path
     * @return [type] [description]
     */
    public function admin_path(){
      return $this->plugin_path . 'pages/';
    }

    /**
     * display submenu link items
     */
    private function display_submenu_link(){
      if ($this->display_submenulink) {
        return $this->menu_slug;
      } else {
        return null;
      }
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
     * add <hr/>  to the footer section
     */
    public function footer_separator(){
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
       * wp page vars
       * @link https://developer.wordpress.org/reference/functions/get_current_screen/
       */
      $screen = get_current_screen();

      # get specific page name
      $id_page_name = explode('_', $screen->id);
      $current_page = $id_page_name[2];
      $current_page = sanitize_text_field($current_page);
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
     * get the page name
     * @return [type] [description]
     */
    public function page_name(){
      $pagefile = str_replace($this->prefix.'-', '', $this->page_title());
      return $pagefile;
    }

    /**
     * Load the admin page header
     * @return [type] [description]
     */
    public function header(){
      $header = plugin_dir_path( __FILE__ ).'pages/header.admin.php';
      $this->require_page($header);
    }

    /**
     * Load the admin page header
     * @return [type] [description]
     */
    public function footer(){
      $header = plugin_dir_path( __FILE__ ).'pages/footer.admin.php';
      $this->require_page($header);
    }

    /**
     * Load the admin page
     *
     * @since 1.0
     * @param  string $admin_page the admin page name
     * @return
     */
    public function load_admin_page() {
      if ($this->admin_submenu) {
        $admin_file = $this->admin_path() . 'admin-options/'.$this->page_name().'.admin.php';
      } else {
        $admin_file = $this->admin_path() . $this->menu_slug().'/'.$this->page_name().'.admin.php';
      }
      return $admin_file;
    }

    /**
     * Admin Page
     *
     * @since 1.0
     * @param  string $page_name
     * @return
     */
    public function admin_page() {
      /**
       * setup the pages
       */
      $this->header();
      $this->require_page($this->load_admin_page());
      $this->footer();
    }

    /**
     * [require_page description]
     * @param  [type] $adminfile [description]
     * @return [type]            [description]
     */
    public function require_page($adminfile){
      if (file_exists($adminfile)) {
        require_once $adminfile;
      } else {
        $file_location_error = '<h1>'. __( 'Menu file location error : Experiencing Technical Issues, Please Contact Admin' ).'</h1>';
          # only show full file path to admin user
        if ( current_user_can('manage_options') ) {
          $file_location_error  = '<h2>' .__('Please check file location, Page Does not Exist').' </h2>';
          $file_location_error .= '<span class="alert-danger">'. $adminfile . '</span> '.__('location of file was not found').' </p>';
        }
          // user feedback
          echo $file_location_error;
      }
    }

    /**
     * Admin Submenu
     *
     * @since 2.0
     */
    public function submenu_page() {
      # this is a submenu
      $this->admin_submenu = true;
      /**
       * setup the pages
       */
      $this->header();
      $this->require_page($this->load_admin_page());
      $this->footer();
    }

    /**
     * Admin Only Callback
     *
     * @since 2.0
     */
    public function adminonly_callback() {
      $this->submenu_page($this->page_title());
    }

    /**
     * Output for the dynamic tabs
     *
     * @since 1.0
     * @link https://developer.wordpress.org/reference/functions/__/
     */
    public function tab_menu() {

      echo '<h2 style="border: unset; " class="wll-admin nav-tab-wrapper wp-clearfix">';
      foreach ($this->submenu_args as $key => $submenu_item) {
         #slugs
        if ($key == 0) {
            $submenu_slug = $this->menu_slug;
        } else {
            $submenu_slug = sanitize_title($this->prefix.'-'.$submenu_item);
        }

        // build out the sub menu items
        if ($submenu_slug == $this->page_title()) {
          echo '<a href="'.admin_url('/admin.php?page='.strtolower($submenu_slug).'').'" style="color:'.$this->mcolor.'" class="wll-admin-tab nav-tab-active">'.ucwords(__($submenu_item)).'</a>';
        } else {
          echo '<a href="'.admin_url('/admin.php?page='.strtolower($submenu_slug).'').'" style="color:'.$this->mcolor.'" class="wll-admin-tab">'.ucwords(__($submenu_item)).'</a>';
        }
      }
      echo '</h2>';
    }

    /**
     * menu_slug
     *
     * get the menu slug without the $prefix
     * @return [type] [description]
     */
    public function menu_slug(){
      $slug = str_replace($this->prefix.'-','',$this->menu_slug);
      return $slug;
    }

    /**
     * Add Header Action
     *
     * @since 1.0
     * @return
     */
    public static function admin_header() {
      do_action('swa_head');
    }

    /**
     * Add Footer Action
     *
     * @since 1.0
     * @return
     */
    public static function admin_footer() {
      do_action('swa_footer');
    }

    /**
     * Admin Page Title
     *
     * @return
     * @since 1.0
     */
    public function menu_title(){
      $menu_title = '<h2 class="wll-admin-dashicons-before ';
      $menu_title .= $this->icon_url;
      $menu_title .= '">';
      $menu_title .= '<span class="wll-admin-title">';
      $menu_title .= __($this->page_title);
      $menu_title .= '</span>';
      $menu_title .= '</h2>';
      return $menu_title;
    }

    /**
     * Main Menu
     *
     * @return
     * @link https://developer.wordpress.org/reference/functions/add_menu_page/
     * @since 1.0
     */
    public function build_menu() {

      // prefix the slug to avoid any conflicts
      $this->menu_slug = $this->prefix.'-'.$this->menu_slug;

      // Main Menu
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
       * here we build out the admin menus submenu items
       * for item 0 we will set the same slug as the main item
       * @link https://developer.wordpress.org/reference/functions/add_submenu_page/
       * @link https://developer.wordpress.org/reference/functions/__/
       */
      foreach ($this->submenu_args as $key => $submenu_item) {
        #slugs
        if ($key == 0) {
          // change the slug for first item to match parent slug
          $submenu_slug = $this->menu_slug;
        } else {
          // keep current slug
          $submenu_slug = sanitize_title($this->prefix.'-'.$submenu_item);
        }
          // build out the sub menu items
          add_submenu_page(
            //$this->menu_slug,
            $this->display_submenu_link(),
            ucfirst(__($submenu_item)),
            ucwords(__($submenu_item)),
            $this->capability,
            $submenu_slug,
            array( $this, 'menu_callback' )
          );
        }

        /**
         * Admin Only Settings Menu
         *
         * Here is where we build a custom settings section under
         * the settings menu in WordPress Admin Backend
         * this is only accessible to Administrators
         * @link https://developer.wordpress.org/reference/functions/__/
         */
        foreach ($this->settings_arg() as $akey => $admin_item) {
          $admin_slug = sanitize_title($admin_item);
          add_submenu_page(
            $this->parent_slug,
            ucfirst(__($admin_item)),
            ucwords(__($admin_item)),
            $this->admin_only_capability,
            $admin_slug,
            array( $this, 'adminonly_callback' )
          );
        }
    }

    /**
     * Admin Only Settings Menu
     *
     * @since 1.0
     * @return
     */
    public function settings_arg(){
        return $this->settings_args;
    }

  }//class
}
