<?php
/**
 * List/Grid widget
 */
class BeRocket_wish_wait_widget_1 extends WP_Widget 
{
    public static $defaults = array(
        'title'         => '',
        'product_type'  => 'top_wait',
        'widget_type'   => 'default',
        'add_to_cart'   => '0',
    );
	public function __construct() {
        parent::__construct("BeRocket_wish_wait_widget_1", "WooCommerce Wish/Wait List",
            array("description" => "Widget for BeRocket Wish/Wait List plugin"));
    }
    /**
     * WordPress widget for display Curency Exchange buttons
     */
    public function widget($args, $instance)
    {
        $instance = wp_parse_args( (array) $instance, self::$defaults );
        set_query_var( 'product_type', apply_filters( 'ww_widget_product_type', $instance['product_type'] ) );
        set_query_var( 'display_type', apply_filters( 'ww_widget_display_type', $instance['widget_type'] ) );
        set_query_var( 'add_to_cart', apply_filters( 'ww_widget_add_to_cart', $instance['add_to_cart'] ) );
        ob_start();
        BeRocket_Wish_List::br_get_template_part( 'widget' );
        $content = ob_get_clean();
        if( $content ) {
            echo $args['before_widget'];
            if( $instance['title'] ) echo $args['before_title'].$instance['title'].$args['after_title'];
            echo $content;
            echo $args['after_widget'];
        }
	}
    /**
     * Update widget settings
     */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['product_type'] = strip_tags( $new_instance['product_type'] );
		$instance['widget_type'] = strip_tags( $new_instance['widget_type'] );
		$instance['add_to_cart'] = strip_tags( $new_instance['add_to_cart'] );
		return $instance;
	}
    /**
     * Widget settings form
     */
	public function form($instance)
	{
        $instance = wp_parse_args( (array) $instance, self::$defaults );
		$title = strip_tags($instance['title']);
		$product_type = strip_tags($instance['product_type']);
		$widget_type = strip_tags($instance['widget_type']);
		$add_to_cart = strip_tags($instance['add_to_cart']);
		?>
        <div class="br_ww_widget_form">
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('product_type'); ?>"><?php _e('Product type:', 'BeRocket_Wish_List_domain'); ?></label>
            <select name="<?php echo $this->get_field_name('product_type'); ?>">
                <?php
                $product_type_array = array(
                    'top_wait'      => __('Top products from wait list', 'BeRocket_Wish_List_domain'), 
                    'top_wish'      => __('Top products from wish list', 'BeRocket_Wish_List_domain'),
                );
                foreach( $product_type_array as $d_type_slug => $d_type_name ) {
                    echo '<option value="'.$d_type_slug.'"'.($product_type == $d_type_slug ? ' selected' : '').'>'.$d_type_name.'</option>';
                }
                ?>
            </select>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('widget_type'); ?>"><?php _e('Type:', 'BeRocket_Wish_List_domain'); ?></label>
            <select name="<?php echo $this->get_field_name('widget_type'); ?>" class="br_ww_display_type">
                <?php
                $display_type = array(
                    'default' => __('Default', 'BeRocket_Wish_List_domain'), 
                    'image' => __('Image', 'BeRocket_Wish_List_domain'), 
                    'image_title' => 'Image with Title', 
                    'image_title_price' => 'Image with Title and Price', 
                    'title' => 'Title', 
                    'title_price' => 'Title with Price',
                );
                foreach( $display_type as $d_type_slug => $d_type_name ) {
                    echo '<option value="'.$d_type_slug.'"'.($widget_type == $d_type_slug ? ' selected' : '').'>'.$d_type_name.'</option>';
                }
                ?>
            </select>
        </p>
		<p>
            <label for="<?php echo $this->get_field_id('add_to_cart'); ?>"><?php _e('Show Add to cart button:'); ?></label>
            <input id="<?php echo $this->get_field_id('add_to_cart'); ?>" name="<?php echo $this->get_field_name('add_to_cart'); ?>" type="checkbox" value="1"<?php if( $add_to_cart ) echo ' checked'; ?>>
        </p>
        </div>
		<?php
	}
}
?>
