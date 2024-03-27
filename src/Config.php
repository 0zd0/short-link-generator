<?php
	
	namespace Pavl\Short_Link_Generator;
	
	/**
	 * Global config
	 */
	class Config {
		/**
		 * Version plugin
		 */
		const VERSION = '1.0';
		
		/**
		 * How long is a click unique
		 */
		const SECONDS_UNIQUE_CLICK = MINUTE_IN_SECONDS * 2;
	}
