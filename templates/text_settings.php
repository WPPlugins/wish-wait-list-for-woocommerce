<?php $options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_text_settings' ); ?>
<input name="br_wish_list_text_settings[settings_name]" type="hidden" value="br_wish_list_text_settings">
<table class="form-table">
    <tr>
        <th><?php _e( 'Text before wish list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <input name="br_wish_list_text_settings[wish_list]" type='text' value="<?php echo $options['wish_list']; ?>"/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Text before wait list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <input name="br_wish_list_text_settings[wait_list]" type='text' value="<?php echo $options['wait_list']; ?>"/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Text on button to show all products in wish list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <input name="br_wish_list_text_settings[show_wish]" type='text' value="<?php echo $options['show_wish']; ?>"/>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Text on button to show all products in wait list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <input name="br_wish_list_text_settings[show_wait]" type='text' value="<?php echo $options['show_wait']; ?>"/>
        </td>
    </tr>
</table>