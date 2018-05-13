<?php
	/*
	Plugin Name:    ACF Faculty Selector
	Description:    An extension for ACF which select Faculty based  on University and Country
	Version:        0.1
	Author:         KVDC
	Author URI:     http://kvconsultant.com
	Text Domain:    kvdc-education-verification
	License:        GPLv2 or later
	License URI:    https://www.gnu.org/licenses/gpl.html
	*/

	// exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	// check if class already exists
	if ( ! class_exists( 'ACF_Faculty_Selector' ) ) :

		class ACF_Faculty_Selector {

			/*
			 * __construct
			 *
			 * This function will setup the class functionality
			 *
			 * @param   n/a
			 * @return  n/a
			 */
			public function __construct() {

				$this->settings = array(
					'version' => '0.3',
					'url'     => plugin_dir_url( __FILE__ ),
					'path'    => plugin_dir_path( __FILE__ )
				);

				// set text domain
				load_plugin_textdomain( 'acf-faculty-selector', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

				register_activation_hook( __FILE__,    array( $this, 'acfedu' ) );
				register_deactivation_hook( __FILE__,  array( $this, 'acfedu_plugin_deactivation' ) );

				// actions
				add_action( 'acf/include_field_types',      array( $this, 'acfedu_include_field_types' ) );    // v5
				add_action( 'acf/register_fields',          array( $this, 'acfedu_include_field_types' ) );    // v4
				add_action( 'admin_enqueue_scripts',        array( $this, 'acfedu_add_css' ) );
				add_action( 'admin_menu',                   array( $this, 'acfedu_add_admin_pages' ) );
				add_action( 'admin_init',                   array( $this, 'acfedu_errors' ) );
				// add_action( 'save_post',                    array( $this, 'acfedu_before_save' ), 10, 3 );

				// always load, move to $this->
				add_action( 'init',                         array( $this, 'acfedu_upload_csv_file' ) );
				add_action( 'init',                         array( $this, 'acfedu_do_something_with_file' ) );
				add_action( 'init',                         array( $this, 'acfedu_import_raw_data' ) );
				add_action( 'init',                         array( $this, 'acfedu_import_preset_countries' ) );
				add_action( 'init',                         array( $this, 'acfedu_preserve_settings' ) );
				add_action( 'init',                         array( $this, 'acfedu_truncate_table' ) );

				// filters
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ),  array( $this, 'acfedu_settings_link' ) );

				// always load
				$this->acfedu_admin_menu();
				$this->acfedu_load_admin_pages();
				$this->acfedu_check_uploads_folder();
				$this->acfedu_check_table();

				include( 'inc/donate-box.php' );
				include( 'inc/help-tabs.php' );
				include( 'inc/country-field.php' );
				include( 'inc/verify-csv-data.php' );
			}


			/*
			 * Do stuff upon plugin activation
			 */
			public function acfedu() {
				if ( false == get_option( 'acfedu_preserve_settings' ) ) {
					$this->acfedu_create_fill_db();
				}
			}


			/*
			 * Do stuff upon plugin activation
			 */
			public function acfedu_plugin_deactivation() {
			    // nothing yet
                // @TODO: delete any settings
			}


			/*
			 * Prepare database upon plugin activation
			 */
			public function acfedu_create_fill_db() {
				$this->acfedu_check_table();
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				ob_start();
				global $wpdb;
                //require_once( 'lib/import_nl.php' );
                //require_once( 'lib/import_be.php' );
                //require_once( 'lib/import_lux.php' );
                require_once( 'lib/import_id.php' );
				$sql = ob_get_clean();
				dbDelta( $sql );
			}


			/*
			 * Check if table exists
			 */
			public function acfedu_check_table() {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				ob_start();
				global $wpdb;
				?>
				CREATE TABLE <?php echo $wpdb->prefix; ?>faculty (
					id int(6) unsigned NOT NULL auto_increment,
					faculty_name varchar(50) NULL,
					univ_code varchar(10) NULL,
					univ_name varchar(50) NULL,
					country_code varchar(2) NULL,
					country varchar(50) NULL,
					price decimal(10) NULL,
					PRIMARY KEY  (id)
				)
                COLLATE <?php echo $wpdb->collate; ?>;
				<?php
				$sql = ob_get_clean();
				dbDelta( $sql );

			}


			/**
             * Force update_post_meta in v4 because values are not saved (probably not needed anymore)
             *
			 * @param $post_id
			 * @param $post
			 * @param $update
			 */
			public function acfedu_before_save( $post_id, $post, $update ) {

				// bail early if no ACF data
				if ( ! isset( $_POST['acf'] ) ) {
					return;
				}

				// only run with v4
				if ( 5 > get_option( 'acf_version' ) ) {

					$field_name = '';
					$fields     = $_POST['acf'];
					$new_value  = '';
					if ( is_array( $fields ) && count( $fields ) > 0 ) {
						foreach( $fields as $key => $value ) {
							$field = get_field_object( $key );
							if ( isset( $field['type' ] ) && $field['type'] == 'acf_faculty_selector' ) {
								$field_name = $field['name'];
								$new_value  = $value;
								break;
							}
						}
					}

					// store data in $field_name
					update_post_meta( $post_id, $field_name, $new_value );
				}
			}


			/*
			 * Check if (upload) folder exists
			 */
			public function acfedu_check_uploads_folder() {

				$target_folder = wp_upload_dir()['basedir'] . '/acfedu/';
				if ( ! file_exists( $target_folder ) ) {
					mkdir( $target_folder, 0755 );
				}
			}


			/*
			 * Load admin pages
			 */
			public function acfedu_load_admin_pages() {
				include( 'inc/dashboard-page.php' );
				include( 'inc/settings-page.php' );
				include( 'inc/pro-page.php' );
			}


			/*
			 * Upload CSV file
			 */
			public function acfedu_upload_csv_file() {
				if ( isset( $_POST["upload_csv_nonce"] ) ) {
					if ( ! wp_verify_nonce( $_POST["upload_csv_nonce"], 'upload-csv-nonce' ) ) {
						$this->acfedu_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'acf-faculty-selector' ) );

						return;
					} else {

						$this->acfedu_check_uploads_folder();
						$target_dir  = wp_upload_dir()['basedir'] . '/acfedu/';
						$target_file = $target_dir . basename( $_FILES['csv_upload']['name'] );

						if ( move_uploaded_file( $_FILES['csv_upload']['tmp_name'], $target_file ) ) {

							// file uploaded succeeded
							$this->acfedu_errors()->add( 'success_file_uploaded', sprintf( esc_html__( "File '%s' is successfully uploaded and now shows under 'Select files to import'", 'acf-faculty-selector' ), $_FILES['csv_upload']['name'] ) );

							return;

						} else {

							// file upload failed
							$this->acfedu_errors()->add( 'error_file_uploaded', esc_html__( 'Upload failed. Please try again.', 'acf-faculty-selector' ) );

							return;
						}
					}
				}
			}


			/**
			 * Read uploaded file for verification or import
			 * Delete file is also included in this function
			 */
			public function acfedu_do_something_with_file() {

				if ( isset( $_POST["select_file_nonce"] ) ) {
					if ( ! wp_verify_nonce( $_POST["select_file_nonce"], 'select-file-nonce' ) ) {
						$this->acfedu_errors()->add( 'error_nonce_no_match', esc_html__( 'Something went wrong. Please try again.', 'acf-faculty-selector' ) );

						return;
					} else {

						if ( ! isset( $_POST['file_name'] ) ) {
							$this->acfedu_errors()->add( 'error_no_file_selected', esc_html__( "You didn't select a file.", 'acf-faculty-selector' ) );

							return;
						}

						$file_name = $_POST['file_name'];
						$import    = ! empty( $_POST['import'] ) ? $_POST['import'] : false;
						$remove    = ! empty( $_POST['remove'] ) ? $_POST['remove'] : false;
						$verify    = ! empty( $_POST['verify'] ) ? $_POST['verify'] : false;

						if ( ! empty( $verify ) ) {

							$read_data     = acfedu_read_file_only( $file_name[0] );
							$verified_data = acfedu_verify_csv_data( $read_data );

							if ( false != $verified_data ) {
								$this->acfedu_errors()->add( 'success_no_errors_in_csv', esc_html__( 'Congratulations, there appear to be no errors in your CSV.', 'acf-faculty-selector' ) );

								do_action( 'acfedu_after_success_verify' );

								return;
							}

						} elseif ( ! empty( $import ) ) {

							// import data
							$read_data     = acfedu_read_file_only( $file_name[0] );
							$verified_data = acfedu_verify_csv_data( $read_data );
							if ( false != $verified_data ) {
								$line_number = 0;
								foreach ( $verified_data as $line ) {
									$line_number ++;

									$faculty         = $line[0];
									$univ_abbr   = $line[1];
									$univ        = $line[2];
									$country_abbr = $line[3];
									$country      = $line[4];
									$price = $line[5];

									$faculty_row = array(
										'faculty_name'    => $faculty,
										'univ_code'   => $univ_abbr,
										'univ_name'   => $univ,
										'country_code' => $country_abbr,
										'country'      => $country,
										'price' => $price,
									);

									global $wpdb;
									$wpdb->insert( $wpdb->prefix . 'faculty', $faculty_row );

								}

								$this->acfedu_errors()->add( 'success_lines_imported', sprintf( esc_html__( 'Congratulations. You have successfully imported %d faculty.', 'acf-faculty-selector' ), $line_number ) );

								do_action( 'acfedu_after_success_import' );

								return;
							}

						} elseif ( ! empty( $remove ) ) {

							if ( isset( $_POST['file_name'] ) ) {
								foreach ( $_POST['file_name'] as $file_name ) {
									// delete file
									unlink( wp_upload_dir()['basedir'] . '/acfedu/' . $file_name );
								}
								if ( count( $_POST['file_name'] ) == 1 ) {
									$this->acfedu_errors()->add( 'success_file_deleted', sprintf( esc_html__( 'File "%s" successfully deleted.', 'acf-faculty-selector' ), $file_name ) );

									return;
								} else {
									$this->acfedu_errors()->add( 'success_files_deleted', esc_html__( 'Files successfully deleted.', 'acf-faculty-selector' ) );

									return;
								}
							}
						}
					}
				}
			}


			/*
			 * Import raw csv data
			 */
			public function acfedu_import_raw_data() {
				if ( isset( $_POST["import_raw_nonce"] ) ) {
					if ( ! wp_verify_nonce( $_POST["import_raw_nonce"], 'import-raw-nonce' ) ) {
						$this->acfedu_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'acf-faculty-selector' ) );

						return;
					} else {

						if ( isset( $_POST['verify'] ) ) {
							$verify_data = acfedu_verify_csv_data( $_POST['raw_csv_import'] );
							if ( false != $verify_data ) {
								$this->acfedu_errors()->add( 'success_csv_valid', esc_html__( 'Congratulations, your CSV data seems valid.', 'acf-faculty-selector' ) );
							}

						} elseif ( isset( $_POST['import'] ) ) {

							$verified_data = acfedu_verify_csv_data( $_POST['raw_csv_import'] );
							if ( false != $verified_data ) {
								// import data
								global $wpdb;
								$line_number = 0;
								foreach ( $verified_data as $line ) {
									$line_number ++;

									$faculty         = $line[0];
									$univ_abbr   = $line[1];
									$univ        = $line[2];
									$country_abbr = $line[3];
									$country      = $line[4];
									$price = $line[5];

									$faculty_row = array(
										'faculty_name'    => $faculty,
										'univ_code'   => $univ_abbr,
										'univ_name'   => $univ,
										'country_code' => $country_abbr,
										'country'      => $country,
										'price' => $price,
									);

									global $wpdb;
									$wpdb->insert( $wpdb->prefix . 'faculty', $faculty_row );

								}
								$this->acfedu_errors()->add( 'success_faculty_imported', sprintf( _n( 'Congratulations, you imported %d faculty.', 'Congratulations, you imported %d faculty.', $line_number, 'acf-faculty-selector' ), $line_number ) );

								do_action( 'acfedu_after_success_import_raw' );

								return;

							}
						}
					}
				}
			}


			/*
			 * Import preset countries
			 */
			public function acfedu_import_preset_countries() {
				if ( isset( $_POST["import_actions_nonce"] ) ) {
					if ( ! wp_verify_nonce( $_POST["import_actions_nonce"], 'import-actions-nonce' ) ) {
						$this->acfedu_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'acf-faculty-selector' ) );

						return;
					} else {

						if ( isset( $_POST['import_nl'] ) || isset( $_POST['import_be'] ) || isset( $_POST['import_lux'] ) ) {
							require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
							ob_start();
							global $wpdb;
							if ( isset( $_POST['import_be'] ) && 1 == $_POST["import_be"] ) {
								require_once( 'lib/import_be.php' );
								do_action( 'acfedu_after_success_import_be' );
							}
							if ( isset( $_POST['import_lux'] ) && 1 == $_POST["import_lux"] ) {
								require_once( 'lib/import_lux.php' );
								do_action( 'acfedu_after_success_import_lu' );
							}
							if ( isset( $_POST['import_nl'] ) && 1 == $_POST["import_nl"] ) {
								require_once( 'lib/import_nl.php' );
								do_action( 'acfedu_after_success_import_nl' );
							}
							$sql = ob_get_clean();
							dbDelta( $sql );
						}
					}
				}
			}


			/*
			 * Truncate faculty table
			 */
			public function acfedu_truncate_table() {
				if ( isset( $_POST["truncate_table_nonce"] ) ) {
					if ( ! wp_verify_nonce( $_POST["truncate_table_nonce"], 'truncate-table-nonce' ) ) {
						$this->acfedu_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'acf-faculty-selector' ) );

						return;
					} else {

						if ( isset( $_POST['delete_faculty'] ) ) {
							if ( isset( $_POST['delete_faculty'] ) && 1 == $_POST["delete_faculty"] ) {

								global $wpdb;
								$prefix = $wpdb->get_blog_prefix();
								$wpdb->query( 'TRUNCATE TABLE ' . $prefix . 'faculty' );
								$this->acfedu_errors()->add( 'success_table_truncated', esc_html__( 'All faculty are deleted.', 'acf-faculty-selector' ) );

								do_action( 'acfedu_after_success_nuke' );

							}
						}
					}
				}
			}


			/*
			 * Preserve settings
			 */
			public function acfedu_preserve_settings() {
				if ( isset( $_POST["preserve_settings_nonce"] ) ) {
					if ( ! wp_verify_nonce( $_POST["preserve_settings_nonce"], 'preserve-settings-nonce' ) ) {
						$this->acfedu_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'acf-faculty-selector' ) );

						return;
					} else {

						if ( isset( $_POST['preserve_settings'] ) ) {
							update_option( 'acfedu_preserve_settings', 1, true );
						} else {
							delete_option( 'acfedu_preserve_settings' );
						}
						$this->acfedu_errors()->add( 'success_settings_saved', esc_html__( 'Settings saved', 'acf-faculty-selector' ) );
					}
				}
			}


			/**
			 * Error function
			 *
			 * @return WP_Error
			 */
			public static function acfedu_errors() {
				static $wp_error; // Will hold global variable safely

				return isset( $wp_error ) ? $wp_error : ( $wp_error = new WP_Error( null, null, null ) );
			}

			/**
			 * Displays error messages from form submissions
			 */
			public static function acfedu_show_admin_notices() {
				if ( $codes = ACF_Faculty_Selector::acfedu_errors()->get_error_codes() ) {
					if ( is_wp_error( ACF_Faculty_Selector::acfedu_errors() ) ) {

						// Loop error codes and display errors
						$error      = false;
						$span_class = false;
						$prefix     = false;
						foreach ( $codes as $code ) {
							if ( strpos( $code, 'success' ) !== false ) {
								$span_class = 'notice-success ';
								$prefix     = false;
							} elseif ( strpos( $code, 'error' ) !== false ) {
								$span_class = 'notice-error ';
								$prefix     = esc_html__( 'Warning', 'action-logger' );
							} elseif ( strpos( $code, 'info' ) !== false ) {
								$span_class = 'notice-info ';
								$prefix     = false;
							} else {
								$error      = true;
								$span_class = 'notice-error ';
								$prefix     = esc_html__( 'Error', 'action-logger' );
							}
						}
						echo '<div class="error notice ' . $span_class . 'is-dismissible">';
						foreach ( $codes as $code ) {
							$message = ACF_Faculty_Selector::acfedu_errors()->get_error_message( $code );
							echo '<div class="">';
							if ( true == $prefix ) {
								echo '<strong>' . $prefix . ':</strong> ';
							}
							echo $message;
							echo '</div>';
							echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' . esc_html__( 'Dismiss this notice', 'action-logger' ) . '</span></button>';
						}
						echo '</div>';
					}
				}
			}


			/*
			 * include_field_types
			 *
			 * This function will include the field type class
			 *
			 * @type    function
			 * @param   $version (int) major ACF version. Defaults to false
			 * @return  n/a
			 */
			public function acfedu_include_field_types( $version = false ) {

				if ( ! $version ) {
					$version = 4;
				}

				// include
				include_once( 'fields/acf-faculty_selector-v' . $version . '.php' );
			}


			/*
			 * Add settings link on plugin page
			 */
			public function acfedu_settings_link( $links ) {
				$settings_link = '<a href="options-general.php?page=acfedu-options">' . esc_html__( 'Settings', 'acf-faculty-selector' ) . '</a>';
				array_unshift( $links, $settings_link );

				return $links;
			}


			/*
			 * Admin menu
			 */
			public static function acfedu_admin_menu() {
				$gopro = ( defined( 'ENV' ) && ENV == 'dev' ) ? ' | <a href="' . site_url() . '/wp-admin/options-general.php?page=acfedu-pro">' . esc_html__( 'Go Pro', 'acf-faculty-selector' ) . '</a>' : false;

				return '<p class="acfedu-admin-menu"><a href="' . site_url() . '/wp-admin/options-general.php?page=acfedu-options">' . esc_html__( 'Dashboard', 'acf-faculty-selector' ) . '</a> | <a href="' . site_url() . '/wp-admin/options-general.php?page=acfedu-settings">' . esc_html__( 'Settings', 'acf-faculty-selector' ) . '</a>' . $gopro . '</p>';
			}


			/*
			 * Adds admin pages
			 */
			public function acfedu_add_admin_pages() {
				add_options_page( 'ACF Faculty Selector', 'Faculty Selector', 'manage_options', 'acfedu-options', 'acfedu_options' );
				add_submenu_page( null, 'Settings', 'Settings', 'manage_options', 'acfedu-settings', 'acfedu_settings' );
				add_submenu_page( null, 'Pro', 'Pro', 'manage_options', 'acfedu-pro', 'acfedu_pro' );
			}


			/*
			 * Adds CSS on the admin side
			 */
			public function acfedu_add_css() {
				wp_enqueue_style( 'acf-faculty-selector', plugins_url( 'assets/css/acf-faculty-selector.css', __FILE__ ) );
			}
		}
		
		/*
		 * CUstom JS for get Price
		 * 
		 */
			add_action('admin_footer', 'my_admin_add_js');
			function my_admin_add_js() {
				echo '<script>window.onload = function($) {
			jQuery("#countryCode").change(function() {
			
				var selectedValue=jQuery(this).children(":selected").data("price");
				jQuery(".load-price").val(selectedValue);
			});
			}</script>';
			}
			

		// initialize
		new ACF_Faculty_Selector();


		// class_exists check
	endif;


