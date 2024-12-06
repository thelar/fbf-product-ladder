<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://4x4tyres.co.uk
 * @since      1.0.0
 *
 * @package    Fbf_Product_Ladder
 * @subpackage Fbf_Product_Ladder/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fbf_Product_Ladder
 * @subpackage Fbf_Product_Ladder/includes
 * @author     Kevin Price-Ward <kevin.price-ward@4x4tyres.co.uk>
 */
class Fbf_Product_Ladder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Fbf_Product_Ladder_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

    /**
     * Define the fields
     */
    const FBF_PL_AT_MT_FIELD = 'field_673e42017a113';
    const FBF_PL_NON_AT_MT_FIELD = 'field_673e48ad67d1d';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FBF_PRODUCT_LADDER_VERSION' ) ) {
			$this->version = FBF_PRODUCT_LADDER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'fbf-product-ladder';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Fbf_Product_Ladder_Loader. Orchestrates the hooks of the plugin.
	 * - Fbf_Product_Ladder_i18n. Defines internationalization functionality.
	 * - Fbf_Product_Ladder_Admin. Defines all hooks for the admin area.
	 * - Fbf_Product_Ladder_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fbf-product-ladder-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fbf-product-ladder-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fbf-product-ladder-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fbf-product-ladder-public.php';

		$this->loader = new Fbf_Product_Ladder_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Fbf_Product_Ladder_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Fbf_Product_Ladder_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Fbf_Product_Ladder_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_options_page');
        $this->loader->add_action('acf/include_fields', $plugin_admin, 'add_acf_fields');

        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673ce4407f6b9', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // AT/MT Budget
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673ce4ba7f6be', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // AT/MT Mid range
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673ce5217f6c3', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // AT/MT Premium
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673ce5977f6c6', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // AT/MT Premium
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673e4a21a7f00', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // Non AT/MT Budget
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673e4c34d49a8', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // Non AT/MT Mid range
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673e4c7cd49aa', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // Non AT/MT Premium
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673e4cf1f4157', $plugin_admin, 'tyre_product_cat_filter', 10, 3); // Non AT/MT Premium

        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673ce4867f6ba', $plugin_admin, 'model_taxonomy_filter', 10, 3); // AT/MT Budget
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673ce4ba7f6bf', $plugin_admin, 'model_taxonomy_filter', 10, 3); // AT/MT Mid range
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673ce5217f6c4', $plugin_admin, 'model_taxonomy_filter', 10, 3); // AT/MT Premium
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673e4a53a7f01', $plugin_admin, 'model_taxonomy_filter', 10, 3); // Non AT/MT Budget
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673e4c54d49a9', $plugin_admin, 'model_taxonomy_filter', 10, 3); // Non AT/MT Mid range
        $this->loader->add_filter('acf/fields/taxonomy/query/key=field_673e4c95d49ab', $plugin_admin, 'model_taxonomy_filter', 10, 3); // Non AT/MT Premium

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Fbf_Product_Ladder_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Fbf_Product_Ladder_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    /**
     * Get the Tyre ladder options
     *
     * @param $is_at_mt
     * @return false|mixed
     */
    private static function get_options($is_at_mt)
    {
        if($is_at_mt){
            $field = self::FBF_PL_AT_MT_FIELD;
        }else{
            $field = self::FBF_PL_NON_AT_MT_FIELD;
        }
        return get_field($field, 'options');
    }

    /**
     * Gets the ID of the Premium/Mid-range/Budget position
     *
     * @param $which
     * @param $is_at_mt
     * @param $ids
     * @return false|int|WP_Post
     */
    public static function get_id($which, $is_at_mt, $ids)
    {
        if($ladder_options = self::get_options($is_at_mt)[$which]){
            // Loop through the rows and perform a get_posts() to find any products that match Brand/Model in $ids
            foreach($ladder_options as $row){
                $args = [
                    'post_type' => 'product',
                    'post__in' => $ids,
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'fields' => 'ids',
                ];
                $tax_query = [];
                $brand = $row['brand'];
                $tax_query[] = [
                    'taxonomy' => 'pa_brand-name',
                    'field' => 'term_id',
                    'terms' => $brand->term_id,
                ];
                if(!empty($row['model'])){
                    $model_ids = [];
                    foreach($row['model'] as $model){
                        $model_ids[] = $model->term_id;
                    }
                    $tax_query[] = [
                        'taxonomy' => 'pa_model-name',
                        'field' => 'term_id',
                        'terms' => $model_ids,
                    ];
                }
                $tax_query[] = [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => 'package',
                    'operator' => 'NOT IN'
                ];
                $args['tax_query'] = $tax_query;
                $args['tax_query']['relation'] = 'AND';

                // Sort by popularity
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'desc';
                $args['meta_key'] = 'total_sales';

                $product_ids = get_posts($args);

                if(!empty($product_ids)){
                    return $product_ids[0];
                }
            }
            return false;
        }else{
            return false;
        }
    }

    public static function get_brand_order($is_at_mt, $ids, $exclude_list)
    {
        if($ladder_options = self::get_options($is_at_mt)['brand_order']){
            $brand_order = [];
            foreach($ladder_options as $brand){
                $brand_term_id = $brand['brand']->term_id;
                $positions = $brand['positions'];
                $args = [
                    'post_type' => 'product',
                    'post__in' => $ids,
                    'posts_per_page' => $positions,
                    'post_status' => 'publish',
                    'fields' => 'ids',
                ];
                // As per https://developer.wordpress.org/reference/classes/wp_query/#post-page-parameters you cannot combine post__in and post__not_in in same query
                /*if(!empty($exclude_list)){
                    $args['post__not_in'] = $exclude_list;
                }*/
                $tax_query = [];
                $tax_query[] = [
                    'taxonomy' => 'pa_brand-name',
                    'field' => 'term_id',
                    'terms' => $brand_term_id,
                ];
                $tax_query[] = [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => 'package',
                    'operator' => 'NOT IN'
                ];
                $args['tax_query'] = $tax_query;
                $args['tax_query']['relation'] = 'AND';

                // Sort by popularity
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'desc';
                $args['meta_key'] = 'total_sales';

                $brand_ids = get_posts($args);

                // Remove items in exclude list
                if(!empty($exclude_list)){
                    foreach($exclude_list as $exclude_id){
                        if(in_array($exclude_id, $brand_ids)){
                            unset($brand_ids[array_search($exclude_id, $brand_ids)]);
                        }
                    }
                }

                if(!empty($brand_ids)){
                    foreach($brand_ids as $a){
                        $brand_order[] = $a;
                    }
                }
            }
            if(!empty($brand_order)){
                return $brand_order;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
