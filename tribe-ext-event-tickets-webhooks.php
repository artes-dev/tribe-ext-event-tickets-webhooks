<?php
/**
 * Plugin Name:       Event Tickets Extension: Event Tickets Webhooks
 * Plugin URI:        https://github.com/artes-dev/tribe-ext-event-tickets-webhooks
 * GitHub Plugin URI: https://github.com/artes-dev/tribe-ext-event-tickets-webhooks
 * Description:       This extension posts to a user-defined webhook when someone registers for an event. The extension supports both RSVPs and Tickets.
 * Version:           1.0.0
 * Author:            robert@artesdev.com
 * Author URI:        https://github.com/artes-dev
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       event-tickets-webhooks
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

/**
 * Define the base file that loaded the plugin for determining plugin path and other variables.
 *
 * @since 1.0.0
 *
 * @var string Base file that loaded the plugin.
 */
define( 'TRIBE_EXTENSION___ET_WEBHOOKS___FILE', __FILE__ );
define( 'TRIBE_EXTENSION_EVENT_TICKETS_WEBHOOKS_OPTS_PREFIX', 'tribe_ext_et_webhooks_opts_' );

function webhook_post( $order_id = null, $event_id = null, $status = null ) {
	$settings = new Tribe\Extensions\ET_Webhooks\Settings( TRIBE_EXTENSION_EVENT_TICKETS_WEBHOOKS_OPTS_PREFIX );
	$url      = $settings->get_option( 'webhook_url' );
	$attendee = tribe_tickets_get_attendees( $order_id );

	$body = apply_filters( 'tribe_ext_et_webhooks_body', $attendee, $order_id, $event_id, $status );
	wp_remote_post( $url, array( 'body' => $body ) );
}

function tribe_extension_event_tickets_webhooks() {
	// When we dont have autoloader from common we bail.
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	$autoloader = Tribe__Autoloader::instance();

	$autoloader->register_prefix(
		'\\Tribe\\Extensions\\ET_Webhooks\\',
		__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Tribe' . DIRECTORY_SEPARATOR . 'Modules',
		'event-tickets-webhooks'
	);
	$autoloader->register_prefix(
		'\\Tribe\\Extensions\\ET_Webhooks\\',
		__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Tribe',
		'event-tickets-webhooks'
	);

	$autoloader->register_autoloader();

	$settings    = new Tribe\Extensions\ET_Webhooks\Settings( TRIBE_EXTENSION_EVENT_TICKETS_WEBHOOKS_OPTS_PREFIX );
	$enabled     = $settings->get_option( 'enabled' );
	$webhook_url = $settings->get_option( 'webhook_url' );

	if ( $enabled && ! empty( $webhook_url ) ) {
		add_action( 'event_tickets_rsvp_tickets_generated', 'webhook_post', 10, 3 );

		// WooCommerce
		add_action( 'event_ticket_woo_attendee_created', 'webhook_post', 10, 3 );

		// Tribe Commerce
		add_action( 'event_tickets_tpp_tickets_generated', 'webhook_post', 10, 3 );

		// EDD
		add_action( 'event_ticket_edd_attendee_created', 'webhook_post', 10, 3 );
	}
}

// Loads after common is already properly loaded.
add_action( 'tribe_common_loaded', 'tribe_extension_event_tickets_webhooks' );