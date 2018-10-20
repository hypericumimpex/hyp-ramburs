<?php
/*
Plugin Name: HYP Ramburs
Plugin URI: https://github.com/hypericumimpex/hyp-ramburs/
Description: <code><strong>HYP Ramburs</strong></code> face ca cererile de rambursare să fie accesibile și ușor de gestionat atât de utilizator, cât și de client.
Version: 1.1.0
Author: Romeo C.
Author URI: https://romeocovaci.com/
Text Domain: yith-advanced-refund-system-for-woocommerce
WC requires at least: 3.0.0
WC tested up to: 3.5.0
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_WCARS_FREE_INIT', plugin_basename( __FILE__ ) );

/* === DEFINE === */
! defined( 'YITH_WCARS_VERSION' )          && define( 'YITH_WCARS_VERSION', '1.1.0' );
! defined( 'YITH_WCARS_INIT' )             && define( 'YITH_WCARS_INIT', plugin_basename( __FILE__ ) );
! defined( 'YITH_WCARS_SLUG' )             && define( 'YITH_WCARS_SLUG', 'yith-advanced-refund-system-for-woocommerce' );
! defined( 'YITH_WCARS_SECRETKEY' )        && define( 'YITH_WCARS_SECRETKEY', 'VUeAMbd9Y0WQAiaoPNED' );
! defined( 'YITH_WCARS_FILE' )             && define( 'YITH_WCARS_FILE', __FILE__ );
! defined( 'YITH_WCARS_PATH' )             && define( 'YITH_WCARS_PATH', plugin_dir_path( __FILE__ ) );
! defined( 'YITH_WCARS_URL' )              && define( 'YITH_WCARS_URL', plugins_url( '/', __FILE__ ) );
! defined( 'YITH_WCARS_ASSETS_URL' )       && define( 'YITH_WCARS_ASSETS_URL', YITH_WCARS_URL . 'assets/' );
! defined( 'YITH_WCARS_ASSETS_JS_URL' )    && define( 'YITH_WCARS_ASSETS_JS_URL', YITH_WCARS_URL . 'assets/js/' );
! defined( 'YITH_WCARS_TEMPLATE_PATH' )    && define( 'YITH_WCARS_TEMPLATE_PATH', YITH_WCARS_PATH . 'templates/' );
! defined( 'YITH_WCARS_WC_TEMPLATE_PATH' ) && define( 'YITH_WCARS_WC_TEMPLATE_PATH', YITH_WCARS_PATH . 'templates/woocommerce/' );
! defined( 'YITH_WCARS_OPTIONS_PATH' )     && define( 'YITH_WCARS_OPTIONS_PATH', YITH_WCARS_PATH . 'plugin-options' );
! defined( 'YITH_WCARS_PREMIUM' )          && define( 'YITH_WCARS_PREMIUM', '1' );
! defined( 'YITH_WCARS_CUSTOM_POST_TYPE' ) && define( 'YITH_WCARS_CUSTOM_POST_TYPE', 'yith_refund_request' );

$wp_upload_dir = wp_upload_dir ();

! defined ( 'YITH_WCARS_UPLOADS_DIR' )                   && define ( 'YITH_WCARS_UPLOADS_DIR', $wp_upload_dir[ 'basedir' ] . '/ywcars/' );
! defined ( 'YITH_WCARS_UPLOADS_URL' )                   && define ( 'YITH_WCARS_UPLOADS_URL', $wp_upload_dir[ 'baseurl' ] . '/ywcars/' );
! defined ( 'YITH_WCARS_ONE_KILOBYTE_IN_BYTES' )         && define ( 'YITH_WCARS_ONE_KILOBYTE_IN_BYTES', 1024 );
! defined ( 'YITH_WCARS_UPLOAD_ERR_ALL_FILES_OK' )       && define ( 'YITH_WCARS_UPLOAD_ERR_ALL_FILES_OK', 20 );
! defined ( 'YITH_WCARS_UPLOAD_ERR_NOT_A_IMAGE' )        && define ( 'YITH_WCARS_UPLOAD_ERR_NOT_A_IMAGE', 21 );
! defined ( 'YITH_WCARS_UPLOAD_ERR_WRONG_IMAGE_FORMAT' ) && define ( 'YITH_WCARS_UPLOAD_ERR_WRONG_IMAGE_FORMAT', 22 );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WCARS_PATH . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WCARS_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WCARS_PATH );

/* Register the plugin when activated */
register_activation_hook( __FILE__, 'flush_rewrite_rules' );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

require_once YITH_WCARS_PATH . '/functions.php';

/* Start the plugin on plugins_loaded */
if ( ! function_exists( 'yith_ywars_install' ) ) {
	/**
	 * Install the plugin
	 */
	function yith_ywars_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywars_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywars_init' );
			YITH_ARS_DB::install();
		}
	}
	add_action( 'plugins_loaded', 'yith_ywars_install', 11 );
}

if ( ! function_exists( 'yith_ywars_install_woocommerce_admin_notice' ) ) {

	function yith_ywars_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _e( 'YITH Advanced Refund System for WooCommerce is enabled but not effective. It requires WooCommerce in order to work.', 'yith-advanced-refund-system-for-woocommerce' ); ?></p>
		</div>
		<?php
	}
}

add_action( 'yith_ywars_init', 'yith_ywars_init' );

if ( ! function_exists( 'yith_ywars_init' ) ) {
	/**
	 * Start the plugin
	 */
	function yith_ywars_init() {
		/**
		 * Load text domain
		 */
		load_plugin_textdomain( 'yith-advanced-refund-system-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( ! function_exists( 'YITH_Advanced_Refund_System' ) ) {
			/**
			 * Unique access to instance of YITH_Advanced_Refund_System class
			 *
			 * @return YITH_Advanced_Refund_System
			 * @since 1.0.0
			 */
			function YITH_Advanced_Refund_System() {
				require_once( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system.php' );
				require_once( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system-db.php' );
				if ( defined( 'YITH_WCARS_PREMIUM' ) && file_exists( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system-premium.php' ) ) {
					require_once( YITH_WCARS_PATH . 'includes/class.yith-advanced-refund-system-premium.php' );
					return YITH_Advanced_Refund_System_Premium::instance();
				}
				return YITH_Advanced_Refund_System::instance();
			}
		}
		// Let's start the game!
		YITH_Advanced_Refund_System();
	}
}