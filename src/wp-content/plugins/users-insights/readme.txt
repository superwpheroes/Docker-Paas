=== Users Insights WordPress Plugin ===

- Plugin Name: Users Insights
- Plugin URI: https://usersinsights.com/
- Description: Everything about your WordPress users in one place
- Version: 3.8.2
- Author: Pexeto
- License: GPLv2 or later
- License URI: http://www.gnu.org/licenses/gpl-2.0.html
- Copyright: Pexeto 2019

Users Insights is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.


== Installation ==

1. Upload the users-insights.zip file from the Plugins -> Add New -> Upload Plugin page.
2. Activate the plugin
3. Go to the UsersInsights page to access the Users Insights users table, filters and other functionality.
4. Visit the Features page: https://usersinsights.com/features/ to learn more about all of the Users Insights functionality


== Using the Geolocation Module ==

1. Copy the Geolocation API license key that comes with your purchase - you should have received an email containing the license key. You can also access your API keys from your account page: http://usersinsights.com/account/
2. Go to the Module Options -> Geolocation, click on the "Settings" button of the Geolocation Module and paste the Geolocation API license key into the license field.
3. Activate the license and then Activate the module

== Updating Users Insights ==

1. Go to your account on the UsersInisghts site: http://usersinsights.com/account/ and download the latest version of the plugin
2. Delete the currently installed plugin from the Plugins page of your WordPress installation
3. Upload and activate the latest version of the plugin (you can follow the instructions from the "Installation" section above)


== Changelog ==

3.8.2
- Fixed: MemberPress - in some cases an unexpected fields configuration format causing an error in dashboard

3.8.1
- Fixed: styling issues of some of the inputs in WordPress 5.3
- Fixed: Profile Builder Pro issue when filtering select (multiple) fields where the options contain special characters
- Fixed: bbPress Reply titles not displayed in the user profile section
- Improved the character escaping of the text-based filters
- Other minor improvements and bug fixes

3.8.0
- New: MemberPress integration allowing you to explore and filter the users' membership data and activity, as well as the MemberPress custom/profile fields data
- New: Made the User ID field available as a table column/filter
- General code improvements and minor bug fixes

3.7.1
- Fixed: issue with Gravity Forms introduced in the 3.7 update - when a user has submissions of different forms, only one of the form's entries was displayed (but multiple times)
- Changed the way the Paid Memberships Pro current membership is determined when a user has multiple membership records. Now it is determined based on the last created membership record for the user (by ID), instead of start date, as in some cases PMPro sets identical start date and time to memberships that were created on different dates.
- Prefix the IDs of the post activity in the user profile section to avoid collisions with other activity items. This might affect previously stored visibility/order settings in the user profile.

3.7.0
- New: Introduced a Page Visit Tracking feature, allowing you to see which pages/posts your users visit and filter your users based on their visits
- New: Profile Builder Pro integration, allowing you to list and search your users' Profile Builder Pro fields
- New: Introduced a new advanced filter in the WooCommerce module, called "Placed an order". This filter allows you to search your users based on the orders they have placed by different order criteria. For example, you can now find all users who have placed an order between date X and X, with order status X, including product X and order value between X and X.
- New: Introduced a new advanced filter in the WooCommerce Subscriptions module, called "Has a subscription". This filter allows you to search your users based on different subscription criteria, such as Start date, End date, Status and Product.
- New: Introduced a User Profile field management, allowing you to hide and reorder the profile fields and user activity, as well as add section titles.
- New: Introduced a "Drop-down select" custom field type, allowing you to specify a set of options to choose as a value for the field
- Redesign of the User Profile section
- Added a "Clear all" button to the filters section, when there are two or more filters applied, allowing you to remove all of the filters
- WooCommerce Subscriptions: for each subscription listed in the user profile, also include the start date, end date, next payment date and a link to the related orders
- WooCommerce Subscriptions: removed the "Is subscribed to" filter, as this can be now replaced with the new "Has a subscription" with "Product X AND Status active"
- WooCommerce Subscriptions: changed the way the "Next payment" field data is retrieved for consistency with the WooCommerce Subscriptions table data: now it also shows past dates for the active subscriptions
- LeranDash: Renamed the "Has/has not enrolled in course" in filter to "Has/has not engaged in course", to be more clear that it actually shows the users who had some activity in the course and not the ones who have access to the course but haven't started it yet
- LearnDash: Separated the "Courses" user activity in user profile into two different lists - "Course Activity" showing progress on all courses that the user has ever engaged in (regardless of whether the user currently has access to the course) and "Course Access" listing all courses that the user has access to (regardless of activity)
- Privacy: added an option to export and erase page visits (from the Page Visit Tracking module) upon user request 
- Privacy: added a suggested text to the Privacy Policy suggestions related with the Page Visit Tracking functionality
- Changed the username link in the user table to be an actual link, instead of attaching a click event to open the user profile. This allows opening the user profile in a new tab.
- User profile: Disable zoom on scroll on the map, as very often the scroll is intended to scroll down the page
- Allow line breaks in note content (notes are no longer created when the Enter key is pressed)
- Fixed: With numeric user meta fields when the value is empty and stored as an empty string, a filter like "smaller than X" or "equals 0" returns those fields
- Fixed: WooCommerce Ordered products filter shows users who have a Subscription with the selected product, but not an actual order that contains the product
- Renamed the "is bigger than" and "is smaller than" numeric operators to "is greater than" and "is less than" respectively
- Various code/style improvements and other minor bug fixes


3.6.6
- New: WooCommerce module - added Billing country, Billing state and Billing city fields and filters
- New: BuddyPress module - added an Active Users report, displaying the number of users who have any kind of BuddyPress activity recorded, supporting daily, weekly, monthly and yearly periods
- New: Ultimate Member module - added an Account status field and filter
- Improved: BuddyPress module - performance optimisation of the way the xProfile fields are loaded in the user profile page
- Learndash module: added a CSS class to the score progress bar, setting the exact score, so that the styles of the separate values can be customized if needed
- Fixed: BuddyPress module - checkboxes and multi-select boxes fields reports not displayed, due to a code change from a previous update
- General code improvements and minor bug fixes

3.6.5
- Easy Digital Downloads reports: Support the Software Licensing 3.6 database structure changes

3.6.4
- Privacy Policy suggestions: Updated the suggested text when the Geolocation module is active to include more details about how the geolocation works, such as how the data is processed and stored
- Removed the usage of the wp_doing_ajax() function on the reports page, to support older WordPress versions

3.6.3
- Implemented GDPR tools available when running WordPress 4.9.6 or newer:
    - Tools to export the Users Insights data when using the WordPress 4.9.6 Personal Data Exporter
    - Tools to remove the Users Insights data when using the WordPress 4.9.6 Personal Data Eraser
    - Regsiters a Privacy module in Module Options where the settings can be configured
    - Suggests texts to add to the WordPress Privacy Policy page
    - More info: https://usersinsights.com/gdpr/
- Moved the Last Seen and Sessions fields to a separate "Activity" module that can be deactivated if needed. This module will be inactive on new installations by default
- Changed the way Geolocation and Device Info are detected, so it also works with the "Activity" module deactivated (it used to depend on the last seen date)
- Make the user table ordered by Date Registered by default when the Activity module is inactive
- Activity module: Increased the minimum time of inactivity required to one hour in order to consider a new user visit as a new session
- Removed the functionality that copies the BuddyPress last login date to the Users Insights last seen field upon module activation
- Fixed: Support the Gravity Forms 2.3 database table name changes
- General code improvements

3.6.2
- Improved: implemented autoload for the plugin files
- Improved: Ultimate Member - make fields available on the Users Insights table based on their privacy settings
- Fixed: Ultimate Member - bug with filtering by an option field when the option has a trailing space
- General code improvements and minor bug fixes

3.6.1
- New: WooCommerce module - added support for the YITH Wishlist and WooCommerce Wishlist plugins, allowing to filter users based on the products that they have in wishlist, as well as explore the individual user wishlists in the user profile section
- New: Easy Digital Downloads module - introduced an "Earnings" report
- New: Event Tickets module - add support for the new PayPal ticket sales functionality, allowing to filter users based on the tickets purchased
- New: WooCommerce subscriptions - introduced a filter allowing to segment the users based on the subscription product that they are subscribed to
- Improved: BuddyPress module - provide a dropdown of the available options when filtering a checkboxes or multiselect field
- Improved: Ultimate Member module - make the 10-star based rating reports show each rating in a separate bar, instead of combining them into ranges
- Fixed: BuddyPress module - do not show unconfirmed group users when filtering by BuddyPress group
- General improvements and minor bug fixes


3.6.0
- New: Events Calendar integration, detecting the data from the Events Calendar and its Events Tickets & Events Tickets Plus extensions
- General code improvements and minor bug fixes

3.5.1
- Optimized the loading of the WooCommerce First Order field - it is now loaded as part of the main query only when used in the filters or the table is sorted by it.

3.5.0
- New: Introduced integration for the Paid Memberships Pro plugin
- New: WooCommerce features:
    - First Order date field & filter in the user table
    - Total Sales Amount report
    - List WooCommerce coupons used in the user profile section
    - Added a link in the WooCommerce order screen linking to the Users Insights profile page of the customer 
    - WooCommerce Memberships: Ended Memberships report
    - WooCommerce Memberships: displayed the cancelled date of the membership (when available) in the user profile section
- Improved: Allow floating labels in the reports that represent amounts
- Improved: Enable AJAX search in the WooCommerce products filter when there is a large number of products available
- Improved: Icons style
- Fixed: Filtering by role issue when there are roles that contain the filtered role as part of their name
- Fixed: Issue with removing expired licenses


3.4.1
- Fixed: empty error message displayed in the Create Segment dialog

3.4.0
- New: Introduced Reports (beta) for most of the modules - now available under Users Insights -> Reports
- WooCommerce module: show order total price in the user profile order list section
- EDD module: show order total price in the user profile order list section
- General code improvements and optimizations

3.3.1
- Fixed: WooCommerce review stars not displayed in user profile section (since 3.3.0 update)
- Fixed: Overflow issue of the custom fields table

3.3.0
- New: Introduced Segments - you can now save your frequently used filters as segments and easily apply them later
- Introduced compatibility with the upcoming Ultimate Member 2.0
- Fixed: Column ordering from the eye icon menu sometimes doesn't work properly
- Fixed: Do not show the bulk action button if the current user is not allowed to update users
- Fixed: Updated the Browser library to fix a PHP7 deprecation notice & detect the Edge browser
- General code improvements

3.2.0
- New: WooCommerce "Has used coupon" filter, showing all customers that have used a selected coupon/discount code
- New: WooCommerce number of reviews column & filter, showing the number of product reviews that each customer has left
- New: List WooCommerce reviews in the user profile page
- New: LearnDash "Has/has not enrolled to course" filters, showing all the users that have/have not enrolled to a particular course, regardless of whether they have completed it or not
- New: LearnDash Number of courses in progress column & filter, showing the number of courses that each user has started but not completed
- New: Added First Name and Last Name as separate columns
- Improved the way the roles are displayed on the table - lists all the role names assigned to the user
- WooCommerce query optimizations - improved the way the Number of Orders, Last Order and Lifetime Value columns are loaded on the table, especially when the table is not sorted by any of these fields


3.1.1
- Fixed: Alignment issue in the user table footer
- Improved: Allow HTML data in the user table
- General code improvements

3.1.0
- Added: Bulk add/remove group functionality
- Improved: The way the WooCommerce Lifetime Value data is loaded - since WooCommerce doesn't always
update this value correctly (it is sometimes set to null), instead of using the WooCommerce value,
we now compute it in the database query
- Improved: General UI improvements of the checkboxes and the dialogs
- Fixed: Compatibility issues with the upcoming WooCommerce 2.7
- General code improvements


3.0.0
- Added: LearnDash Module - detects the LearnDash user activity and makes it available in the user table and filters
- Added: Icons to the user activity list in the user profile section
- Added: A refresh button in the license section of the Module Options page, allowing to refresh the license status
- Fixed: WooCommerce Memberships - cannot filter by membership status when there are no columns from the memberships module visible on the table
- General code improvements

2.9.0
- Added: WooCommerce Memberships module (beta) - retrieves and displays the user data from the WooCommerce Memberships extension
- Added: Next Payment field to the WooCommerce Subscriptions module
- Improved: The style of the elements like EDD & WooCommerce orders in the user profile section
- General code improvements

2.8.0
- Added: WooCommerce Subscriptions module (beta) - retrieves and displays the WooCommerce Subscriptions extension user data, such as number of subscriptions and subscription status
- Added: WooCommerce Lifetime Value field, showing the total amount spent by each user
- Minor bug fixes

2.7.0
- Improved: Introduced custom capabilities for accessing the Users Insights page, managing groups & custom fields and managing options
- Improved: The maps design in the map view and user profile sections
- Improved: The design of the filters - added a search to the option list when it's too long and added icons to the fields to improve the visibility
- Added: Option to filter BuddyPress users by the groups that they belong/don't belong to
- Added: "View Ultimate Member Profile" button in the user profile section
- Added: A read-only date type for the custom fields section. This field can be used to retrieve already stored user meta from a date type. The filters will provide date-based operators and also the table will allow sorting by this field in a chronological order.
- Improved: Replaced the year/month/day selects with a date picker
- General code and design improvements - better dialogs, tooltips on the action buttons, etc.

2.6.1
- Fixed: bug with the date filters

2.6.0
- Replaced Google maps with Leaflet maps (http://leafletjs.com/). Using map tiles by OpenStreetMap contributors(http://www.openstreetmap.org/copyright) and map layers by Stamen Design (http://stamen.com/)
- General code improvements

2.5.0:
- Added: "Is set" and "Is not set" operators for the option fields in the filters
- Added: support for the User Tags extension of Ultimate Member
- Improved: Query optimizations for the single user profile section when a large number of custom fields are registered
- Improved: Ultimate Member Module - provide a drop-down with the available options for the multi-option and checkbox fields in the filters
- Fixed: Issue with the available year range when filtering by a date field
- Fixed: Ultimate Member Module - add support for radio fields that store the data in a PHP serialized format
- Code improvements and minor bug fixes

2.4.2:
- Fixed: Issue with the database query when using special characters
- Added: A debug page that can be helpful to troubleshoot issues

2.4.1:
- Fixed: User table not loading when the Gravity Forms & User Registration Add-on are active, but the Gravity Forms Module of Users Insights is inactive

2.4.0:
- Added: Gravity Forms Module - Provides Gravity Forms related filters and data. Detects and displays the custom user data saved with the Gravity Forms User Registration Add-on.
- Added: New multi-option filter type that works like the text type, but only searches strings for a query - it doesn't include string options like "starts with" or "ends with", as usually those options are saved as serialized or JSON data
- Improved: BuddyPress & Ultimate Member: for performance and usability reasons make the custom user profile fields hidden on the table by default, so that when there are too many fields registered they won't be all displayed on the table
- General code improvements


2.3.0
- Added: BuddyPress Module - automatically detects and displays the custom user profile fields in the user table
- Fixed: Saved year not selected when editing a date filter and date not reset properly when changing the field to filter by option

2.2.0
- Added: Ultimate Member Module - automatically detects and displays the custom user fields data generated with the Ultimate Member forms
- Added: Option to change the default columns order in the Users Insights table
- Added: Option to set the year range for the date fields filters
- Improved: General design and responsive layout improvements
- Fixed: WP 4.5 compatibility issue - color options not displayed when editing a group

2.1.0
- Added: automatic plugin updates from the dashboard. Added a Users Insights License section in the Module Options page that allows adding one global license for both the geolocation and automatic updates
- Improved: Query Optimizations - major refactoring to optimize the query in the users table and the export
- Improved: Design improvements on the modules page
- Fixed: issues with the BuddyPress module in some cases on multi-site
- Fixed: issue with filtering users by role in some cases
- Fixed: issue with the bbPress query when the table includes a left join that returns more than one row per user (e.g. applying a filter "group is set" and the user has more than one group set)
- Fixed: EDD filtering by product ordered not working in some cases
- Fixed: cannot remove all the assigned groups from a user
- Fixed: BuddyPress Groups Created list not displayed in the user profile section on multisite


2.0.1
- Made the CRM features (groups, notes and custom fields) more customizable - added hooks that can be used to change some of their options and functionality from 3rd party plugins_url
- Fixed: Empty map element displayed on the user profile section when the user has a location saved, but the geolocation module is disabled
- Fixed: WooCommerce module - exclude trashed orders from the orders column
- Fixed: Issue with editing a custom field value from the user profile - when the field is a number field and the value of the field is deleted, it shows "null" instead of an empty value
- Fixed: Filtering by role not showing any results
- Improved: The geolocation lookup functionality
- General code improvements

2.0.0
- Added CRM Features, such as:
- Added an option to assign groups to users
- Added notes section where you can add notes for each user
- Added custom user meta fields - added an interface to register custom fields and after the fields are registered, they are available in the users table and filters and they can be updated in the user profile section
- General code improvements and minor bug fixes

1.1.1
- Improved: EDD Module - changed the URL of the View Orders link in the profile section to open the default EDD Payments page filtered by the selected customer (rather than Users Insights generating the payments info)
- Improved: EDD + Geolocation Module - Run the check to save location on purchase confirmation
- Improved: EDD Module - general DB query improvements: made the query joins use the EDD customer ID, instead of relying that the customer will be an author of the payment post
- Fixed: EDD Module - issue with the Lifetime Value filter


1.1.0
- Added: Easy Digital Downloads support, included as a separate module, it retrieves and displays data from the Easy Digital Downloads orders made by the WordPress users
- Fixed: WP 4.4 issue - the line height of the number inputs in the filters section is too big
- Fixed: issue with columns that are casted - apply the casting when ordering by the column as well
- General code improvements and minor bug fixes
