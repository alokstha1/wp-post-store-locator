<?php

$aka_store_setting = get_option('slwp_store_options');

$aka_locators = get_post_meta( get_the_ID(), 'aka_saved_locators', true );
$fields_count = ( isset( $aka_locators ) && !empty( $aka_locators ) ) ? count( $aka_locators ) : 0;


?>
<div id="aka-store-stuff">

    <p>
        <strong><?php _e( 'Add New Store location:', 'slwp-stores' ); ?></strong>
    </p>
    <table id="aka-newmeta">
        <thead>
            <tr>
                <th class="left"><label for="metakeyselect"><?php _e( 'Name', 'slwp-stores' ); ?></label></th>
                <th><label for="metavalue"><?php _e( 'Location', 'slwp-stores' ); ?></label></th>
                <?php
                if ( 1 == $aka_store_setting['show_url_field'] ) {
                    ?>
                    <th><label for="aka-url"><?php _e( 'Url', 'slwp-stores' ); ?></label></th>
                    <?php
                }
                if ( 1 == $aka_store_setting['show_phone_field'] ) {
                    ?>
                    <th><label for="aka-phone"><?php _e( 'Phone', 'slwp-stores' ); ?></label></th>
                    <?php
                }
                if ( 1 == $aka_store_setting['show_description_field'] ) {
                    ?>
                    <th><label for="aka-description"><?php _e( 'Description', 'slwp-stores' ); ?></label></th>
                    <?php
                }
                ?>
                <th class="aka-del-edit"></th>
            </tr>
        </thead>

        <tbody class="list-meta-body">
            <?php
            if ( isset( $aka_locators ) && !empty( $aka_locators ) ) {
                foreach ($aka_locators as $aka_key => $aka_value) {

                    ?>
                    <tr>
                        <td>
                            <span>
                                <?php echo esc_attr( $aka_value['aka_name'] ); ?>
                            </span>
                            <div class="aka-input-wrap"><input class="hidden" type="text" name="aka_store_meta[<?php echo $aka_key; ?>][aka_name]" value="<?php echo esc_attr( $aka_value['aka_name'] ); ?>"></div>
                        </td>
                        <td>
                            <span>
                                <?php echo esc_attr( $aka_value['aka_location'] ); ?>
                            </span>
                            <div class="aka-input-wrap">
                                <input class="hidden" type="text" name="aka_store_meta[<?php echo $aka_key; ?>][aka_location]" value="<?php echo esc_attr( $aka_value['aka_location'] ); ?>">
                                <input type="hidden" name="aka_store_meta[<?php echo $aka_key; ?>][aka_location_latn]" value="<?php echo esc_attr( $aka_value['aka_location_latn'] ); ?>">
                            </div>
                        </td>
                        <?php
                        if ( 1 == $aka_store_setting['show_url_field'] ) {
                            ?>
                            <td>
                                <span>
                                    <?php echo ( !empty( $aka_value['aka_url'] )  ) ? esc_url( $aka_value['aka_url'] ) : '' ; ?>

                                </span>
                                <div class="aka-input-wrap">
                                    <input class="hidden" type="text" name="aka_store_meta[<?php echo $aka_key; ?>][aka_url]" value="<?php echo ( !empty( $aka_value['aka_url'])  ) ? esc_url( $aka_value['aka_url'] ) : '' ; ?>">
                                </div>
                            </td>
                            <?php
                        }
                        if ( 1 == $aka_store_setting['show_phone_field'] ) {
                            ?>
                            <td>
                                <span>
                                    <?php echo ( !empty( $aka_value['aka_phone'] )  ) ? esc_attr( $aka_value['aka_phone'] ) : '' ; ?>

                                </span>
                                <div class="aka-input-wrap">
                                    <input class="hidden" type="text" name="aka_store_meta[<?php echo $aka_key; ?>][aka_phone]" value="<?php echo ( !empty( $aka_value['aka_phone'] )  ) ? esc_attr( $aka_value['aka_phone'] ) : '' ; ?>">
                                </div>
                            </td>
                            <?php
                        }
                        if ( 1 == $aka_store_setting['show_description_field'] ) {
                            ?>
                            <td>
                                <span>
                                    <?php echo ( !empty( $aka_value['aka_description'] )  ) ? esc_attr( $aka_value['aka_description'] ) : '' ; ?>

                                </span>
                                <div class="aka-input-wrap">
                                    <textarea class="hidden" name="aka_store_meta[<?php echo $aka_key; ?>][aka_description]"><?php echo ( !empty( $aka_value['aka_description'] )  ) ? esc_textarea( $aka_value['aka_description'] ) : '' ; ?></textarea>
                                </div>
                            </td>
                            <?php
                        }
                        ?>

                        <td class="aka-del-edit">
                            <a href="#" data-list="<?php echo $aka_key; ?>" class="aka-button-delete"></a>
                        </td>

                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td class="left">
                    <input type="text" id="aka-name" placeholder="Name" class="aka-fields" name="aka_name">
                </td>
                <td>
                    <input type="text" name="aka_location" class="aka-fields" id="aka-location">
                </td>
                <?php
                if ( 1 == $aka_store_setting['show_url_field'] ) {
                    ?>
                    <td>
                        <input type="text" name="aka_url" placeholder="http://" class="aka-fields" id="aka_url">
                    </td>
                    <?php
                }

                if ( 1 == $aka_store_setting['show_phone_field'] ) {
                    ?>
                    <td>
                        <input type="text" name="aka_phone" placeholder="Phone No." class="aka-fields" id="aka_phone">
                    </td>
                    <?php
                }

                if ( 1 == $aka_store_setting['show_description_field'] ) {
                    ?>
                    <td>
                        <textarea name="aka_description" class="aka-fields" id="aka_description" rows="5" cols="4"></textarea>
                    </td>
                    <?php
                }

                ?>
                <td colspan="2">
                    <div class="submit">
                        <input type="hidden" name="aka_fields_count" id="aka_fields_count" value="<?php echo $fields_count; ?>">
                        <input name="aka_submitmeta" id="aka-newmeta-submit" class="button" value="Submit" type="button">
                    </div>
                </td>
            </tr>
    </tbody>
</table>
</div>