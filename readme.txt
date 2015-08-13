=== miniOrange SAML 2.0 Single Sign On ===
Contributors: miniOrange
Donate link: http://miniorange.com
Tags: single sign on, SSO, single sign on saml, sso saml, sso integration WordPress, sso using SAML, SAML 2.0 Service Provider, Wordpress SAML, SAML Single Sign-On, SSO using SAML, SAML 2.0, SAML 20, Wordpress SSO to another Site.
Requires at least: 2.5.0
Tested up to: 4.2.4
Stable tag: 3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

miniOrange SAML 2.0 SSO provides Single Sign on to your Wordpress site with any SAML supported Identity Provider.

== Description ==

miniOrange SAML 2.0 SSO allows users residing at SAML 2.0 Compliant Identity Provider to login to your Wordpress website. 

miniOrange SAML SSO Plugin acts as a SAML 2.0 Service Provide which can be configured to establish the trust between the plugin and various SAML 2.0 supported Identity Providers to securely authenticate the user to the Wordpress site.

If you require any Single Sign On application or need any help with installing this plugin, please feel free to email us at info@miniorange.com or <a href="http://miniorange.com/contact">Contact us</a>.

= Features :- =

*	Login to your Wordpress site using SAML 2.0 Compliant Identity Providers.
*   Easily Configure the Identity Provider by providing just the SAML login URL, IDP Entity ID and Certificate.
*	Valid user registrations verified by the plugin.
*	Easily integrate the login link with your Wordpress site using widgets/short code. Just drop it in a desirable place in your site.
*	Automatic user registration after login if the user is not already registered with your site.
*	Use the Attribute Mapping feature to map wordpress user profile attributes to your IdP attributes.
*	Use the Role Mapping feature to assign roles in your IdP to your wordpress users during auto registration.
*	Auto redirect users to your IdP for authentication without showing them your site's login page.
* 	Supports plethora of SAML 2.0 Identity Providers like ADFS, Shibboleth, SimpleSAMLphp, Okta, OpenAM, etc.

= Website - =
Check out our website for other plugins <a href="http://miniorange.com/plugins" >http://miniorange.com/plugins</a> or <a href="https://wordpress.org/plugins/search.php?q=miniorange" >click here</a> to see all our listed WordPress plugins.
For more support or info email us at info@miniorange.com or <a href="http://miniorange.com/contact" >Contact us</a>. You can also submit your query from plugin's configuration page.

== Installation ==

= From your WordPress dashboard =
1. Visit `Plugins > Add New`.
2. Search for `miniOrange - SAML 2.0 Single Sign-On`. Find and Install `miniOrange - SAML 2.0 Single Sign-On`.
3. Activate the plugin from your Plugins page.

= From WordPress.org =
1. Download miniOrange - SAML 2.0 Single Sign-On.
2. Unzip and upload the `miniorange-login-saml-service-provider` directory to your `/wp-content/plugins/` directory.
3. Activate miniOrange SAML 2.0 Single Sign-On from your Plugins page.

= Once Activated =
1. Go to `miniOrange SAML 2.0 SSO` from side menu and follow the instructions.
2. Copy-paste the short code (available in plugin) to your webpage OR go to `Appearance->Widgets`,in available widgets you will find `Login with <YOUR_IdP>` widget, drag it to chosen widget area where you want it to appear.
3. Now visit your site/webpage and you will see login-with link of your IdP.

= Settings =
1. Provide the plugin with following Identity Provider settings : Identity Provider Name, SAML Login URL, IdP Entity ID, X.509 Certificate of the IdP.
2. Configure your Identity Provider with the provided details in the plugin.

== Frequently Asked Questions ==

= I am not able to configure the Identity Provider with the provided settings =
Please email us at info@miniorange.com or <a href="http://miniorange.com/contact" >Contact us</a>. You can also submit your app request from plugin's configuration page.

= I don't see any settings to add the Identity Provider to enable. I only see Register with miniOrange? =
Our very simple and easy registration lets you register with miniOrange. SAML 2.0 SSO with Identity Provider login works if you are connected to miniOrange. 
Once you have registered with a valid email-address and phone number, you will be able to add your Identity Provider.

= For any other query/problem/request =
Visit Help & FAQ section in the plugin OR email us at info@miniorange.com or <a href="http://miniorange.com/contact">Contact us</a>. You can also submit your query from plugin's configuration page.

== Screenshots ==

1. General settings like auto redirect user to your IdP.
2. Guide to configure your Wordpress site as Service Provider to your IdP.
3. Configure your IdP in your Wordpress site.

== Changelog ==

= 2.0 =
Added new feature like role mapping and auto redirect user to your IdP.

= 1.7.0 =
Resolved UI issues for some users

= 1.6.0 =
Added help and troubleshooting guide.

= 1.5.0 =
Added error messaging.

= 1.4.0 =
Added fixes.

= 1.3.0 =
Added validations and fixes.
UI Improvements.

= 1.2.0 =
* this is the third release.

= 1.1.0 =
* this is the second release.

= 1.0.0 =
* this is the first release.

== Upgrade Notice ==

= 2.0 =
Added new feature like role mapping and auto redirect user to your IdP.

= 1.7 =
Resolved UI issues for some users

= 1.6 =
Added help and troubleshooting guide.

= 1.5 =
Added error messaging.

= 1.4 =
Added fixes.

= 1.3 =
Added validations and fixes.
UI Improvements.

= 1.2 =
Some UI improvements.

= 1.1 =
Added Attribute mapping / Role mapping and test application.

= 1.0 =
I will update this plugin when ever it is required.
