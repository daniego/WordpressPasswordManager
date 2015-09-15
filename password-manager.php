<?php   
/* 
Plugin Name: Password Manager
Plugin URI: http://www.daniego.com
Description: Plugin for store password 
Author: Daniel Floris
Version: 0.2 betax
Author URI: http://www.daniego.com
License: GPLv2
*/

/* 
Copyright (C) 2015 dfloris

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
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

global $wpdb;
define('PASSMANAGER_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('PASSMANAGER_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('TABLE_GRP', $wpdb->prefix . "pmgr_grp");
define('TABLE_USERGRP', $wpdb->prefix . "pmgr_usergrp");
define('TABLE_PASS', $wpdb->prefix . "pmgr_pass");
define('TABLE_TYPE', $wpdb->prefix . "pmgr_type");
define('TABLE_ASS', $wpdb->prefix . "pmgr_ass");
define('TABLE_LOGS', $wpdb->prefix . "pmgr_logs");

require 'inc/front_password.php';
require 'inc/admin_groups.php';
require 'inc/admin_usersgroup.php';
require 'inc/admin-passwordtype.php';


register_activation_hook( __FILE__, 'pmgr_install' );
//create table on activation
function pmgr_install()
{

    $sql1 = "CREATE TABLE IF NOT EXISTS " .TABLE_GRP. " (
            id INT(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            trash  varchar(255) NOT NULL,
            UNIQUE KEY id (id),
            UNIQUE (name)
            );";
    $sql2 = "CREATE TABLE IF NOT EXISTS " .TABLE_USERGRP. " (
            id INT(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            trash  varchar(255) NOT NULL,
            UNIQUE KEY id (id),
            UNIQUE (name)
            );";
   		
    $sql3 = "CREATE TABLE IF NOT EXISTS " .TABLE_PASS. " (
            id_pass INT(9) NOT NULL AUTO_INCREMENT,
            user varchar(255) NOT NULL,
            pass varchar(255) NOT NULL,
            host varchar(255) NOT NULL,
            id_grp varchar(255) NOT NULL,
            id_type varchar(255) NOT NULL,
            name varchar(255) NOT NULL,
            description  varchar(255) NOT NULL,
            trash  varchar(255) NOT NULL,
            UNIQUE KEY id (id)
            );";
    		
    $sql4 = "CREATE TABLE IF NOT EXISTS " .TABLE_TYPE. " (
            id INT(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            UNIQUE KEY id (id)
            );"; 
    
    $sql5 = "CREATE TABLE IF NOT EXISTS " .TABLE_ASS. " (
            id INT(9) NOT NULL AUTO_INCREMENT,
            type varchar(255) NOT NULL,
            user_id INT(9) NULL,
            usergroup_id INT(9) NULL,
            pass_id varchar(255) NULL,
            UNIQUE KEY id (id)
            );"; 
    
    $sql6 = "CREATE TABLE IF NOT EXISTS " .TABLE_LOGS. " (
            id INT(9) NOT NULL AUTO_INCREMENT,
            user INT(9) NOT NULL,
            action varchar(255) NOT NULL,
            ip varchar(255) NOT NULL,
            date varchar(255) NOT NULL,
            UNIQUE KEY id (id)
            );";   		 		

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
    dbDelta($sql2);
    dbDelta($sql3);
    dbDelta($sql4);
    dbDelta($sql5);
    dbDelta($sql6);
    

    /**Insert one record in the table by using the array method**/
    $ini_software = "Wordpress";
    $ini_developer = "Wordpress Foundation";
    $ini_type = "Blog";
    $ini_license = "GNU";

    //$results = $wpdb->insert( $table_name1, array( 'software' => $ini_software, 'developer' => $ini_developer, 'type' => $ini_type, 'license' => $ini_license ) );     
}

register_deactivation_hook(__FILE__, 'pmgr_uninstall');
//register_uninstall_hook(__FILE__, 'pmgr_uninstall');

//delete table on unistall
function pmgr_uninstall(){
    
    global $wpdb;

    $sql1 = "DROP TABLE IF EXISTS ".TABLE_GRP;
    $sql2 = "DROP TABLE IF EXISTS ".TABLE_USERGRP;
    $sql3 = "DROP TABLE IF EXISTS ".TABLE_PASS;
    $sql4 = "DROP TABLE IF EXISTS ".TABLE_TYPE;
    $sql5 = "DROP TABLE IF EXISTS ".TABLE_ASS;
    $sql6 = "DROP TABLE IF EXISTS ".TABLE_LOGS;
    
    $wpdb->query($sql1);
    $wpdb->query($sql2);
    $wpdb->query($sql3);
    $wpdb->query($sql4);
    $wpdb->query($sql5);
    $wpdb->query($sql6);
}

//wp_register_style( 'password-manager', plugins_url( 'password-manager/style.css' ) );
//wp_enqueue_style( 'password-manager' );

add_action( 'admin_menu', 'passwordmanager_menu' );
//add admin menu
function passwordmanager_menu() {
        add_menu_page('Password Manager Option', 'Password Manager', 'manage_options', 'password-manager_options', 'password_manager_options');
        //add_menu_page('Password Manager Option', 'General options', 'manage_options', 'password-manager_options', 'password_manager_options');
        add_submenu_page( 'password-manager_options', 'Passowrd groups', 'Passowrd groups', 'manage_options', 'passwordgroups-passwordmanager', 'passwordmanager_manage_groups');
        add_submenu_page( 'password-manager_options', 'Users Group', 'Users group', 'manage_options', 'usersgroup-passwordmanager', 'passwordmanager_manage_usersgroup');
        add_submenu_page( 'password-manager_options', 'Password type', 'Password type', 'manage_options', 'passtype-passwordmanager', 'passwordmanager_manage_passwordtype');
        add_submenu_page( 'password-manager_options', 'About', 'About', 'manage_options', 'about-passwordmanager', 'passwordmanager_about');
}


function password_manager_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
        echo '<h1>General Options</h1>';
	echo '<p>Here is where the form would go if I actually had options.</p>';
        echo 'aggiungere option per eliminazione dei dati alla disinstallazione<br>';
        echo 'quando modifichi un gruppo o usergroup rinominalo<br>';
        echo 'load icon on group assosiation list<br>';
        echo 'On type manage function on delete check if any password have this type assigned<br>';
        echo 'Refine type page(db flag on deletation)<br>';
	echo '</div>';
}  



function passwordmanager_about() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>ABOUT!!!!!!!!!!!!!!!!!!!!!!!Here is where the form would go if I actually had options.</p>';
	echo '</div>';
} 

// Hook for adding admin menus
add_action('admin_menu', 'mt_add_pages');

// action function for above hook
function mt_add_pages() {
    // Add a new submenu under Settings:
    add_options_page(__('Test Settings','menu-test'), __('Test Settings','menu-test'), 'manage_options', 'testsettings', 'mt_settings_page');

    // Add a new submenu under Tools:
    add_management_page( __('Test Tools','menu-test'), __('Test Tools','menu-test'), 'manage_options', 'testtools', 'mt_tools_page');

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('Test Toplevel','menu-test'), __('Test Toplevel','menu-test'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page' );

    // Add a submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Test Sublevel','menu-test'), __('Test Sublevel','menu-test'), 'manage_options', 'sub-page', 'mt_sublevel_page');

    // Add a second submenu to the custom top-level menu:
    add_submenu_page('mt-top-level-handle', __('Test Sublevel 2','menu-test'), __('Test Sublevel 2','menu-test'), 'manage_options', 'sub-page2', 'mt_sublevel_page2');
}

// mt_settings_page() displays the page content for the Test settings submenu
function mt_settings_page() {
    echo "<h2>" . __( 'Test Settings', 'menu-test' ) . "</h2>";
}

// mt_tools_page() displays the page content for the Test Tools submenu
function mt_tools_page() {
    echo "<h2>" . __( 'Test Tools', 'menu-test' ) . "</h2>";
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function mt_toplevel_page() {
    echo "<h2>" . __( 'Test Toplevel', 'menu-test' ) . "</h2>";
}

// mt_sublevel_page() displays the page content for the first submenu
// of the custom Test Toplevel menu
function mt_sublevel_page() {
    echo "<h2>" . __( 'Test Sublevel', 'menu-test' ) . "</h2>";
}

// mt_sublevel_page2() displays the page content for the second submenu
// of the custom Test Toplevel menu
function mt_sublevel_page2() {
    echo "<h2>" . __( 'Test Sublevel2', 'menu-test' ) . "</h2>";
}


//SHORTCODE DEFINITIONs
add_shortcode( 'passwordmanager', 'passwd_shortcode' );


function passwd_shortcode($action){
    //print_r($action);
    if(isset($action['action'])){
        if($action['action']=== 'list'){
            echo 'listo le password';
            passwordmanager_list_password();
            $current_user = get_current_user_id();
        }
        if($action['action']=== 'add'){
            echo 'form inserimento password';
            $current_user = get_current_user_id();
            passwordmanager_add_password();
        }
    }
}
$wpdb->show_errors();

