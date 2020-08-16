<?php
/**
 * The Main admin Class used the genereate the dmin pages
 *
 * @copyright 	Copyright © 2020 Uriel Wilson.
 * @package   	AdminPage
 * @version   	2.1.0
 * @license   	GPL-2.0
 * @author    	Uriel Wilson
 * @link      	https://github.com/devuri/wp-admin-page/
 */

namespace WPAdminPage;

use WPAdminPage\FormHelper as Form;

if (!defined('ABSPATH')) exit;


if (!class_exists('WPAdminPage\AdminPage')) {
  abstract class AdminPage {

    /**
     * class version
     */
    const ADMINVERSION = '2.1.0';

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
     * menu color
     * @var [type]
     */
    private $mcolor = '#0071A1';

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
        array $submenu_items = array()
    ) {

      /**
       * user defined
       * @var array
       */
      $args = $main_menu;

      /**
       * default params
       * @link https://developer.wordpress.org/reference/functions/wp_parse_args/
       */
      $default = array();
      $default['id'] 	        = 'no-ID-provided';
      $default['pro'] 	      = false;
      $default['mcolor'] 	    = $this->mcolor;
      $default['page_title'] 	= $args[0];
      $default['menu_title'] 	= $args[1];
      $default['capability'] 	= $args[2];
      $default['menu_slug'] 	= $args[3];
      $default['function'] 	  = array( $this, 'menu_callback' );
      $default['icon_url'] 	  = $args[5];
      $default['position'] 	  = null;
      $default['prefix']      = $args[7];
      $default['plugin_path'] = $args[8];
      $args = wp_parse_args( $args , $default );

      /**
       * define menu vars
       * @var [type]
       */
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

      // submenu
      $this->submenu_args = $submenu_items;

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
     * whats the version
     * @return [type] [description]
     */
    public function admin_gui_version(){
      return self::ADMINVERSION;
    }

    /**
     * admin  path
     * @return [type] [description]
     */
    public function admin_path(){
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
    public function header( $access = 'manage_options' ){
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
      $admin_file = $this->admin_path() . $this->menu_slug().'/'.$this->page_name().'.admin.php';
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

        # first item is always admin only
        if ($key == 0) {
          $submenu_access = 'manage_options';
        } else {
          $submenu_access = $this->submenu_val($submenu_item,'capability');
        }

          # check if user has access for the menu
          if (current_user_can($submenu_access)) {
            #slugs
            if ($key == 0) {
              $submenu_slug = $this->menu_slug;
            } else {
              $submenu_slug = sanitize_title($this->prefix.'-'.$this->submenu_val($submenu_item,'name'));
            }

            # build out the sub menu items
            if ($submenu_slug == $this->page_title()) {
              echo '<a href="'.admin_url('/admin.php?page='.strtolower($submenu_slug).'').'" style="color:'.$this->mcolor.'" class="wll-admin-tab nav-tab-active">'.ucwords(__($this->submenu_val($submenu_item,'name'))).'</a>';
            } else {
              echo '<a href="'.admin_url('/admin.php?page='.strtolower($submenu_slug).'').'" style="color:'.$this->mcolor.'" class="wll-admin-tab">'.ucwords(__($this->submenu_val($submenu_item,'name'))).'</a>';
            }
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

        # access
        if ($key == 0) {
          $submenu_access = 'manage_options';
        } else {
          $submenu_access = $this->submenu_val($submenu_item,'capability');
        }

        #slugs
        if ($key == 0) {
          // change the slug for first item to match parent slug
          $submenu_slug = $this->menu_slug;
        } else {
          // keep current slug
          $submenu_slug = sanitize_title($this->prefix.'-'.$this->submenu_val($submenu_item,'name'));
        }

          # build out the sub menu items
          add_submenu_page(
            $this->menu_slug,
            ucfirst(__($this->submenu_val($submenu_item,'name'))),
            ucwords(__($this->submenu_val($submenu_item,'name'))),
            $submenu_access,
            $submenu_slug,
            array( $this, 'menu_callback' )
          );
        }

    }

    /**
     * Return the correct submenu value
     * @param  mixed  $val  value
     * @param  string $item value
     * @return string
     */
    public function submenu_val( $val , $item = 'name'){
      if (is_array($val)) {
        return $val[$item];
      } else {
        $subm = array(
          'name'        => $val,
          'capability'  => $this->capability,
        );
        return $subm[$item];
      }
    }

  }//class
}
