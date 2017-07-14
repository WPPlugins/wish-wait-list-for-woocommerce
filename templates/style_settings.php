<?php $options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_style_settings' ); ?>
<input name="br_wish_list_style_settings[settings_name]" type="hidden" value="br_wish_list_style_settings">
<table class="form-table">
    <tr>
        <th><?php _e( 'Position for buttons', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <table class="berocket_ww_position_table">
                <?php 
                $position = array(
                    'head' => array(
                        '', 
                        __( 'Wait/Wish', 'BeRocket_Wish_List_domain' ),
                    ),
                    'after_image' => array(
                        'th' => __( 'After image', 'BeRocket_Wish_List_domain' ),
                        'ww_pos'
                    ),
                    'after_add_to_cart' => array(
                        'th' => __( 'After add to cart button', 'BeRocket_Wish_List_domain' ),
                        'ww_pos'
                    ),
                    'single_product' => array(
                        'th' => __( 'Single product page', 'BeRocket_Wish_List_domain' ),
                        'ww_pos'
                    )
                );
                foreach( $position as $pos_name => $positions ) {
                    echo '<tr class="berocket_pos_table_'.$pos_name.'">';
                    foreach( $positions as $but_name => $button_pos ) {
                        if( $pos_name === 'head' || $but_name === 'th' ) {
                            echo '<th>'.$button_pos.'</th>';
                        } else {
                            echo '<td><input type="checkbox" value="1" name="br_wish_list_style_settings['.$button_pos.']['.$pos_name.']"'.(@ $options[$button_pos][$pos_name] ? ' checked' : '').'></td>';
                        }
                    }
                    echo '</tr>';
                }
                ?>
            </table>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Wish list button icon', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <?php echo berocket_font_select_upload('', 'br_wish_list_icon_wish', 'br_wish_list_style_settings[icon_wish]', $options['icon_wish'], true, false, false); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Wait list button icon', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <?php echo berocket_font_select_upload('', 'br_wish_list_icon_wait', 'br_wish_list_style_settings[icon_wait]', $options['icon_wait'], true, false, false); ?>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Update status icon', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <?php echo berocket_font_select_upload('', 'br_wish_list_icon_wait', 'br_wish_list_style_settings[icon_load]', $options['icon_load'], true, false, false); ?>
        </td>
    </tr>
</table>
