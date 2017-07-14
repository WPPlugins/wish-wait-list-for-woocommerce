<?php $options = BeRocket_Wish_List::get_wish_list_option ( 'br_wish_list_javascript_settings' ); ?>
<input name="br_wish_list_javascript_settings[settings_name]" type="hidden" value="br_wish_list_javascript_settings">
<table class="form-table">
    <tr>
        <th><?php _e( 'Before add to wish list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <textarea name="br_wish_list_javascript_settings[before_wish]"><?php echo $options['before_wish']; ?></textarea>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'After add to wish list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <textarea name="br_wish_list_javascript_settings[after_wish]"><?php echo $options['after_wish']; ?></textarea>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'Before add to wait list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <textarea name="br_wish_list_javascript_settings[before_wait]"><?php echo $options['before_wait']; ?></textarea>
        </td>
    </tr>
    <tr>
        <th><?php _e( 'After add to wait list', 'BeRocket_Wish_List_domain' ) ?></th>
        <td>
            <textarea name="br_wish_list_javascript_settings[after_wait]"><?php echo $options['after_wait']; ?></textarea>
        </td>
    </tr>
</table>