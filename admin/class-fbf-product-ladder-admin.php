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

        $ajax_params = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce($this->plugin_name),
        );
        wp_localize_script($this->plugin_name, 'fbf_product_ladder_admin', $ajax_params);

	}

    /**
     * Add ACF options page
     */
    public function add_options_page()
    {
        if (function_exists('acf_add_options_page')) {
            $tyre_child = acf_add_options_sub_page([
                'page_title' => 'Tyre Product Ladder Options',
                'menu_title' => 'Tyre Product Ladder Options',
                'parent_slug' => 'theme-general-settings'
            ]);
            $wheel_child = acf_add_options_sub_page([
                'page_title' => 'Wheel Product Ladder Options',
                'menu_title' => 'Wheel Product Ladder Options',
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

    public function brand_taxonomy_filter($args, $field, $post_id)
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
                    'terms' => ['alloy-wheel', 'steel-wheel'],
                ]
            ],
            'meta_query' => [
                [
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '=',
                ],
            ],
        ];
        $product_ids = get_posts($product_args);
        foreach($product_ids as $product_id){
            if(get_the_terms($product_id, 'pa_brand-name')){
                $brand_term_id = get_the_terms($product_id, 'pa_brand-name')[0]->term_id;
                if(!isset($args['include'])){
                    $args['include'] = [$brand_term_id];
                }else{
                    if(!in_array($brand_term_id, $args['include'])){
                        $args['include'][] = $brand_term_id;
                    }
                }
            }

        }

        return $args;
    }

    public function manufacturer_filter($field)
    {
        global $wpdb;
        $manufacturer_table = $wpdb->prefix . 'fbf_vehicle_manufacturers';
        $q = $wpdb->prepare("SELECT * FROM {$manufacturer_table} WHERE enabled = TRUE");
        $r = $wpdb->get_results($q);
        if($r){
            foreach($r as $k => $v){
                $field['choices'][$v->boughto_id] = $v->display_name;
            }
        }

        return $field;
    }

    public function chassis_filter($field)
    {
        if(!isset($_POST['manufacturer_id']) || !is_numeric($_POST['manufacturer_id'])){
            return $field;
        }
        $manufacturer_id = filter_var($_POST['manufacturer_id'], FILTER_SANITIZE_STRING);
        if(is_plugin_active('fbf-wheel-search/fbf-wheel-search.php')) {
            require_once plugin_dir_path(WP_PLUGIN_DIR . '/fbf-wheel-search/fbf-wheel-search.php') . 'includes/class-fbf-wheel-search-boughto-api.php';
            $api = new \Fbf_Wheel_Search_Boughto_Api('fbf_wheel_search', 'fbf-wheel-search');

            $data = $api->get_chasis($manufacturer_id);

            if(!empty($data)&&!array_key_exists('error', $data)) {
                $all_chassis = [];
                $i = 0;
                foreach ($data as $chassis) {
                    if (strpos(strtolower($chassis['generation']['start_date']), 'hidden') === false) {
                        $ds = DateTime::createFromFormat('Y-m-d', $chassis['generation']['start_date']);
                        $de = DateTime::createFromFormat('Y', $chassis['generation']['end_date']);
                        if ($ds) {
                            $data[$i]['ds'] = $ds->format('Y');
                        }
                        if ($de) {
                            $data[$i]['de'] = $de->format('Y');
                        }
                    } else {
                        unset($data[$i]);
                    }
                    $i++;
                }

                if (!empty($data)) {
                    usort($data, function ($a, $b) {
                        return [$a['chassis']['display_name'], $b['ds']] <=> [$b['chassis']['display_name'], $a['ds']];
                    });
                }

                foreach($data as $d){
                    $field['choices'][$d['chassis']['id']] = $d['chassis']['display_name'];
                }
            }
        }
        return $field;
    }

    public function brand_filter($field)
    {
        if(!isset($_POST['chassis_id']) || !is_numeric($_POST['chassis_id'])){
            return $field;
        }
        $chassis_id = filter_var($_POST['chassis_id'], FILTER_SANITIZE_STRING);
        global $wpdb;
        $chassis_wheel_table = $wpdb->prefix . 'fbf_chassis_wheel';
        $q = $wpdb->prepare("SELECT data FROM {$chassis_wheel_table} WHERE chassis_id = %s", $chassis_id);
        $r = $wpdb->get_row($q);
        if($r){
            $brands = [];
            $data = unserialize($r->data);
            foreach($data as $product_id){
                $brand_term = get_the_terms($product_id, 'pa_brand-name')[0];
                if($brand_term){
                    if(!in_array($brand_term->name, $brands)){
                        $brands[] = $brand_term->name;
                        $field['choices'][$brand_term->term_id] = $brand_term->name;
                    }
                }
            }
        }
        return $field;
    }

    public function product_filter($field)
    {
        if(!isset($_POST['chassis_id']) || !is_numeric($_POST['chassis_id'])){
            return $field;
        }
        $chassis_id = filter_var($_POST['chassis_id'], FILTER_SANITIZE_STRING);
        global $wpdb;
        $chassis_wheel_table = $wpdb->prefix . 'fbf_chassis_wheel';
        $q = $wpdb->prepare("SELECT data FROM {$chassis_wheel_table} WHERE chassis_id = %s", $chassis_id);
        $r = $wpdb->get_row($q);
        if($r){
            $data = unserialize($r->data);
            foreach($data as $product_id){
                $product = wc_get_product($product_id);
                $field['choices'][$product_id] = sprintf('%s (%s)', $product->get_title(), $product->get_sku());
            }
        }

        return $field;
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
                        'value' => 'acf-options-tyre-product-ladder-options',
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

        acf_add_local_field_group( array(
            'key' => 'group_6877a7181d0e0',
            'title' => 'Wheel Ladder options',
            'fields' => array(
                array(
                    'key' => 'field_6877a776504ad',
                    'label' => 'Wheel Ladder (Alloy/Steel)',
                    'name' => 'wheel_ladder_alloy_steel',
                    'aria-label' => '',
                    'type' => 'group',
                    'instructions' => 'These settings will apply to Wheel Searches where the result set includes 1 or more Alloy Wheel AND 1 or more Steel Wheel',
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
                            'key' => 'field_6877c0f5504af',
                            'label' => 'Brand order',
                            'name' => 'brand_order',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Brands and order them accord to how you want them to be displayed. For each Brand, specify the number of Products of that Brand you want to show. Within the Brand, items will be ordered by Popularity, i.e. how many have been sold.',
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
                                    'key' => 'field_6877c11f504b0',
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
                                    'parent_repeater' => 'field_6877c0f5504af',
                                ),
                                array(
                                    'key' => 'field_6877c16b504b1',
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
                                    'min' => '1',
                                    'max' => '',
                                    'allow_in_bindings' => 0,
                                    'placeholder' => '',
                                    'step' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'parent_repeater' => 'field_6877c0f5504af',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_6877bff3504ae',
                    'label' => 'Wheel Ladder (Alloy only)',
                    'name' => 'wheel_ladder_alloy_only',
                    'aria-label' => '',
                    'type' => 'group',
                    'instructions' => 'These settings will apply to Wheel Searches where the result set includes 1 or more Alloy Wheel and NO Steel Wheels',
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
                            'key' => 'field_6877c237504b2',
                            'label' => 'Brand order',
                            'name' => 'brand_order',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Select the Brands and order them accord to how you want them to be displayed. For each Brand, specify the number of Products of that Brand you want to show. Within the Brand, items will be ordered by Popularity, i.e. how many have been sold.',
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
                                    'key' => 'field_6877c27a504b3',
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
                                    'parent_repeater' => 'field_6877c237504b2',
                                ),
                                array(
                                    'key' => 'field_6877c2b8504b4',
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
                                    'min' => '1',
                                    'max' => '',
                                    'allow_in_bindings' => 0,
                                    'placeholder' => '',
                                    'step' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'parent_repeater' => 'field_6877c237504b2',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_6878cce937433',
                    'label' => 'Vehicle (Chassis) Overrides',
                    'name' => 'chassis_overrides',
                    'aria-label' => '',
                    'type' => 'group',
                    'instructions' => 'This allows you to override the above options on a vehicle by vehicle basis. You can select individual Products (SKUs) and/or Brands then order them how you wish for each individual vehicle',
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
                            'key' => 'field_6878cd5e37434',
                            'label' => 'Vehicle',
                            'name' => 'chassis',
                            'aria-label' => '',
                            'type' => 'repeater',
                            'instructions' => 'Add a Vehicle (Chassis) by selecting the Manufacturer and Vehicle, then specify the Order you want Product to appear either by Brand or individual SKU',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'row',
                            'pagination' => 0,
                            'min' => 0,
                            'max' => 0,
                            'collapsed' => '',
                            'button_label' => 'Add Vehicle (Chassis)',
                            'rows_per_page' => 20,
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_6878cd7c37435',
                                    'label' => 'Manufacturer',
                                    'name' => 'manufacturer',
                                    'aria-label' => '',
                                    'type' => 'select',
                                    'instructions' => '',
                                    'required' => 1,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'choices' => array(
                                    ),
                                    'default_value' => false,
                                    'return_format' => 'value',
                                    'multiple' => 0,
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'ui' => 1,
                                    'ajax' => 1,
                                    'placeholder' => '',
                                    'create_options' => 0,
                                    'save_options' => 0,
                                    'parent_repeater' => 'field_6878cd5e37434',
                                ),
                                array(
                                    'key' => 'field_6878cdef98ede',
                                    'label' => 'Chassis',
                                    'name' => 'chassis',
                                    'aria-label' => '',
                                    'type' => 'select',
                                    'instructions' => '',
                                    'required' => 1,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'choices' => array(
                                    ),
                                    'default_value' => false,
                                    'return_format' => 'value',
                                    'multiple' => 0,
                                    'allow_null' => 0,
                                    'allow_in_bindings' => 0,
                                    'ui' => 1,
                                    'ajax' => 1,
                                    'placeholder' => '',
                                    'create_options' => 0,
                                    'save_options' => 0,
                                    'parent_repeater' => 'field_6878cd5e37434',
                                ),
                                array(
                                    'key' => 'field_6878d0efbea88',
                                    'label' => 'Order',
                                    'name' => 'order',
                                    'aria-label' => '',
                                    'type' => 'repeater',
                                    'instructions' => '',
                                    'required' => 1,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'layout' => 'row',
                                    'min' => 1,
                                    'max' => 0,
                                    'collapsed' => '',
                                    'button_label' => 'Add Brand or Single Product',
                                    'rows_per_page' => 20,
                                    'sub_fields' => array(
                                        array(
                                            'key' => 'field_6878d10fbea89',
                                            'label' => 'Brand or Product',
                                            'name' => 'brand_or_product',
                                            'aria-label' => '',
                                            'type' => 'radio',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'choices' => array(
                                                'brand' => 'Brand',
                                                'product' => 'Product',
                                            ),
                                            'default_value' => 'brand',
                                            'return_format' => 'value',
                                            'allow_null' => 0,
                                            'other_choice' => 0,
                                            'allow_in_bindings' => 0,
                                            'layout' => 'horizontal',
                                            'save_other_choice' => 0,
                                            'parent_repeater' => 'field_6878d0efbea88',
                                        ),
                                        array(
                                            'key' => 'field_6878d159bea8a',
                                            'label' => 'Brand',
                                            'name' => 'brand',
                                            'aria-label' => '',
                                            'type' => 'select',
                                            'instructions' => '',
                                            'required' => 1,
                                            'conditional_logic' => array(
                                                array(
                                                    array(
                                                        'field' => 'field_6878d10fbea89',
                                                        'operator' => '==',
                                                        'value' => 'brand',
                                                    ),
                                                ),
                                            ),
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'choices' => array(
                                            ),
                                            'default_value' => false,
                                            'return_format' => 'value',
                                            'multiple' => 0,
                                            'allow_null' => 0,
                                            'allow_in_bindings' => 0,
                                            'ui' => 1,
                                            'ajax' => 1,
                                            'placeholder' => '',
                                            'create_options' => 0,
                                            'save_options' => 0,
                                            'parent_repeater' => 'field_6878d0efbea88',
                                        ),
                                        array(
                                            'key' => 'field_6878d322bea8b',
                                            'label' => 'Brand positions',
                                            'name' => 'brand_positions',
                                            'aria-label' => '',
                                            'type' => 'number',
                                            'instructions' => '',
                                            'required' => 1,
                                            'conditional_logic' => array(
                                                array(
                                                    array(
                                                        'field' => 'field_6878d10fbea89',
                                                        'operator' => '==',
                                                        'value' => 'brand',
                                                    ),
                                                ),
                                            ),
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
                                            'parent_repeater' => 'field_6878d0efbea88',
                                        ),
                                        array(
                                            'key' => 'field_6878d347bea8c',
                                            'label' => 'Product (SKU)',
                                            'name' => 'sku',
                                            'aria-label' => '',
                                            'type' => 'select',
                                            'instructions' => '',
                                            'required' => 1,
                                            'conditional_logic' => array(
                                                array(
                                                    array(
                                                        'field' => 'field_6878d10fbea89',
                                                        'operator' => '==',
                                                        'value' => 'product',
                                                    ),
                                                ),
                                            ),
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'choices' => array(
                                            ),
                                            'default_value' => false,
                                            'return_format' => 'value',
                                            'multiple' => 0,
                                            'allow_null' => 0,
                                            'allow_in_bindings' => 0,
                                            'ui' => 1,
                                            'ajax' => 1,
                                            'placeholder' => '',
                                            'create_options' => 0,
                                            'save_options' => 0,
                                            'parent_repeater' => 'field_6878d0efbea88',
                                        ),
                                    ),
                                    'parent_repeater' => 'field_6878cd5e37434',
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
                        'value' => 'acf-options-wheel-product-ladder-options',
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
