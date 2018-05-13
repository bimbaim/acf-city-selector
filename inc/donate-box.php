<?php
	if ( ! function_exists( 'acfedu_donate_meta_box' ) ) {
		function acfrdu_donate_meta_box() {
			if ( apply_filters( 'remove_acfcs_donate_nag', false ) ) {
				return;
			}

			$id       = 'donate-acf-cs';
			$title    = '<a style="text-decoration: none; font-size: 1em;" href="https://github.com/beee4life" target="_blank">' . sprintf( esc_html__( '%s says "Thank you"', 'acf-city-selector' ), 'Beee' ) . '</a>';
			$callback = 'show_donate_meta_box';
			$screens  = array();
			$context  = 'side';
			$priority = 'low';
			add_meta_box( $id, $title, $callback, $screens, $context, $priority );

		} // end function donate_meta_box
		add_action( 'add_meta_boxes', 'acfedu_donate_meta_box' );

		function show_donate_meta_box() {
			echo '<p style="margin-bottom: 0;">' . sprintf( __( 'Thank you for installing the \'faculty Selector\' plugin. I hope you enjoy it. Please <a href="%s" target="_blank">consider a donation</a> if you do, so I can continue to improve it even more.', 'acf-faculty-selector' ), esc_url( 'paypal.me/ibrahimquraisy' ) ) . '</p>';
		}
	} // end if !function_exists

