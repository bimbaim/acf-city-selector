<?php

	/**
	 * Add help tabs
	 *
	 * @param $old_help  string
	 * @param $screen_id int
	 * @param $screen    object
	 */
	function acfedu_help_tabs( $old_help, $screen_id, $screen ) {

		// echo '<pre>'; var_dump($screen_id); echo '</pre>'; exit;

		$screen_array = array(
			'settings_page_acfedu-options',
			'settings_page_acfedu-settings',
		);
		if ( ! in_array( $screen_id, $screen_array ) ) {
			return false;
		}

		if ( 'settings_page_acfedu-options' == $screen_id ) {
			$screen->add_help_tab( array(
				'id'      => 'import-file',
				'title'   => esc_html__( 'Import CSV from file', 'acf-faculty-selector' ),
				'content' =>
					'<h5>Import CSV from file</h5>
					<p>' . esc_html__( 'On this page you can import a CSV file which contains faculty to import.', 'acf-faculty-selector' ) . '</p>
					<p>' . esc_html__( 'You can only upload *.csv files.', 'acf-faculty-selector' ) . '</p>
					<p>' . esc_html__( 'The required order is "Faculty,Univ code,Univ,Country code,Country".', 'acf-faculty-selector' ) . '</p>
					<table class="" cellpadding="0" cellspacing="0">
					<thead>
					<tr>
					<th>' . esc_html__( 'Field', 'acf-faculty-selector' ) . '</th>
					<th>' . esc_html__( 'What to enter', 'acf-faculty-selector' ) . '</th>
					<th>' . esc_html__( 'Note', 'acf-faculty-selector' ) . '</th>
					</tr>					
					</thead>
					<tbody>
					<tr>
					<td>' . esc_html__( 'Faculty', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'full name', 'acf-faculty-selector' ) . '</td>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Univ code', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'univ abbreviation', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'exactly 2 characters', 'acf-faculty-selector' ) . '</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Univ', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'full state name', 'acf-faculty-selector' ) . '</td>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Country code', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'country abbreviation', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'exactly 2 characters', 'acf-faculty-selector' ) . '</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Country', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'full country name', 'acf-faculty-selector' ) . '</td>
					<td>&nbsp;</td>
					</tr>
					</tbody>
					</table>'
			) );

			$screen->add_help_tab( array(
				'id'      => 'import-raw',
				'title'   => esc_html__( 'Import raw CSV data', 'acf-faculty-selector' ),
				'content' =>
					'<h5>Import cities through CSV data</h5>
					<p>' . esc_html__( 'On this page you can import faculty. You can select faculty from The Netherlands, Belgium and Luxembourg which come included in the plugin.', 'acf-faculty-selector' ) . '</p>
					<p>' . esc_html__( 'You can also import raw csv data, but this has to be formatted (and ordered) in a certain way, otherwise it won\'t work.', 'acf-faculty-selector' ) . '</p>
					<p>' . esc_html__( 'The required order is "Faculty,Univ code,Univ,Country code,Country".', 'acf-faculty-selector' ) . '</p>
					<table class="" cellpadding="0" cellspacing="0">
					<thead>
					<tr>
					<th>' . esc_html__( 'Field', 'acf-faculty-selector' ) . '</th>
					<th>' . esc_html__( 'What to enter', 'acf-faculty-selector' ) . '</th>
					<th>' . esc_html__( 'Note', 'acf-faculty-selector' ) . '</th>
					</tr>					
					</thead>
					<tbody>
					<tr>
					<td>' . esc_html__( 'Faculty', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'full name', 'acf-faculty-selector' ) . '</td>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Univ code', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'univ abbreviation', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'exactly 2 characters', 'acf-faculty-selector' ) . '</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Univ', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'full state name', 'acf-faculty-selector' ) . '</td>
					<td>&nbsp;</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Country code', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'country abbreviation', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'exactly 2 characters', 'acf-faculty-selector' ) . '</td>
					</tr>
					<tr>
					<td>' . esc_html__( 'Country', 'acf-faculty-selector' ) . '</td>
					<td>' . esc_html__( 'full country name', 'acf-faculty-selector' ) . '</td>
					<td>&nbsp;</td>
					</tr>
					</tbody>
					</table>'
			) );

		}

		if ( 'settings_page_acfedu-settings' == $screen_id ) {
			$screen->add_help_tab( array(
				'id'      => 'import-file',
				'title'   => esc_html__( 'Import preset countries', 'acf-faculty-selector' ),
				'content' => '<h5>Import preset countries</h5>
					<p>' . esc_html__( 'On this page you can (re-)import the countries which come with the plugin when it\'s installed; Netherlands, Belgium and Luxembourg (if needed).', 'acf-faculty-selector' ) . '</p>
					<h5>Clear database</h5>
					<p>' . esc_html__( 'There\'s also an option to delete all faculty, which can be helpful if you activate the plugin a second time. Right now all faculty are imported again if you activate the plugin. This will also happen if you still have all the faculty in the database from a previous activation.', 'acf-faculty-selector' ) . '</p>
					<h5>Preserve settings</h5>
					<p>' . esc_html__( 'If you select preserve settings, all values will not be deleted from the database when the plugin is deleted.', 'acf-faculty-selector' ) . '</p>'
			) );
		}


		get_current_screen()->set_help_sidebar(
			'<p><strong>' . esc_html__( 'Author\'s website', 'acf-faculty-selector' ) . '</strong></p>
			<p><a href="http://www.kvconsultant.com?utm_source=' . $_SERVER[ 'SERVER_NAME' ] . '&utm_medium=plugin_admin&utm_campaign=free_promo">kvconsultant.com</a></p>'
		);

		return $old_help;
	}
	add_filter( 'contextual_help', 'acfedu_help_tabs', 5, 3 );
