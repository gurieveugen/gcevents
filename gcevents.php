<?php
/*
Plugin Name: GC Events for WordPress
Plugin URI: http://www.gurievcreative.com
Description: Events manager for your WordPress
Version: 1.0
Author: Guriev Creative
Author URI: http://www.gurievcreative.com
*/
require_once dirname(__FILE__).'/gcbase.php';
require_once dirname(__FILE__).'/gcoptions_page.php';
require_once dirname(__FILE__).'/gcevents_post_type.php';
require_once dirname(__FILE__).'/gcvenues_post_type.php';
require_once dirname(__FILE__).'/gcorganizers_post_type.php';
require_once dirname(__FILE__).'/gccalendar_widget.php';

class GCEvents extends GCBase{	
	//                          __              __      
	//   _________  ____  _____/ /_____ _____  / /______
	//  / ___/ __ \/ __ \/ ___/ __/ __ `/ __ \/ __/ ___/
	// / /__/ /_/ / / / (__  ) /_/ /_/ / / / / /_(__  ) 
	// \___/\____/_/ /_/____/\__/\__,_/_/ /_/\__/____/  
	const LANG_FILE_EN = 'gcevents_en.php';
	const LANG_FILE_RU = 'gcevents_ru.php';

	//                __  _                 
	//   ____  ____  / /_(_)___  ____  _____
	//  / __ \/ __ \/ __/ / __ \/ __ \/ ___/
	// / /_/ / /_/ / /_/ / /_/ / / / (__  ) 
	// \____/ .___/\__/_/\____/_/ /_/____/  
	//     /_/                              
	public $gcevent_post;    
	public $gcvenues_post;   
	public $gcorganizer_post;
	public $gcoptions_page;  
	//                    __  __              __    
	//    ____ ___  ___  / /_/ /_  ____  ____/ /____
	//   / __ `__ \/ _ \/ __/ __ \/ __ \/ __  / ___/
	//  / / / / / /  __/ /_/ / / / /_/ / /_/ (__  ) 
	// /_/ /_/ /_/\___/\__/_/ /_/\____/\__,_/____/  	
	public function __construct()
	{	
		parent::__construct('gcevents_ru.php');
		// =========================================================
		// Add new post types
		// =========================================================
		$this->gcevent_post     = new GCEventsPost();		
		$this->gcvenues_post    = new GCVenuesPost();
		$this->gcorganizer_post = new GCOrganizersPost();
		$this->gcoptions_page   = new GCOptionsPage();
		// =========================================================
		// Add JAVA and CSS
		// =========================================================		
		// =========================================================
		// Fot theme
		// =========================================================
		if(is_admin())
		{
			wp_enqueue_script('gc-scripts', $this->plugin_url.'/js/gcscripts_admin.js', array('jquery'));
			wp_enqueue_style('tribe-jquery-ui',  $this->plugin_url.'/css/jquery-ui.min.css');
			wp_enqueue_script('tribe-jquery-ui-datepicker', $this->plugin_url.'/js/jquery.ui.datepicker.js', array('jquery-ui-core'), null, true);
		}
		// =========================================================
		// For Admin
		// =========================================================
		else
		{
			wp_enqueue_style('calendario',  $this->plugin_url.'/css/calendar.css');
			wp_enqueue_style('calendario-cust2',  $this->plugin_url.'/css/custom_2.css');
			wp_enqueue_script('calendario', $this->plugin_url.'/js/jquery.calendario.js', array('jquery'));
			wp_enqueue_script('modernizr', $this->plugin_url.'/js/modernizr.js', array('jquery'));
			wp_enqueue_script('gc-scripts', $this->plugin_url.'/js/gcscripts.js', array('jquery'));
		}		

		wp_enqueue_style('font-awesome', $this->plugin_url.'/css/font-awesome.min.css');
		wp_enqueue_style('admin-styles', $this->plugin_url.'/css/admin_styles.css');				
		
		add_theme_support('post-thumbnails');		
	}
}
// =========================================================
// LAUNCH
// =========================================================
$GLOBALS['gcevents'] = new GCEvents();
