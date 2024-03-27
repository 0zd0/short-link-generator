<?php
	
	/*
	Plugin Name: Short Link Generator
	Description: Plugin for generating shortened links
	Version: 1.0
	Author: Pavl
	License: A "Slug" license name e.g. GPL2
	Domain Path: /languages
	*/
	
	namespace Pavl\Short_Link_Generator;
	
	use Pavl\Short_Link_Generator\Init;
	
	if ( ! defined( 'ABSPATH' ) ) {
		die();
	}
	
	define( 'SHORT_LINK_GENERATOR_PATH', dirname( __FILE__ ) );
	define( 'SHORT_LINK_GENERATOR_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
	
	if ( file_exists( SHORT_LINK_GENERATOR_PATH . '/vendor/autoload.php' ) ) {
		require_once SHORT_LINK_GENERATOR_PATH . '/vendor/autoload.php';
	}
	Init::init();
	register_activation_hook( __FILE__, [ Init::get_instance(), 'activate' ] );
	register_deactivation_hook( __FILE__, [ Init::get_instance(), 'deactivate' ] );

