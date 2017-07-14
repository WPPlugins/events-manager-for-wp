# Events Manager for WordPress

Events Manager for WP is a simple and lightweight plugin to maintain your events. Works perfectly with the Genesis Framework. It offers the following features:

## Adding events
An event needs to have it's title, description (main post content) and start/end times set. You may also set an event to be a whole day event, and set a location to display a map on the event.

## Future events
To create an event scheduled for the future, simply set the start time to a future time.

## Displaying a calendar
A calendar can be displayed on a page through use of the `[events-calendar]` shortcode.

## Upcoming Events Widget
The plugin includes an upcoming events widget. The widget includes settings for the title, number of events and button text for the "view all events" button.

## Modifying schema.org markup

The following example will allow you to edit the schema.org event description markup.

```php
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
