<div class="wrap">
<?php 
$dplugin_name = 'WooCommerce Wish/Wait List';
$dplugin_link = 'http://berocket.com/product/woocommerce-wish-wait-list';
$dplugin_price = 20;
$dplugin_desc = '';
@ include 'settings_head.php';
@ include 'discount.php';
?>
<div class="wrap show_premium">  
    <div id="icon-themes" class="icon32"></div>  
    <h2>Wish/Wait List Settings</h2>  
    <?php settings_errors(); ?>  

    <?php $active_tab = isset( $_GET[ 'tab' ] ) ? @ $_GET[ 'tab' ] : 'general'; ?>  

    <h2 class="nav-tab-wrapper">  
        <a href="?page=br-wish-list&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'BeRocket_Wish_List_domain') ?></a>
        <a href="?page=br-wish-list&tab=style" class="nav-tab <?php echo $active_tab == 'style' ? 'nav-tab-active' : ''; ?>"><?php _e('Style', 'BeRocket_Wish_List_domain') ?></a>
        <a href="?page=br-wish-list&tab=text" class="nav-tab <?php echo $active_tab == 'text' ? 'nav-tab-active' : ''; ?>"><?php _e('Text', 'BeRocket_Wish_List_domain') ?></a>
        <a href="?page=br-wish-list&tab=javascript" class="nav-tab <?php echo $active_tab == 'javascript' ? 'nav-tab-active' : ''; ?>"><?php _e('JavaScript', 'BeRocket_Wish_List_domain') ?></a>
    </h2>  

    <form class="wish_list_submit_form" method="post" action="options.php">  
        <?php 
        if( $active_tab == 'general' ) { 
            settings_fields( 'br_wish_list_general_settings' );
            do_settings_sections( 'br_wish_list_general_settings' );
        } else if( $active_tab == 'style' ) {
            settings_fields( 'br_wish_list_style_settings' );
            do_settings_sections( 'br_wish_list_style_settings' ); 
        } else if( $active_tab == 'text' ) {
            settings_fields( 'br_wish_list_text_settings' );
            do_settings_sections( 'br_wish_list_text_settings' ); 
        } else if( $active_tab == 'javascript' ) {
            settings_fields( 'br_wish_list_javascript_settings' );
            do_settings_sections( 'br_wish_list_javascript_settings' ); 
        }
        ?>             
        <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'BeRocket_Wish_List_domain') ?>" />
    </form> 
</div>
<?php
$feature_list = array(
    'Different positions for buttons',
    'Wait and Wish lists on different pages',
    'Widget with products from wait list, that in stock',
    'Widget with products from wish list, that on sale',
    'Widget with related products to products from wish list',
    'Widget with most popular products',
    'Customization for wish/wait list',
    'Customization for buttons',
    'Is customers want to remove products in order from list',
);
@ include 'settings_footer.php';
?>
</div>
