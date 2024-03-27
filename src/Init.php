<?php
	
	namespace Pavl\Short_Link_Generator;
	
    use Pavl\Short_Link_Generator\Admin;
	
	class Init extends Singleton {
        public function activate(): void {
        
        }
		public static function init(): void {
			$localization = new Localization();
            $localization->register();
            
            $post_type_short_link = new Post\Type\Short_Link();
			$post_type_short_link->register();
            $post_type_short_link->add_meta_boxes();
            $post_type_short_link->add_columns();
			
			$admin_assets = new Admin\Assets();
            $admin_assets->register();
            
		}
	}
