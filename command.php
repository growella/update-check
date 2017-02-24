<?php
/**
 * WP-CLI command to automatically check WordPress core and all installed themes and plugins for
 * available updates.
 *
 * @package Growella\WP_CLI\UpdateCheck
 * @author  Growella
 */

namespace Growella\WP_CLI;

use WP_CLI;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Checks WordPress core and all installed plugins and themes for available updates.
 */
class UpdateCheck extends \WP_CLI_Command {

	/**
	 * Check WordPress core, themes, and plugins for available updates.
	 *
	 * ## OPTIONS
	 *
	 * [--email=<email>]
	 * : If provided, email the generated report to the provided email address instead of printing to STDOUT. If the --email option is used but no address is provided, the report will be sent to the site's administrator email address.
	 *
	 * [--report-current]
	 * : Send an email report, even if there are no pending updates.
	 *
	 * [--quiet]
	 * : Silence all output, useful when running as a cron task.
	 *
	 * ## EXAMPLES
	 *
	 *   wp update-check run
	 *   wp update-check run --email=myname@example.com
	 *   wp update-check run --email=myname@example.com --quiet
	 *   wp update-check run --email
	 *
	 * @param array $args       Optional. Positional arguments. Default is empty.
	 * @param array $assoc_args Optional. Associative arguments. Default is empty.
	 */
	public function run( $args = array(), $assoc_args = array() ) {
		$report      = esc_html( sprintf( __( 'Update check for %s', 'update-check' ), get_bloginfo( 'url' ) ) );
		$report     .= PHP_EOL . esc_html( sprintf( _x( 'Generated %s', 'report date', 'update-check' ), date( 'r' ) ) );
		$report     .= PHP_EOL . PHP_EOL;
		$quiet      = isset( $assoc_args['quiet'] );
		$send_email = false;

		/*
		 * WordPress core.
		 */
		$report .= esc_html_x( 'WordPress Core:', 'update check report section', 'update-check' );
		$core    = WP_CLI::launch_self( 'core check-update', array(), array( 'format' => 'json' ), false, true );
		$core    = json_decode( $core->stdout );

		if ( ! empty( $core ) ) {
			foreach ( $core as $version ) {
				$report .= PHP_EOL . esc_html( sprintf(
					/** Translators: %1$s is the type (major|minor), %2$s is the version number. */
					__( '- [%1$s] WordPress version %2$s is now available, please upgrade as soon as possible.', 'update-check' ),
					$version->update_type,
					$version->version
				) );
			}
			$send_email = true;
		} else {
			$report .= PHP_EOL . esc_html__( 'WordPress core is up-to-date.' );
		}
		$report .= PHP_EOL . PHP_EOL;

		/*
		 * Plugins.
		 */
		$report .= esc_html_x( 'Plugin Updates:', 'update check report section', 'update-check' );
		$plugins = WP_CLI::launch_self( 'plugin update', array(), array( 'all' => true, 'dry-run' => true, 'format' => 'json' ), false, true );
		$plugins = json_decode( $plugins->stdout );

		if ( ! empty( $plugins ) ) {
			foreach ( $plugins as $plugin ) {
				$report .= PHP_EOL . sprintf(
					/** Translators: %1$s is the plugin name, %2$s is the current version, %3$s is the latest version. */
					__( '- An update is available for %1$s (%2$s => %3$s)', 'update-check' ),
					$plugin->name,
					$plugin->version,
					$plugin->update_version
				);
			}
			$send_email = true;
		} else {
			$report .= PHP_EOL . esc_html__( 'All plugins are up-to-date.' );
		}
		$report .= PHP_EOL . PHP_EOL;

		/*
		 * Themes.
		 */
		$report .= esc_html_x( 'Theme Updates:', 'update check report section', 'update-check' );
		$themes  = WP_CLI::launch_self( 'theme update', array(), array( 'all' => true, 'dry-run' => true, 'format' => 'json' ), false, true );
		$themes  = json_decode( $themes->stdout );

		if ( ! empty( $themes ) ) {
			foreach ( $themes as $theme ) {
				$report .= PHP_EOL . sprintf(
					/** Translators: %1$s is the theme name, %2$s is the current version, %3$s is the latest version. */
					__( '- An update is available for %1$s (%2$s => %3$s)', 'update-check' ),
					$theme->name,
					$theme->version,
					$theme->update_version
				);
			}
			$send_email = true;
		} else {
			$report .= PHP_EOL . esc_html__( 'All themes are up-to-date.' );
		}
		$report .= PHP_EOL . PHP_EOL;

		// Finally, deliver the report.
		if ( isset( $assoc_args['email'] ) ) {

			// No need to send an email.
			if ( ! $send_email ) {
				return WP_CLI::debug( __( 'Everything up to date, no email has been sent.', 'update-check' ) );
			}

			$email = $this->send_email( $report, (string) $assoc_args['email'] );

			if ( ! $quiet ) {
				WP_CLI::success( sprintf( __( 'Report has been sent to %s', 'update-check' ), $email ) );
			}
		} else {
			WP_CLI::log( $report );
		}
	}

	/**
	 * Send the report email to the specified email address.
	 *
	 * @param string $report The report contents.
	 * @param string $email  Optional. The email address to deliver to. If not specified, will
	 *                       default to the site's admin email.
	 * @return string The email address the report was sent to.
	 */
	protected function send_email( $report, $email = null ) {
		$subject = sprintf( __( 'Updates are available for %s', 'update-check' ), get_bloginfo( 'url' ) );

		if ( ! $email ) {
			$email = get_option( 'admin_email' );
		}

		/*
		 * WP-CLI doesn't have access to the $_SERVER['SERVER_NAME'], so we need to explicitly set the
		 * "from" address.
		 */
		$hostname = parse_url( site_url(), PHP_URL_HOST );

		// Strip 'www.', if it exists.
		if ( 'www.' === substr( $hostname, 0, 4 ) ) {
			$hostname = substr( $hostname, 4 );
		}

		// Finally, send the email.
		wp_mail( $email, $subject, $report, array(
			sprintf( 'From: updates@%s', $hostname ),
		) );

		return $email;
	}
}

WP_CLI::add_command( 'update-check', __NAMESPACE__ . '\UpdateCheck' );
