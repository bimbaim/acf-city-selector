<?php
	/*
	 * Content for the settings page
	 */
	function acfedu_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		ACF_Faculty_Selector::acfedu_show_admin_notices();

		?>

		<div class="wrap acfedu">
            <div id="icon-options-general" class="icon32"><br /></div>

            <h1><?php esc_html_e( 'ACF Faculty Selector Settings', 'acf-faculty-selector' ); ?></h1>

			<?php echo ACF_Faculty_Selector::acfedu_admin_menu(); ?>

            <!-- left part -->
            <div class="admin_left">

                <form method="post" action="">
                    <input name="import_actions_nonce" value="<?php echo wp_create_nonce( 'import-actions-nonce' ); ?>" type="hidden" />
                    <h2><?php esc_html_e( 'Import countries', 'acf-faculty-selector' ); ?></h2>
                    <p><?php esc_html_e( "Here you can (re-)import all faculty for the individual countries listed below.", 'acf-faculty-selector' ); ?></p>
                    <p>
                        <label for="import_be" class="screen-reader-text"></label>
                        <input type="checkbox" name="import_be" id="import_be" value="1" /> <?php esc_html_e( 'Import all faculty in Belgium', 'acf-faculty-selector' ); ?> (1166)
                    </p>
                    <p>
                        <label for="import_lux" class="screen-reader-text"></label>
                        <input type="checkbox" name="import_lux" id="import_lux" value="1" /> <?php esc_html_e( 'Import all faculty in Luxembourg', 'acf-faculty-selector' ); ?> (12)
                    </p>
                    <p>
                        <label for="import_nl" class="screen-reader-text"></label>
                        <input type="checkbox" name="import_nl" id="import_nl" value="1" /> <?php esc_html_e( 'Import all faculty in Holland/The Netherlands', 'acf-faculty-selector' ); ?> (2449)
                    </p>
                    <input name="" type="submit" class="button button-primary" value="<?php esc_html_e( 'Import selected countries', 'acf-faculty-selector' ); ?>" />
                </form>

                <br /><hr />

                <form method="post" action="">
                    <input name="truncate_table_nonce" value="<?php echo wp_create_nonce( 'truncate-table-nonce' ); ?>" type="hidden" />
                    <h2><?php esc_html_e( 'Clear the database', 'acf-faculty-selector' ); ?></h2>
                    <p><?php esc_html_e( "By selecting this option, you will remove all faculty, which are present in the database. This is handy if you don't need the preset faculty or you want a fresh start.", 'acf-faculty-selector' ); ?></p>
                    <p>
                        <label for="delete_faculty" class="screen-reader-text"></label>
                        <input type="checkbox" name="delete_faculty" id="delete_faculty" value="1" /> <?php esc_html_e( 'Delete all faculty from the database', 'acf-faculty-selector' ); ?>
                    </p>
                    <input name="" type="submit" class="button button-primary"  onclick="return confirm( 'Are you sure you want to delete all faculty ?' )" value="<?php esc_html_e( 'Nuke \'em', 'acf-faculty-selector' ); ?>" />
                </form>

                <br /><hr />

                <form method="post" action="">
                    <input name="preserve_settings_nonce" value="<?php echo wp_create_nonce( 'preserve-settings-nonce' ); ?>" type="hidden" />
                    <h2><?php esc_html_e( 'Save data', 'acf-faculty-selector' ); ?></h2>
                    <p><?php esc_html_e( "When the plugin is deleted, all settings and faculty are deleted as well. Select this option to preserve this data upon deletion.", 'acf-faculty-selector' ); ?></p>
                    <?php $checked = get_option( 'acfedu_preserve_settings' ) ? ' checked="checked"' : false; ?>
                    <p>
                        <span class="acfedu_input">
                            <label for="preserve_settings" class="screen-reader-text"></label>
                            <input type="checkbox" name="preserve_settings" id="preserve_settings" value="1" <?php echo $checked; ?>/> <?php esc_html_e( 'Preserve settings on plugin deletion', 'acf-faculty-selector' ); ?>
                        </span>
                    </p>
                    <input name="" type="submit" class="button button-primary" value="<?php esc_html_e( 'Save settings', 'acf-faculty-selector' ); ?>" />
                </form>

            </div><!-- end .admin_left -->

			<?php include( 'admin-right.php' ); ?>

        </div><!-- end .wrap -->
		<?php
	}

