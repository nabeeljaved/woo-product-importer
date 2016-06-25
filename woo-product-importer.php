<?php /*
    Plugin Name: Woo Product Importer
    Plugin URI: http://webpresencepartners.com/2012/09/19/a-free-simple-woocommerce-csv-importer/
    Description: Free CSV import utility for WooCommerce
    Version: 1
    Author: Daniel Grundel, Web Presence Partners
    Author URI: http://www.webpresencepartners.com
    Text Domain: woo-product-importer
    Domain Path: /languages/
*/

/*
    This file is part of Woo Product Importer.
    
    Woo Product Importer is Copyright 2012-2013 Web Presence Partners LLC.

    Woo Product Importer is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    Woo Product Importer is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.
    
    You should have received a copy of the GNU Lesser General Public License
    along with Woo Product Importer.  If not, see <http://www.gnu.org/licenses/>.
*/
    
    class WebPres_Woo_Product_Importer {
        
        public function __construct() {
            add_action( 'init', array( 'WebPres_Woo_Product_Importer', 'translations' ), 1 );
            register_activation_hook(__FILE__, array(__CLASS__, 'activation'));
            add_action('admin_menu', array('WebPres_Woo_Product_Importer', 'admin_menu'));
            add_action('wp_ajax_woo-product-importer-ajax', array('WebPres_Woo_Product_Importer', 'render_ajax_action'));
        }

        public static function translations() {
            load_plugin_textdomain( 'woo-product-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        public static function admin_menu() {
            add_menu_page( __( 'Product Importer', 'woo-product-importer' ), __( 'Product Importer', 'woo-product-importer' ), 'vendor_bulk_import', 'woo-product-importer', array('WebPres_Woo_Product_Importer', 'render_admin_action'));
        }
        
        public static function render_admin_action() {
            $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'upload';
            require_once(plugin_dir_path(__FILE__).'woo-product-importer-common.php');
            require_once(plugin_dir_path(__FILE__)."woo-product-importer-{$action}.php");
        }
        
        public static function render_ajax_action() {
            require_once(plugin_dir_path(__FILE__)."woo-product-importer-ajax.php");
            die(); // this is required to return a proper result
        }

        public function activation() {
            self::add_cap();
        }

        // Add the new capability to all roles having a certain built-in capability
        private static function add_cap() {
            $roles = get_editable_roles();
            foreach ($GLOBALS['wp_roles']->role_objects as $key => $role) {
                if (isset($roles[$key]) && $role->has_cap('delete_product')) {
                    $role->add_cap('vendor_bulk_import');
                }
            }
        }
    }
    
    $webpres_woo_product_importer = new WebPres_Woo_Product_Importer();
