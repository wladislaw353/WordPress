<?php
/**
 * Includes functions related to actions while in the admin area.
 *
 * - All AJAX related features
 * - Enqueueing of JS and CSS files
 * - Settings link on "Plugins" page
 * - Creation of local avatar image files
 * - Connecting accounts on the "Configure" tab
 * - Displaying admin notices
 * - Clearing caches
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function sb_instagram_admin_style() {
	wp_register_style( 'sb_instagram_admin_css', SBI_PLUGIN_URL . 'css/sb-instagram-admin.css', array(), SBIVER );
	wp_enqueue_style( 'sb_instagram_font_awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' );
	wp_enqueue_style( 'sb_instagram_admin_css' );
	wp_enqueue_style( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'sb_instagram_admin_style' );

function sb_instagram_admin_scripts() {
	if ( ! current_user_can( 'manage_instagram_feed_options' ) ) {
		return;
	}
	wp_enqueue_script( 'sb_instagram_admin_js', SBI_PLUGIN_URL . 'js/sb-instagram-admin-2-2.js', array(), SBIVER, true );
	wp_localize_script(
		'sb_instagram_admin_js',
		'sbiA',
		array(
			'ajax_url'  => admin_url( 'admin-ajax.php' ),
			'sbi_nonce' => wp_create_nonce( 'sbi_nonce' ),
		)
	);
	$strings = array(
		'addon_activate'                  => esc_html__( 'Activate', 'instagram-feed' ),
		'addon_activated'                 => esc_html__( 'Activated', 'instagram-feed' ),
		'addon_active'                    => esc_html__( 'Active', 'instagram-feed' ),
		'addon_deactivate'                => esc_html__( 'Deactivate', 'instagram-feed' ),
		'addon_inactive'                  => esc_html__( 'Inactive', 'instagram-feed' ),
		'addon_install'                   => esc_html__( 'Install Addon', 'instagram-feed' ),
		'addon_error'                     => esc_html__( 'Could not install addon. Please download from wpforms.com and install manually.', 'instagram-feed' ),
		'plugin_error'                    => esc_html__( 'Could not install a plugin. Please download from WordPress.org and install manually.', 'instagram-feed' ),
		'addon_search'                    => esc_html__( 'Searching Addons', 'instagram-feed' ),
		'ajax_url'                        => admin_url( 'admin-ajax.php' ),
		'cancel'                          => esc_html__( 'Cancel', 'instagram-feed' ),
		'close'                           => esc_html__( 'Close', 'instagram-feed' ),
		'nonce'                           => wp_create_nonce( 'sbi-admin' ),
		'almost_done'                     => esc_html__( 'Almost Done', 'instagram-feed' ),
		'oops'                            => esc_html__( 'Oops!', 'instagram-feed' ),
		'ok'                              => esc_html__( 'OK', 'instagram-feed' ),
		'plugin_install_activate_btn'     => esc_html__( 'Install and Activate', 'instagram-feed' ),
		'plugin_install_activate_confirm' => esc_html__( 'needs to be installed and activated to import its forms. Would you like us to install and activate it for you?', 'instagram-feed' ),
		'plugin_activate_btn'             => esc_html__( 'Activate', 'instagram-feed' ),
	);
	$strings = apply_filters( 'sbi_admin_strings', $strings );

	wp_localize_script(
		'sb_instagram_admin_js',
		'sbi_admin',
		$strings
	);

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'sb_instagram_admin_scripts' );

// Add a Settings link to the plugin on the Plugins page
$sbi_plugin_file = 'instagram-feed/instagram-feed.php';
add_filter( "plugin_action_links_$sbi_plugin_file", 'sbi_add_settings_link', 10, 2 );

//modify the link by unshifting the array
function sbi_add_settings_link( $links ) {
	$pro_link = '<a href="https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=plugins-page&utm_medium=upgrade-link" target="_blank" style="font-weight: bold; color: #1da867;">' . __( 'Try the Pro Demo', 'instagram-feed' ) . '</a>';

	$sbi_settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=sb-instagram-feed' ) ) . '">' . esc_html__( 'Settings', 'instagram-feed' ) . '</a>';
	array_unshift( $links, $pro_link, $sbi_settings_link );

	return $links;
}

/**
 * Called via ajax to automatically save access token and access token secret
 * retrieved with the big blue button
 */
function sbi_auto_save_tokens() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	$options              = sbi_get_database_settings();
	$new_access_token     = isset( $_POST['access_token'] ) ? sanitize_text_field( wp_unslash( $_POST['access_token'] ) ) : false;
	$split_token          = $new_access_token ? explode( '.', $new_access_token ) : array();
	$new_user_id          = isset( $split_token[0] ) ? $split_token[0] : '';
	$connected_accounts   = isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();
	$test_connection_data = sbi_account_data_for_token( $new_access_token );

	$connected_accounts[ $new_user_id ] = array(
		'access_token'    => sbi_get_parts( $new_access_token ),
		'user_id'         => $test_connection_data['id'],
		'username'        => $test_connection_data['username'],
		'is_valid'        => $test_connection_data['is_valid'],
		'last_checked'    => $test_connection_data['last_checked'],
		'profile_picture' => $test_connection_data['profile_picture'],
	);

	if ( ! $options['sb_instagram_disable_resize'] ) {
		if ( sbi_create_local_avatar( $test_connection_data['username'], $test_connection_data['profile_picture'] ) ) {
			$connected_accounts[ $new_user_id ]['local_avatar'] = true;
		}
	} else {
		$connected_accounts[ $new_user_id ]['local_avatar'] = false;
	}

	$options['connected_accounts'] = $connected_accounts;

	update_option( 'sb_instagram_settings', $options );

	wp_send_json_success( $connected_accounts[ $new_user_id ] );
}
add_action( 'wp_ajax_sbi_auto_save_tokens', 'sbi_auto_save_tokens' );

function sbi_delete_local_avatar( $username ) {
	$upload = wp_upload_dir();

	$image_files = glob( trailingslashit( $upload['basedir'] ) . trailingslashit( SBI_UPLOADS_NAME ) . $username . '.jpg' ); // get all matching images
	foreach ( $image_files as $file ) { // iterate files
		if ( is_file( $file ) ) {
			unlink( $file );
		}
	}
}

function sbi_connect_business_accounts() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	$raw_accounts = ! empty( $_POST['accounts'] ) ? json_decode( wp_unslash( $_POST['accounts'] ), true ) : array();
	$access_token = ! empty( $raw_accounts[0] ) ? sbi_sanitize_alphanumeric_and_equals( $raw_accounts[0]['access_token'] ) : '';
	if ( empty( $access_token ) ) {
		wp_send_json_success( 'No access token' );
	}

	$ids_to_connect = array();
	foreach ( $raw_accounts as $raw_account ) {
		$ids_to_connect[] = sbi_sanitize_instagram_ids( $raw_account['id'] );
	}

	$api_accounts = sbi_get_business_pages_list( $access_token );
	if ( empty( $api_accounts ) || is_wp_error( $api_accounts ) ) {
		wp_send_json_success( 'Could not connect' );
	}

	$return = array();
	foreach ( $api_accounts->data as $page => $page_data ) {
		if ( isset( $page_data->instagram_business_account ) && in_array( $page_data->instagram_business_account->id, $ids_to_connect, true ) ) {

			$instagram_business_id = sbi_sanitize_instagram_ids( $page_data->instagram_business_account->id );
			$page_access_token     = isset( $page_data->access_token ) ? sbi_sanitize_alphanumeric_and_equals( $page_data->access_token ) : '';

			//Make another request to get page info
			$instagram_account_url = 'https://graph.facebook.com/' . $instagram_business_id . '?fields=name,username,profile_picture_url&access_token=' . $access_token;

			$args = array(
				'timeout' => 20,
			);
			if ( version_compare( get_bloginfo( 'version' ), '3.7', '<' ) ) {
				$args['sslverify'] = false;
			}
			$result                 = wp_remote_get( $instagram_account_url, $args );
			$instagram_account_info = '{}';
			if ( ! is_wp_error( $result ) ) {
				$instagram_account_info = $result['body'];
				$instagram_account_data = json_decode( $instagram_account_info, true );

				$instagram_biz_img = ! empty( $instagram_account_data['profile_picture_url'] ) ? $instagram_account_data['profile_picture_url'] : false;
				$account           = array(
					'id'                  => $instagram_account_data['id'],
					'name'                => $instagram_account_data['name'],
					'username'            => $instagram_account_data['username'],
					'profile_picture_url' => $instagram_biz_img,
					'access_token'        => $access_token,
					'page_access_token'   => $page_access_token,
					'type'                => 'business',
				);

				$connector = new SBI_Account_Connector();

				$connector->add_account_data( $account );
				if ( $connector->update_stored_account() ) {
					$connector->after_update();

					$return[ $connector->get_id() ] = $connector->get_account_data();
				}
			}
		}
	}

	wp_send_json_success( $return );
}
add_action( 'wp_ajax_sbi_connect_business_accounts', 'sbi_connect_business_accounts' );

function sbi_auto_save_id() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	$options = get_option( 'sb_instagram_settings', array() );

	$options['sb_instagram_user_id'] = array( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );

	update_option( 'sb_instagram_settings', $options );

	wp_send_json_success();
}
add_action( 'wp_ajax_sbi_auto_save_id', 'sbi_auto_save_id' );

function sbi_formatted_error( $response ) {
	if ( isset( $response['error'] ) ) {
		$error  = '<p>' . esc_html( sprintf( __( 'API error %s:', 'instagram-feed' ), $response['error']['code'] ) ) . ' ' . esc_html( $response['error']['message'] ) . '</p>';
		$error .= '<p class="sbi-error-directions"><a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . esc_html__( 'Directions on how to resolve this issue', 'instagram-feed' ) . '</a></p>';

		return $error;
	} else {
		$message = '<p>' . esc_html( sprintf( __( 'Error connecting to %s.', 'instagram-feed' ), $response['url'] ) ) . '</p>';
		if ( isset( $response['response'] ) && isset( $response['response']->errors ) ) {
			foreach ( $response['response']->errors as $key => $item ) {
				'<p>' . $message .= ' ' . esc_html( $key ) . ' - ' . esc_html( $item[0] ) . '</p>';
			}
		}
		$message .= '<p class="sbi-error-directions"><a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . esc_html__( 'Directions on how to resolve this issue', 'instagram-feed' ) . '</a></p>';

		return $message;
	}
}

function sbi_test_token() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	$access_token = isset( $_POST['access_token'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['access_token'] ) ) ) : false;
	$account_id   = isset( $_POST['account_id'] ) ? sanitize_text_field( wp_unslash( $_POST['account_id'] ) ) : false;
	$return_json  = sbi_connect_new_account( $access_token, $account_id );

	if ( strpos( $return_json, '{' ) === 0 ) {
		$return_arr = json_decode( $return_json );
	} else {
		$return_arr = array( 'error_message' => $return_json );
	}

	wp_send_json_success( $return_arr );
}
add_action( 'wp_ajax_sbi_test_token', 'sbi_test_token' );

function sbi_delete_account() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}
	$account_id = isset( $_POST['account_id'] ) ? sanitize_text_field( wp_unslash( $_POST['account_id'] ) ) : false;
	sbi_do_account_delete( $account_id );

	wp_send_json_success();
}
add_action( 'wp_ajax_sbi_delete_account', 'sbi_delete_account' );

function sbi_account_data_for_token( $access_token ) {
	$return = array(
		'id'           => false,
		'username'     => false,
		'is_valid'     => false,
		'last_checked' => time(),
	);
	$url    = 'https://api.instagram.com/v1/users/self/?access_token=' . sbi_maybe_clean( $access_token );
	$args   = array(
		'timeout' => 20,
	);
	if ( version_compare( get_bloginfo( 'version' ), '3.7', '<' ) ) {
		$args['sslverify'] = false;
	}
	$result = wp_remote_get( $url, $args );

	if ( ! is_wp_error( $result ) ) {
		$data = json_decode( $result['body'] );
	} else {
		$data = array();
	}

	if ( isset( $data->data->id ) ) {
		$return['id']              = $data->data->id;
		$return['username']        = $data->data->username;
		$return['is_valid']        = true;
		$return['profile_picture'] = $data->data->profile_picture;

	} elseif ( isset( $data->error_type ) && $data->error_type === 'OAuthRateLimitException' ) {
		$return['error_message'] = 'This account\'s access token is currently over the rate limit. Try removing this access token from all feeds and wait an hour before reconnecting.';
	} else {
		$return = false;
	}

	$sbi_options                    = get_option( 'sb_instagram_settings', array() );
	$sbi_options['sb_instagram_at'] = '';
	update_option( 'sb_instagram_settings', $sbi_options );

	return $return;
}

function sbi_do_account_delete( $account_id ) {
	$options            = get_option( 'sb_instagram_settings', array() );
	$connected_accounts = isset( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();
	global $sb_instagram_posts_manager;
	$sb_instagram_posts_manager->reset_api_errors();

	$username = $connected_accounts[ $account_id ]['username'];
	$sb_instagram_posts_manager->add_action_log( 'Deleting account ' . $username );

	$num_times_used = 0;

	$new_con_accounts = array();
	foreach ( $connected_accounts as $connected_account ) {

		if ( $connected_account['username'] === $username ) {
			$num_times_used++;
		}

		if ( $connected_account['username'] !== '' && $account_id !== $connected_account['user_id'] && ! empty( $connected_account['user_id'] ) ) {
			$new_con_accounts[ $connected_account['user_id'] ] = $connected_account;
		}
	}

	if ( $num_times_used < 2 ) {
		sbi_delete_local_avatar( $username );
	}

	$options['connected_accounts'] = $new_con_accounts;

	update_option( 'sb_instagram_settings', $options );
}

function sbi_delete_platform_data_listener() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}
	sbi_delete_all_platform_data();

	wp_send_json_success();
}
add_action( 'wp_ajax_sbi_delete_platform_data', 'sbi_delete_platform_data_listener' );

function sbi_connect_new_account( $access_token, $account_id ) {
	$split_id   = explode( ' ', trim( $account_id ) );
	$account_id = preg_replace( '/[^A-Za-z0-9 ]/', '', $split_id[0] );
	if ( ! empty( $account_id ) ) {
		$split_token  = explode( ' ', trim( $access_token ) );
		$access_token = preg_replace( '/[^A-Za-z0-9 ]/', '', $split_token[0] );
	}

	$account = array(
		'access_token' => $access_token,
		'user_id'      => $account_id,
		'type'         => 'business',
	);

	if ( sbi_code_check( $access_token ) ) {
		$account['type'] = 'basic';
	}

	$connector = new SBI_Account_Connector();

	$response = $connector->fetch( $account );

	if ( isset( $response['access_token'] ) ) {
		$connector->add_account_data( $response );
		$connector->update_stored_account();
		$connector->after_update();
		return sbi_json_encode( $connector->get_account_data() );
	} else {
		return $response['error'];
	}
}

function sbi_no_js_connected_account_management() {
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$nonce = isset( $_POST['sb_instagram_settings_nonce'] ) ? $_POST['sb_instagram_settings_nonce'] : false;
	if ( ! wp_verify_nonce( $nonce, 'sb_instagram_saving_settings' ) ) {
		return;
	}
	if ( isset( $_POST['sb_manual_at'] ) ) {
		$access_token = isset( $_POST['sb_manual_at'] ) ? sbi_sanitize_alphanumeric_and_equals( $_POST['sb_manual_at'] ) : false;
		$account_id   = isset( $_POST['sb_manual_account_id'] ) ? sbi_sanitize_instagram_ids( $_POST['sb_manual_account_id'] ) : false;
		if ( ! $access_token || ! $account_id ) {
			return;
		}
		sbi_connect_new_account( $access_token, $account_id );
	} elseif ( isset( $_GET['disconnect'] ) && isset( $_GET['page'] ) && 'sb-instagram-feed' === $_GET['page'] ) {
		$account_id = sbi_sanitize_instagram_ids( $_GET['disconnect'] );
		sbi_do_account_delete( $account_id );
	}

}
add_action( 'admin_init', 'sbi_no_js_connected_account_management' );

add_action( 'admin_notices', 'sbi_admin_ssl_notice' );
function sbi_admin_ssl_notice() {
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'sb-instagram-feed' ), true ) ) {
		global $current_user;
		$user_id       = $current_user->ID;
		$was_dismissed = get_user_meta( $user_id, 'sbi_ignore_openssl', true );

		if ( ! $was_dismissed && ! sbi_doing_openssl() ) : ?>
			<div class="notice notice-warning is-dismissible sbi-admin-notice">
				<p><?php echo wp_kses_post( sprintf( __( 'Instagram Feed recommends Open SSL for encrypting Instagram platform data in your database. Contact your host or follow %1$sthese%2$s directions.', 'instagram-feed' ), '<a href="https://www.php.net/manual/en/openssl.installation.php" target="_blank">', '</a>' ) ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=sb-instagram-feed&openssldismiss=1' ) ); ?>"><?php esc_html_e( 'Dismiss', 'instagram-feed' ); ?></a></p>
			</div>
			<?php
		endif;
	}

}

add_action( 'admin_init', 'sbi_check_notice_dismiss' );
function sbi_check_notice_dismiss() {
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		return;
	}
	global $current_user;
	$user_id = $current_user->ID;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['openssldismiss'] ) ) {
		add_user_meta( $user_id, 'sbi_ignore_openssl', 'true', true );
	}
}

/**
 * @return array
 * @deprecated
 */
function sbi_get_connected_accounts_data() {
	$sbi_options = get_option( 'sb_instagram_settings', array() );

	return ! empty( $sbi_options['connected_accounts'] ) ? $sbi_options['connected_accounts'] : array();
}

function sbi_connect_basic_account( $new_account_details ) {
	$options            = sbi_get_database_settings();
	$connected_accounts = ! empty( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

	$accounts_to_save    = array();
	$old_account_user_id = '';
	$ids_to_save         = array();
	$user_ids            = is_array( $options['sb_instagram_user_id'] ) ? $options['sb_instagram_user_id'] : explode( ',', str_replace( ' ', '', $options['sb_instagram_user_id'] ) );

	$profile_picture = '';

	// do not connect as a basic display account if already connected as a business account
	if ( isset( $connected_accounts[ $new_account_details['user_id'] ]['type'] ) && 'business' === $connected_accounts[ $new_account_details['user_id'] ]['type'] ) {
		return $options;
	}

	foreach ( $connected_accounts as $account ) {
		$account_type = ! empty( $account['type'] ) ? $account['type'] : 'personal';
		if ( ( $account['username'] !== $new_account_details['username'] ) || 'business' === $account_type ) {
			$accounts_to_save[ $account['user_id'] ] = $account;
		} else {
			$old_account_user_id = $account['user_id'];
			$profile_picture     = ! empty( $account['profile_picture'] ) ? $account['profile_picture'] : '';
		}
	}

	foreach ( $user_ids as $id ) {
		if ( $id === $old_account_user_id ) {
			$ids_to_save[] = $new_account_details['user_id'];
		} else {
			$ids_to_save[] = $id;
		}
	}

	$accounts_to_save[ $new_account_details['user_id'] ] = array(
		'access_token'      => sbi_fixer( $new_account_details['access_token'] ),
		'user_id'           => $new_account_details['user_id'],
		'username'          => $new_account_details['username'],
		'is_valid'          => true,
		'last_checked'      => time(),
		'expires_timestamp' => $new_account_details['expires_timestamp'],
		'profile_picture'   => $profile_picture,
		'account_type'      => strtolower( $new_account_details['account_type'] ),
		'type'              => 'basic',
	);

	if ( ! empty( $old_account_user_id ) && $old_account_user_id !== $new_account_details['user_id'] ) {
		$accounts_to_save[ $new_account_details['user_id'] ]['old_user_id'] = $old_account_user_id;

		// get last saved header data
		$fuzzy_matches = sbi_fuzzy_matching_header_data( $old_account_user_id );
		if ( ! empty( $fuzzy_matches[0] ) ) {
			$header_data = sbi_find_matching_data_from_results( $fuzzy_matches, $old_account_user_id );
			$bio         = SB_Instagram_Parse::get_bio( $header_data );
			$accounts_to_save[ $new_account_details['user_id'] ]['bio'] = sbi_sanitize_emoji( $bio );
		}
	}

	if ( ! empty( $profile_picture ) && ! $options['sb_instagram_disable_resize'] ) {
		if ( sbi_create_local_avatar( $new_account_details['username'], $profile_picture ) ) {
			$accounts_to_save[ $new_account_details['user_id'] ]['local_avatar'] = true;
		}
	} else {
		$accounts_to_save[ $new_account_details['user_id'] ]['local_avatar'] = false;
	}

	delete_transient( SBI_USE_BACKUP_PREFIX . 'sbi_' . $new_account_details['user_id'] );
	$refresher = new SB_Instagram_Token_Refresher( $accounts_to_save[ $new_account_details['user_id'] ] );
	$refresher->attempt_token_refresh();

	if ( (int) $refresher->get_last_error_code() === 10 ) {
		$accounts_to_save[ $new_account_details['user_id'] ]['private'] = true;
	}

	$accounts_to_save[ $new_account_details['user_id'] ] = SB_Instagram_Connected_Account::encrypt_connected_account_tokens( $accounts_to_save[ $new_account_details['user_id'] ] );

	$options['connected_accounts']   = $accounts_to_save;
	$options['sb_instagram_user_id'] = $ids_to_save;

	update_option( 'sb_instagram_settings', $options );

	return $options;
}

function sbi_fuzzy_matching_header_data( $user_id ) {
	if ( empty( $user_id ) || strlen( $user_id ) < 4 ) {
		return array();
	}
	global $wpdb;

	$values = $wpdb->get_results(
		$wpdb->prepare(
			"
    SELECT option_value
    FROM $wpdb->options
    WHERE option_name LIKE (%s)
    LIMIT 10",
			'%!sbi\_header\_' . $user_id . '%'
		),
		ARRAY_A
	);

	return $values;
}

function sbi_find_matching_data_from_results( $results, $user_id ) {

	$match = array();
	$i     = 0;

	while ( empty( $match ) && isset( $results[ $i ] ) ) {
		if ( ! empty( $results[ $i ] ) ) {
			$header_data = json_decode( $results[ $i ]['option_value'], true );
			if ( isset( $header_data['id'] ) && (string) $header_data['id'] === (string) $user_id ) {
				$match = $header_data;
			}
		}
		$i++;
	}

	return $match;
}

function sbi_matches_existing_personal( $new_account_details ) {
	$options            = sbi_get_database_settings();
	$connected_accounts = ! empty( $options['connected_accounts'] ) ? $options['connected_accounts'] : array();

	$matches_one_account = false;
	foreach ( $connected_accounts as $account ) {
		$account_type = ! empty( $account['type'] ) ? $account['type'] : 'personal';
		if ( ( 'personal' === $account_type || 'basic' === $account_type ) && $account['username'] === $new_account_details['username'] ) {
			$matches_one_account = true;
		}
	}

	return $matches_one_account;
}

function sbi_business_account_request( $url, $account, $remove_access_token = true ) {
	$args = array(
		'timeout' => 20,
	);
	if ( version_compare( get_bloginfo( 'version' ), '3.7', '<' ) ) {
		$args['sslverify'] = false;
	}
	$result = wp_remote_get( $url, $args );

	if ( ! is_wp_error( $result ) ) {
		$response_no_at = $remove_access_token ? str_replace( sbi_maybe_clean( $account['access_token'] ), '{accesstoken}', $result['body'] ) : $result['body'];
		return $response_no_at;
	} else {
		return sbi_json_encode( $result );
	}
}

function sbi_after_connection() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	if ( isset( $_POST['access_token'] ) ) {
		$access_token = sbi_sanitize_alphanumeric_and_equals( wp_unslash( $_POST['access_token'] ) );
		$account_info = sbi_account_data_for_token( $access_token );

		wp_send_json_success( $account_info );
	}

	wp_send_json_error();
}
add_action( 'wp_ajax_sbi_after_connection', 'sbi_after_connection' );

function sbi_get_business_pages_list( $access_token ) {
	$url  = 'https://graph.facebook.com/me/accounts?fields=instagram_business_account,access_token&limit=500&access_token=' . $access_token;
	$args = array(
		'timeout' => 20,
	);
	if ( version_compare( get_bloginfo( 'version' ), '3.7', '<' ) ) {
		$args['sslverify'] = false;
	}
	$result = wp_remote_get( $url, $args );
	if ( ! is_wp_error( $result ) ) {
		$pages_data = $result['body'];
		$return     = json_decode( $pages_data );

	} else {
		$return = $result;
	}

	return $return;
}

function sbi_get_business_account_connection_modal( $sb_instagram_user_id ) {
	if ( ! isset( $_GET['sbi_con'] ) || ! wp_verify_nonce( $_GET['sbi_con'], 'sbi-connect' ) ) {
		return;
	}

	$access_token = ! empty( $_GET['sbi_access_token'] ) ? sbi_sanitize_alphanumeric_and_equals( sbi_maybe_clean( urldecode( ( $_GET['sbi_access_token'] ) ) ) ) : '';
	$api_response = sbi_get_business_pages_list( $access_token );
	$pages_data   = array();
	if ( ! is_wp_error( $api_response ) ) {
		$pages_data = $api_response;
	} else {
		$page_error = $api_response;
	}

	$pages_data_arr = $pages_data;
	$num_accounts   = 0;
	if ( isset( $pages_data_arr ) ) {
		$num_accounts = is_array( $pages_data_arr->data ) ? count( $pages_data_arr->data ) : 0;
	}
	?>
<div id="sbi_config_info" class="sb_list_businesses sbi_num_businesses_<?php echo esc_attr( $num_accounts ); ?>">
	<div class="sbi_config_modal">
		<div class="sbi-managed-pages">
			<?php
			if ( isset( $page_error ) && isset( $page_error->errors ) ) {
				foreach ( $page_error->errors as $key => $item ) {
					echo '<div class="sbi_user_id_error" style="display:block;"><strong>Connection Error: </strong>' . esc_html( $key ) . ': ' . esc_html( $item[0] ) . '</div>';
				}
			}
			?>
			<?php if ( empty( $pages_data_arr->data ) ) : ?>
				<div id="sbi-bus-account-error">
					<p style="margin-top: 5px;"><strong style="font-size: 16px">Couldn't find Business Profile</strong><br />
					Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">Facebook account</a> in this browser which is associated with your Instagram Business Profile.</p>
					<p><strong style="font-size: 16px">Why do I need a Business Profile?</strong><br />
					A Business Profile is only required if you are displaying a Hashtag feed. If you want to display a regular User feed then you can do this by selecting to connect a Personal account instead. For directions on how to convert your Personal profile into a Business profile please <a href="https://smashballoon.com/instagram-business-profiles" target="_blank">see here</a>.</p>
				</div>

			<?php elseif ( empty( $num_accounts ) ) : ?>
				<div id="sbi-bus-account-error">
					<p style="margin-top: 5px;"><strong style="font-size: 16px">Couldn't find Business Profile</strong><br />
					Uh oh. It looks like this Facebook account is not currently connected to an Instagram Business profile. Please check that you are logged into the <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer">Facebook account</a> in this browser which is associated with your Instagram Business Profile.</p>
					<p>If you are, in fact, logged-in to the correct account please make sure you have Instagram accounts connected with your Facebook account by following <a href="https://smashballoon.com/reconnecting-an-instagram-business-profile/" target="_blank">this FAQ</a></p>
				</div>
			<?php else : ?>
				<p class="sbi-managed-page-intro"><strong style="font-size: 16px;">Instagram Business profiles for this account</strong><br /><em style="color: #666;">Note: In order to display a Hashtag feed you first need to select a Business profile below.</em></p>
				<?php if ( $num_accounts > 1 ) : ?>
					<div class="sbi-managed-page-select-all"><input type="checkbox" id="sbi-select-all" class="sbi-select-all"><label for="sbi-select-all">Select All</label></div>
				<?php endif; ?>
				<div class="sbi-scrollable-accounts">

					<?php foreach ( $pages_data_arr->data as $page => $page_data ) : ?>

						<?php
						if ( isset( $page_data->instagram_business_account ) ) :

							$instagram_business_id = sbi_sanitize_instagram_ids( $page_data->instagram_business_account->id );

							$page_access_token = isset( $page_data->access_token ) ? sbi_sanitize_alphanumeric_and_equals( $page_data->access_token ) : '';

							//Make another request to get page info
							$instagram_account_url = 'https://graph.facebook.com/' . $instagram_business_id . '?fields=name,username,profile_picture_url&access_token=' . $access_token;

							$args = array(
								'timeout' => 20,
							);
							if ( version_compare( get_bloginfo( 'version' ), '3.7', '<' ) ) {
								$args['sslverify'] = false;
							}
							$result                 = wp_remote_get( $instagram_account_url, $args );
							$instagram_account_info = '{}';
							if ( ! is_wp_error( $result ) ) {
								$instagram_account_info = $result['body'];
							} else {
								$page_error = $result;
							}

							$instagram_account_data = json_decode( $instagram_account_info );

							$instagram_biz_img = ! empty( $instagram_account_data->profile_picture_url ) ? $instagram_account_data->profile_picture_url : false;
							$selected_class    = $instagram_business_id === $sb_instagram_user_id ? ' sbi-page-selected' : '';

							?>
							<?php
							if ( isset( $page_error ) && isset( $page_error->errors ) ) :
								foreach ( $page_error->errors as $key => $item ) {
									echo '<div class="sbi_user_id_error" style="display:block;"><strong>Connection Error: </strong>' . esc_html( $key ) . ': ' . esc_html( $item[0] ) . '</div>';
								}
						else :
							?>
							<div class="sbi-managed-page<?php echo esc_attr( $selected_class ); ?>" data-page-token="<?php echo esc_attr( $page_access_token ); ?>" data-token="<?php echo esc_attr( $access_token ); ?>" data-page-id="<?php echo esc_attr( $instagram_business_id ); ?>">
								<div class="sbi-add-checkbox">
									<input id="sbi-<?php echo esc_attr( $instagram_business_id ); ?>" type="checkbox" name="sbi_managed_pages[]" value="<?php echo esc_attr( $instagram_account_info ); ?>">
								</div>
								<div class="sbi-managed-page-details">
									<label for="sbi-<?php echo esc_attr( $instagram_business_id ); ?>"><img class="sbi-page-avatar" height="50" width="50" src="<?php echo esc_url( $instagram_biz_img ); ?>" alt="<?php echo esc_attr( $instagram_business_id ); ?>"><strong style="font-size: 16px;"><?php echo esc_html( $instagram_account_data->name ); ?></strong>
										<br />@<?php echo esc_html( $instagram_account_data->username ); ?><span style="font-size: 11px; margin-left: 5px;">(<?php echo esc_html( $instagram_business_id ); ?>)</span></label>
								</div>
							</div>
						<?php endif; ?>

						<?php endif; ?>

					<?php endforeach; ?>

				</div> <!-- end scrollable -->
				<p style="font-size: 11px; line-height: 1.5; margin-bottom: 0;"><em style="color: #666;">*<?php echo wp_kses_post( sprintf( __( 'Changing the password, updating privacy settings, or removing page admins for the related Facebook page may require %1$smanually reauthorizing our app%2$s to reconnect an account.', 'instagram-feed' ), '<a href="https://smashballoon.com/reauthorizing-our-instagram-facebook-app/" target="_blank" rel="noopener noreferrer">', '</a>' ) ); ?></em></p>

				<button id="sbi-connect-business-accounts" class="button button-primary" disabled="disabled" style="margin-top: 20px;"><?php esc_html_e( 'Connect Accounts', 'instagram-feed' ); ?></button>

			<?php endif; ?>

			<a href="JavaScript:void(0);" class="sbi_modal_close"><i class="fa fa-times"></i></a>
		</div>
	</div>
	</div>
	<?php
}

function sbi_get_personal_connection_modal( $connected_accounts, $action_url = 'admin.php?page=sb-instagram-feed' ) {
	if ( ! isset( $_GET['sbi_con'] ) || ! wp_verify_nonce( $_GET['sbi_con'], 'sbi-connect' ) ) {
		return;
	}
	$access_token      = ! empty( $_GET['sbi_access_token'] ) ? sbi_sanitize_alphanumeric_and_equals( sbi_maybe_clean( urldecode( ( $_GET['sbi_access_token'] ) ) ) ) : '';
	$account_type      = ! empty( $_GET['sbi_account_type'] ) ? sbi_sanitize_alphanumeric_and_equals( wp_unslash( $_GET['sbi_account_type'] ) ) : '';
	$user_id           = ! empty( $_GET['sbi_id'] ) ? sbi_sanitize_alphanumeric_and_equals( wp_unslash( $_GET['sbi_id'] ) ) : '';
	$user_name         = ! empty( $_GET['sbi_username'] ) ? sbi_sanitize_username( wp_unslash( $_GET['sbi_username'] ) ) : '';
	$expires_in        = ! empty( $_GET['sbi_expires_in'] ) ? (int) $_GET['sbi_expires_in'] : '';
	$expires_timestamp = time() + $expires_in;

	$new_account_details = array(
		'access_token'      => $access_token,
		'account_type'      => $account_type,
		'user_id'           => $user_id,
		'username'          => $user_name,
		'expires_timestamp' => $expires_timestamp,
		'profile_picture'   => '',
		'type'              => 'basic',
	);

	$matches_existing_personal = sbi_matches_existing_personal( $new_account_details );
	$button_text               = $matches_existing_personal ? __( 'Update This Account', 'instagram-feed' ) : __( 'Connect This Account', 'instagram-feed' );

	$account_json = sbi_json_encode( $new_account_details );

	$already_connected_as_business_account = ! empty( $connected_accounts[ $user_id ] ) && 'business' === $connected_accounts[ $user_id ]['type'];
	?>

	<div id="sbi_config_info" class="sb_get_token">
		<div class="sbi_config_modal">
			<div class="sbi_ca_username"><strong><?php echo esc_html( $user_name ); ?></strong></div>
			<form action="<?php echo esc_url( admin_url( $action_url ) ); ?>" method="post">
				<p class="sbi_submit">
					<?php
					if ( $already_connected_as_business_account ) :
						esc_html_e( 'The Instagram account you are logged into is already connected as a "business" account. Remove the business account if you\'d like to connect as a basic account instead (not recommended).', 'instagram-feed' );
						?>
					<?php else : ?>
						<input type="submit" name="sbi_submit" id="sbi_connect_account" class="button button-primary" value="<?php echo esc_html( $button_text ); ?>">
					<?php endif; ?>
					<input type="hidden" name="sbi_account_json" value="<?php echo esc_attr( $account_json ); ?>">
					<input type="hidden" name="sbi_connect_username" value="<?php echo esc_attr( $user_name ); ?>">
					<a href="JavaScript:void(0);" class="button button-secondary" id="sbi_switch_accounts"><?php esc_html_e( 'Switch Accounts', 'instagram-feed' ); ?></a>
				</p>
			</form>
			<a href="JavaScript:void(0);"><i class="sbi_modal_close fa fa-times"></i></a>
		</div>
	</div>
	<?php
}

function sbi_account_type_display( $type, $private = false ) {
	if ( 'basic' === $type ) {
		$type = 'personal';
		if ( $private ) {
			$type .= ' (private)';
		}
	}
	return $type;
}

function sbi_clear_backups() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	//Delete all transients
	global $wpdb;

	$wpdb->query(
		"
    DELETE
    FROM $wpdb->options
    WHERE `option_name` LIKE ('%!sbi\_%')
    "
	);
	$wpdb->query(
		"
    DELETE
    FROM $wpdb->options
    WHERE `option_name` LIKE ('%\_transient\_&sbi\_%')
    "
	);
	$wpdb->query(
		"
    DELETE
    FROM $wpdb->options
    WHERE `option_name` LIKE ('%\_transient\_timeout\_&sbi\_%')
    "
	);

	wp_send_json_success();
}
add_action( 'wp_ajax_sbi_clear_backups', 'sbi_clear_backups' );

function sbi_reset_resized() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	global $sb_instagram_posts_manager;
	$sb_instagram_posts_manager->delete_all_sbi_instagram_posts();
	delete_option( 'sbi_top_api_calls' );

	$sb_instagram_posts_manager->add_action_log( 'Reset resizing tables.' );

	wp_send_json_success( '1' );
}
add_action( 'wp_ajax_sbi_reset_resized', 'sbi_reset_resized' );

function sbi_reset_log() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	global $sb_instagram_posts_manager;

	$sb_instagram_posts_manager->remove_all_errors();

	wp_send_json_success( '1' );
}
add_action( 'wp_ajax_sbi_reset_log', 'sbi_reset_log' );

function sbi_reset_api_errors() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	global $sb_instagram_posts_manager;
	$sb_instagram_posts_manager->add_action_log( 'View feed and retry button clicked.' );

	$sb_instagram_posts_manager->reset_api_errors();

	wp_send_json_success( '1' );
}
add_action( 'wp_ajax_sbi_reset_api_errors', 'sbi_reset_api_errors' );

function sbi_lite_dismiss() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	set_transient( 'instagram_feed_dismiss_lite', 'dismiss', 1 * WEEK_IN_SECONDS );

	wp_send_json_success( '1' );
}
add_action( 'wp_ajax_sbi_lite_dismiss', 'sbi_lite_dismiss' );

add_action( 'admin_notices', 'sbi_admin_error_notices' );
function sbi_admin_error_notices() {
	if ( ! current_user_can( 'manage_instagram_feed_options' ) ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'sb-instagram-feed' ), true ) ) {

		global $sb_instagram_posts_manager;

		$errors = $sb_instagram_posts_manager->get_errors();
		if ( ! empty( $errors ) && ( ! empty( $errors['database_create'] ) || ! empty( $errors['upload_dir'] ) ) ) :
			?>
			<div class="notice notice-warning is-dismissible sbi-admin-notice">
				<?php
				if ( ! empty( $errors['database_create'] ) ) {
					echo '<p>' . wp_kses_post( $errors['database_create'] ) . '</p>';
				}
				if ( ! empty( $errors['upload_dir'] ) ) {
					echo '<p>' . wp_kses_post( $errors['upload_dir'] ) . '</p>';
				}
				?>
				<p><?php echo wp_kses_post( sprintf( __( 'Visit our %s page for help', 'instagram-feed' ), '<a href="https://smashballoon.com/instagram-feed/support/faq/" target="_blank">FAQ</a>' ) ); ?></p>

			</div>

			<?php
		endif;
		$errors = $sb_instagram_posts_manager->get_critical_errors();
		if ( $sb_instagram_posts_manager->are_critical_errors() && ! empty( $errors ) ) :
			?>
			<div class="notice notice-warning is-dismissible sbi-admin-notice">
				<p><strong><?php echo esc_html__( 'Instagram Feed is encountering an error and your feeds may not be updating due to the following reasons:', 'instagram-feed' ); ?></strong></p>

				<?php echo wp_kses_post( $errors ); ?>

				<?php
				$error_page = $sb_instagram_posts_manager->get_error_page();
				if ( $error_page ) {
					echo '<a href="' . esc_url( get_the_permalink( $error_page ) ) . '" class="sbi-clear-errors-visit-page sbi-space-left button button-secondary">' . esc_html__( 'View Feed and Retry', 'instagram-feed' ) . '</a>';
				}
				if ( $sb_instagram_posts_manager->was_app_permission_related_error() ) :
					$accounts_revoked = $sb_instagram_posts_manager->get_app_permission_related_error_ids();
					if ( count( $accounts_revoked ) > 1 ) {
						$accounts_revoked = implode( ', ', $accounts_revoked );
					} else {
						$accounts_revoked = $accounts_revoked[0];
					}
					?>
					<p class="sbi_notice"><?php echo esc_html( sprintf( __( 'Instagram Feed related data for the account(s) %s was removed due to permission for the Smash Balloon App on Facebook or Instagram being revoked.', 'instagram-feed' ), $accounts_revoked ) ); ?></p>
				<?php endif; ?>
			</div>
			<?php
		endif;
	}

}

function sbi_admin_hide_unrelated_notices() {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['page'] ) || ( strpos( $_GET['page'], 'sb-instagram-feed' ) === false && strpos( $_GET['page'], 'sbi-' ) === false ) ) {
		return;
	}

	// Extra banned classes and callbacks from third-party plugins.
	$blacklist = array(
		'classes'   => array(),
		'callbacks' => array(
			'sbidb_admin_notice', // 'Database for sbi' plugin.
		),
	);

	global $wp_filter;

	foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $notices_type ) {
		if ( empty( $wp_filter[ $notices_type ]->callbacks ) || ! is_array( $wp_filter[ $notices_type ]->callbacks ) ) {
			continue;
		}
		foreach ( $wp_filter[ $notices_type ]->callbacks as $priority => $hooks ) {
			foreach ( $hooks as $name => $arr ) {
				if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
					continue;
				}
				$class = ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) ? strtolower( get_class( $arr['function'][0] ) ) : '';
				if (
					! empty( $class ) &&
					strpos( $class, 'sbi' ) !== false &&
					! in_array( $class, $blacklist['classes'], true )
				) {
					continue;
				}
				if (
					! empty( $name ) && (
						strpos( $name, 'sbi' ) === false ||
						in_array( $class, $blacklist['classes'], true ) ||
						in_array( $name, $blacklist['callbacks'], true )
					)
				) {
					unset( $wp_filter[ $notices_type ]->callbacks[ $priority ][ $name ] );
				}
			}
		}
	}
}
add_action( 'admin_print_scripts', 'sbi_admin_hide_unrelated_notices' );

/* Usage */
add_action( 'admin_notices', 'sbi_usage_opt_in' );
function sbi_usage_opt_in() {
	$cap = current_user_can( 'manage_instagram_feed_options' ) ? 'manage_instagram_feed_options' : 'manage_options';

	$cap = apply_filters( 'sbi_settings_pages_capability', $cap );
	if ( ! current_user_can( $cap ) ) {
		return;
	}

	if ( isset( $_GET['trackingdismiss'] ) ) {
		if ( ! isset( $_GET['sbi_nonce'] ) || ! wp_verify_nonce( $_GET['sbi_nonce'], 'sbi-trackingdismiss' ) ) {
			return;
		}
		$usage_tracking = get_option(
			'sbi_usage_tracking',
			array(
				'last_send' => 0,
				'enabled'   => false,
			)
		);

		$usage_tracking['enabled'] = false;

		update_option( 'sbi_usage_tracking', $usage_tracking, false );

		return;
	}

	$usage_tracking = sbi_get_option( 'sbi_usage_tracking', false );
	if ( $usage_tracking ) {
		return;
	}
	$dismiss_href = wp_nonce_url( admin_url( 'admin.php?page=sb-instagram-feed&trackingdismiss=1' ), 'sbi-trackingdismiss', 'sbi_nonce' );
	?>
	<div class="notice notice-warning is-dismissible sbi-admin-notice">

		<p>
			<strong><?php esc_html_e( 'Help us improve the Instagram Feed plugin', 'instagram-feed' ); ?></strong><br>
			<?php esc_html_e( 'Understanding how you are using the plugin allows us to further improve it. Opt-in below to agree to send a weekly report of plugin usage data.', 'instagram-feed' ); ?>
			<a target="_blank" rel="noopener noreferrer" href="https://smashballoon.com/instagram-feed/usage-tracking/"><?php esc_html_e( 'More information', 'instagram-feed' ); ?></a>
		</p>
		<p>
			<a href="<?php echo esc_url( $dismiss_href ); ?>" class="button button-primary sb-opt-in"><?php esc_html_e( 'Yes, I\'d like to help', 'instagram-feed' ); ?></a>
			<a href="<?php echo esc_url( $dismiss_href ); ?>" class="sb-no-usage-opt-out sbi-space-left button button-secondary"><?php esc_html_e( 'No, thanks', 'instagram-feed' ); ?></a>
		</p>

	</div>

	<?php
}

function sbi_usage_opt_in_or_out() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	$usage_tracking = sbi_get_option(
		'sbi_usage_tracking',
		array(
			'last_send' => 0,
			'enabled'   => false,
		)
	);

	$usage_tracking['enabled'] = ! empty( $_POST['opted_in'] ) && 'true' === $_POST['opted_in'];

	sbi_update_option( 'sbi_usage_tracking', $usage_tracking, false );

	wp_send_json_success( '1' );
}
add_action( 'wp_ajax_sbi_usage_opt_in_or_out', 'sbi_usage_opt_in_or_out' );

function sbi_oembed_disable() {
	check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		wp_send_json_error();
	}

	$oembed_settings                 = get_option( 'sbi_oembed_token', array() );
	$oembed_settings['access_token'] = '';
	$oembed_settings['disabled']     = true;
	echo '<strong>';
	if ( update_option( 'sbi_oembed_token', $oembed_settings ) ) {
		esc_html_e( 'Instagram oEmbeds will no longer be handled by Instagram Feed.', 'instagram-feed' );
	} else {
		esc_html_e( 'An error occurred when trying to delete your oEmbed token.', 'instagram-feed' );
	}
	echo '</strong>';

	die();
}
add_action( 'wp_ajax_sbi_oembed_disable', 'sbi_oembed_disable' );
