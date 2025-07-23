<?php

class Fbf_Product_Ladder_Admin_Ajax
{
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

    public function fbf_product_ladder_populate_chassis()
    {
        check_ajax_referer($this->plugin_name, 'ajax_nonce');
        $index = filter_var($_POST['row'], FILTER_SANITIZE_STRING);
        $status = 'success';
        $resp = [];

        $values = get_field('field_6878cce937433', 'options');
        $chassis_id = $values['chassis'][$index]['chassis'];

        if($chassis_id){
            global $wpdb;
            $chassis_table = $wpdb->prefix . 'fbf_chassis_wheel';
            $q = $wpdb->prepare("SELECT name FROM {$chassis_table} WHERE chassis_id = %s", $chassis_id);
            $r = $wpdb->get_col($q);
            if($r){
                $name = $r[0];
            }else if(is_plugin_active('fbf-wheel-search/fbf-wheel-search.php')){
                require_once plugin_dir_path(WP_PLUGIN_DIR . '/fbf-wheel-search/fbf-wheel-search.php') . 'includes/class-fbf-wheel-search-boughto-api.php';
                $api = new \Fbf_Wheel_Search_Boughto_Api('fbf_wheel_search', 'fbf-wheel-search');
                $chassis_data = $api->get_chassis_detail($chassis_id);
                if(!empty($chassis_data)){
                    $manufacturer_id = $chassis_data['manufacturer']['id'];
                    $chassis = $api->get_chasis($manufacturer_id);
                    $my_chassis = $chassis[array_search($chassis_id, array_column(array_column($chassis, 'chassis'), 'id'))];
                    if(!empty($my_chassis)){
                        $ds = DateTime::createFromFormat('Y-m-d', $my_chassis['generation']['start_date']);
                        $de = DateTime::createFromFormat('Y', $my_chassis['generation']['end_date']);

                        $name = str_replace(' All Engines', '', $my_chassis['chassis']['display_name']); // Remove ' All Engines' from string
                    }
                }
            }
        }

        if(isset($name) && isset($chassis_id)){
            $resp['name'] = $name;
            $resp['chassis_id'] = $chassis_id;
            $status = 'success';
            $resp['status'] = $status;
        }else{
            $status = 'error';
            $resp['status'] = $status;
            $resp['error'] = 'Either name or chassis_id could not be found';
        }

        echo json_encode($resp);
        die();
    }

    public function fbf_product_ladder_populate_order()
    {
        check_ajax_referer($this->plugin_name, 'ajax_nonce');
        $order_index = filter_var($_POST['order_index'], FILTER_SANITIZE_STRING);
        $chassis_index = filter_var($_POST['chassis_index'], FILTER_SANITIZE_STRING);
        $status = 'success';
        $resp = [];

        $values = get_field('field_6878cce937433', 'options');

        $chassis_order = $values['chassis'][$chassis_index]['order'];
        $position = $chassis_order[$order_index];

        if($position['brand_or_product']==='product'){
            $product_id = $position['sku'];
            $product = wc_get_product($product_id);
            $order_data = [
                'type' => 'product',
                'name' => sprintf('%s (%s)', $product->get_title(), $product->get_sku()),
                'id' => $product_id,
            ];
            $status = 'success';
        }else if($position['brand_or_product']==='brand'){
            $order_data = [
                'type' => 'brand',
                'id' => $position['brand'],
                'name' => get_term_by('id', $position['brand'], 'pa_brand-name')->name,
            ];
            $status = 'success';
        }

        $resp['data'] = $order_data;
        $resp['status'] = $status;
        echo json_encode($resp);
        die();
    }
}
