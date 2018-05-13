INSERT INTO <?php echo $wpdb->prefix; ?>faculty ( faculty_name, univ_code, univ_name, country_code, country, price ) VALUES ("Fakultas Ilmu Budaya", "UI", "Universitas Indonesia", "ID", "Indonesia", "15");
INSERT INTO <?php echo $wpdb->prefix; ?>faculty ( faculty_name, univ_code, univ_name, country_code, country, price ) VALUES ("Fakultas Farmasi", "UI", "Universitas Indonesia", "ID", "Indonesia", "15");

<?php $this->acfedu_errors()->add( 'success_faculty_imported_indonesia', esc_html__( 'Successfully imported faculty for Indonesia.', 'acf-faculty-selector' ) ); ?>
