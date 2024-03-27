<?php
	
	namespace Pavl\Short_Link_Generator\Post\Type;
	
	use Pavl\Short_Link_Generator\Config;
	
	class Short_Link {
		private array  $labels                  = [];
		private array  $new_columns             = [];
		private string $slug                    = 'short-link';
		private string $field_key_redirect      = 'short-link-redirect';
		private string $column_key_page_url     = 'page_url';
		private string $column_key_redirect_url = 'redirect_url';
		
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
				$this->column_key_page_url     => __( 'Page URL', 'short-link-generator' ),
				$this->column_key_redirect_url => __( 'Redirect URL', 'short-link-generator' ),
			];
		}
		
		public function register(): void {
			add_action( 'init', array( $this, 'register_post_type' ) );
		}
		
		public function register_post_type(): void {
			$args = array(
				'labels'             => $this->labels,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => $this->slug ),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title' )
			);
			
			register_post_type( $this->slug, $args );
		}
		
		public function add_meta_boxes(): void {
			add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
			add_action( "save_post_{$this->slug}", array( $this, 'register_save_meta_boxes' ) );
		}
		
		public function register_meta_boxes(): void {
			add_meta_box(
				'short_link_meta_box_main',
				__( 'Link info', 'short-link-generator' ),
				array( $this, 'render_meta_box' ),
				$this->slug,
			);
		}
		
		public function render_meta_box( object $post ) {
			$redirect_value = get_post_meta( $post->ID, $this->field_key_redirect, true );
			$redirect_label = __( 'Redirect to:', 'short-link-generator' );
			$html_output    = <<<HTML
				<div class="slg-meta-box__field">
					<label for="{$this->field_key_redirect}" class="slg-meta-box__label">{$redirect_label}</label>
			        <input type="text" id="{$this->field_key_redirect}" name="{$this->field_key_redirect}" value="%s" class="slg-meta-box__input">
				</div>
			HTML;
			echo sprintf( $html_output, esc_attr( $redirect_value ) );
		}
		
		public function register_save_meta_boxes( string $post_id ): void {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			$redirect_value = $_POST[ $this->field_key_redirect ] ?? '';
			if (filter_var( $redirect_value, FILTER_VALIDATE_URL )) {
				remove_action( 'save_post', array( $this, 'register_save_meta_boxes' ) );
				update_post_meta( $post_id, $this->field_key_redirect, sanitize_text_field( $_POST[ $this->field_key_redirect ] ) );
				add_action( 'save_post', array( $this, 'register_save_meta_boxes' ) );
			}
		}
		
		public function add_columns(): void {
			add_filter( "manage_{$this->slug}_posts_columns", array( $this, 'register_columns' ) );
			add_action( "manage_{$this->slug}_posts_custom_column", array( $this, 'register_columns_content' ) );
		}
		
		public function register_columns( array $columns ): array {
			return array_slice( $columns, 0, 2 ) + $this->new_columns + $columns;
		}
		
		public function register_columns_content( string $column_name ): void {
			if ($column_name == $this->column_key_page_url) {
				$post_url = get_the_permalink();
				echo sprintf('<a href="%s">%s</a>', esc_url($post_url), urldecode(esc_url($post_url)));
			}
			elseif ($column_name == $this->column_key_redirect_url) {
				$redirect_value = get_post_meta( get_the_ID(), $this->field_key_redirect, true );
				echo sprintf('<a href="%s">%s</a>', esc_url($redirect_value), urldecode(esc_url($redirect_value)));
			}
		}
	}
