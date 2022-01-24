<?php

$slwp_store_setting = get_option('slwp_store_options');

$slwp_locators = get_post_meta( get_the_ID(), 'aka_saved_locators', true );
$fields_count = ( isset( $slwp_locators ) && !empty( $slwp_locators ) ) ? count( $slwp_locators ) : 0;


?>
<div id="slwp-store-stuff">

    <p>
        <strong><?php _e( 'Add New Store location:', 'slwp-stores' ); ?></strong>
    </p>
    <table id="slwp-newmeta">
        <thead>
            <tr>
                <th class="left"><label for="metakeyselect"><?php _e( 'Name', 'slwp-stores' ); ?></label></th>
                <th><label for="metavalue"><?php _e( 'Location', 'slwp-stores' ); ?></label></th>
                <?php
                if ( 1 == $slwp_store_setting['show_url_field'] ) {
                    ?>
                    <th><label for="slwp-url"><?php _e( 'Url', 'slwp-stores' ); ?></label></th>
                    <?php
                }
                if ( 1 == $slwp_store_setting['show_phone_field'] ) {
                    ?>
                    <th><label for="slwp-phone"><?php _e( 'Phone', 'slwp-stores' ); ?></label></th>
                    <?php
                }
                if ( 1 == $slwp_store_setting['show_description_field'] ) {
                    ?>
                    <th><label for="slwp-description"><?php _e( 'Description', 'slwp-stores' ); ?></label></th>
                    <?php
                }
                ?>
                <th class="slwp-del-edit"></th>
            </tr>
        </thead>

        <tbody class="list-meta-body">
            <?php
            if ( isset( $slwp_locators ) && !empty( $slwp_locators ) ) {
                foreach ($slwp_locators as $slwp_key => $slwp_value) {

                    ?>
                    <tr>
                        <td>
                            <span>
                                <?php echo esc_attr( $slwp_value['aka_name'] ); ?>
                            </span>
                            <div class="slwp-input-wrap"><input class="hidden" type="text" name="slwp_store_meta[<?php echo $slwp_key; ?>][aka_name]" value="<?php echo esc_attr( $slwp_value['aka_name'] ); ?>"></div>
                        </td>
                        <td>
                            <span>
                                <?php echo esc_attr( $slwp_value['aka_location'] ); ?>
                            </span>
                            <div class="slwp-input-wrap">
                                <input class="hidden" type="text" name="slwp_store_meta[<?php echo $slwp_key; ?>][aka_location]" value="<?php echo esc_attr( $slwp_value['aka_location'] ); ?>">
                                <input type="hidden" name="slwp_store_meta[<?php echo $slwp_key; ?>][aka_location_latn]" value="<?php echo esc_attr( $slwp_value['aka_location_latn'] ); ?>">
                            </div>
                        </td>
                        <?php
                        if ( 1 == $slwp_store_setting['show_url_field'] ) {
                            ?>
                            <td>
                                <span>
                                    <?php echo ( !empty( $slwp_value['aka_url'] )  ) ? esc_url( $slwp_value['aka_url'] ) : '' ; ?>

                                </span>
                                <div class="slwp-input-wrap">
                                    <input class="hidden" type="text" name="slwp_store_meta[<?php echo $slwp_key; ?>][aka_url]" value="<?php echo ( !empty( $slwp_value['aka_url'])  ) ? esc_url( $slwp_value['aka_url'] ) : '' ; ?>">
                                </div>
                            </td>
                            <?php
                        }
                        if ( 1 == $slwp_store_setting['show_phone_field'] ) {
                            ?>
                            <td>
                                <span>
                                    <?php echo ( !empty( $slwp_value['aka_phone'] )  ) ? esc_attr( $slwp_value['aka_phone'] ) : '' ; ?>

                                </span>
                                <div class="slwp-input-wrap">
                                    <input class="hidden" type="text" name="slwp_store_meta[<?php echo $slwp_key; ?>][aka_phone]" value="<?php echo ( !empty( $slwp_value['aka_phone'] )  ) ? esc_attr( $slwp_value['aka_phone'] ) : '' ; ?>">
                                </div>
                            </td>
                            <?php
                        }
                        if ( 1 == $slwp_store_setting['show_description_field'] ) {
                            ?>
                            <td>
                                <span>
                                    <?php echo ( !empty( $slwp_value['aka_description'] )  ) ? esc_attr( $slwp_value['aka_description'] ) : '' ; ?>

                                </span>
                                <div class="slwp-input-wrap">
                                    <textarea class="hidden" name="slwp_store_meta[<?php echo $slwp_key; ?>][aka_description]"><?php echo ( !empty( $slwp_value['aka_description'] )  ) ? esc_textarea( $slwp_value['aka_description'] ) : '' ; ?></textarea>
                                </div>
                            </td>
                            <?php
                        }
                        ?>

                        <td class="slwp-del-edit">
                            <a href="#" data-list="<?php echo $slwp_key; ?>" class="slwp-button-delete"></a>
                        </td>

                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
                <td class="left">
                    <input type="text" id="slwp-name" placeholder="Name" class="slwp-fields" name="slwp_name">
                </td>
                <td>
                    <input type="text" name="slwp_location" class="slwp-fields" id="slwp-location">
                </td>
                <?php
                if ( 1 == $slwp_store_setting['show_url_field'] ) {
                    ?>
                    <td>
                        <input type="text" name="slwp_url" placeholder="http://" class="slwp-fields" id="slwp_url">
                    </td>
                    <?php
                }

                if ( 1 == $slwp_store_setting['show_phone_field'] ) {
                    ?>
                    <td>
                        <input type="text" name="slwp_phone" placeholder="Phone No." class="slwp-fields" id="slwp_phone">
                    </td>
                    <?php
                }

                if ( 1 == $slwp_store_setting['show_description_field'] ) {
                    ?>
                    <td>
                        <textarea name="slwp_description" class="slwp-fields" id="slwp_description" rows="5" cols="4"></textarea>
                    </td>
                    <?php
                }

                ?>
                <td colspan="2">
                    <div class="submit">
                        <input type="hidden" name="slwp_fields_count" id="slwp_fields_count" value="<?php echo $fields_count; ?>">
                        <input name="slwp_submitmeta" id="slwp-newmeta-submit" class="button" value="Submit" type="button">
                    </div>
                </td>
            </tr>
    </tbody>
</table>
</div>