=== ICS Calendar Pro ===
Contributors: room34
Donate link: https://icscalendar.com
Tags: calendar, iCal, iCalendar
Requires at least: 5.3
Tested up to: 5.5
Requires PHP: 7.0.0
License: Copyright 2019-2020 Room 34 Creative Services, LLC. All rights reserved. This plugin may not be copied or distributed without a paid license or written permission from Room 34 Creative Services, LLC.

The "PRO" add-on to the free ICS Calendar plugin for WordPress adds advanced layout and customization options.

== Description ==

== Installation ==

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==

= 1.2.1 - 2020.08.21 =

* Fixed search filter on month-with-sidebar view so the grid shows all matching events when filtering, not just the first three per day.
* Configured month-with-sidebar CSS to *only* show event end times in the sidebar, not the main grid, to avoid a frequent issue of the space allocated for the end time causing a blank line on most events, depending on the window width.

= 1.2.0 - 2020.08.20 =

* Added Regular Expressions feature for customization of event text output.
* Reorganized tabs on saved calendar editing screen.
* Added link to Pro documentation website in the admin user guide.
* Updated ICS Calendar requirement to 5.9.1.
* Bumped tested up to version to 5.5.

= 1.1.2.1 - 2020.06.29 =

* Added type checks for certain arrays to resolve PHP Countable warnings in certain circumstances.
* Updated ICS Calendar requirement to 5.8.0.5. (Pro 1.1.2.1 probably still works with versions of ICS Calendar back to 5.7.2, but we are bumping the version requirement to encourage users to stay up-to-date, especially since the ICS Parser library was updated in 5.8.0.)
* Bumped tested up to version to 5.4.2.

= 1.1.2 - 2020.05.22 =

* Improved logic for detecting and notifying site administrator of expired or modified licenses.

= 1.1.1 - 2020.05.05 =

* Added support for new `legendinline` option.
* Implemented new `r34ics_feed_colors_css()` function in all templates.
* Bumped tested up to version to 5.4.1.
* Updated ICS Calendar requirement to 5.7.2.

= 1.1.0 - 2020.04.13 =

* Customizer enhancements incorporating several CSS-based options, based on suggestions we've made for users in the WordPress support forums.
* Bumped tested up to version to 5.4.

= 1.0.3 - 2020.04.09 =

* Fixed logic error in licensing-related admin notices when copy has not yet been registered, and improved general admin notice details.
* Fixed issue that could prevent license activation if auto-generated `r34icspro_instance` string contained certain special characters.
* Improved handling of WP-Cron for pre-caching.
* Streamlined admin Settings & Tools screen with help pop-ups.
* Added recommendation that transient expiration be set to 7200 (2 hours) or more if pre-caching is being used.
* Added hook to remove pre-caching WP-Cron task on plugin deactivation.

= 1.0.2 - 2020.04.06 =

* Fixed issues with license activation/deactivation on multisite installations.
* Refactored calls to licensing API.
* Modified `r34icspro_instance` option to reset if the server's IP address changes (i.e. the site has been moved).

= 1.0.1 - 2020.04.06 =

* Added **System Report** tab to **Settings & Tools** page. Information in the system report should be pasted into the [support request form](https://icscalendar.com/pro-support/).

= 1.0.0 - 2020.03.23 =

* First official release version.
* Updated license and update handling logic.
* Updated ICS Calendar requirement to 5.7.1.

= 0.9.4 - 2020.03.14 =

* Added machine translations for Chinese (Simplified), Dutch, Finnish, French, German, Greek, Hungarian, Italian, Japanese, Norwegian (Bokmål and Nynorsk), Portuguese (Brazil), Spanish (Mexico) and Swedish. Note: As with the free plugin, translations are currently limited to phrases displayed on front-end templates, not the admin screens.

= 0.9.3 - 2020.03.14 =

* Restructured WP-Cron logic for pre-caching saved calendars, resolved issues with cron job not running.
* Moved some logic for display of events in month-with-sidebar view from jQuery to main template.
* Added auto-scroll to top of sidebar when clicking a day in month-with-sidebar view.
* Additional Customizer options.

= 0.9.2.1 - 2020.03.13 =

* Bug fixes with filter on month-with-sidebar view.

= 0.9.2 - 2020.03.10 =

* Fully removed all code related to deprecated `tzoffset` and `tzignore` parameters.
* Added `r34icspro_day_events_count()` function to correct "n more events" display value on month-with-sidebar view.
* Added current (selected) date indicator in month-with-sidebar view.
* Removed option to change column headers on widget view. Always displays "min" (single-letter) headers.
* Fixed issue with calendar not displaying in year-with-sidebar view if only one year.
* CSS refinements.
* Updated ICS Calendar requirement to 5.7.0.

= 0.9.1.1 - 2020.03.10 =

* Added missing logic for Subscribe Link option.
* Updated ICS Calendar requirement to 5.6.1.1.

= 0.9.1 - 2020.03.10 =

* Added Subscribe Link option and related actions/methods. (Requires ICS Calendar v. 5.6.1.)
* Added year-with-sidebar template to repository. (Missing in 0.9.0 check-in.)
* Added custom updater icon.
* Bumped embedded Plugin Update Checker version to 4.9.
* Updated ICS Calendar requirement to 5.6.1.

= 0.9.0 - 2020.03.09 =

* Added new year-with-sidebar view. (Needs additional work for usability at phone breakpoint.)
* Added optional pre-caching of saved calendars. (Still experimental; may require a large amount of server memory as currently configured.)
* Fixed bug if a start date or rolling past date had been set, and then "Start On" was changed to "current date".
* UX/UI changes with color code key in conjunction with updates to the free plugin v. 5.6.0.
* Minor adjustments to admin settings interface.
* Updated ICS Calendar requirement to 5.6.0.
* Bumped embedded ACF Pro version to 5.8.8.

= 0.8.7 - 2020.03.03 =

* Added URL Tester feature to plugin's Settings page.
* Enhanced CSS for widget view.
* Expanded scope of Customizer color settings.

= 0.8.6 - 2020.03.03 =

* Fixed end time formatting in widget and month-with-sidebar view.
* Fixed bad include path for free plugin's admin sidebar.
* Added Transient Expiration option to plugin's Settings page.
* Updated ICS Calendar requirement to 5.5.1.

= 0.8.5 - 2020.02.28 =

* Added support for new `attach` parameter added to core version 5.5.0.
* Updated ICS Calendar requirement to 5.5.0.

= 0.8.4.1 - 2020.02.11 =

* Fixed bugs that were preventing month-with-sidebar view from showing calendar grid after 0.8.4 update.

= 0.8.4 - 2020.02.09 =

* Improved wrapper CSS classes and associated jQuery to prevent functionality conflicts when multiple calendars are present on the same page. (Follows updates to free version 5.3.0.)
* Improved CSS for widget view; should now display properly in sidebars as narrow as 200 pixels wide, depending on font.
* Updated ICS Calendar requirement to 5.3.0.

= 0.8.3 - 2020.02.03 =

* Fixed bug in widget view that would prevent calendar grid from displaying.
* Fixed bug with "Column Labels" setting that would not properly handle "Default for view" option in widget view.
* Fixed bug in widget and month-with-sidebar views in setting `data-event-count` attribute for months with no events.
* Updated ICS Calendar requirement to version 5.2.9.

= 0.8.2 - 2020.01.27 =

* Added license handling logic with purchase site API integration.

= 0.8.1 - 2020.01.27 =

* Implemented GitLab updater functionality.
* Updated admin notice about automatic deactivation of ACF (free), since we can't (yet) reliably reactivate ACF when ICS Calendar Pro is deactivated.

= 0.8.0 - 2020.01.27 =

* **Unstable test version.**
* Moved project from GitHub to GitLab.

= 0.7.3 - 2020.01.27 =

* Customizer enhancements: font selection and text scaling.
* Removed separate template file for search/filter.
* Additional logic for handling ACF (free). (Needs further refinement.)
* Minor CSS tweaks.

= 0.7.2 - 2020.01.23 =

* Added plugin conflict handling for sites that have Advanced Custom Fields (free) installed.

= 0.7.1 - 2020.01.21 =

* Added support for new `formatmonthyear` parameter.
* Updated ICS Calendar requirement to version 5.2.7.

= 0.7.0 - 2020.01.19 =

* Added admin settings page.
* Added License tab with entry/deletion form on settings page. (Not yet connected to license API.)
* Added Customize tab with link to Customizer on settings page.
* Additional license-handling logic.
* Reorganized admin template file structure to match core plugin version 5.2.6.1.
* Updated ICS Calendar requirement to version 5.2.6.1.

= 0.6.0 - 2020.01.18 =

* Added `showfilter` parameter, which adds a simple filter form to the top of a calendar, allowing users to narrow down the display to events matching their text string.
* Added **Hyphenation Off** option to Customizer to turn off the plugin's automatic hyphenation of long words in event titles and descriptions.
* Revised layout of Saved Calendars editing screen.
* Updated ICS Calendar requirement to version 5.2.6.

= 0.5.2 - 2020.01.09 =

* Added support for new `hidealldayindicator` parameter.
* Replaced all uses of PHP `mktime()` function with `gmmktime()`. See the changelog in ICS Calendar version 5.2.4.1 for more details on this issue.

= 0.5.1 - 2020.01.04 =

* Added support for event description excerpts in Saved Calendars for list and widget views.
* Added support for new toggle handling of event description excerpts.
* Revised layout of Saved Calendars editing screen.
* Refactored R34ICSPro class to remove unused methods.
* Added basic license expiration handling and related admin notices.
* Updated ICS Calendar requirement to version 5.2.2.

= 0.5.0 - 2020.01.01 =

* Added Customizer support for custom colors.
* Updated ICS Calendar requirement to version 5.1.1.

= 0.4.0 - 2019.12.31 =

* Introduced Saved Calendars feature.
* Added embedded Advanced Custom Fields plugin to support Saved Calendars editing interface. (Embedded ACF only loads if not already detected in site installation.)
* Reorganized admin pages.
* Updated ICS Calendar requirement to version 5.0.2.

= 0.3.1 - 2019.12.31 =

* Updated uses of `wp\_date()` function to use timezone property introduced in ICS Calendar version 5.0.1.
* Redesigned widget view.

= 0.3.0 - 2019.12.30 =

* Bumped WordPress requirement to 5.3 and PHP requirement to 7.0.0. WordPress requirement is due to the use of the new `wp\_date()` function to replace `date\_i18n()`.
* Replaced all uses of `date\_i18n()` with `wp\_date()` (if translations are needed for display) or PHP `date()` function (if dates are being formatted for processing only). Currently also using `date()` to format time-only display, due to issues on certain servers with redundant timezone offset adjustments when using `wp_date()`.
* Added guid check on interactions between calendar grid and detail list so multiple widget or month-with-sidebar views can exist on the same page without interfering with each other.
* Fixed some CSS issues with Twenty Twenty theme.
* Refactored r34icspro\_get\_timezone() function.
* Removed timezone offset indicator in grid view because the timezone defined for the overall feed may not actually match the timezone of the majority of events in the feed. (To be revisited in a future update.)

= 0.2.8.1 - 2019.12.18 =

* Completed month step navigation functionality for widget view introduced in version 0.2.8.

= 0.2.8 - 2019.12.17 =

* Updates for compatibility with changes in ICS Calendar 4.7.0. (Requires ICS Calendar 4.7.0 or later.)
* Added new social image.
* Added widget view (basically functional, but jQuery and CSS are incomplete).
* Various JavaScript and CSS updates.

= 0.2.7 - 2019.12.12 =

* Updates for compatibility with changes in ICS Calendar 4.6.2. (Requires ICS Calendar 4.6.2 or later.)

= 0.2.6 - 2019.11.18 =

* Updated grid template to use new logic introduced in ICS Calendar. (Requires ICS Calendar 4.6.0 or later.)
* Began development on "month with sidebar" view.

= 0.2.5 - 2019.10.02 =

* On grid view, made entire date header clickable to expand/collapse rather than just the toggle icon; hid toggle icon on closed state on phone breakpoint because it often overlaps date text.
* Renamed "Shortcode Builder" to "Calendar Builder". (Functionality still not yet implemented.)
* Updated panels in admin screen sidebar.
* Set bare minimum height for event blocks, to prevent first line of text being cut off vertically on very short events (less than 15 minutes).
* Minor CSS visual updates.

= 0.2.4.1 - 2019.09.30 =

* Updated to use new functions added to ICS Calendar. (This version requires ICS Calendar 4.3.1.)
* Fixed issue with event descriptions displaying at all times. (Should only display on hover/tap.)

= 0.2.4 - 2019.09.30 =

* Added time range handling to grid view. (Also increased spacing at top of time grid to account for extension of events that begin before displayed time range.)
* Changed minimum width of event hover state on grid view from 120 pixels to 180 pixels.

= 0.2.3 - 2019.09.27 =

* Updated grid template to use new R34ICS::event\_description\_html() method. (Requires ICS Calendar 4.3.0 or later.)
* Added missing color key below calendar.
* CSS and layout enhancements.

= 0.2.2 - 2019.09.23 =

* Changed handling of event background color to prevent illegible overlapping text on events that start at slightly different times. (Display still does not handle events that start at *very* close times, e.g. less than 15 minutes. For now users are encouraged to increase the `zoom` value if close staggered starts are needed.) **This change requires ICS Calendar v. 4.1.4.**
* Added toggle feature to grid view to focus on a single day.
* Reversed order of description and location in detail view.
* Added stub code for more advanced display of overlapping events. (Not yet implemented.)
* General layout improvements.
* Mobile optimizations.
* Minor refactoring.

= 0.2.1 - 2019.09.22 =

* Added default gray background colors for events in grid view if no custom colors are set.

= 0.2.0 - 2019.09.21 =

* Added `grid` view and related features, including `zoom` and `hours` shortcode parameters.

= 0.1.1 - 2019.09.12 =

* New logo files.
* Initial setup of admin pages and methods to access new hooks in the free plugin.

= 0.1.0 - 2019.09.10 =

* Initial check-in of barebones plugin framework.