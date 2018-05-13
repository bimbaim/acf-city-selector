<?php
	/*
	 * Content for the settings page
	 */
	function acfedu_pro() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		ACF_City_Selector::acfedu_show_admin_notices();

    ?>

		<div class="wrap acfedu">
            <div id="icon-options-general" class="icon32"><br /></div>

            <h1><?php esc_html_e( 'ACF City Selector Settings', 'acf-faculty-selector' ); ?></h1>

			<?php echo ACF_Faculty_Selector::acfedu_admin_menu(); ?>

            <!-- left part -->
            <div class="admin_left">

                <h2><?php esc_html_e( 'Go Pro', 'acf-faculty-selector' ); ?></h2>
                <p><?php esc_html_e( "Default the plugin comes with 3 languages included, namely the Benelux; Belgium, Netherlands, Luxembourg, but you might want to add more countries to choose from. And now you can !!!", 'acf-faculty-selector' ); ?></p>
                <p><?php esc_html_e( "We have more countries available. You can either buy a seperate country packages or get a Pro subscription and get every new update when we'll make a new country available.", 'acf-faculty-selector' ); ?></p>

                <hr />

                <h2><?php esc_html_e( 'Pro subscription', 'acf-faculty-selector' ); ?></h2>
                <p><?php esc_html_e( "Buy once and get all coming countries for free !!! One price for all packages.", 'acf-faculty-selector' ); ?></p>
                <p><?php esc_html_e( "Order now !!!", 'acf-faculty-selector' ); ?></p>

                <hr />

                <h2><?php esc_html_e( 'Country packages', 'acf-faculty-selector' ); ?></h2>

                <table cellpadding="0" cellspacing="0">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( 'Country', 'acf-faculty-selector' ); ?></th>
                        <th># <?php esc_html_e( 'univ', 'acf-faculty-selector' ); ?></th>
                        <th># <?php esc_html_e( 'faculty', 'acf-faculty-selector' ); ?></th>
                        <th><?php esc_html_e( 'Price', 'acf-faculty-selector' ); ?></th>
                        <th><?php esc_html_e( 'Order', 'acf-faculty-selector' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>[country]</td>
                        <td>#</td>
                        <td>#</td>
                        <td>$ xx,-</td>
                        <td><a href="">link</a></td>
                    </tr>
                    </tbody>
                </table>


            </div><!-- end .admin_left -->

            <?php include( 'admin-right.php' ); ?>

        </div><!-- end .wrap -->
		<?php
	}

