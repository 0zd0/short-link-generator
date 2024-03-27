<?php
	
	namespace Pavl\Short_Link_Generator;
	
	class Localization {
		public function register(): void {
			add_action( 'init', [ $this, 'load' ] );
		}
		
		public static function load(): void {
			load_plugin_textdomain( 'short-link-generator', false, basename( SHORT_LINK_GENERATOR_PATH ) . '/languages/' );
		}
	}
