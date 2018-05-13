<?php

	/**
	 * Set admin-ajax.php on the front side (by default it is available only for Backend)
	 */
	function faculty_selector_ajaxurl() {
		?>
        <script type="text/javascript">
            var ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
        </script>
		<?php
	}
	add_action( 'wp_head', 'faculty_selector_ajaxurl' );

	/**
     * Fill the countries select
     *
	 * @param null $selectedCountry
	 * @param $field
	 *
	 * @return array
	 */
	function populate_country_select( $selectedCountry = null, $field ) {

		global $wpdb;
		$db = $wpdb->get_results( "
        SELECT * FROM " . $wpdb->prefix . "faculty
        group by country_code
        order by country ASC
    " );

		$items = array();
		if ( null == $selectedCountry ) {
			if ( $field['show_labels'] == 1 ) {
				$items[] = '-';
			} else {
				$items[] = esc_html__( 'Select a country', 'acf-faculty-selector' );
			}
		}
		foreach ( $db as $data ) {
			$items[ $data->country_code ] = $data->country;
			//$items[ $data->price ] = $data->country;
		}

		return $items;
	}

    /**
     * Create an array with states based on a Country Code.
     *
     * @param bool|string $country_code
     *
     * @return array
     */
    function get_univ( $country_code = false ) {

        if ( ! $country_code ) {
            $country_code = $country_code;
        }

        global $wpdb;

        $items = array();

        $sql = $wpdb->prepare( "
            SELECT * 
            FROM " . $wpdb->prefix . "faculty
            WHERE country_code = '%s'
            group by univ_code
            order by univ_name ASC",  $country_code
        );

        $db = $wpdb->get_results( $sql );

        foreach ( $db as $data ) {
            $items[ $data->univ_code ] = $data->univ_name;
        }
        return $items;
    }

	/*
     * Get states by related Country Code
     *
	 * @param bool $country_code
     * @return JSON Object
	 */
	function get_univ_call( $country_code = false ) {

		if ( ! $country_code ) {
			$country_code = $_POST['country_code'];
		}

		global $wpdb;

        $sql = $wpdb->prepare( "
            SELECT * 
            FROM " . $wpdb->prefix . "faculty
            WHERE country_code = '%s'
            group by univ_code
            order by univ_name ASC", $country_code
        );

        $db = $wpdb->get_results( $sql );

		$items                    = array();
		$items[0]['country_code'] = "";
		$items[0]['univ_code']   = "";
		// $items[0]['state_name']   = "";
		$items[0]['univ_name']   = esc_html__( 'Select a university', 'acf-faculty-selector' );
		$i                        = 1;

        // @TODO: check if $field['show_labels'] == 1
        // if == 1, $items[0]['state_name'] = '-';
        // __( 'Select a province/state', 'acf-city-selector' )

		foreach ( $db as $data ) {
			$items[ $i ]['country_code'] = $data->country_code;
			$items[ $i ]['univ_code']   = $data->univ_code;
			if ( $data->univ_name != 'N/A' ) {
				$items[ $i ]['univ_name'] = $data->univ_name;
			} else {
				$items[ $i ]['univ_name'] = $data->country;
			}
			$i++;
		}
		ob_clean();
		echo json_encode( $items );
		die();
	}

	/*
	 * Get faculty by related State Code or Country Code (IF State code == "00" or States == 'N/A')
	 *
	 * @return JSON Object
	 */
	function get_faculty_call() {

		if ( trim( $_POST['row_code'] ) ) {
			$codes        = explode( '-', $_POST['row_code'] );
			$country_code = $codes[0];
			$univ_code   = $codes[1];

			global $wpdb;

			if ( $univ_code == '00' ) {
				$db = $wpdb->get_results( "
                SELECT * 
                FROM " . $wpdb->prefix . "faculty
                WHERE country_code = '" . $country_code . "'
                order by faculty_name ASC
            " );
			} else {
				$db = $wpdb->get_results( "
                SELECT * 
                FROM " . $wpdb->prefix . "faculty
                WHERE univ_code = '" . $univ_code . "'
                AND country_code='" . $country_code . "'
                order by faculty_name ASC
            " );
			}
			$items                 = array();
			$items[0]['id']        = "";
			// $items[0]['city_name'] = "";
			$items[0]['faculty_name'] = esc_html__( 'Select a faculty', 'acf-faculty-selector' );
			$i                     = 1;

			foreach ( $db as $data ) {
				$items[ $i ]['id']        = $data->univ_code;
				$items[ $i ]['faculty_name'] = $data->faculty_name;
				$i ++;
			}
			ob_clean();
			echo json_encode( $items );
			die();
		}
	}
	
	add_action( 'wp_ajax_get_univ_call', 'get_univ_call' );
	add_action( 'wp_ajax_nopriv_get_univ_call', 'get_univ_call' );
	add_action( 'wp_ajax_get_faculty_call', 'get_faculty_call' );
	add_action( 'wp_ajax_nopriv_get_faculty_call', 'get_faculty_call' );
