<?php

use WPAdminPage\AdminPage;

final class MyPluginAdmin extends AdminPage {
  /**
   * admin_menu()
   *
   * Main top level admin menus
   * @return [type] [description]
   */
  private static function admin_menu(){
    $menu = array();
    $menu[] = 'My Plugin Menu Settings';
    $menu[] = 'My Plugin';
    $menu[] = 'manage_options';
    $menu[] = 'my-plugin';
    $menu[] = 'myplugin_callback';
    $menu[] = 'dashicons-admin-generic';
    $menu[] = null;
    $menu[] = 'myp';
    $menu[] = plugin_dir_path( __FILE__ );
    return $menu;
  }

	/**
	 * submenu()
	 * array of submenu items
	 * @return [type] [description]
	 */
	private static function submenu(){
		$submenu = array();
		$submenu[] = 'Menu One';
		$submenu[] = 'Menu Two';
		$submenu[] = 'etc';
		return $submenu;
	}

  /**
   * init
   * @return [type] [description]
   */
  public static function init(){
    return new MyPluginAdmin(self::admin_menu(),self::submenu());
  }
}

  // create admin pages
  MyPluginAdmin::init();
