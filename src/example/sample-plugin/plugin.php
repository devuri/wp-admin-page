<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI:  https://github.com/my-plugin
 * Description: My Plugin does stuff like this.
 * Author:      Author Name
 * Author URI:  https://github.com/devuri
 * Version:     0.1.0
 * License:     GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: my-plugin
 *
 * Requires PHP: 5.6+
 * Tested up to PHP: 7.3
 *
 * Copyright 2020 Author Name, support@example.com
 * License: GNU General Public License
 * GPLv2 Full license details in license.txt
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 * ----------------------------------------------------------------------------
 * @category  	Plugin
 * @copyright 	Copyright © 2020 Author Name.
 * @package   	MyPlugin
 * @author    	Author Name
 * @link      	https://github.com/devuri
 *  ----------------------------------------------------------------------------
 */

  # deny direct access
    if ( ! defined( 'WPINC' ) ) {
      die;
    }

  # plugin directory
	  define("MYPLGN_VERSION", '0.1.0');

  # plugin directory
    define("MYPLGN_DIR", dirname(__FILE__));

  # plugin url
    define("MYPLGN_URL", plugins_url( "/",__FILE__ ));
#  -----------------------------------------------------------------------------

    // You can install via Composer.
    require_once 'vendor/autoload.php';

  // Menu Item
  require_once plugin_dir_path( __FILE__ ). 'src/Admin/MyPluginAdmin.php';
