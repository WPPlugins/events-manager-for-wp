=== Events Manager for WordPress ===
Contributors: forsitemedia
Donate link: https://forsite.media/
Tags: events, event, events-manager, genesiswp
Requires at least: 4.5
Tested up to: 4.6.1
Stable tag: 1.2.6
Text Domain: events-manager-for-wp
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A full featured Events Manager plugin, including recurring events, maps/location and multisite support.

== Description ==

Events Manager for WP is a simple and lightweight plugin to maintain your events. Works perfectly with the Genesis Framework. It offers the following features:

= Features =
* Simple event registration
* Recurring events (soon!)
* Multisite support
* Genesis support
* Location / maps support
* Event categories
* Events widget
* Schema.org markup

= Settings =
* The Single Event Slug prefix
* The Archive Slug
* Event Types prefix
* Events Overview slug
* Optionally hide Post Info on events in case Genesis is used


== Installation ==

Install and activate the plugin. An "Events" menu item will appear in the WordPress admin panel, from which you can add events and alter the URL setup for the plugin.

= Adding events =
An event needs to have it's title, description (main post content) and start/end times set. You may also set an event to be a whole day event, and set a location to display a map on the event.

= Future events =
To create an event scheduled for the future, simply set the start time to a future time.

= Displaying a calendar =
A calendar can be displayed on a page through use of the `[events-calendar]` shortcode.

= Widget =
The plugin includes an upcoming events widget. The widget includes settings for the title, number of events and button text for the "view all events" button.

= Modifying schema.org markup =

The following example will allow you to edit the Schema.org event description markup if your theme requires it to be different from what the plugin outputs.
```
<?php

add_filter( 'the_content', 'your_prefix_replace_markup' );
/**
 * Filter the default EM4WP Schema.org markup.
 *
 * @param string $content Existing content.
 * @return string Amended content.
 */
function your_prefix_replace_markup( $content ) {

    return str_replace( '<div itemprop="description">', '<div itemprop="something-else">', $content );

}
```

== Support ==
If you would like to file a bug report or ask a question, please do so in the WordPress.org support forums.

== Changelog ==

= 1.2.5 =
* Last Schema.org improvements for the day. We promise.

= 1.2.4 = 
* Improve Location handling

= 1.2.3 = 
* Further Schema.org improvements to Post Info on single events
* Added Location Name metabox
* Fixed bug where single events would display all event types
* Add setting to hide Post Info on single events entirely
* Added some missing text domains

= 1.2.2 = 
* i18n all the things..

= 1.2.1 = 
* Improve example schema.org example. Props Gary Jones.
* By declaring `"use strict";`, browsers can use their strict JS engines, which are often faster. Props Gary Jones.
* Better default location coordinates

= 1.2.0 =
* Various improvements from schema fixes to enhanced options
* Documentation improvements

= 1.1 (2016-07-06) =
* Initial release on WordPress.org

= 1.0 (2016-06-15) =
* Initial test release
