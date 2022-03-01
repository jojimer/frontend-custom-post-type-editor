<?php

/* 
	Plugin Name: Field Report CRUD
	Description: Frontend Field Report CRUD
	Version: 1.0.0
	Author: Jojimer Castino
	Author URI: jojimercastino.com
*/

if( ! defined('ABSPATH')) exit(); // No direct Access Allowed

/**
 * Define Plugins Constant
 * 
*/
define('FFRCRUD_PATH',trailingslashit(plugin_dir_path(__FILE__)));
define('FFRCRUD_URL',trailingslashit(plugins_url('/',__FILE__)));

/**
 * Load Classes
 * 
*/
include_once FFRCRUD_PATH.'classes/Template.php';
include_once FFRCRUD_PATH.'classes/Control.php';

class FieldReportShortcodes {
	private $_template;
	private $_control;

	function __construct(){
		//Instanciate Classes
		$this->_template = new Template;
		$this->_control = new Control;

		//Initiate Plugin Settings 
		add_action('admin_menu', [$this, 'pluginPage']);
		add_action('wp_enqueue_scripts', [$this, 'load_scripts']);
		add_shortcode('new_field_report_post', [$this, 'load_frontend']);

		//Creating Ajax call to add, update, delete field report
		add_action('wp_ajax_nopriv_fr_request', [$this, 'control_process']);
		add_action('wp_ajax_fr_request', [$this, 'control_process']);
	}

	// Add Plugin Page on settings menu
	function pluginPage() {
		add_options_page('Frontend Field Report Shortcodes','Field Report Shortcodes','manage_options','frontend-field-report-shortcodes',[$this,'show_shortcode_in_admin']);
	}

	// Load JS script from React	
	function load_scripts() {
	    wp_enqueue_script( 'frontend-fr-crud', FFRCRUD_URL . 'includes/index.js', [ 'jquery' ], null, true );
	    wp_enqueue_style('main_css',FFRCRUD_URL . 'includes/main.css',false,null);
	    $ajax_params = array(
	        'ajax_url' => admin_url('admin-ajax.php'),
	        'ajax_nonce' => wp_create_nonce('my_nonce'),
	    );
	    wp_localize_script( 'frontend-fr-crud', 'fr_crudajax', $ajax_params );
	}

	function load_frontend($attr){
		return $this->_template->getFrontEndTemplate($attr);
	}

	function show_shortcode_in_admin(){
		return $this->_template->viewShortcodesInAdmin();
	}

	function control_process(){
		return $this->_control->processRequest();
	}
}

$fieldReportsShortcode = new FieldReportShortcodes;