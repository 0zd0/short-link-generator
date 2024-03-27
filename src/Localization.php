<?php
	
	namespace Pavl\Short_Link_Generator;
	
	class Localization {
		/**
		 * Registers the localization
		 *
		 * @return void
		 */
		public function register(): void {
			add_action( 'plugins_loaded', [ $this, 'load' ] );
		}
		
		/**
		 * Loads the plugin text domain for localization
		 *
		 * @return void
		 */
		public static function load(): void {
			load_plugin_textdomain( 'short-link-generator', false, basename( SHORT_LINK_GENERATOR_PATH ) . '/languages/' );
		}
	}
