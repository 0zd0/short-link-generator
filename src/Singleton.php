<?php
	
	namespace Pavl\Short_Link_Generator;
	
	abstract class Singleton {
		private static array $instances = [];
		
		private function __construct() {
		}
		
		public static function get_instance(): Singleton {
			if ( ! isset( self::$instances[ static::class ] ) ) {
				self::$instances[ static::class ] = new static();
			}
			
			return self::$instances[ static::class ];
		}
	}
