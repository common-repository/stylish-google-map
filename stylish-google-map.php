<?php
/*
Plugin Name: Stylish Google Map
Description: Very simple way to add stylish google map to your wordpress site.
Version: 1.4
Author: Krishna H. Prajapati
Author URI: http://khprajapati.com.np
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

add_action( 'widgets_init', function(){
	register_widget( 'Stylish_google_map' );
});	

include('sgm_main.php');
include('sgm_widget.php');