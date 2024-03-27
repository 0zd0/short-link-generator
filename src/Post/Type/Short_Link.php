<?php
	
	namespace Pavl\Short_Link_Generator\Post\Type;
	
	use Pavl\Short_Link_Generator\Config;
	
	class Short_Link {
		public static string $slug                            = 'short-link';
		private array        $labels                          = [];
		private array        $new_columns                     = [];
		public static string $field_key_redirect              = 'short-link-redirect';
		public static string $field_key_number_clicks         = 'short-link-number-clicks';
		public static string $field_key_number_unique_clicks  = 'short-link-number-unique-clicks';
		private string       $column_key_page_url             = 'page-url';
		private string       $column_key_redirect_url         = 'redirect-url';
		private string       $column_key_number_clicks        = 'number-clicks';
		private string       $column_key_number_unique_clicks = 'number-unique-clicks';
		
		/**
		 * Constructor to set labels and new columns
		 */
		public function __construct() {
			$this->labels      = [
				'name'               => __( 'Short Links', 'short-link-generator' ),
				'singular_name'      => __( 'Short Link', 'short-link-generator' ),
				'menu_name'          => __( 'Short Links', 'short-link-generator' ),
				'name_admin_bar'     => __( 'Short Links', 'short-link-generator' ),
				'add_new'            => __( 'Add New', 'short-link-generator' ),
				'add_new_item'       => __( 'Add New Short Link', 'short-link-generator' ),
				'new_item'           => __( 'New Short Link', 'short-link-generator' ),
				'edit_item'          => __( 'Edit Short Link', 'short-link-generator' ),
				'view_item'          => __( 'View Short Link', 'short-link-generator' ),
				'all_items'          => __( 'All Short Links', 'short-link-generator' ),
				'search_items'       => __( 'Search Short Links', 'short-link-generator' ),
				'parent_item_colon'  => __( 'Parent Short Link:', 'short-link-generator' ),
				'not_found'          => __( 'No Short Links found', 'short-link-generator' ),
				'not_found_in_trash' => __( 'No Short Links found in Trash', 'short-link-generator' )
			];
			$this->new_columns = [
				$this->column_key_page_url             => __( 'Page URL', 'short-link-generator' ),
				$this->column_key_redirect_url         => __( 'Redirect URL', 'short-link-generator' ),
				$this->column_key_number_clicks        => __( 'Number of clicks', 'short-link-generator' ),
				$this->column_key_number_unique_clicks => __( 'Number of unique clicks', 'short-link-generator' ),
			];
		}
		
		/**
		 * Registers post type registration hook
		 *
		 * @return void
		 */
		public function register(): void {
			add_action( 'init', array( $this, 'register_post_type' ) );
		}
		
		/**
		 * Registers the custom post type
		 *
		 * @return void
		 */
		public function register_post_type(): void {
			$args = array(
				'labels'             => $this->labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $this::$slug ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title' )
			);
			
			register_post_type( $this::$slug, $args );
		}
		
		/**
		 * Registers meta boxes hooks
		 *
		 * @return void
		 */
		public function add_meta_boxes(): void {
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
			add_action( "save_post_{$this::$slug}", array( $this, 'register_save_meta_boxes' ) );
		}
		
		/**
		 * Registers meta boxes
		 *
		 * @return void
		 */
		public function register_meta_boxes(): void {
			add_meta_box(
				'short_link_meta_box_main',
				__( 'Link info', 'short-link-generator' ),
				array( $this, 'render_meta_box' ),
				$this::$slug,
			);
		}
		
		/**
		 * Renders the meta box content
		 *
		 * @param object $post
		 *
		 * @return void
		 */
		public function render_meta_box( object $post ) {
			$redirect_value = get_post_meta( $post->ID, self::$field_key_redirect, true );
			$redirect_label = __( 'Redirect to:', 'short-link-generator' );
			$redirect_key   = self::$field_key_redirect;
			$html_output    = <<<HTML
				<div class="slg-meta-box__field">
					<label for="{$redirect_key}" class="slg-meta-box__label">{$redirect_label}</label>
			        <input type="text" id="{$redirect_key}" name="{$redirect_key}" value="%s" class="slg-meta-box__input">
				</div>
			HTML;
			echo sprintf(
				$html_output,
				esc_attr( $redirect_value )
			);
		}
		
		/**
		 * Saves meta box data
		 *
		 * @param string $post_id
		 *
		 * @return void
		 */
		public function register_save_meta_boxes( string $post_id ): void {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			$redirect_value = $_POST[ self::$field_key_redirect ] ?? '';
			if ( filter_var( $redirect_value, FILTER_VALIDATE_URL ) ) {
				remove_action( 'save_post', array( $this, 'register_save_meta_boxes' ) );
				update_post_meta( $post_id, self::$field_key_redirect, sanitize_text_field( $_POST[ self::$field_key_redirect ] ) );
				add_action( 'save_post', array( $this, 'register_save_meta_boxes' ) );
			}
		}
		
		/**
		 * Registers columns hooks
		 *
		 * @return void
		 */
		public function add_columns(): void {
			add_filter( "manage_{$this::$slug}_posts_columns", array( $this, 'register_columns' ) );
			add_action( "manage_{$this::$slug}_posts_custom_column", array( $this, 'register_columns_content' ) );
			add_filter( "manage_edit-{$this::$slug}_sortable_columns", array( $this, 'register_sortable_columns' ) );
		}
		
		/**
		 * Add sorting to new columns
		 *
		 * @param array $columns
		 *
		 * @return array
		 */
		public function register_sortable_columns( array $columns ): array {
			$columns[ $this->column_key_number_clicks ]        = $this->column_key_number_clicks;
			$columns[ $this->column_key_number_unique_clicks ] = $this->column_key_number_unique_clicks;
			
			return $columns;
		}
		
		/**
		 * Add new columns
		 *
		 * @param array $columns
		 *
		 * @return array
		 */
		public function register_columns( array $columns ): array {
			return array_slice( $columns, 0, 2 ) + $this->new_columns + $columns;
		}
		
		/**
		 * Add columns content
		 *
		 * @param string $column_name
		 *
		 * @return void
		 */
		public function register_columns_content( string $column_name ): void {
			if ( $column_name == $this->column_key_page_url ) {
				$post_url = get_the_permalink();
				echo sprintf(
					'<a href="%s">%s</a>',
					esc_url( $post_url ),
					urldecode( esc_url( $post_url ) )
				);
			} elseif ( $column_name == $this->column_key_redirect_url ) {
				$redirect_value = get_post_meta( get_the_ID(), self::$field_key_redirect, true );
				echo sprintf(
					'<a href="%s">%s</a>',
					esc_url( $redirect_value ),
					urldecode( esc_url( $redirect_value ) )
				);
			} elseif ( $column_name == $this->column_key_number_clicks ) {
				$number_clicks_value = get_post_meta( get_the_ID(), self::$field_key_number_clicks, true );
				echo sprintf(
					'%d', $number_clicks_value
				);
			} elseif ( $column_name == $this->column_key_number_unique_clicks ) {
				$number_unique_clicks_value = get_post_meta( get_the_ID(), self::$field_key_number_unique_clicks, true );
				echo sprintf(
					'%d',
					$number_unique_clicks_value
				);
			}
		}
		
		/**
		 * Registers submit box hooks
		 *
		 * @return void
		 */
		public function add_info() {
			add_action( 'post_submitbox_misc_actions', [ $this, 'register_post_info_submitbox' ] );
		}
		
		/**
		 * Adds additional information to the post submit box
		 *
		 * @return void
		 */
		public function register_post_info_submitbox() {
			global $post;
			$clicks_value = get_post_meta( $post->ID, self::$field_key_number_clicks, true );
			$unique_clicks_value = get_post_meta( $post->ID, self::$field_key_number_unique_clicks, true );
			$clicks_label = __( 'Number of clicks:', 'short-link-generator' );
			$unique_clicks_label = __( 'Number of unique clicks:', 'short-link-generator' );
			$html_output    = <<<HTML
				<div class="misc-pub-section dashicons-admin-links misc-pub-section__clicks">
					{$clicks_label}
					<b>{$clicks_value}</b>
				</div>
				<div class="misc-pub-section dashicons-admin-links misc-pub-section__unique-clicks">
					{$unique_clicks_label}
					<b>{$unique_clicks_value}</b>
				</div>
			HTML;
			echo $html_output;
		}
		
		/**
		 * Retrieves the redirect URL for a post
		 *
		 * @param int $post_id
		 *
		 * @return string
		 */
		public static function get_redirect( int $post_id ): string {
			return get_post_meta( $post_id, self::$field_key_redirect, true );
		}
		
		/**
		 * Retrieves the number of clicks for a post
		 *
		 * @param int $post_id
		 *
		 * @return int
		 */
		public static function get_number_clicks( int $post_id ): int {
			$value = get_post_meta( $post_id, self::$field_key_number_clicks, true );
			
			return (int) $value;
		}
		
		/**
		 * Retrieves the number of unique clicks for a post
		 *
		 * @param int $post_id
		 *
		 * @return int
		 */
		public static function get_unique_number_clicks( int $post_id ): int {
			$value = get_post_meta( $post_id, self::$field_key_number_unique_clicks, true );
			
			return (int) $value;
		}
		
		/**
		 * Increases the click count for a post
		 *
		 * @param int $post_id
		 *
		 * @return void
		 */
		public static function add_click( int $post_id ): void {
			$clicks = self::get_number_clicks( $post_id );
			update_post_meta( $post_id, self::$field_key_number_clicks, ++ $clicks );
		}
		
		/**
		 * Increases the unique click count for a post
		 *
		 * @param int $post_id
		 *
		 * @return void
		 */
		public static function add_unique_click( int $post_id ): void {
			$clicks = self::get_unique_number_clicks( $post_id );
			update_post_meta( $post_id, self::$field_key_number_unique_clicks, ++ $clicks );
		}
	}
