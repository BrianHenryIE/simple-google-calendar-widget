=== Simple Google Calendar Widget ===
Tags: google calendar, google, calendar, widget
Requires at least: 3.1
Contributors: pemrich, nboehr
Tested up to: 4.0
Stable tag: trunk

Displays events from a public Google Calendar as a sidebar widget.

== Description ==

Google offers some HTML snippets to embed your public Google Calendar into your website. These are great, but as soon as you want to make a few adjustments to the styling, that goes beyond changing some colors, they’re useless. Because of that I wrote a very simple widget, that fetches events from a public google calendar and nicely displays them in form of a widget, allowing you to apply all kinds of CSS. The source code should be quite easy to modify.

== Installation ==

1. Do the usual setup procedure... you know... downloading... unpacking... uploading... activating. Or just install it through the wordpress plugin directory.
2. As soon as you activated the plugin, you should see a new widget under Design › Widgets. Just drag it into your sidebar.
3. Fill out all the necessary configuration fields. Under Calendar ID enter the calendar ID displayed by Google Calendar. You can find it by going to Calendar settings › Calendars, clicking on the appropriate calendar, scrolling all the way down to "Calendar address". There’s your calendar id.
4. You’re done!

== License ==
The plugin is published under the GPL v3. Previous versions (<= 0.2) did not
mention a license; this has been fixed in the latest trunk and will be
included in the next release version.

== Changelog ==

= 0.1 =
* First release.

= 0.2 =
* Fix a bug with recurring events, thanks Atur for reporting

= 0.3 =
* Fix possible Fatal Error when adding the widget to the sidebar, thanks pwndrian for reporting and larcher for the suggested fix.

= 0.4 =
* The plugin now respects the time zone set for your wordpress instance (Settings > General > Time Zone).
