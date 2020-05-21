## WP Admin Page, Quick and easy WP Admin Pages

Simple way to build out Admin Pages in WordPress

## Getting Started

Please check out the example/ folder

## You can install via Composer.

`$ composer require devuri/wp-admin-page`

* You can also edit composer.json manually then do a composer update

`"require": {
    "devuri/wp-admin-page": "^1.0"
}`

* Simple Example with composer
  ```php
  // You can install via Composer.
  require_once 'vendor/autoload.php';

  // build out the admin page
  require_once plugin_dir_path( __FILE__ ). 'src/Admin/MyPluginAdmin.php';
  ```

* Sample Admin Class  MyPluginAdmin will extend the base class 
```php
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
  ```


### Prerequisites

Tested up to WordPress: 5.4

```
Requires PHP: 5.6+
Tested up to PHP: 7.4
```



## Authors

* **Devuri** - *Initial work* - [Devuri](https://github.com/devuri)


## License

This project is licensed under the **GNU General Public License v2.0** - see the [LICENSE.txt](LICENSE.txt) file for details

## Acknowledgments

* wp codex
* Inspiration: plugin dev
