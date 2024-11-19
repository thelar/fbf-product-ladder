<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://4x4tyres.co.uk
 * @since      1.0.0
 *
 * @package    Fbf_Product_Ladder
 * @subpackage Fbf_Product_Ladder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fbf_Product_Ladder
 * @subpackage Fbf_Product_Ladder/admin
 * @author     Kevin Price-Ward <kevin.price-ward@4x4tyres.co.uk>
 */
class Fbf_Product_Ladder_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fbf_Product_Ladder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fbf_Product_Ladder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fbf-product-ladder-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fbf_Product_Ladder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fbf_Product_Ladder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fbf-product-ladder-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Add ACF options page
     */
    public function add_options_page()
    {
        if (function_exists('acf_add_options_page')) {
            $child = acf_add_options_sub_page([
                'page_title' => 'Test Product Ladder Options',
                'menu_title' => 'Test Product Ladder Options',
                'parent_slug' => 'theme-general-settings'
            ]);
        }
    }

public function model_taxonomy_filter($args, $field, $post_id)
{
    $brand_id = filter_var($_POST['brand_id'], FILTER_SANITIZE_STRING);
    if(!empty($brand_id)){
        $product_args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'tax_query' => [
                [
                    'taxonomy' => 'pa_brand-name',
                    'field' => 'term_id',
                    'terms' => $brand_id,
                ]
            ],
        ];
        $product_ids = get_posts($product_args);
        if(!empty($product_ids)){
            $args['object_ids'] = $product_ids; // Array to pass here is all the product ids of Brand
        }
    }
    return $args;
}

    /**
     * Define fields
     */
    public function add_acf_fields()
    {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group( array(
            'key' => 'group_673371613b006',
            'title' => 'Test Brand Model',
            'fields' => array(
                array(
                    'key' => 'field_6733716272bd9',
                    'label' => 'Brand',
                    'name' => 'brand',
                    'aria-label' => '',
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'taxonomy' => 'pa_brand-name',
                    'add_term' => 0,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'id',
                    'field_type' => 'select',
                    'allow_null' => 0,
                    'allow_in_bindings' => 0,
                    'bidirectional' => 0,
                    'multiple' => 0,
                    'bidirectional_target' => array(
                    ),
                ),
                array(
                    'key' => 'field_673371aa72bda',
                    'label' => 'Model',
                    'name' => 'model',
                    'aria-label' => '',
                    'type' => 'taxonomy',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'taxonomy' => 'pa_model-name',
                    'add_term' => 0,
                    'save_terms' => 0,
                    'load_terms' => 0,
                    'return_format' => 'id',
                    'field_type' => 'select',
                    'allow_null' => 0,
                    'allow_in_bindings' => 0,
                    'bidirectional' => 0,
                    'multiple' => 0,
                    'bidirectional_target' => array(
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'acf-options-test-product-ladder-options',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ) );
    }
}
