<style type="text/css">
.wrap {
    max-width: 800px;
}
.hint {
    margin-top: 1em;
    font-style: italic;
}
.linkback {
    font-size: 11px;
}
.gb-sf4wp-logo {
    height: 26px;
    vertical-align: -4px;
    margin: 0 5px 0 0;
}
.gb-sf4wp-status {
    display: inline-block;
    padding: 5px 10px;
    color: #FFF;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 13px;
}
.gb-sf4wp-status-grey {
    background: #8d8d8d;
}
.gb-sf4wp-status-green {
    background: #45b94e;
}
.gb-sf4wp-status-orange {
    background: #f1b530;
}
.gb-sf4wp-status-red {
    background: #cc2b2b;
}
.copyrights {
    font-size: 10px;
    font-style: italic;
}
.copyrights a {
    color: #444;
}

.gb-sf4wp-pb {
    position: relative;
    width: 100%;
    background: #DDD;
    margin: 10px 0;
}
.gb-sf4wp-label {
    position: absolute;
    line-height: 38px;
    text-align: center;
    width: 100%;
}
.gb-sf4wp-progress {
    min-width: 0%;
    background: #9eb8ff;
    padding: 5px 0;
    height: 28px;
}
.gb-sf4wp-import-status {
    font-style: italic;
}
.gb-sf4wp-import-status,
.gb-sf4wp-processed-success,
.gb-sf4wp-processed-failed, 
.gb-sf4wp-processed-failed-emails, 
.gb-sf4wp-sync-settings {
    margin: 10px 0;
}
.gb-sf4wp-processed-results {
    border: 1px solid #BBB;
    padding: 0 14px;
    margin-top: 15px;
}
.gb-sf4wp-page-log textarea {
    width: 100%;
    height: 500px;
    overflow-x: auto;
    overflow-y: scroll;
}
</style>
<div class="wrap">

    <h2>
        <img src="<?php echo plugins_url( 'assets/img/sendfox-icon.svg', GB_SF4WP_CORE_FILE ); ?>"
                 title="<?php _e( 'Thank you for using this plugin. You are amazing!', 'sf4wp' ); ?>" class="gb-sf4wp-logo" />
        <span><?php echo GB_SF4WP_NAME; ?></span>
    </h2>

    <?php

        $options = get_option( 'gb_sf4wp_options' );

        $gb_admin_menu = array(
            'connect'           => __( 'Connect', 'sf4wp' ),
            // 'forms'             => __( 'Forms', 'sf4wp' ),
            'integrations'      => __( 'Integrations', 'sf4wp' ),
            'sync'              => __( 'Export', 'sf4wp' ),
            // 'settings'          => __( 'Settings', 'sf4wp' ),
        );

        if( empty( $_GET[ 'tab' ] ) )
        {
            $_GET[ 'tab' ] = 'connect';
        }

        if( !empty( $options['enable_log'] ) )
        {
            $gb_admin_menu['log'] = __( 'Log', 'sf4wp' );
        }

        echo '<h2 class="nav-tab-wrapper">';

        foreach( $gb_admin_menu as $k => $m )
        {
            echo '<a href="?page=' . GB_SF4WP_ID . '&tab=' . $k . '" class="nav-tab' . ( ( $_GET[ 'tab' ] == $k ) ? ' nav-tab-active' : '' ) . '">' . $m . '</a>';
        }

        echo '</h2>';

        // display tabs

        if( 'connect' === $_GET['tab'] ):

            if( !empty( $options['api_key'] ) )
            {
                $request = gb_sf4wp_api_request( 'me' );

                if( !empty( $request['status'] ) )
                {
                    if( $request['status'] === 'success' )
                    {
                        $status = '<div class="gb-sf4wp-status gb-sf4wp-status-green">';
                        $status .= __( 'Connected', 'sf4wp' );

                        if( !empty( $request['result'] ) && !empty( $request['result']['name'] ) )
                        {
                            $status .= ': ' . $request['result']['name'];
                        }

                        $status .= '</div>';
                    }
                    elseif( $request['status'] === 'error' )
                    {
                        $status = '<div class="gb-sf4wp-status gb-sf4wp-status-red">' . __( 'Error:', 'sf4wp' ) . ' ' . $request['error_text'] . '</div>';
                    }
                    else
                    {
                        $status = '<div class="gb-sf4wp-status gb-sf4wp-status-red">' . __( 'Error: Undefined', 'sf4wp' ) . '</div>';
                    }
                }
                else
                {
                    $status = '<div class="gb-sf4wp-status gb-sf4wp-status-red">' . __( 'Error: Response Status', 'sf4wp' ) . '</div>';
                }
            }
            else
            {
                $status = '<div class="gb-sf4wp-status gb-sf4wp-status-grey">' . __( 'Not Connected', 'sf4wp' ) . '</div>';
            }

            if( empty( $options['enable_log'] ) )
            {
                $options['enable_log'] = 0;
            }

            if( !empty( $_GET['action'] ) )
            {
                if( $_GET['action'] === 'reload_lists' )
                {
                    delete_site_transient( 'gb_sf4wp_api_lists' );

                    echo '<div class="notice notice-success"><p>' . __( 'Lists were successfully reloaded from your SendFox account.', 'sf4wp' ) . '</p></div>';
                }
            }
?>

        <h3><?php _e( 'API Settings', 'sf4wp' ); ?></h3>

        <div>

            <form method="POST" action="options.php">

                <?php settings_fields( 'gb_sf4wp_options' ); ?>

                <table class="form-table">
                    <tbody>

                    <tr>
                        <th><?php _e( 'Status:', 'sf4wp' ); ?></th>
                        <td>
                            <?php echo $status; ?>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'API Token:', 'sf4wp' ); ?></th>
                        <td>
                            <input type="text" name="gb_sf4wp_options[api_key]" value="<?php echo ( !empty( $options[ 'api_key' ] ) ) ? $options[ 'api_key' ] : ''; ?>" placeholder="<?php _e( 'Your SendFox API key', 'sf4wp' ); ?>" class="widefat" />
                                
                            <p class="hint"><?php echo sprintf( __( 'Personal Access Token to connect with your SendFox account. <a href="%s" target="_blank">Get your API key here.</a>', 'sf4wp' ), 'https://sendfox.com/account/oauth' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'Enable log?', 'sf4wp' ); ?></th>
                        <td>
                            <select name="gb_sf4wp_options[enable_log]">
                                <option value="0" <?php selected( $options['enable_log'], 0 ); ?>><?php _e( 'disabled', 'sf4wp' ); ?></option>
                                <option value="1" <?php selected( $options['enable_log'], 1 ); ?>><?php _e( 'enabled', 'sf4wp' ); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'Reload lists', 'sf4wp' ); ?></th>
                        <td>
                            <a href="admin.php?page=<?php echo esc_attr( $_GET['page'] ); ?>&action=reload_lists" class="button button-secondary"><?php _e( 'Reload lists', 'sf4wp' ); ?></a>
                        </td>
                    </tr>

                    </tbody>
                </table>

                <div>
                    <?php submit_button(); ?>
                </div>

                <div class="copyrights">
                    <?php _e( 'SendFox website, title and logo are owned by', 'sf4wp' ); ?> <a href="https://sumo.com/?utm_source=sf4wp&utm_medium=web&utm_campaign=wppluginadmin" target="_blank">Sumo Group, Inc.</a>
                </div>

            </form>

        </div>

<?php

        // elseif( 'forms' === $_GET['tab'] ):


        elseif( 'integrations' === $_GET['tab'] ):

            $integration = 'list';

            $all_integrations = array(
                'wp-comment-form' => array(
                    'title' => __( 'Comment Form', 'sf4wp' ),
                    'description' => __( 'Subscribes people from your WordPress comment form', 'sf4wp' ),
                ),
                'wp-registration-form' => array(
                    'title' => __( 'Registration Form', 'sf4wp' ),
                    'description' => __( 'Subscribes people from your WordPress registration form', 'sf4wp' ),
                ),

                // WooCommerce checkout integration

                'woocommerce-checkout' => array(
                    'title' => __( 'WooCommerce Checkout', 'sf4wp' ),
                    'description' => __( 'Subscribes people from WooCommerce\'s checkout form', 'sf4wp' ),
                ),

                // Divi Builder integration

                'divi' => array(
                    'title' => __( 'Divi Email Optin', 'sf4wp' ),
                    'description' => __( 'Adds SendFox to the list of providers in Divi\'s Email Optin block', 'sf4wp' ),
                ),

                // Gutenberg integration

                'gutenberg' => array(
                    'title' => __( 'Gutenberg Email Optin', 'sf4wp' ),
                    'description' => __( 'Adds new customizable Email Optin block to Gutenberg. Pick a list and let everybody subscribe from any page.', 'sf4wp' ),
                ),

                // LearnDash integration

                'learndash-course' => array(
                    'title' => __( 'LearnDash Course Enrollment', 'sf4wp' ),
                    'description' => __( 'Subscribes people, when they enroll into your LearnDash courses', 'sf4wp' ),
                ),
            );

            if( !empty( $_GET['integration'] ) && array_key_exists( $_GET['integration'], $all_integrations ) )
            {
                $integration = $_GET['integration'];
            }

?>
        <h3>
            <?php _e( 'Integrations', 'sf4wp' ); ?>&nbsp;
            <?php echo ( ( !empty( $integration ) && $integration !== 'list' ) ? '<a class="linkback" href="' . admin_url( 'admin.php?page=' . GB_SF4WP_ID . '&tab=integrations' ) . '">' . __( 'back to all integrations', 'sf4wp' ) . '</a>' : '' ); ?>
        </h3>

        <div>

<?php
            if( 'list' === $integration ):
?>

            <p><?php _e( 'The table below shows all available integrations. Click on the name of an integration to edit all settings specific to that integration.', 'sf4wp' ); ?></p>

<?php
            else:

                if( !empty( $all_integrations[ $integration ] ) )
                {
                    echo '<p>' . 
                        $all_integrations[ $integration ]['title'] . ' ' . __( 'integration settings', 'sf4wp' ) . 
                        '</p>';
                }

            endif;
?>

            <form method="POST" action="options.php">

                <?php settings_fields( 'gb_sf4wp_options' ); ?>

<?php
                if( 'list' === $integration ):
?>
                <table class="mc4wp-table widefat striped">
                    <thead>
                    <tr>
                        <th><?php _e( 'Name', 'sf4wp' ); ?></th>
                        <th><?php _e( 'Description', 'sf4wp' ); ?></th>
                        <th><?php _e( 'Status', 'sf4wp' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
<?php
                    foreach( $all_integrations as $k => $i ):
?>
                    <tr>
                        <td>
                            <strong><a href="<?php echo admin_url( 'admin.php?page=' . GB_SF4WP_ID . '&tab=integrations&integration=' . $k ); ?>" title="<?php _e( 'Configure this integration', 'sf4wp' ); ?>"><?php echo $i['title']; ?></a></strong>
                        </td>
                        <td class="desc">
                            <?php echo $i['description']; ?>
                        </td>
                        <td>
                        <?php 

                            if( !empty( $options[ $k ] ) && !empty( $options[ $k ]['enabled'] ) )
                            {
                                echo '<div class="gb-sf4wp-status gb-sf4wp-status-green">Active</div>';
                            }
                            else
                            {
                                echo '<div class="gb-sf4wp-status gb-sf4wp-status-grey">Inactive</div>';
                            }

                        ?>
                        </td>
                    </tr>
<?php 
                    endforeach;
?>
                    </tbody>
                </table>
<?php
                elseif( 
                    'wp-comment-form' === $integration || 
                    'wp-registration-form' === $integration || 
                    'woocommerce-checkout' === $integration 
                ):

                    if( empty( $options[ $integration ] ) )
                    {
                        $options[ $integration ] = array();
                    }
?>
                <table class="form-table"><tbody>

                    <tr>
                        <th><?php _e( 'Enable?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][enabled]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['enabled'] ) ? 
                                    checked( $options[ $integration ]['enabled'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>

                            <p class="hint"><?php echo sprintf( __( 'Enable the %s integration? This will add a sign-up checkbox to the form.', 'sf4wp' ), $all_integrations[ $integration ]['title'] ); ?></p>

                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'Implicit?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][implicit]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['implicit'] ) ? 
                                    checked( $options[ $integration ]['implicit'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>

                            <p class="hint"><?php _e( 'Select "yes" if you want to subscribe people without asking them explicitly.', 'sf4wp' ); ?></p>

                            <p class="hint"><?php _e( 'Does not bypass the double opt-in on Free & Lifetime plans.', 'sf4wp' ); ?></p>

                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'Pre-checked?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][prechecked]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['prechecked'] ) ? 
                                    checked( $options[ $integration ]['prechecked'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>

                            <p class="hint"><?php _e( 'Select "yes" if the checkbox should be pre-checked.', 'sf4wp' ); ?></p>

                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'SendFox list', 'sf4wp' ); ?></th>
                        <td>
                            <?php 

                                $lists = gb_sf4wp_get_lists();

                                if( 
                                    $lists['status'] === 'error' || 
                                    empty( $lists['result'] ) || 
                                    empty( $lists['result']['data'] )
                                )
                                {
                                    echo 'No lists found, <a href="' . admin_url( 'admin.php?page=' . GB_SF4WP_ID . '&tab=connect' ) . '">' . __( 'are you connected to SendFox?', 'sf4wp' ) . '</a>';
                                }
                                else
                                {
                                    echo '<select name="gb_sf4wp_options[' . $integration . '][list]" class="widefat">';

                                    if( empty( $options[ $integration ]['list'] ) )
                                    {
                                        $options[ $integration ]['list'] = '';
                                    }

                                    echo '<option value="">' . __( 'select the list...', 'sf4wp' ) . '</option>';

                                    foreach( $lists['result']['data'] as $l )
                                    {
                                        if( $options[ $integration ]['list'] == $l['id'] )
                                        {
                                            echo '<option value="' . $l['id'] . '" selected="selected">' . $l['name'] . '</option>';
                                        }
                                        else
                                        {
                                            echo '<option value="' . $l['id'] . '">' . $l['name'] . '</option>';
                                        }
                                    }

                                    echo '</select>';
                                }

                            ?>

                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'Checkbox label text', 'sf4wp' ); ?></th>
                        <td>

                            <input 
                                type="text" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][label]"
                                value="<?php 
                                echo ( !empty( $options[ $integration ]['label'] ) ? 
                                    htmlspecialchars( $options[ $integration ]['label'] ) : '' );
                                ?>"
                                class="widefat" />

                            <p class="hint"><?php _e( 'HTML tags like <code>&lt;strong&gt&lt;em&gt&lt;a&gt</code> are allowed in the label text.', 'sf4wp' ); ?></p>

                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'Load default CSS?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][css]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['css'] ) ? 
                                    checked( $options[ $integration ]['css'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>

                            <p class="hint"><?php _e( 'Select "yes" if the checkbox looks sloppy.', 'sf4wp' ); ?></p>

                        </td>
                    </tr>

                <?php if( 'wp-comment-form' === $integration ): ?>

                    <tr>
                        <th><?php _e( 'Manual comment approval check?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][approved_check]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['approved_check'] ) ? 
                                    checked( $options[ $integration ]['approved_check'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>

                            <p class="hint"><?php _e( 'Submit email to SendFox after comment was approved manually. Otherwise, only emails from auto approved comments will be submitted.', 'sf4wp' ); ?></p>

                        </td>
                    </tr>

                <?php endif; ?>

                <?php if( 'woocommerce-checkout' === $integration ): ?>

                    <tr>
                        <th><?php _e( 'Position on checkout', 'sf4wp' ); ?></th>
                        <td>
                        <?php 

                            echo '<select name="gb_sf4wp_options[' . $integration . '][position]" class="widefat">';
                            echo '<option value="">' . __( 'select the position...', 'sf4wp' ) . '</option>';

                            if( empty( $options[ $integration ]['position'] ) )
                            {
                                $options[ $integration ]['position'] = '';
                            }

                            $positions = array(
                                // 'after_email'       => __( 'After email field', 'sf4wp' ),
                                'after_billing'     => __( 'After billing details', 'sf4wp' ),
                                'after_shipping'    => __( 'After shipping details', 'sf4wp' ),
                                'after_customer'    => __( 'After customer details', 'sf4wp' ),
                                'before_submit'     => __( 'Before submit button', 'sf4wp' ),
                                'after_notes'       => __( 'After order notes', 'sf4wp' ),
                            );

                            foreach( $positions as $k => $p )
                            {
                                if( $options[ $integration ]['position'] == $k )
                                {
                                    echo '<option value="' . $k . '" selected="selected">' . $p . '</option>';
                                }
                                else
                                {
                                    echo '<option value="' . $k . '">' . $p . '</option>';
                                }
                            }

                            echo '</select>';

                        ?>
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody></table>

                <div>
                    <?php submit_button(); ?>
                </div>

<?php
                elseif(
                    'learndash-course' === $integration
                ):

                    if( empty( $options[ $integration ] ) )
                    {
                        $options[ $integration ] = array();
                    }
?>
                <table class="form-table"><tbody>

                    <tr>
                        <th><?php _e( 'Enable?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][enabled]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['enabled'] ) ? 
                                    checked( $options[ $integration ]['enabled'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>

                            <p class="hint"><?php echo sprintf( __( 'Enable the %s integration? All new enrollees to any course will be subscribed to your list.', 'sf4wp' ), $all_integrations[ $integration ]['title'] ); ?></p>

                        </td>
                    </tr>

                    <tr>
                        <th><?php _e( 'SendFox list', 'sf4wp' ); ?></th>
                        <td>
                            <?php 

                                $lists = gb_sf4wp_get_lists();

                                if( 
                                    $lists['status'] === 'error' || 
                                    empty( $lists['result'] ) || 
                                    empty( $lists['result']['data'] )
                                )
                                {
                                    echo 'No lists found, <a href="' . admin_url( 'admin.php?page=' . GB_SF4WP_ID . '&tab=connect' ) . '">' . __( 'are you connected to SendFox?', 'sf4wp' ) . '</a>';
                                }
                                else
                                {
                                    echo '<select name="gb_sf4wp_options[' . $integration . '][list]" class="widefat">';

                                    if( empty( $options[ $integration ]['list'] ) )
                                    {
                                        $options[ $integration ]['list'] = '';
                                    }

                                    echo '<option value="">' . __( 'select the list...', 'sf4wp' ) . '</option>';

                                    foreach( $lists['result']['data'] as $l )
                                    {
                                        if( $options[ $integration ]['list'] == $l['id'] )
                                        {
                                            echo '<option value="' . $l['id'] . '" selected="selected">' . $l['name'] . '</option>';
                                        }
                                        else
                                        {
                                            echo '<option value="' . $l['id'] . '">' . $l['name'] . '</option>';
                                        }
                                    }

                                    echo '</select>';
                                }

                            ?>

                        </td>
                    </tr>

                </tbody></table>

                <?php // to help pre_update_option function process single checkbox properly ?>
                <input type="hidden" name="gb_sf4wp_options[<?php echo $integration; ?>][dummy]" value="1" />

                <div>
                    <?php submit_button(); ?>
                </div>

<?php
                elseif(
                    'divi' === $integration
                ):

                    if( empty( $options[ $integration ] ) )
                    {
                        $options[ $integration ] = array();
                    }
?>
                <p><b><?php _e( 'Important: Please, make sure only one Divi Theme or Divi Builder is enabled on your site. Either one theme or one plugin.', 'sf4wp' ); ?></b></p>

                <table class="form-table"><tbody>

                    <tr>
                        <th><?php _e( 'Enable?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][enabled]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['enabled'] ) ? 
                                    checked( $options[ $integration ]['enabled'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>

                            <p class="hint"><?php echo sprintf( __( 'Enable the %s integration? This will add SendFox to the list of email providers in Divi.', 'sf4wp' ), $all_integrations[ $integration ]['title'] ); ?></p>

                        </td>
                    </tr>

                </tbody></table>

                <?php // to help pre_update_option function process single checkbox properly ?>
                <input type="hidden" name="gb_sf4wp_options[<?php echo $integration; ?>][dummy]" value="1" />

                <div>
                    <?php submit_button(); ?>
                </div>

<?php
                elseif(
                    'gutenberg' === $integration
                ):
                    if( empty( $options[ $integration ] ) )
                    {
                        $options[ $integration ] = array();
                    }
?>
                <table class="form-table"><tbody>
                    <tr>
                        <th><?php _e( 'Enable?', 'sf4wp' ); ?></th>
                        <td>
                            
                            <input 
                                type="checkbox" 
                                name="gb_sf4wp_options[<?php echo $integration; ?>][enabled]"
                                value="1" 
                                <?php 
                                echo ( !empty( $options[ $integration ]['enabled'] ) ? 
                                    checked( $options[ $integration ]['enabled'], '1', FALSE ) : '' );
                                ?> /> <?php _e( 'yes', 'sf4wp' ); ?>
                            <p class="hint"><?php echo sprintf( __( 'Enable the %s integration? This will add new Email Optin block to Gutenberg blocks.', 'sf4wp' ), $all_integrations[ $integration ]['title'] ); ?></p>
                        </td>
                    </tr>
                </tbody></table>
                <?php // to help pre_update_option function process single checkbox properly ?>
                <input type="hidden" name="gb_sf4wp_options[<?php echo $integration; ?>][dummy]" value="1" />
                <div>
                    <?php submit_button(); ?>
                </div>


<?php
                endif;
?>

            </form>

        </div>
<?php

        elseif( 'sync' === $_GET['tab'] ):

?>

        <h3><?php _e( 'Export your contacts', 'sf4wp' ); ?></h3>

        <div>

            <p><?php _e( 'Here you can export either all your Wordpress user emails or all your WooCommerce customers into your SendFox account.', 'sf4wp' ); ?></p>

            <p><?php _e( 'If email already exists in your list(s), it will be ignored.', 'sf4wp' ); ?></p>

            <br />

            <p><b><?php _e( 'To start pick the destination and source below and click "Start export":', 'sf4wp' ); ?></b></p>

            <div class="gb-sf4wp-sync-settings">

                <label for="gb-sf4wp-sync-list">
                1. <?php _e( 'Pick the List (export destination):', 'sf4wp' ); ?> 
                </label>

                <?php 

                    $lists = gb_sf4wp_get_lists();

                    if( 
                        $lists['status'] === 'error' || 
                        empty( $lists['result'] ) || 
                        empty( $lists['result']['data'] )
                    )
                    {
                        echo 'No lists found, <a href="' . admin_url( 'admin.php?page=' . GB_SF4WP_ID . '&tab=connect' ) . '">' . __( 'are you connected to SendFox?', 'sf4wp' ) . '</a>';
                    }
                    else
                    {
                        echo '<select id="gb-sf4wp-sync-list">';

                        foreach( $lists['result']['data'] as $l )
                        {
                            echo '<option value="' . $l['id'] . '">' . $l['name'] . '</option>';
                        }

                        echo '</select>';
                    }

                ?>
            </div>

            <div class="gb-sf4wp-sync-settings">

                <label for="gb-sf4wp-sync-mode">
                2. <?php _e( 'Pick Export mode (export source):', 'sf4wp' ); ?> 
                </label>

                <select id="gb-sf4wp-sync-mode">
                    <option value="wp-users"><?php _e( 'Export Wordpress user emails', 'sf4wp' ); ?></option>
                    <option value="wc-customers"><?php _e( 'Export WooCommerce customer emails', 'sf4wp' ); ?></option>
                </select>

            </div>

            <p><b><?php _e( 'Do not close or reload the page during export.', 'sf4wp' ); ?></b></p>

            <br />            

            <div class="gb-sf4wp-pb">
                <div class="gb-sf4wp-label">0%</div>
                <div class="gb-sf4wp-progress" style="width: 0%;"></div>
            </div>

            <div class="gb-sf4wp-import-status">...</div>

            <div>
                <button class="button button-primary button-hero" id="gb-sf4wp-start-sync"><?php _e( 'Start export', 'sf4wp' ); ?></button>
            </div>

            <div class="gb-sf4wp-processed-results" style="display: none;">
                <div class="gb-sf4wp-processed-success"><?php _e( 'Contacts exported successfully:', 'sf4wp' ); ?> <span>0</span></div>
                <div class="gb-sf4wp-processed-failed"><?php _e( 'Contacts not exported:', 'sf4wp' ); ?> <span>0</span></div>
                <div class="gb-sf4wp-processed-failed-emails"></div>
            </div>

            <script type="text/javascript">
            jQuery(document).ready(function(){

                var $list = jQuery('#gb-sf4wp-sync-list');
                var $mode = jQuery('#gb-sf4wp-sync-mode');
                var $button = jQuery('#gb-sf4wp-start-sync');

                var $barLabel = jQuery('.gb-sf4wp-label');
                var $barProgress = jQuery('.gb-sf4wp-progress');

                var $status = jQuery('.gb-sf4wp-import-status');

                var $processed_s = jQuery('.gb-sf4wp-processed-success span');
                var $processed_f = jQuery('.gb-sf4wp-processed-failed span');
                var $processed_fe = jQuery('.gb-sf4wp-processed-failed-emails');

                var processed_fe = [];

                var list, mode;

                $button.on('click', function(){

                    $list.attr( 'disabled', 'disabled' );
                    $mode.attr( 'disabled', 'disabled' );
                    $button.attr( 'disabled', 'disabled' );

                    $processed_s.closest('.gb-sf4wp-processed-results').show();

                    $status.text( 'requesting export amount...' );

                    $processed_s.text( '0' );
                    $processed_f.text( '0' );

                    $barLabel.text( '0%' );
                    $barProgress.animate({ 'width': '0%' });

                    list = $list.find('option:selected').val();
                    mode = $mode.find('option:selected').val();

                    var data = {
                        action: 'sf4wp_process_sync',
                        stage: 1,
                        list: list,
                        mode: mode,
                        nonce: '<?php echo wp_create_nonce('sf4wp-sync-nonce'); ?>'
                    };

                    var data2 = {};

<?php 
                    if( !empty( $options['enable_log'] ) ):
?>

                    console.log( 'before step 1:', data );

<?php 
                    endif;
?>

                    jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: data,
                        dataType: 'json'
                    })
                    .done( function( response ) {

                        // setup progress bar and start import

<?php 
                        if( !empty( $options['enable_log'] ) ):
?>

                        console.log( 'step 1:', response );

<?php 
                        endif;
?>

                        var total = total_steps = processed_success = processed_fail = 0;

                        if( 
                            typeof response.result !== 'undefined' && response.result == 'success' &&
                            typeof response.total !== 'undefined' &&
                            typeof response.total_steps !== 'undefined' 
                        )
                        {
                            $status.text( response.result );

                            $barLabel.text( '0%' );

                            step = 1;

                            total_steps = parseInt( response.total_steps );

                            // start import cycle

                            gb_sf4wp_import_by_step( list, mode, step, total_steps );
                        }
                        else
                        {
                            $status.text( 'initial response error' );

                            if( typeof response.error_text !== 'undefined' )
                            {
                                $status.text( 'initial response error: ' + response.error_text );
                            }

                            $list.removeAttr( 'disabled' );
                            $mode.removeAttr( 'disabled' );
                            $button.removeAttr( 'disabled' );
                        }
                    })
                    .fail( function( a, b, c ) {

                        console.log( a, b, c );

                        $list.removeAttr( 'disabled' );
                        $mode.removeAttr( 'disabled' );
                        $button.removeAttr( 'disabled' );
                    });
                });

                function gb_sf4wp_import_by_step( list, mode, step, total_steps )
                {
                    var $list = jQuery('#gb-sf4wp-sync-list');
                    var $mode = jQuery('#gb-sf4wp-sync-mode');
                    var $button = jQuery('#gb-sf4wp-start-sync');

                    var $barLabel = jQuery('.gb-sf4wp-label');
                    var $barProgress = jQuery('.gb-sf4wp-progress');

                    var $status = jQuery('.gb-sf4wp-import-status');

                    $status.text( 'processing step ' + parseInt( step ) + ' / ' + parseInt( total_steps ) + '...' );

                    var data2 = {
                        action: 'sf4wp_process_sync',
                        stage: 2,
                        list: list,
                        mode: mode,
                        step: step,
                        total_steps: total_steps
                    };

<?php 
                    if( !empty( $options['enable_log'] ) ):
?>

                    console.log( 'before step 2:', data2 );

<?php 
                    endif;
?>

                    jQuery.ajax({
                        method: "POST",
                        url: ajaxurl,
                        data: data2,
                        dataType: 'json'
                    })
                    .done( function( response ) {

<?php 
                        if( !empty( $options['enable_log'] ) ):
?>

                        console.log( 'step 2:', step, response );

<?php 
                        endif;
?>

                        if( 
                            typeof response.result !== 'undefined' && response.result == 'success'
                        )
                        {
                            var current_progress = parseInt( ( 100 / total_steps ) * step );

                            $barLabel.text( current_progress + '%' );

                            $barProgress.animate({ 'width': parseInt( current_progress ) + '%' });

                            $status.text( response.result );

                            processed_success = processed_success + response.import_success_count;
                            processed_fail = processed_fail + response.import_fail_count;

                            processed_fe = processed_fe.concat( response.import_fail_emails );

                            $processed_s.text( processed_success );
                            $processed_f.text( processed_fail );

                            if( step < total_steps )
                            {
                                step = step + 1;

                                // recursive

                                var loop = setTimeout( function(){

                                    gb_sf4wp_import_by_step( list, mode, step, total_steps );

                                }, <?php echo GB_SF4WP_STEP_TIMEOUT; ?> );
                            }
                            else
                            {
                                $status.text( 'import finished' );

                                $list.removeAttr( 'disabled' );
                                $mode.removeAttr( 'disabled' );
                                $button.removeAttr( 'disabled' );

                                $processed_fe.text('').append( '<b>Not exported:</b> ' + processed_fe.join( ', ' ) );
                            }
                        }
                        else
                        {
                            $status.text( 'error at step ' + parseInt( step ) + ' / ' + parseInt( total_steps ) );

                            if( typeof response.error_text !== 'undefined' )
                            {
                                $status.text( 'error at step ' + parseInt( step ) + ' / ' + parseInt( total_steps ) + ': ' + response.error_text );
                            }
                        }
                    })
                    .fail( function( a, b, c ) {

                        console.log( a, b, c );
                    });
                }
            });
            </script>

        </div>

<?php 

        elseif( 'log' == $_GET['tab'] ):
?>

        <h3><?php _e( 'Logged data', 'sf4wp' ); ?></h3>
        
        <div class="gb-sf4wp-page gb-sf4wp-page-log">

            <p><?php _e( 'Here you can find the detailed description of all the SendFox API requests and responses.', 'sf4wp' ); ?></p>

<?php
            $log = dirname( __FILE__ ) . '/debug.log';

            // clear log

            if( !empty( $_GET['clear-log'] ) )
            {
                if( file_put_contents( $log, '' ) !== FALSE )
                {
                    echo '<p><b>' . __( 'Log was cleared successfully.', 'sf4wp' ) . '</b></p>';

                    echo '<script type="text/javascript">document.location.replace("admin.php?page=' . esc_attr( $_GET['page'] ) . '&tab=log");</script>';
                }
            }

            // output log

            $contents = __( 'Oops, log is empty...', 'sf4wp' );

            if( file_exists( $log ) )
            {
                $contents_raw = file_get_contents( $log );

                if( !empty( $contents_raw ) )
                {
                    $contents = $contents_raw;
                }
            }

            echo '<div><textarea>' . trim( $contents ) . '</textarea></div>';

            echo '<div><br /><a href="admin.php?page=' . esc_attr( $_GET['page'] ) . '&tab=log&clear-log=1" class="button button-secondary">' . __( 'Clear log', 'sf4wp' ) . '</a></div>';
?>

        </div>

<?php
        endif;

?>

</div>