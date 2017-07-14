<?php
global $br_current_wish, $br_current_wait;
$terms_wait = array();
if ( isset( $br_current_wait ) && is_array( $br_current_wait ) && count( $br_current_wait ) > 0 ) {
    foreach ( $br_current_wait as $product ) {
        $term = array();
        $current_language= apply_filters( 'wpml_current_language', NULL );
        $product = apply_filters( 'wpml_object_id', $product, 'product', true, $current_language );
        $post_get = wc_get_product($product);
        $term['id'] = $product;
        $term['title'] = $post_get->get_title();
        $term['image'] = $post_get->get_image();
        $term['price'] = $post_get->get_price_html();
        $term['link'] = $post_get->get_permalink();
        $term['availability'] = $post_get->get_availability();
        $term['is_in_stock'] = $post_get->is_in_stock();
        $terms_wait[] = $term;
    }
?>
<?php do_action ( 'berocket_before_wait_list' ); ?>
<h2 class="berocket_ww_list_title"><?php echo $text_options['wait_list']; ?></h2>
<div class="berocket_ww_container">
    <div class="berocket_ww_list berocket_wait_list" data-type="wait">
        <div class="berocket_ww_ul_container">
            <ul class="berocket_ww_products">
            <?php
            generate_ww_list ( $terms_wait );
            ?>
            </ul>
            <div style="clear:both;"></div>
        </div>
    </div>
    <span class="berocket_ww_show_all" style="display: none;"><?php echo $text_options['show_wait']; ?></span>
</div>
<?php do_action ( 'berocket_after_wait_list' );
}
?>
