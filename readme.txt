=== Nivoda API Integration ===
Contributors: talentedamrut
Tags: nivoda, diamonds, api, e-commerce, inventory
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional WordPress plugin for integrating Nivoda GraphQL API - search diamonds, display inventory, and manage jewelry products.

== Description ==

The Nivoda API Integration plugin provides a complete integration with the Nivoda GraphQL API, enabling WordPress websites to search and display diamond inventory with advanced filtering capabilities.

= Features =

* Advanced diamond search with multiple filters
* Responsive and mobile-friendly design
* Easy-to-use shortcodes
* REST API endpoints for developers
* Admin dashboard for settings and search
* Support for images and 360° videos
* Order creation capabilities
* Hold management features

= Shortcode Usage =

`[nivoda_search]` - Display complete diamond search interface
`[nivoda_search shapes="ROUND,PRINCESS" labgrown="false" limit="20"]` - Display filtered search

= API Endpoints =

* GET /wp-json/nivoda/v1/search - Search diamonds
* POST /wp-json/nivoda/v1/order - Create order (requires auth)
* POST /wp-json/nivoda/v1/hold - Create hold (requires auth)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/nivoda-api-integration`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Nivoda API > Settings to configure your credentials
4. Test your connection
5. Use shortcodes or REST API to display diamonds

== Frequently Asked Questions ==

= Do I need a Nivoda account? =

Yes, you need an active Nivoda account with API access. You can sign up at Nivoda.net for diamond inventory access.

= How do I get API credentials? =

For production, use your Nivoda platform credentials. For staging/testing, contact tech@nivoda.net for access.

= Can I customize the design? =

Yes, you can override the plugin CSS with your theme's custom styles. All elements have CSS classes for easy customization.

= Is this plugin free? =

Yes, this plugin is completely free and open source under GPL v2+ license.

= What are the system requirements? =

* WordPress 5.8 or higher
* PHP 7.4 or higher  
* Active internet connection for API access
* Valid Nivoda API credentials

= Does this work with my theme? =

Yes, the plugin is designed to work with any properly coded WordPress theme. It uses standard WordPress hooks and filters.

= Can I use this on multisite? =

The plugin works on multisite installations but requires individual configuration per site.

== Screenshots ==

1. Admin settings page for API configuration and testing
2. Diamond search interface with advanced filters
3. Search results with responsive grid layout and diamond details
4. Admin search page for backend diamond management

== Changelog ==

= 1.0.0 =
* Initial release
* Diamond search functionality with advanced filters
* REST API endpoints for integration
* Admin settings and search interface
* Shortcode support for frontend display
* Order and hold creation capabilities
* Mobile-responsive design
* WordPress 6.4 compatibility

== Upgrade Notice ==

= 1.0.0 =
Initial release of the Nivoda API Integration plugin.

== Documentation ==

For complete documentation, visit: https://bitbucket.org/nivoda/nivoda-api/

For support, contact: talented.amrut@gmail.com
