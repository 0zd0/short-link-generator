<?php
	
	namespace Pavl\Short_Link_Generator;
	
	class Session {
		public static string $key_last_time_click = 'last_click_time';
		
		public function start_session() {
			if ( ! session_id() ) {
				session_start();
			}
		}
		
		public function get_last_time_click( int $post_id ): int {
			$session_last_click_time = 0;
			if ( isset( $_SESSION[ $this::$key_last_time_click ][ $post_id ] ) ) {
				$session_last_click_time = (int) $_SESSION[ $this::$key_last_time_click ][ $post_id ];
			}
			
			return $session_last_click_time;
		}
		
		public function save_last_time_click( int $post_id, int $time ): void {
			if ( ! isset( $_SESSION[ $this::$key_last_time_click ] ) ) {
				$_SESSION[ $this::$key_last_time_click ] = array();
			}
			$_SESSION[ $this::$key_last_time_click ][ $post_id ] = $time;
		}
	}
