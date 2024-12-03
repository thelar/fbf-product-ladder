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
                'page_title' => 'New Product Ladder Options',
                'menu_title' => 'New Product Ladder Options',
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

    public function tyre_product_cat_filter($args, $field, $post_id)
    {
        $product_args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids',
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => 'tyre',
                ]
            ],
        ];
        $product_ids = get_posts($product_args);
        if(!empty($product_ids)){
            $args['object_ids'] = $product_ids; // Array to pass here is all the product ids of Brand
        }
        return $args;
    }

    /**
     * Define fields
     */
    public function add_acf_fields()
    {
        /*if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }
        acf_add_local_field_group( array(
                'key' => 'group_673ce3c26c54f',
                'title' => 'Tyre Ladder',
                'fields' => array(
                    array(
                        'key' => 'field_673ce3c47f6b8',
                        'label' => 'Budget',
                        'name' => 'budget',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => 'Select the Tyre Brand and Models for the Budget position. For any given search, only 1 product is shown in the Budget position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Budget option will not be shown.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 0,
                        'max' => 0,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_673ce4407f6b9',
                                'label' => 'Brand',
                                'name' => 'brand',
                                'aria-label' => '',
                                'type' => 'taxonomy',
                                'instructions' => '',
                                'required' => 1,
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
                                'return_format' => '',
                                'field_type' => 'select',
                                'allow_null' => 1,
                                'allow_in_bindings' => 0,
                                'bidirectional' => 0,
                                'multiple' => 0,
                                'bidirectional_target' => array(
                                ),
                                'parent_repeater' => 'field_673ce3c47f6b8',
                            ),
                            array(
                                'key' => 'field_673ce4867f6ba',
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
                                'return_format' => '',
                                'field_type' => 'select',
                                'allow_null' => 1,
                                'allow_in_bindings' => 0,
                                'bidirectional' => 0,
                                'multiple' => 0,
                                'bidirectional_target' => array(
                                ),
                                'parent_repeater' => 'field_673ce3c47f6b8',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_673ce4ba7f6bb',
                        'label' => 'Mid range',
                        'name' => 'mid_range',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => 'Select the Tyre Brand and Models for the Mid-Range position. For any given search, only 1 product is shown in the Mid-Range position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Mid-Range option will not be shown.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 0,
                        'max' => 0,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_673ce4ba7f6be',
                                'label' => 'Brand',
                                'name' => 'brand',
                                'aria-label' => '',
                                'type' => 'taxonomy',
                                'instructions' => '',
                                'required' => 1,
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
                                'return_format' => '',
                                'field_type' => 'select',
                                'allow_null' => 1,
                                'allow_in_bindings' => 0,
                                'bidirectional' => 0,
                                'multiple' => 0,
                                'bidirectional_target' => array(
                                ),
                                'parent_repeater' => 'field_673ce4ba7f6bb',
                            ),
                            array(
                                'key' => 'field_673ce4ba7f6bf',
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
                                'return_format' => '',
                                'field_type' => 'select',
                                'allow_null' => 1,
                                'allow_in_bindings' => 0,
                                'bidirectional' => 0,
                                'multiple' => 0,
                                'bidirectional_target' => array(
                                ),
                                'parent_repeater' => 'field_673ce4ba7f6bb',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_673ce5217f6c0',
                        'label' => 'Premium',
                        'name' => 'premium',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => 'Select the Tyre Brand and Models for the Premium position. For any given search, only 1 product is shown in the Premium position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Premium option will not be shown.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 0,
                        'max' => 0,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_673ce5217f6c3',
                                'label' => 'Brand',
                                'name' => 'brand',
                                'aria-label' => '',
                                'type' => 'taxonomy',
                                'instructions' => '',
                                'required' => 1,
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
                                'allow_null' => 1,
                                'allow_in_bindings' => 0,
                                'bidirectional' => 0,
                                'multiple' => 0,
                                'bidirectional_target' => array(
                                ),
                                'parent_repeater' => 'field_673ce5217f6c0',
                            ),
                            array(
                                'key' => 'field_673ce5217f6c4',
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
                                'allow_null' => 1,
                                'allow_in_bindings' => 0,
                                'bidirectional' => 0,
                                'multiple' => 0,
                                'bidirectional_target' => array(
                                ),
                                'parent_repeater' => 'field_673ce5217f6c0',
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_673ce5637f6c5',
                        'label' => 'Brand order',
                        'name' => 'brand_order',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'pagination' => 0,
                        'min' => 0,
                        'max' => 0,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_673ce5977f6c6',
                                'label' => 'Brand',
                                'name' => 'brand',
                                'aria-label' => '',
                                'type' => 'taxonomy',
                                'instructions' => '',
                                'required' => 1,
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
                                'allow_null' => 1,
                                'allow_in_bindings' => 0,
                                'bidirectional' => 0,
                                'multiple' => 0,
                                'bidirectional_target' => array(
                                ),
                                'parent_repeater' => 'field_673ce5637f6c5',
                            ),
                            array(
                                'key' => 'field_673ce5b47f6c7',
                                'label' => 'Positions',
                                'name' => 'positions',
                                'aria-label' => '',
                                'type' => 'number',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 1,
                                'min' => 1,
                                'max' => '',
                                'allow_in_bindings' => 0,
                                'placeholder' => '',
                                'step' => '',
                                'prepend' => '',
                                'append' => '',
                                'parent_repeater' => 'field_673ce5637f6c5',
                            ),
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
            ) );*/

        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group( array(
            'key' => 'group_673ce3c26c54f',
            'title' => 'Tyre Ladder options',
            'fields' => array(
                array(
                    'key' => Fbf_Product_Ladder::FBF_PL_AT_MT_FIELD,
                    'label' => 'Tyre Ladder (AT/MT)',
                    'name' => 'tyre_ladder_atmt',
                    'aria-label' => '',
                    'type' => 'group',
                    'instructions' => 'These settings will apply to Tyre Searches where the result set includes 1 or more All Terrain or Mud Terrain tyre',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_673ce3c47f6b8',
                            'label' => 'Budget',
                            'name' => 'budget',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Tyre Brand and Models for the Budget position. For any given search, only 1 product is shown in the Budget position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Budget option will not be shown.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Budget Brand/Model',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673ce4407f6b9',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673ce3c47f6b8',
                                ),
                                array(
                                    'key' => 'field_673ce4867f6ba',
                                    'label' => 'Model(s)',
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
                                    'return_format' => 'object',
                                    'field_type' => 'multi_select',
                                    'allow_null' => 1,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673ce3c47f6b8',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_673ce4ba7f6bb',
                            'label' => 'Mid range',
                            'name' => 'mid_range',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Tyre Brand and Models for the Mid-Range position. For any given search, only 1 product is shown in the Mid-Range position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Mid-Range option will not be shown.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Mid Range Brand/Model',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673ce4ba7f6be',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673ce4ba7f6bb',
                                ),
                                array(
                                    'key' => 'field_673ce4ba7f6bf',
                                    'label' => 'Model(s)',
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
                                    'return_format' => 'object',
                                    'field_type' => 'multi_select',
                                    'allow_null' => 1,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673ce4ba7f6bb',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_673ce5217f6c0',
                            'label' => 'Premium',
                            'name' => 'premium',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Tyre Brand and Models for the Premium position. For any given search, only 1 product is shown in the Premium position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Premium option will not be shown.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Premium Brand/Model',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673ce5217f6c3',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673ce5217f6c0',
                                ),
                                array(
                                    'key' => 'field_673ce5217f6c4',
                                    'label' => 'Model(s)',
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
                                    'return_format' => 'object',
                                    'field_type' => 'multi_select',
                                    'allow_null' => 1,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673ce5217f6c0',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_673ce5637f6c5',
                            'label' => 'Brand order',
                            'name' => 'brand_order',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Brands and order them accord to how you want them to be displayed after the top 3 positions. For each Brand, specify the number of Products of that Brand you want to show. Within the Brand, items will be ordered by Popularity, i.e. how many have been sold.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Brand',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673ce5977f6c6',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673ce5637f6c5',
                                ),
                                array(
                                    'key' => 'field_673ce5b47f6c7',
                                    'label' => 'Positions',
                                    'name' => 'positions',
                                    'aria-label' => '',
                                    'type' => 'number',
                                    'instructions' => '',
                                    'required' => 1,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => 1,
                                    'min' => 1,
                                    'max' => '',
                                    'allow_in_bindings' => 0,
                                    'placeholder' => '',
                                    'step' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'parent_repeater' => 'field_673ce5637f6c5',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => Fbf_Product_Ladder::FBF_PL_NON_AT_MT_FIELD,
                    'label' => 'Tyre Ladder (non AT/MT)',
                    'name' => 'tyre_ladder_non_atmt',
                    'aria-label' => '',
                    'type' => 'group',
                    'instructions' => 'These settings will apply to Tyre Searches where the result set does not include any All Terrain or Mud Terrain tyres',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'field_673e4a02a7eff',
                            'label' => 'Budget',
                            'name' => 'budget',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Tyre Brand and Models for the Budget position. For any given search, only 1 product is shown in the Budget position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Budget option will not be shown.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Budget Brand/Model',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673e4a21a7f00',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673e4a02a7eff',
                                ),
                                array(
                                    'key' => 'field_673e4a53a7f01',
                                    'label' => 'Model(s)',
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
                                    'return_format' => 'object',
                                    'field_type' => 'multi_select',
                                    'allow_null' => 1,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673e4a02a7eff',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_673e4b84d49a5',
                            'label' => 'Mid range',
                            'name' => 'mid_range',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Tyre Brand and Models for the Mid-Range position. For any given search, only 1 product is shown in the Mid-Range position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Mid-Range option will not be shown.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Mid Range Brand/Model',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673e4c34d49a8',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673e4b84d49a5',
                                ),
                                array(
                                    'key' => 'field_673e4c54d49a9',
                                    'label' => 'Model(s)',
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
                                    'return_format' => 'object',
                                    'field_type' => 'multi_select',
                                    'allow_null' => 1,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673e4b84d49a5',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_673e4bacd49a6',
                            'label' => 'Premium',
                            'name' => 'premium',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Tyre Brand and Models for the Premium position. For any given search, only 1 product is shown in the Premium position, if a Tyre cannot be found that matches any of the Brand/Models chosen, a Premium option will not be shown.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Premium Brand/Model',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673e4c7cd49aa',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673e4bacd49a6',
                                ),
                                array(
                                    'key' => 'field_673e4c95d49ab',
                                    'label' => 'Model(s)',
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
                                    'return_format' => 'object',
                                    'field_type' => 'multi_select',
                                    'allow_null' => 1,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673e4bacd49a6',
                                ),
                            ),
                        ),
                        array(
                            'key' => 'field_673e4be4d49a7',
                            'label' => 'Brand order',
                            'name' => 'brand_order',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Brands and order them accord to how you want them to be displayed after the top 3 positions. For each Brand, specify the number of Products of that Brand you want to show. Within the Brand, items will be ordered by Popularity, i.e. how many have been sold.',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'table',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Brand',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_673e4cf1f4157',
                                    'label' => 'Brand',
                                    'name' => 'brand',
                                    'aria-label' => '',
                                    'type' => 'taxonomy',
                                    'instructions' => '',
                                    'required' => 1,
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
                                    'return_format' => 'object',
                                    'field_type' => 'select',
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'bidirectional' => 0,
                                    'multiple' => 0,
                                    'bidirectional_target' => array(
                                    ),
                                    'parent_repeater' => 'field_673e4be4d49a7',
                                ),
                                array(
                                    'key' => 'field_673e4d27f4158',
                                    'label' => 'Positions',
                                    'name' => 'positions',
                                    'aria-label' => '',
                                    'type' => 'number',
                                    'instructions' => '',
                                    'required' => 1,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => 1,
                                    'min' => 1,
                                    'max' => '',
                                    'allow_in_bindings' => 0,
                                    'placeholder' => '',
                                    'step' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'parent_repeater' => 'field_673e4be4d49a7',
                                ),
                            ),
                        ),
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
            'style' => 'seamless',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ) );
    }
}
