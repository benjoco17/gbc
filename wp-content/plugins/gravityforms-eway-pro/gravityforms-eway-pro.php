<?php
/*
Plugin Name: Gravity Forms eWAY Pro
Plugin URI: https://gfeway.webaware.net.au/
Description: Easily create online payment forms with Gravity Forms and eWAY.
Version: 1.1.3
Author: WebAware
Author URI: https://shop.webaware.com.au/
Text Domain: gravityforms-eway-pro
Domain Path: /languages/
*/

/*
copyright (c) 2016-2017 WebAware Pty Ltd (email : support@webaware.com.au)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined('ABSPATH')) {
	exit;
}

define('GFEWAYPRO_PLUGIN_FILE', __FILE__);
define('GFEWAYPRO_PLUGIN_ROOT', dirname(__FILE__) . '/');
define('GFEWAYPRO_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('GFEWAYPRO_PLUGIN_VERSION', '1.1.3');

// instantiate the plug-in
require GFEWAYPRO_PLUGIN_ROOT . 'includes/class.GFEwayProPlugin.php';
GFEwayProPlugin::getInstance();
