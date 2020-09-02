=== Event Tickets Extension: Event Tickets Webhooks ===
Contributors: artesdevel
Donate link: https://bit.ly/34ZLSQk
Tags: events, calendar, webhook
Requires at least: 4.9
Tested up to: 5.4.1
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPL version 3 or any later version
License URI: https://www.gnu.org/licenses/gpl-3.0.html

This extension posts to a user-defined webhook when someone registers for an event. The extension supports both RSVPs and Tickets.

== Installation ==

Install and activate like any other plugin!

* You can upload the plugin zip file via the *Plugins ‣ Add New* screen
* You can unzip the plugin and then upload to your plugin directory (typically _wp-content/plugins_) via FTP
* Once it has been installed or uploaded, simply visit the main plugin list and activate it

== Hooks ==

To modify the contents of the POST body use the `tribe_ext_et_webhooks_body` filter:

    add_filter( 'tribe_ext_et_webhooks_body', 'modify_hook_body', 10, 4 );

    function modify_hook_body( $attendee, $order_id, $event_id, $status ) {
    // Your code
        return $variable;
    }

== Changelog ==

= [1.0.0] 2020-09-02 =

* Initial release