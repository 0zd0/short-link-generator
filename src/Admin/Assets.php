<?php
	
	namespace Pavl\Short_Link_Generator\Admin;
	
	/**
	 * To work with assets in the admin panel
	 */
	class Assets {
		/**
		 * Registers admin assets hooks
		 *
		 * @return void
		 */
		public function register() {
			add_action( 'admin_enqueue_scripts', [$this, 'connect_scripts'], 99 );
		}
		
		/**
		 * Enqueues the required scripts and styles
		 *
		 * @return void
		 */
		public function connect_scripts() {
			wp_enqueue_style( 'slg-admin', SHORT_LINK_GENERATOR_URL .'/assets/admin/css/main.css' );
			wp_enqueue_script( 'slg-admin-post', SHORT_LINK_GENERATOR_URL .'/assets/admin/js/post.js', ['jquery'] );
		}
	}
