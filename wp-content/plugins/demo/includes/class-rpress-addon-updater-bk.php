<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allows plugins to use their own update API.
 *
 * @author Restro Press
 * @version 1.6.19
 */
class RP_Addon_Updater {

	private $api_url	= '';
	private $api_data	= array();
	private $item_id	= '';
	private $name		= '';
	private $title		= '';
	private $slug		= '';
	private $version	= '';
	private $update_api_url = '';

	/**
	 * Class constructor.
	 *
	 * @uses plugin_basename()
	 * @uses hook()
	 *
	 * @param string  $_api_url     The URL pointing to the custom API endpoint.
	 * @param string  $_plugin_file Path to the plugin file.
	 * @param array   $_api_data    Optional data to send with API calls.
	 */
	public function __construct( $_api_url, $_plugin_file, $_api_data = null ) {

		$this->api_url	= trailingslashit( $_api_url );
		$this->api_data	= $_api_data;
		$this->name		= $_plugin_file;
		$this->slug		= $_api_data['slug'];
		$this->item_id	= $_api_data['item_id'];
		$this->title	= $_api_data['title'];
		$this->version	= $_api_data['version'];

		// Temporary api URL for testing. Enable it inorder to test plugin update without a proper license key
		// $this->update_api_url = 'http://restropress.com/addon-repo/' . $this->slug . '/info.json';

		// Set up hooks.
		$this->init();
	}

	/**
	 * Set up WordPress filters to hook into WP's update process.
	 *
	 * @uses add_filter()
	 *
	 * @return void
	 */
	public function init() {

		// Check if this plugin is having any new update or not
		add_filter( 'site_transient_update_plugins', array( $this, 'rp_check_addon_update' ) );

		// If there is a new update, this action will add all details to view version details window
		add_filter( 'plugins_api', array( $this, 'rp_plugins_api_filter' ), 20, 3 );

		// Clear the transients once the addon update process is complete
		add_action( 'upgrader_process_complete', array( $this, 'rp_after_addon_update'), 10, 2 );

		// Prepare the custom message based on the resonse we got from update server
		add_action( 'after_plugin_row_' . $this->name, array( $this, 'rp_addon_license_verification'), 5, 2 );
	}

	/**
	 * License check from server. Will return a valid package
	 * URL if the new update is available for the addon
	 *
	 * @since 2.5.1
	 * @author RestroPress
	 * @param str License key to check
	 */
	public function rp_verify_addon_license( $license ) {

		if( get_transient( 'rp_chk_license_' . $this->slug ) )
			return '';

		set_transient( 'rp_chk_license_' . $this->slug, 'on_progress', 20 ); 
		
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'check_license',
			'item_id' 	 => $this->item_id,
			'item_name'  => urlencode( $this->title ),
			'license'    => $license,
			'url'        => home_url(),
			'license_key'=> $this->slug,
		);

		// Call the custom API.
		$response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) 
			|| 200 !== wp_remote_retrieve_response_code( $response ) ) {
			
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} 
			else {
				$message = __( 'An error occurred, please try again.' );
			}
		} else {
			$checked_data = json_decode( wp_remote_retrieve_body( $response ) );
			if( 'valid' === $checked_data->license ) {
				$update_api_url = $checked_data->update_api_url;
				if( $update_api_url ) {
					$this->update_api_url = $update_api_url;
					return 'valid';
				}
				else
					$message = __( 'An error occurred, please try again.' );
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
		}

		return $message;
	}

	/** 
	 * Checks if addon have any new version available to be installed
	 * Will proceed only after the license key is validated
	 *
	 * @since 2.5.1
	 * @author RestroPress
	 * @param obj Transient object holding all plugins information
	 */
	public function rp_check_addon_update( $transient ) {
 
		if ( empty($transient->checked ) ) {
            return $transient;
        }

        // Verify Addon License 
        if( get_option( $this->slug ) ) {
        	$update_json = $this->rp_verify_addon_license( get_option( $this->slug ) );
        } else {
        	return $transient;
        }

        if( $update_json != 'valid' ) {
        	return $transient;
        }

		// trying to get from cache first, to disable cache comment 10,20,21,22,24
		if( false == $remote = get_transient( 'rp_upgrade_' . $this->slug ) ) {
	 
			// info.json is the file with the actual plugin information on your server
			$remote = wp_remote_get( $this->update_api_url, array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				) )
			);
	 
			if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
				
				// 12 hours cache
				set_transient( 'rp_upgrade_' . $this->slug, $remote, 43200 ); 
			}
	 
		}

		if( $remote ) {

			$remote = json_decode( $remote['body'] );
	 
			// your installed plugin version should be on the line below! You can obtain it dynamically of course 
			if( $remote && version_compare( $this->version, $remote->version, '<' ) && version_compare($remote->requires, get_bloginfo('version'), '<' ) ) {
				$res = new stdClass();
				$res->slug = $this->slug;
				$res->plugin = $this->name;
				$res->new_version = $remote->version;
				$res->tested = $remote->tested;
				$res->package = $remote->download_url;
				$res->license_check = 'verified';
				$transient->response[$res->plugin] = $res;
				$transient->checked[$res->plugin] = $remote->version;
			}
		}
		return $transient;
	}

	/**
	 * Get the plugin update information from server
	 * Will be called only after a transient value available for plugin update
	 *
	 * @since 2.5.1
	 * @author RestroPress
	 * @param obj Trnasient of Update Plugins details
	 * @param str Action to be done
	 * @param arr Array of parameters
	 */
	public function rp_plugins_api_filter( $res, $action, $args ){
 
		// do nothing if this is not about getting plugin information
		if( 'plugin_information' !== $action ) {
			return false;
		}
	 
		$plugin_slug = $this->slug; // we are going to use it in many places in this function
	 
		// do nothing if it is not our plugin
		if( $plugin_slug !== $args->slug ) {
			return false;
		}

		// Check if update api url is available or not
		if( empty( $this->update_api_url ) ) {
			return false;
		}
	 
		// trying to get from cache first
		if( false == $remote = get_transient( 'rp_update_' . $plugin_slug ) ) {
	 
			// info.json is the file with the actual plugin information on your server
			$remote = wp_remote_get( $this->update_api_url, array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'application/json'
				) )
			);
	 
			if ( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
				
				// 12 hours cache
				set_transient( 'rp_update_' . $plugin_slug, $remote, 43200 );
			}

			if( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {

				$remote = json_decode( $remote['body'] );
				$res = new stdClass();
		 
				$res->name = $remote->name;
				$res->slug = $plugin_slug;
				$res->version = $remote->version;
				$res->tested = $remote->tested;
				$res->requires = $remote->requires;
				$res->author = '<a href="https://magnigenie.com/">Magnigenie</a>';
				$res->author_profile = 'https://profiles.wordpress.org/magnigenie';
				$res->download_link = $remote->download_url;
				$res->trunk = $remote->download_url;
				$res->requires_php = '5.3';
				$res->last_updated = $remote->last_updated;
				$res->sections = array(
					'description' => $remote->sections->description,
					'installation' => $remote->sections->installation,
					'changelog' => $remote->sections->changelog
				);

				if( !empty( $remote->sections->screenshots ) ) {
					$res->sections['screenshots'] = $remote->sections->screenshots;
				}
		 
				$res->banners = array();
				return $res;
			}
		}
		return false;
	}

	/**
	 * Remove the transient creatd to update the plugin
	 *
	 * @since 2.5.1
	 * @author RestroPress
	 */
	public function rp_after_addon_update( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && $options['type'] === 'plugin' )  {
			// just clean the cache when new plugin version is installed
			delete_transient( 'rp_upgrade_' . $this->slug );
		}
	}

	/** 
	 * License verification status message in Plugins area
	 *
	 * @since 2.5.1
	 * @author RestroPress
	 * @param str __FILE__ value of the plugin
	 * @param obj Plugin update object
	 */
	public function rp_addon_license_verification( $file, $plugin_data ) {

		remove_action( 'after_plugin_row_' . $this->name, 'wp_plugin_update_row', 10, 2 );

		$current = get_site_transient( 'update_plugins' );
		if ( ! isset( $current->response[ $file ] ) ) {
			return false;
		}

		$response = $current->response[ $file ];

		$plugins_allowedtags = array(
			'a'       => array(
				'href'  => array(),
				'title' => array(),
			),
			'abbr'    => array( 'title' => array() ),
			'acronym' => array( 'title' => array() ),
			'code'    => array(),
			'em'      => array(),
			'strong'  => array(),
		);

		$plugin_name = wp_kses( $plugin_data['Name'], $plugins_allowedtags );
		$details_url = self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $response->slug . '&section=changelog&TB_iframe=true&width=600&height=800' );

		/** @var WP_Plugins_List_Table $wp_list_table */
		$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

		if ( is_network_admin() || ! is_multisite() ) {
			if ( is_network_admin() ) {
				$active_class = is_plugin_active_for_network( $file ) ? ' active' : '';
			} else {
				$active_class = is_plugin_active( $file ) ? ' active' : '';
			}

			$requires_php   = isset( $response->requires_php ) ? $response->requires_php : null;
			$compatible_php = is_php_version_compatible( $requires_php );
			$notice_type    = $compatible_php ? 'notice-warning' : 'notice-error';

			printf(
				'<tr class="plugin-update-tr%s" id="%s" data-slug="%s" data-plugin="%s">' .
				'<td colspan="%s" class="plugin-update colspanchange">' .
				'<div class="update-message notice inline %s notice-alt"><p>',
				$active_class,
				esc_attr( $response->slug . '-update' ),
				esc_attr( $response->slug ),
				esc_attr( $file ),
				esc_attr( $wp_list_table->get_column_count() ),
				$notice_type
			);

			if ( ! current_user_can( 'update_plugins' ) ) {
				printf(
					/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number. */
					__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>.' ),
					$plugin_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Plugin name, 2: Version number. */
						esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
					),
					esc_attr( $response->new_version )
				);
			} elseif ( empty( $response->package ) ) {
				printf(
					 // translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number. 
					__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>. <em>Automatic update is unavailable for this plugin.</em>' ),
					$plugin_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Plugin name, 2: Version number. */
						esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
					),
					esc_attr( $response->new_version )
				);
			} elseif ( 'verified' !== $response->license_check ) {
				printf(
					 // translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number. 
					__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a>. <em>Automatic update is unavailable for this plugin.</em>' ),
					$plugin_name,
					esc_url( $details_url ),
					sprintf(
						'class="thickbox open-plugin-details-modal" aria-label="%s"',
						/* translators: 1: Plugin name, 2: Version number. */
						esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
					),
					esc_attr( $response->new_version )
				);
			} else {
				if ( $compatible_php ) {
					printf(
						/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number, 5: Update URL, 6: Additional link attributes. */
						__( 'There is a new version of %1$s available. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s" %6$s>update now</a>.' ),
						$plugin_name,
						esc_url( $details_url ),
						sprintf(
							'class="thickbox open-plugin-details-modal" aria-label="%s"',
							/* translators: 1: Plugin name, 2: Version number. */
							esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
						),
						esc_attr( $response->new_version ),
						wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file, 'upgrade-plugin_' . $file ),
						sprintf(
							'class="update-link" aria-label="%s"',
							/* translators: %s: Plugin name. */
							esc_attr( sprintf( __( 'Update %s now' ), $plugin_name ) )
						)
					);
				} else {
					printf(
						/* translators: 1: Plugin name, 2: Details URL, 3: Additional link attributes, 4: Version number 5: URL to Update PHP page. */
						__( 'There is a new version of %1$s available, but it doesn&#8217;t work with your version of PHP. <a href="%2$s" %3$s>View version %4$s details</a> or <a href="%5$s">learn more about updating PHP</a>.' ),
						$plugin_name,
						esc_url( $details_url ),
						sprintf(
							'class="thickbox open-plugin-details-modal" aria-label="%s"',
							/* translators: 1: Plugin name, 2: Version number. */
							esc_attr( sprintf( __( 'View %1$s version %2$s details' ), $plugin_name, $response->new_version ) )
						),
						esc_attr( $response->new_version ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '<br><em>', '</em>' );
				}
			}

			echo '</p></div></td></tr>';
		}
	}
}