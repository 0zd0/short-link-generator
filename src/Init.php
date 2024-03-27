<?php
	
	namespace Pavl\Short_Link_Generator;
	
    use Pavl\Short_Link_Generator\Admin;
	
	class Init extends Singleton {
		private string $rewrite_option = 'rewrite_rules';
		
		/**
		 * Function to activate the plugin
		 *
		 * @return void
		 */
        public function activate(): void {
	        delete_option( $this->rewrite_option );
        }
		
		/**
		 * Function to deactivate the plugin
		 *
		 * @return void
		 */
		public function deactivate(): void {
			delete_option( $this->rewrite_option );
		}
		
		/**
		 * Initializes the plugin
		 *
		 * @return void
		 */
		public static function init(): void {
			$localization = new Localization();
            $localization->register();
            
            $post_type_short_link = new Post\Type\Short_Link();
			$post_type_short_link->register();
            $post_type_short_link->add_meta_boxes();
            $post_type_short_link->add_columns();
            $post_type_short_link->add_info();
			
			$admin_assets = new Admin\Assets();
            $admin_assets->register();
			
			$redirect = new Redirect();
            $redirect->register();
		}
	}
