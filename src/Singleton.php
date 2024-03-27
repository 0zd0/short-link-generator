<?php
	
	namespace Pavl\Short_Link_Generator;
	
	/**
	 * Class for working singleton
	 */
	abstract class Singleton {
		/**
		 * @var array $instances singleton instances
		 */
		private static array $instances = [];
		
		/**
		 * Private constructor to prevent instantiation from outside
		 */
		private function __construct() {
		}
		
		/**
		 * Retrieves the singleton instance of the class
		 *
		 * @return Singleton
		 */
		public static function get_instance(): Singleton {
			if ( ! isset( self::$instances[ static::class ] ) ) {
				self::$instances[ static::class ] = new static();
			}
			
			return self::$instances[ static::class ];
		}
	}
