<?php $options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_general_settings' ); ?>
<input name="br_wish_list_general_settings[settings_name]" type="hidden" value="br_wish_list_general_settings">
<table class="form-table">
    <tr>
        <th><?php _e( 'Wish Page', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <select name="br_wish_list_general_settings[ww_page]">
                <?php 
                $pages = get_pages();
                foreach ( $pages as $page ) {
                    echo '<option value="'.$page->ID.'"'.( ( $options['ww_page'] == $page->ID ) ? ' selected' : '' ).'>'.$page->post_title.'</option>';
                }
                ?>
            </select>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Disable wish list button', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <input name="br_wish_list_general_settings[wish_list]" type='checkbox' value="1"<?php if ( $options['wish_list'] ) echo ' checked'; ?>/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Disable wait list button', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <input name="br_wish_list_general_settings[wait_list]" type='checkbox' value="1"<?php if ( $options['wait_list'] ) echo ' checked'; ?>/>
        </td>
    </tr>
</table>