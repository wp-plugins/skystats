=== SkyStats ===
Contributors: SkyStats
URL: https://skystats.com
Tags: skystats, stats, analytics, insights, dashboard, mashboard, google analytics, facebook, twitter, google+, youtube, mailchimp, linkedin, campaign monitor, paypal
Requires at least: 3.0
Tested up to: 4.2
Stable tag: 0.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Better WordPress Dashboard

== Description ==

SkyStats is an all-in-one business dashboard for WordPress. The SkyStats plugin will allow you to customize your WordPress dashboard by seamlessly integrating such business data as web analytics, social media, paid search, email, video, and other marketing channels into a single view.

= Features =

* CUSTOMIZABLE MASHBOARD - Customize your overview screen to see the data that is most important to you.

* TRACK PERFORMANCE - Never wonder again if your online marketing efforts are making a positive impact.

* LEADING INTEGRATIONS - SkyStats integrates with the tools you already use and our list of integrations is growing!

* INNOVATIVE DESIGN - We believe design matters, even when crunching statistics & numbers.

* USER ROLES - Decide who gets to view SkyStats data and who doesn’t with user permissions.

== Installation ==

1. From your WordPress dashboard go to Plugins -> Add New -> Upload Plugin and select the `skystats.zip` file from your computer.
2. Activate the plugin through the 'Plugins' menu in your WordPress administration dashboard.
3. A SkyStats menu will appear at the top left of your WordPress dashboard menu.
4. Go to SkyStats -> Settings, enter your license key and click `validate` (if you don't have a license key, visit https://skystats.com/#pricing to purchase a free, or premium, license key).
5. If everything worked successfully, you'll then be presented with a link to be able to activate your desired integrations.

== Frequently Asked Questions ==

= Why do I need a SkyStats license key? =

* In order to use this plugin, you have to provide a valid SkyStats license key. That's currently the only way you can use SkyStats.

== Screenshots ==

1. Mashboard Overview
2. Google Analytics Detail Page
3. Facebook Detail Page
4. Settings Page

== Changelog ==

= 0.2.3 - 15th April 2015 =
* [BUG] Fixed issue where you couldn't update the plugin if you're using a free license key.
* [CHANGE] Date range notice will now include a link to the pricing section on the SkyStats marketing website.
* [CHANGE] Plugin name renamed to "SkyStats Lite" on the plugins page.

= 0.2.2 - 14th April 2015 =
* [CHANGE] Only the value for the period is now shown on the chart point tooltips.
* [BUG] Fixed bug where the bounce rate percentage change symbol was missing. These will now show up correctly, most importantly to be compatible with Safari 4, 5, and 5.1

= 0.2.1 - 13th April 2015 =
* [CHANGE] Chart points are now filled in.
* [BUG] Fixed bug with Flot tooltip plugin causing Internet Explorer 8 to crash.
* [BUG] Fixed bug where search engine visits would not show correctly on the Mashboard for IE > 8 & Safari 4 and upward (confirmed).
* [BUG] Fixed bug where certain data point tooltips were not being displayed for Safari browser versions 4, 5, and 5.1.
* [BUG] Fixed bug where the loading icon scale was incorrect on Safari browser versions 4, 5, and 5.1.

= 0.2.0 - 10th April 2015 =
* [CHANGE] Utilising new chart library, Flot.

= 0.1.9 =
* [BUG] Fixed missing change direction icon(s) for Google Analytics Users data point.

= 0.1.8 =
* [BUG] Fixed script data handling for WP versions < 3.4 which don't support multidimensional arrays.
* [CHANGE] Improved menu icon for WP versions < 3.8.
* [CHANGE] Chart markers will no longer be cut-off.
* [CHANGE] Chart scale will always begin at zero.
* [CHANGE] Start and end date will always be displayed on the chart(s).
* [CHANGE] Amount of markers on the chart(s) will now always be the same for the Mashboard or where there is more than one chart displayed on the same page.

= 0.1.7 =
* [BUG] Fixes error when trying to view the updated changelog when an update is available for the plugin (versions 0.1.4 - 0.1.6 affected).

= 0.1.6 =
* [BUG] Fixed caching issue when there is an error fetching data for any integration and a cached version of the profiles or pages is available.
* [TYPO] Number of votes on the Mashboard will now correctly be described. "0 votes", "1 vote", etc, instead of "0 vote".

= 0.1.5 =
* [BUG] Fixed loading icon rendering issue for Google Analytics integration.
* [FEATURE] The number of votes each coming soon integration currently has will be displayed on the mashboard.
* [CHANGE] When a license has expired a link will be provided allowing the user to renew their license, or visit their settings to enter a different one.

= 0.1.4 =
* Lots of code improvements and optimizations.
* Grid and settings icons added to the integration detail pages to allow you to show and hide data points on the detail pages as well as authorize, select a profile/page, reauthorize, and deauthorize, without having to do this through the mashboard alone.
* .pot translation file added to the plugin (in the root folder) so you can now translate the plugin.
* You can now use a background color instead of a background image if you wish by leaving the background image box blank and just selecting a color.
* New helpful messages added when you're trying to activate or validate your license and collect data for your profiles and pages.

= 0.1.3 =
* Added notice to Google Analytics profile selection description to notify users that if they see a profile named "All Website Data", they can change this from within Google Analytics to make it easier to select a profile.
* SkyStats has been tested up to the latest WordPress version of 4.1.1.

= 0.1.2 =
* Twitter integration now available. See your stats in real time including your tweets, number of followers, number of users you're following, number of favourites you've made, the number of retweets on your tweets by other users and how many times you have been mentioned in a tweet. You can also see your top latest tweets and top latest favourites. You can also see the latest reweets of your tweets by popularity and the latest mentions of your screen name by popularity. Historical data is currently not made available by Twitter, but stay tuned.
* Font size of data point values decreased.
* Settings link added to plugin row on plugins page.

= 0.1.1 =
* Fixes feedback button not staying fixed on the bottom left of the window when scrolling.

= 0.1.0 =
* Fixes undefined offset notices when in WP_DEBUG mode or when a sufficient error reporting directive is set, caused by a bug in the WordPress core that doesn't check whether a index exists before trying to access it.
* Feedback/Support form now integrates with our UserVoice system to allow users to easily post their feedback (as well as an optional screenshot), and offers the ability to suggest new ideas and vote on current ones.

= 0.0.9 =
* Fixed missing error messages when a Facebook or Google account does not have any pages or profiles to manage.

= 0.0.8 =
* Fixed remaining resources which were incompatible with requests loaded using HTTPS protocol.

= 0.0.7 =
* Fixed compatibility issues with installations using the Easy Digital Downloads software licensing extension's automatic plugin updater.

= 0.0.6 =
* Top 5 metrics percentages on Google Analytics detail page changed to display percentage out of total amount of sessions during the selected period.
* Google Analytics search engine visits metric changed to display number of sessions instead of page views.
* Minor optimizations.

= 0.0.5 =
* Activation message will be displayed whenever the plugin is activated, including a link to go to the settings page.
* Message will be displayed if a Google or Facebook account does not have access to any content to collect data for, and will be given the
option to deauthorize and reauthorize using an account that does.
* All WP error messages that can be displayed on the admin dashboard should now be removed on any SkyStats pages.
* Background color fix for accordion contents on the settings page for WP installations using the WP Mobile Pack plugin (2.1.1 confirmed).

= 0.0.4 =
* Fixed automatic plugin update issues.
* Various typos fixed.

= 0.0.3 =
* Automatic plugin updater added to the plugin.
* Now using one loading icon for the chart and data points on the mashboard.

= 0.0.2 =
* Info icons added next to data point percentage change to show that percentages are calculated by comparing the selected period against
the previous period using the same amount of days.
* Minor bug fixes and optimizations.

= 0.0.1 =
* Alpha release.