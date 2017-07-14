<?php 
$product_count = 4;
if( $product_type == 'top_wait' ) {
    $meta_key = 'wait_users_count';
} else {
    $meta_key = 'wish_users_count';
}
$loop = new WP_Query(array(
    'post_type'			=> 'product',
    'posts_per_page'	=> $product_count,
    'meta_key'			=> $meta_key,
    'orderby'			=> 'meta_value_num',
    'order'				=> 'DESC'
));

if( is_object($loop) && $loop->post_count == 0 ) {
    $loop= false;
}
if($loop === false) {
    return false;
}

if ($display_type === false || $display_type == 'default' ) {
    if( $display_type === false ) {
        echo '<h2>', $options['suggestions_title'], '</h2>';
    }
    woocommerce_product_loop_start();
    do_action('woocommerce_before_shop_loop_products');
    $x = 0;

    if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post(); global $product, $post;
        $product = wc_get_product(get_the_ID());
        $post = get_post( get_the_ID() );
        if ( !$product->is_visible() ) continue;
        if( function_exists('wc_get_template') ) {
            wc_get_template('content-product.php', array('product' => $product));
        } else {
            woocommerce_get_template('content-product.php', array('product' => $product));
        }
    endwhile; endif;
    do_action('woocommerce_after_shop_loop_products');
    woocommerce_product_loop_end();
} elseif( $display_type == 'image' || $display_type == 'image_title' || $display_type == 'image_title_price' ) {
    ?>
    <ul class="brcs_image">
    <?php
        if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post(); global $product;
            $product = wc_get_product(get_the_ID());
            $product_id = br_wc_get_product_id($product);
            if ( !$product->is_visible() ) continue;
            echo '<li><a href="', get_permalink($product_id), '">', woocommerce_get_product_thumbnail(), ($display_type == 'image_title' ? $product->get_title() : ($display_type == 'image_title_price' ? $product->get_title().' - '.( function_exists('wc_price') ? wc_price( $product->get_price() ) : woocommerce_price( $product->get_price() ) ) : '')), '</a>';
            if ( $add_to_cart ) {
                woocommerce_template_loop_add_to_cart();
            }
            echo '</li>';
        endwhile; endif;
    ?>
    </ul>
    <?php
} elseif( $display_type == 'title' || $display_type == 'title_price' ) {
    ?>
    <ul class="brcs_name">
    <?php
        if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post(); global $product;
            $product = wc_get_product(get_the_ID());
            $product_id = br_wc_get_product_id($product);
            if ( !$product->is_visible() ) continue;
            echo '<li><a href="', get_permalink($product_id), '">', ($display_type == 'title' ? $product->get_title() : ($display_type == 'title_price' ? $product->get_title().' - '.( function_exists('wc_price') ? wc_price( $product->get_price() ) : woocommerce_price( $product->get_price() ) ) : '')), '</a>';
            if ( $add_to_cart ) {
                woocommerce_template_loop_add_to_cart();
            }
            echo '</li>';
        endwhile; endif;
    ?>
    </ul>
    <?php
}
?>
