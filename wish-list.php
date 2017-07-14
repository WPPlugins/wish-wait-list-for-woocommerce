<?php
/**
 * Plugin Name: Wish Wait List for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/wish-wait-list-for-woocommerce/
 * Description: Wish and Wait products list for users
 * Version: 1.0.6
 * Author: BeRocket
 * Requires at least: 4.0
 * Author URI: http://berocket.com
 * Text Domain: BeRocket_Wish_List_domain
 * Domain Path: /languages/
 */
define( "BeRocket_Wish_List_version", '1.0.6' );
define( "BeRocket_Wish_List_domain", 'BeRocket_Wish_List_domain'); 
define( "Wish_List_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain('BeRocket_Wish_List_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once(plugin_dir_path( __FILE__ ).'includes/admin_notices.php');
require_once(plugin_dir_path( __FILE__ ).'includes/functions.php');
require_once(plugin_dir_path( __FILE__ ).'includes/widget.php');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Class BeRocket_Compare_Products
 */
class BeRocket_Wish_List {

    public static $info = array( 
        'id'        => 5,
        'version'   => BeRocket_Wish_List_version,
        'plugin'    => '',
        'slug'      => '',
        'key'       => '',
        'name'      => ''
    );

    /**
     * Defaults values
     */
    public static $defaults = array(
        'br_wish_list_general_settings'     => array(
            'ww_page'                           => '',
            'wish_list'                         => '',
            'wait_list'                         => '',
        ),
        'br_wish_list_style_settings'       => array(
            'icon_wish'                         => 'fa-heart-o',
            'icon_wait'                         => 'fa-clock-o',
            'icon_load'                         => 'fa-cog',
        ),
        'br_wish_list_text_settings'        => array(
            'wish_list'                         => 'Wish List',
            'wait_list'                         => 'Wait List',
            'show_wish'                         => 'Show all products from wish list',
            'show_wait'                         => 'Show all products from wait list',
        ),
        'br_wish_list_javascript_settings'  => array(
            'before_wish'                       => '',
            'before_wait'                       => '',
            'after_wish'                        => '',
            'after_wait'                        => '',
        ),
    );
    public static $values = array(
        'settings_name' => '',
        'option_page'   => 'br-wish-list',
        'premium_slug'  => 'woocommerce-wish-wait-list',
    );
    
    function __construct () {
        register_activation_hook(__FILE__, array( __CLASS__, 'activation' ) );

        if ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) && br_get_woocommerce_version() >= 2.1 ) {
            add_action ( 'init', array( __CLASS__, 'init' ) );
            add_action ( 'admin_init', array( __CLASS__, 'admin_init' ) );
            add_action ( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
            add_action ( 'admin_menu', array( __CLASS__, 'options' ) );
            add_action ( 'wp_head', array( __CLASS__, 'wp_head_style' ) );
            add_filter ( 'the_content', array( __CLASS__, 'wish_page' ) );
            add_action( "wp_ajax_br_wish_add", array ( __CLASS__, 'listener_wish_add' ) );
            add_action( "wp_ajax_br_wait_add", array ( __CLASS__, 'listener_wait_add' ) );
            add_action( "woocommerce_product_set_stock_status", array ( __CLASS__, 'send_mail' ), 10, 2 );
            add_action ( "widgets_init", array( __CLASS__, 'widgets_init' ) );
            add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
            $plugin_base_slug = plugin_basename( __FILE__ );
            add_filter( 'plugin_action_links_' . $plugin_base_slug, array( __CLASS__, 'plugin_action_links' ) );
            add_filter( 'is_berocket_settings_page', array( __CLASS__, 'is_settings_page' ) );
        }
    }
    public static function is_settings_page($settings_page) {
        if( ! empty($_GET['page']) && $_GET['page'] == self::$values[ 'option_page' ] ) {
            $settings_page = true;
        }
        return $settings_page;
    }
    public static function plugin_action_links($links) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page='.self::$values['option_page'] ) . '" title="' . __( 'View Plugin Settings', 'BeRocket_products_label_domain' ) . '">' . __( 'Settings', 'BeRocket_products_label_domain' ) . '</a>',
		);
		return array_merge( $action_links, $links );
    }
    public static function plugin_row_meta($links, $file) {
        $plugin_base_slug = plugin_basename( __FILE__ );
        if ( $file == $plugin_base_slug ) {
			$row_meta = array(
				'docs'    => '<a href="http://berocket.com/docs/plugin/'.self::$values['premium_slug'].'" title="' . __( 'View Plugin Documentation', 'BeRocket_products_label_domain' ) . '" target="_blank">' . __( 'Docs', 'BeRocket_products_label_domain' ) . '</a>',
				'premium'    => '<a href="http://berocket.com/product/'.self::$values['premium_slug'].'" title="' . __( 'View Premium Version Page', 'BeRocket_products_label_domain' ) . '" target="_blank">' . __( 'Premium Version', 'BeRocket_products_label_domain' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}
		return (array) $links;
    }
    public static function widgets_init() {
        register_widget("berocket_wish_wait_widget_1");
    }

    public static function activation () {
        $options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_general_settings' );
        if ( @ ! $options['ww_page'] ) {
            $wish_page = array(
                'post_title' => 'Wish and Wait List',
                'post_content' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
            );

            $post_id = wp_insert_post($wish_page);
            $options['ww_page'] = $post_id;
            update_option('br_wish_list_general_settings', $options);
        }
    }

    public static function wish_page ($content) {
        global $wp_query;
        $options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_general_settings' );
        $page = $options['ww_page'];
        $page_id = @ $wp_query->queried_object->ID;
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $page_id = apply_filters( 'wpml_object_id', $page_id, 'page', true, $default_language );
        if ( $page == $page_id ) {
            $text_options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_text_settings' );
            set_query_var( 'text_options', @ $text_options );
            set_query_var( 'options', @ $options );
            self::br_get_template_part('wish');
            self::br_get_template_part('wait');
            self::br_get_template_part('wish_wait');
        }
        return $content;
    }

    public static function init () {
        add_action( 'woocommerce_after_order_notes', array(__CLASS__, 'remove_products_from_lists') );
        add_action( 'woocommerce_checkout_process', array(__CLASS__, 'remove_products_on_checkout') );
        $user_id = get_current_user_id();
        global $br_current_wish, $br_current_wait;
        $br_current_wish = get_user_meta($user_id, 'berocket_wish', true);
        if ( ! isset( $br_current_wish ) || ! is_array( $br_current_wish ) ) {
            $br_current_wish = array();
        }
        $br_current_wait = get_user_meta($user_id, 'berocket_wait', true);
        if ( ! isset( $br_current_wait ) || ! is_array( $br_current_wait ) ) {
            $br_current_wait = array();
        }
        wp_register_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
        wp_enqueue_style( 'font-awesome' );
        wp_register_style( 'berocket_wish_list_style', plugins_url( 'css/wish_list.css', __FILE__ ), "", BeRocket_Wish_List_version );
        wp_enqueue_style( 'berocket_wish_list_style' );
        wp_enqueue_script( 'berocket_wish_list_script', plugins_url( 'js/wish_list.js', __FILE__ ), array( 'jquery' ), BeRocket_Wish_List_version );
        $style_options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_style_settings' );
        if ( is_user_logged_in() ) {
            $pos_name = 'ww_pos';
            $positions = 'ww';
            if( @ $style_options[$pos_name]['after_image'] ) {
                add_action ( 'woocommerce_before_shop_loop_item_title', array( __CLASS__, 'get_wish_button_'.$positions ), 32 );
                add_action ( 'lgv_advanced_after_img', array( __CLASS__, 'get_wish_button_'.$positions ), 32 );
            }
            if( @ $style_options[$pos_name]['after_add_to_cart'] ) {
                add_action ( 'woocommerce_after_shop_loop_item', array( __CLASS__, 'get_wish_button_'.$positions ), 32 );
                add_action ( 'lgv_advanced_after_add_to_cart', array( __CLASS__, 'get_wish_button_'.$positions ), 32 );
            }
            if( @ $style_options[$pos_name]['single_product'] ) {
                add_action ( 'woocommerce_single_product_summary', array( __CLASS__, 'get_wish_button_'.$positions ), 32 );
                add_action ( 'berocket_pp_popup_after_buttons', array( __CLASS__, 'get_wish_button_'.$positions ), 32 );
            }
        }
        $javascript_options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_javascript_settings' );
        wp_localize_script(
            'berocket_wish_list_script',
            'the_wish_list_data',
            array(
                'ajax_url'      => admin_url( 'admin-ajax.php' ),
                'user_func'     => apply_filters( 'berocket_wish_wait_user_func', $javascript_options ),
                'icon_load'     => ( ( @ $style_options['icon_load'] ) ? ( ( substr( $style_options['icon_load'], 0, 3 ) == 'fa-' ) ? '<i class="fa ww_animate ' . $style_options['icon_load'] . '"></i>' : '<i class="fa ww_animate"><image src="' . $style_options['icon_load'] . '" alt=""></i>' ) : '<i class="fa"></i>' ),
            )
        );
    }

    public static function admin_enqueue_scripts() {
        if ( function_exists( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        } else {
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'thickbox' );
        }
    }

    public static function admin_init () {
        add_filter( 'manage_edit-product_columns', array(__CLASS__, 'add_product_columns') );
        add_action( 'manage_product_posts_custom_column', array(__CLASS__, 'add_product_columns_data'), 2 );
        add_filter( "manage_edit-product_sortable_columns", array(__CLASS__, 'product_columns_sort') );
        add_action( 'pre_get_posts', array(__CLASS__, 'product_apply_sort') );
        wp_enqueue_script( 'berocket_wish_list_admin_script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ) );
        if( @ $_GET['page'] == 'br-wish-list' ) {
            wp_register_style( 'berocket_wish_list_fa_select_style', plugins_url( 'css/select_fa.css', __FILE__ ), "", BeRocket_Wish_List_version );
            wp_enqueue_style( 'berocket_wish_list_fa_select_style' );
            wp_enqueue_script( 'berocket_wish_list_admin_fa', plugins_url( 'js/admin_select_fa.js', __FILE__ ), array( 'jquery' ), BeRocket_Wish_List_version );
            wp_enqueue_script( 'berocket_aapf_widget-colorpicker', plugins_url( 'js/colpick.js', __FILE__ ), array( 'jquery' ) );
            wp_register_style( 'berocket_aapf_widget-colorpicker-style', plugins_url( 'css/colpick.css', __FILE__ ) );
            wp_enqueue_style( 'berocket_aapf_widget-colorpicker-style' );
            wp_register_style( 'berocket_wish_list_admin_style', plugins_url( 'css/admin.css', __FILE__ ), "", BeRocket_Wish_List_version );
            wp_enqueue_style( 'berocket_wish_list_admin_style' );
        }
        register_setting('br_wish_list_general_settings', 'br_wish_list_general_settings', array( __CLASS__, 'sanitize_wish_list_option' ));
        register_setting('br_wish_list_style_settings', 'br_wish_list_style_settings', array( __CLASS__, 'sanitize_wish_list_option' ));
        register_setting('br_wish_list_text_settings', 'br_wish_list_text_settings', array( __CLASS__, 'sanitize_wish_list_option' ));
        register_setting('br_wish_list_javascript_settings', 'br_wish_list_javascript_settings', array( __CLASS__, 'sanitize_wish_list_option' ));
        register_setting('br_wish_list_license_settings', 'br_wish_list_license_settings', array( __CLASS__, 'sanitize_wish_list_option' ));
        add_settings_section( 
            'br_wish_list_general_page',
            'General Settings',
            'br_wish_list_general_callback',
            'br_wish_list_general_settings'
        );

        add_settings_section( 
            'br_wish_list_style_page',
            'Style Settings',
            'br_wish_list_style_callback',
            'br_wish_list_style_settings'
        );

        add_settings_section( 
            'br_wish_list_text_page',
            'Text Settings',
            'br_wish_list_text_callback',
            'br_wish_list_text_settings'
        );

        add_settings_section( 
            'br_wish_list_javascript_page',
            'JavaScript Settings',
            'br_wish_list_javascript_callback',
            'br_wish_list_javascript_settings'
        );

        add_settings_section( 
            'br_wish_list_license_page',
            'License Settings',
            'br_wish_list_license_callback',
            'br_wish_list_license_settings'
        );
    }

    public static function options() {
        add_submenu_page( 'woocommerce', __('Wish/Wait List settings', 'BeRocket_Wish_List_domain'), __('Wish/Wait List', 'BeRocket_Wish_List_domain'), 'manage_options', 'br-wish-list', array(
            __CLASS__,
            'option_form'
        ) );
    }
    /**
     * Function add options form to settings page
     *
     * @access public
     *
     * @return void
     */
    public static function option_form() {
        $plugin_info = get_plugin_data(__FILE__, false, true);
        include Wish_List_TEMPLATE_PATH . "settings.php";
    }
    /**
     * Load template
     *
     * @access public
     *
     * @param string $name template name
     *
     * @return void
     */
    public static function br_get_template_part( $name = '' ) {
        $template = '';

        // Look in your_child_theme/woocommerce-wish-list/name.php
        if ( $name ) {
            $template = locate_template( "woocommerce-wish-list/{$name}.php" );
        }

        // Get default slug-name.php
        if ( ! $template && $name && file_exists( Wish_List_TEMPLATE_PATH . "{$name}.php" ) ) {
            $template = Wish_List_TEMPLATE_PATH . "{$name}.php";
        }

        // Allow 3rd party plugin filter template file from their plugin
        $template = apply_filters( 'wish_list_get_template_part', $template, $name );

        if ( $template ) {
            load_template( $template, false );
        }
    }
    public static function get_wish_button() {
        global $product, $wp_query, $br_current_wish, $br_current_wait;
        $product_id = br_wc_get_product_id($product);
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $default_language );
        $options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_general_settings' );
        $style_options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_style_settings' );
        echo '<div class="br_wish_wait_block br_wish_wait_'.$product_id.'" data-id="'.$product_id.'">';
        do_action( 'berocket_before_wish_button' );
        if ( ! $options['wish_list'] ) {
            echo '<span class="'.( ( array_search( $product_id, $br_current_wish ) !== FALSE ) ? 'br_ww_button_true ' : '' ).( ( ! $product->is_in_stock() && ! $options['wait_list'] ) ? 'br_ww_button_40 ' : '' ).'br_ww_button br_wish_button br_wish_add button" data-type="wish" href="#add_to_wish_list">'.( ( @ $style_options['icon_wish'] ) ? ( ( substr( $style_options['icon_wish'], 0, 3 ) == 'fa-' ) ? '<i class="fa ' . $style_options['icon_wish'] . '"></i>' : '<i class="fa"><image src="' . $style_options['icon_wish'] . '" alt=""></i>' ) : '' ).'</span>';
        }
        do_action( 'berocket_after_wish_button' );
        do_action( 'berocket_before_wait_button' );
        if ( ! $product->is_in_stock() && ! $options['wait_list'] ) {
            echo '<span class="'.( ( array_search( $product_id, $br_current_wait ) !== FALSE ) ? 'br_ww_button_true ' : '' ).'br_ww_button_40 br_ww_button br_wait_button br_wait_add button" data-type="wait" href="#add_to_wait_list">'.( ( @ $style_options['icon_wait'] ) ? ( ( substr( $style_options['icon_wait'], 0, 3 ) == 'fa-' ) ? '<i class="fa ' . $style_options['icon_wait'] . '"></i>' : '<i class="fa"><image src="' . $style_options['icon_wait'] . '" alt=""></i>' ) : '' ).'</span>';
        }
        do_action( 'berocket_after_wait_button' );
        echo '</div>';
    }
    public static function get_wish_button_ww() {
        self::get_wish_button();
    }
    public static function listener_wish_add() {
        self::update_list( 'wish' );
        wp_die();
    }
    public static function listener_wait_add() {
        self::update_list( 'wait' );
        wp_die();
    }
    public static function update_list( $type, $product_id = false, $only_remove = false ) {
        if( $product_id == false ) {
            $product_id = $_POST[$type.'_id'];
        }
        $default_language = apply_filters( 'wpml_default_language', NULL );
        $product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $default_language );
        $user_id = get_current_user_id();
        $current_wish = get_user_meta($user_id, 'berocket_'.$type, true);
        if ( isset( $current_wish ) && is_array( $current_wish ) && count( $current_wish ) > 0 ) {
            $find = array_search( $product_id, $current_wish );
            if ( $find === FALSE ) {
                if( ! $only_remove ) {
                    $current_wish[] = $product_id;
                    $operation = 'add';
                } else {
                    $operation = 'remove';
                }
            } else {
                unset( $current_wish[$find] );
                $operation = 'remove';
            }
        } else {
            $current_wish = array();
            $current_wish[] = $product_id;
            $operation = 'add';
        }
        if ( update_user_meta( $user_id, 'berocket_'.$type, $current_wish ) ) {
            $status = 'ok';
            $users = get_post_meta($product_id, $type.'_users', true);
            if ( $operation == 'remove' ) {
                if ( isset( $users ) && is_array( $users ) ) {
                    $find = array_search( $user_id, $users );
                    if ( $find !== FALSE ) {
                        unset( $users[$find] );
                    }
                }
            } else {
                if ( ! isset( $users ) || ! is_array( $users ) || count( $users ) == 0 ) {
                    $users = array();
                }
                $find = array_search( $user_id, $users );
                if ( $find === FALSE ) {
                    $users[] = $user_id;
                }
            }
            update_post_meta($product_id, $type.'_users', $users);
            if( count($users) > 0 ) {
                update_post_meta($product_id, $type.'_users_count', count($users));
            } else {
                delete_post_meta($product_id, $type.'_users_count');
            }
        } else {
            $status = 'error';
        }
        echo json_encode( array('status' => $status, 'operation' => $operation, 'user' => $user_id) );
    }
    public static function send_mail( $product_id, $status ) {
        if ( $status == 'instock' ) {
            $users = get_post_meta($product_id, 'wait_users', true);
            if ( isset( $users ) && is_array( $users ) && count( $users ) > 0 ) {
                $product = wc_get_product($product_id);
                if ( isset( $wp_sended ) && is_array( $wp_sended ) ) {
                    $wp_sended = array();
                }
                $wp_sended[] = $product_id.' => '.implode( ',', $users );
                foreach ( $users as $key_users => $user_id ) {
                    $user = new WP_User( $user_id );
                    $subject = $product->get_title().' is now available';
                    $message = '<h3>'.$product->get_title().' is now available</h3>';
                    $message .= '<div style="width: 250px;"><a href="'.$product->get_permalink().'">
                        <p>'.$product->get_title().'</p>
                        '.$product->get_image().'
                        <p>'.$product->get_price_html().'</p>
                    </a></div>';
                    wp_mail( $user->user_email, $subject, $message );
                }
                $users = array();
                update_post_meta($product_id, 'wait_users', $users);
            }
        }
    }

    public static function add_product_columns($columns) {
        $new_columns = (is_array($columns)) ? $columns : array();
        $new_columns['wish_count'] = __( 'Wish', 'BeRocket_Wish_List_domain' );
        $new_columns['wait_count'] = __( 'Wait', 'BeRocket_Wish_List_domain' );
        return $new_columns;
    }

    public static function add_product_columns_data($column) {
        global $post;
        if( $column == 'wish_count' ) {
            $data = get_post_meta($post->ID, 'wish_users_count', true);
            if( @ ! $data ) {
                $data = 0;
            }
            echo $data;
        }
        if( $column == 'wait_count' ) {
            $data = get_post_meta($post->ID, 'wait_users_count', true);
            if( @ ! $data ) {
                $data = 0;
            }
            echo $data;
        }
    }

    public static function product_columns_sort($columns) {
        $custom = array(
            'wish_count'    => array( 'wish_users_count', true ),
            'wait_count'    => array( 'wait_users_count', true )
        );
        return wp_parse_args( $custom, $columns );
    }

    public static function product_apply_sort($query) {
        if( ! is_admin() ) {
            return false;
        }
        $orderby = $query->get('orderby');
        if( @ $orderby == 'wish_users_count' || @ $orderby == 'wait_users_count' ) {
            $query->set('meta_key',$orderby);  
            $query->set('orderby','meta_value_num');
        }
    }

    public static function remove_products_from_lists($checkout) {
        $user_id = get_current_user_id();
        $array_cart = array();
        $array_user = array();
        $array_intersect = array();
        $types = array('wait', 'wish');
        foreach($types as $type) {
            $array_user[$type] = get_user_meta($user_id, 'berocket_'.$type, true);
            if(empty($array_user[$type]) || ! is_array($array_user[$type])) {
                $array_user[$type] = array();
            }
        }
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        foreach($items as $item => $values) {
            $array_cart[] = $values['product_id'];
        }
        $array_intersect['wait'] = array_intersect($array_user['wait'], $array_cart);
        $array_intersect['wish'] = array_intersect($array_user['wish'], $array_cart);
        if( count($array_intersect['wait']) > 0 || count($array_intersect['wish']) > 0 ) {
            echo '<div id="id_remove_wait_product"><h3>' . __('Products in your lists', 'BeRocket_Wish_List_domain') . '</h3>';
            if( count($array_intersect['wait']) > 0 ) { 
                woocommerce_form_field( 'remove_wait_product', array(
                    'type'         => 'checkbox',
                    'class'         => array('remove_wait_product'),
                    'label'         => __('Remove products from your wait list', 'BeRocket_Wish_List_domain'),
                ), true);
            }
            if( count($array_intersect['wish']) > 0 ) { 
                woocommerce_form_field( 'remove_wish_product', array(
                    'type'         => 'checkbox',
                    'class'         => array('remove_wish_product'),
                    'label'         => __('Remove products from your wish list', 'BeRocket_Wish_List_domain'),
                ), true);
            }

            echo '</div>';
        }
    }

    public static function remove_products_on_checkout() {
        $types = array('wait', 'wish');
        foreach($types as $type) {
            if( @ $_POST['remove_'.$type.'_product'] ) {
                global $woocommerce;
                $items = $woocommerce->cart->get_cart();
                foreach($items as $item => $values) {
                    $product_id = $values['product_id'];
                    $default_language = apply_filters( 'wpml_default_language', NULL );
                    $product_id = apply_filters( 'wpml_object_id', $product_id, 'product', true, $default_language );
                    self::update_list( $type, $product_id, true );
                }
            }
        }
    }

    public static function sanitize_wish_list_option( $input ) {
        $default = BeRocket_Wish_List::$defaults[$input['settings_name']];
        $result = self::recursive_array_set( $default, $input );
        return $result;
    }
    public static function recursive_array_set( $default, $options ) {
        foreach( $default as $key => $value ) {
            if( array_key_exists( $key, $options ) ) {
                if( is_array( $value ) ) {
                    if( is_array( $options[$key] ) ) {
                        $result[$key] = self::recursive_array_set( $value, $options[$key] );
                    } else {
                        $result[$key] = self::recursive_array_set( $value, array() );
                    }
                } else {
                    $result[$key] = $options[$key];
                }
            } else {
                if( is_array( $value ) ) {
                    $result[$key] = self::recursive_array_set( $value, array() );
                } else {
                    $result[$key] = '';
                }
            }
        }
        foreach( $options as $key => $value ) {
            if( ! array_key_exists( $key, $result ) ) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    public static function get_wish_list_option( $option_name ) {
        $options = get_option( $option_name );
        if ( @ $options && is_array ( $options ) ) {
            $options = array_merge( BeRocket_Wish_List::$defaults[$option_name], $options );
        } else {
            $options = BeRocket_Wish_List::$defaults[$option_name];
        }
        return $options;
    }
    public static function wp_head_style() {
        
    }
    public static function array_to_style ( $styles ) {
        $color = array( 'color', 'background-color', 'border-color' );
        $size = array( 'border-width', 'border-top-width', 'border-bottom-width', 'border-left-width', 'border-right-width',
            'padding-top', 'padding-bottom', 'padding-left', 'padding-right',
            'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius',
            'margin-top', 'margin-bottom', 'margin-left', 'margin-right', 'top', 'bottom', 'left', 'right',
            'width', 'height', 'max-height', 'max-width', 'line-height', 'font-size', 'border-radius' );
        foreach( $styles as $name => $value ) {
            if ( isset( $value ) && strlen($value) > 0 ) {
                if ( in_array( $name, $color ) ) {
                    if ( $value[0] != '#' ) {
                        $value = '#' . $value;
                    }
                    echo $name . ':' . $value . '!important;';
                } else if ( in_array( $name, $size ) ) {
                    if ( strpos( $value, '%' ) || strpos( $value, 'em' ) || strpos( $value, 'px' ) || $value == 'initial' || $value == 'inherit' ) {
                        echo $name . ':' . $value . '!important;';
                    } else {
                        echo $name . ':' . $value . 'px!important;';
                    }
                } else {
                    echo $name . ':' . $value . '!important;';
                }
            }
        }
    }
}

new BeRocket_Wish_List;

berocket_admin_notices::generate_subscribe_notice();
new berocket_admin_notices(array(
    'start' => 1498413376, // timestamp when notice start
    'end'   => 1504223940, // timestamp when notice end
    'name'  => 'name', //notice name must be unique for this time period
    'html'  => 'Only <strong>$10</strong> for <strong>Premium</strong> WooCommerce Load More Products plugin!
        <a class="berocket_button" href="http://berocket.com/product/woocommerce-load-more-products" target="_blank">Buy Now</a>
         &nbsp; <span>Get your <strong class="red">50% discount</strong> and save <strong>$10</strong> today</span>
        ', //text or html code as content of notice
    'righthtml'  => '<a class="berocket_no_thanks">No thanks</a>', //content in the right block, this is default value. This html code must be added to all notices
    'rightwidth'  => 80, //width of right content is static and will be as this value. berocket_no_thanks block is 60px and 20px is additional
    'nothankswidth'  => 60, //berocket_no_thanks width. set to 0 if block doesn't uses. Or set to any other value if uses other text inside berocket_no_thanks
    'contentwidth'  => 400, //width that uses for mediaquery is image_width + contentwidth + rightwidth
    'subscribe'  => false, //add subscribe form to the righthtml
    'priority'  => 10, //priority of notice. 1-5 is main priority and displays on settings page always
    'height'  => 50, //height of notice. image will be scaled
    'repeat'  => false, //repeat notice after some time. time can use any values that accept function strtotime
    'repeatcount'  => 1, //repeat count. how many times notice will be displayed after close
    'image'  => array(
        'local' => plugin_dir_url( __FILE__ ) . 'images/ad_white_on_orange.png', //notice will be used this image directly
    ),
));
