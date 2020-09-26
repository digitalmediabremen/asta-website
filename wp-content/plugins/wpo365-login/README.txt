=== WordPress + Microsoft Office 365 / Azure AD | LOGIN ===
Contributors: wpo365
Tags: office 365, O365, Microsoft 365, azure active directory, Azure AD, AAD, authentication, single sign-on, sso, SAML, SAML 2.0, OpenID Connect, OIDC, login, oauth, microsoft, microsoft graph, teams, microsoft teams, sharepoint online, sharepoint, spo, onedrive, SCIM, User synchronization, yammer, powerbi, power bi,
Requires at least: 4.8.1
Tested up to: 5.5
Stable tag: 11.5
Requires PHP: 5.6.40
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Microsoft Office 365 / Azure AD based single sign-on so users can seamlessly and securely log on to your WordPress intranet, extranet or internet website.

= Plugin Features =

== WPO365 | LOGIN (free) ==

- **Single sign-on (SSO)** for Microsoft Office 365 / Azure AD accounts [more](https://www.wpo365.com/sso-for-office-365-azure-ad-user/)
- Administrators can choose between **OpenID Connect** and **SAML** based single sign-on (SSO) [more](https://docs.wpo365.com/article/100-single-sign-on-with-saml-2-0-for-wordpress)
- New users that *Sign in with Microsoft* are **automatically registered** with your WordPress [more](https://www.wpo365.com/sso-for-office-365-azure-ad-user/)
- Restrict access to pages / posts in **intranet** mode [more](https://www.wpo365.com/make-your-wordpress-intranet-private/)
- Support for integration of your WordPress website into **Microsoft Teams** Tabs and Apps [more](https://docs.wpo365.com/article/70-adding-a-wordpress-tab-to-microsoft-teams-and-use-single-sign-on)
- Support for **WordPress Multisite** [more](https://www.wpo365.com/support-for-wordpress-multisite-networks/)
- Client-side solutions can request access tokens e.g. for SharePoint Online and Microsoft Graph [more](https://www.wpo365.com/pintra-fx/)
- Authors can inject Pintra Framework apps into any page or post using a simple WordPress shortcode [more](https://www.wpo365.com/pintra-fx/)
- Developers can include a simple and robust API from [npm](https://www.npmjs.com/package/pintra-fx) [more](https://www.wpo365.com/pintra-fx/)
- **PHP hooks** for developers to build custom Microsoft Graph / Office 365 integrations [more](https://docs.wpo365.com/article/82-developer-hooks)

Now all editions of the plugin include four new modern Microsoft (Office) 365 apps

- Embed Microsoft **Power BI** content [more](https://www.wpo365.com/power-bi-for-wordpress/)
- **SharePoint Online** Library [more](https://www.wpo365.com/documents/) 
- **Microsoft Graph / Azure AD** based Employee Directory [more](https://www.wpo365.com/employee-directory/)
- **SharePoint Online** Search [more](https://www.wpo365.com/content-by-search/)


== WPO365 | PROFILE+ ==

- **All features of the LOGIN edition, plus ...**
- Complete the WordPress user profile with first, last and full name and email address [more](https://www.wpo365.com/sso-for-office-365-azure-ad-user/)

== WPO365 | LOGIN+ ==

- **All features of the PROFILE+ edition, plus ...**
- Let users choose to login with O365 or with WordPress [more](https://www.wpo365.com/redirect-to-login/)
- Require authentication for only a few **Private pages** [more](https://www.wpo365.com/private-pages/)
- Require authentication for all pages but not for the **Public homepage** [more](https://www.wpo365.com/public-homepage/)
- Redirect users to a **custom login error** page [more](https://www.wpo365.com/error-page/)
- Allow users from other Office 365 tenants to register (**Multitenant**) [more](https://www.wpo365.com/automatically-register-new-users-from-other-tenants/)
- Allow users with a Microsoft Services Account (**MSAL**) e.g. outlook.com to register (extranet) [more](https://www.wpo365.com/automatically-register-new-users-with-msal-accounts/)
- Prevent Office 365 user from changing their WordPress password and / or email address [more](https://www.wpo365.com/prevent-update-email-address-and-password/)
- Intercept manual login attempts for Office 365 users [more](https://www.wpo365.com/intercept-manual-login/)
- **Sign out from Microsoft Office 365** when signin out from your website [more](https://www.wpo365.com/intercept-manual-login/)
- Support for **single sign-out** [more](https://docs.wpo365.com/article/90-single-sign-out)

== WPO365 | SYNC ==

- **All features of the LOGIN+ edition, plus ...**
- (On-demand and scheduled) **User synchronization** from Azure Active Directory to WordPress (per user or in batches) [more](https://www.wpo365.com/synchronize-users-between-office-365-and-wordpress/)
- **Delete / de-activate** WordPress users without a matching Azure AD account [more](https://www.wpo365.com/synchronize-users-between-office-365-and-wordpress/)
- Dynamically assign **WordPress user role(s)** based on Azure AD group membership(s) [more](https://www.wpo365.com/role-based-access-using-azure-ad-groups/)
- Dynamically assign **WordPress user role(s)** based on Azure AD User properties [more](https://www.wpo365.com/role-based-access-using-azure-ad-user-properties/)
- Dynamically assign **itthinx Groups** based on Azure AD group membership(s) [more](https://www.wpo365.com/role-based-access-using-azure-ad-groups/)
- Dynamically assign **itthinx Groups** based on Azure AD User properties [more](https://www.wpo365.com/assign-itthinx-groups-based-on-azure-ad-user-properties/)
- Synchronize **WordPress and / or BuddyPress user profiles** with Azure AD e.g. job title, department and mobile phone [more](https://www.wpo365.com/extra-buddypress-profile-fields-from-azure-ad/)
- Replace a user's default **WordPress avatar** with a profile image downloaded from Office 365 [more](https://www.wpo365.com/office-365-profile-picture-as-wp-avatar/)
- **Azure AD group membership(s) based access** (and deny all others) [more](https://www.wpo365.com/role-based-access-using-azure-ad-groups/)
- Place a customizable **Sign in with Microsoft** link on a post, page or theme using a simple shortcode [more](https://www.wpo365.com/authentication-shortcode/)

== WPO365 | INTRANET ==

- **All features of the SYNC edition, plus ...**
- Support for Azure AD User provisioning (**SCIM**) [more](https://docs.wpo365.com/article/59-wordpress-user-provisioning-with-azure-ad-scim)
- Advanced versions of the INTRANET apps that can be customized using **Handlebars.js templates** [more](https://www.wpo365.com/working-with-handlebars-templates/)
- **SharePoint Online / OneDrive Library** with support for folder and breadcrumb navigation [more](https://www.wpo365.com/documents/)
- Recently used documents [more](https://www.wpo365.com/documents/)
- **SharePoint Online Search** with support for query templates, auto-search, templates and [more](https://www.wpo365.com/content-by-search/)
- Employee Directory with a builtin **interactive clickable org(anizational) chart** incl. support for user profile images and additional fields (Microsoft Graph / Azure AD) [more](https://www.wpo365.com/employee-directory/)
- Microsoft **Power BI** [more](https://www.wpo365.com/power-bi-for-wordpress/)
- **Yammer** feed(s) [more](https://www.wpo365.com/yammer-for-wordpress/)

https://youtu.be/ZkK-hzAIARo

= Prerequisites =

- Make sure that you have disabled caching for your Website in case your website is an intranet and access to WP Admin and all pubished pages and posts requires authentication. With caching enabled, the plugin may not work as expected
- We have tested our plugin with Wordpress >= 4.8.1 and PHP >= 5.6.40
- You need to be (Office 365) Tenant Administrator to configure both Azure Active Directory and the plugin
- You may want to consider restricting access to the otherwise publicly available wp-content directory

= Support =

We will go to great length trying to support you if the plugin doesn't work as expected. Go to our [Support Page](https://www.wpo365.com/how-to-get-support/) to get in touch with us. We haven't been able to test our plugin in all endless possible Wordpress configurations and versions so we are keen to hear from you and happy to learn!

= Feedback =

We are keen to hear from you so share your feedback with us on [Twitter](https://twitter.com/WPO365) and help us get better!

= Open Source =

When you’re a developer and interested in the code you should have a look at our repo over at [WordPress](http://plugins.svn.wordpress.org/wpo365-login/).

== Installation ==

Please refer to [these **Getting started** articles](https://docs.wpo365.com/category/21-getting-started) for detailed installation and configuration instructions.

== Frequently Asked Questions ==

== Screenshots ==
1. Dual login feature
2. SharePoint Online Search
3. Employee Directory
4. Configure Single Sign-on (SSO, Sign in with Microsoft)
5. User registration incl. new user email Notification
6. Customize login / logout behavior
7. Use Office 365 Avatar and custom profile fields
8. User synchronization / enrollment (Azure AD)
9. Debug information
10. Scheduled user synchronization

== Upgrade Notice ==

* Please check the online version of the [release notes for version 11.0](https://www.wpo365.com/release-notes-v11-0/).

== Changelog ==

= v11.5 =
* Feature: The plugin can now dynamically assign WordPress roles to new and existing users based on properties of that user's Azure AD user account e.g. 'Department'.
* Feature: The plugin can now dynamically assign (itthinx) groups to new and existing users based on properties of that user's Azure AD user account e.g. 'Department'.
* Improvement: Now administrators can configure the plugin to use a proxy for the upcoming outgoing server-side request - when ever the plugin tries to build up a connection with PHP cURL e.g. to Microsoft Graph to retrieve information on behalf of a user. 
* Fix: Now throttling of retrieving avatars is working as expected and max. 5 avatars will be refresh per request, to avoid some users experiencing performance degradation.

= v11.4 =
* Fix: Activation of (premium) licenses is now working as expected.
* Fix: Auto-update of (premium) extensions is now working as expected.

= v11.3 =
* Improvement: The nonce generator and validator have been updated in an effort to reduce the risk of nonce not being found.
* Improvement: The plugin won't generate errors anymore when it cannot connect to Microsoft Graph to retrieve the current user's profile in an attempt to improve the data quality when the administrator has not configured the integration portion of the plugin.
* Fix: For reasons of backward compatibility, the plugin now only tries and retrieve all groups that a user is a member of if the ID token doesn't contain this information
* Fix: The plugin now generates a warning instead of an error when it cannot retrieve a user's manager.

= v11.2 =
* Fix: Added missing class method to parse manager details (WPO365 | SYNC and WPO365 | INTRANET edition).

= v11.1 =
* Fix: Domain whitelist now looks both at the email and the login domain.
* Fix: The plugin now checks if the administrator has configured an application secret.
* Fix: The plugin now only tries to save a refresh token if one is present.
* Fix: The wizard now ensures that the INTRANET apps are loaded from the correct source folder.

= v11.0 =
* Breaking Change: The source code of the plugin has been completely restructured. Developers that extended the plugin with own functionality must carefully review the changes.
* Breaking Change: All premium editions of the plugin now require the latest BASIC edition of the plugin to be installed and activated. An notification will be shown to admins upon upgrade to update, install and / or activate it.
* Breaking Change: Support for legacy Azure AD App registrations has been removed. The plugin will now always try and connect to **Azure AD v2** endpoints for authorization and optionally to obtain tokens.
* Breaking Change: Support for Avatars stored as WordPress user meta (in the WordPress database) has been removed. Avatars downloaded from Microsoft 365 / Azure AD will now always be stored in the /wp-content folder.
* Breaking Change: Support for the deprecated **Dual Login** feature is removed. Admins can instead toggle WP Admin > WPO365 > Login / Logout > Dual login V2.
* Breaking Change: Support for the deprecated **Sign in with Microsoft** shortcode [wpo365-sign-in-with-microsoft-sc] has been removed. Admins should configure the [Sign in with Microsoft v2](https://docs.wpo365.com/article/99-add-sign-in-with-microsoft-button-anywhere-shortcode) shortcode instead.
* Feature: Administrators can now choose between [**SAML 2.0 based single sign-on**](https://docs.wpo365.com/article/100-configure-single-sign-on-with-saml-2-0) and OpenID Connect based single sign-on (which remains the default option).
* Feature: The BASIC edition of the plugin will automatically create a new user in WordPress (but not synchronize user profile fields such as first and last name). However, this feature can be disabled by admins.
* Improvement: **User synchronization** now supports WordPress Multisite (WPMU) installations and always synchronizes users to the subsite from which the synchronization was started.
* Improvement: The plugin now remembers the tenant ID of a user and uses that information when - in case of multi tenancy - it needs to retrieve data e.g. a user's profile image from Microsoft Graph.
* Fix: The plugin no longer relies on the ID token to contain the (Azure AD / Microsoft 365 / distribution list) groups that a user is member of. Instead the plugin will always try to obtain this information from Microsoft Graph (but only if needed).
* Fix: The plugin no longer replaces stored avatars when it tries to refresh that avatar but it fails e.g. because of insufficient permissions.

= v10.10 =
* Improvement: The plugin will try to detect a possible infinite loop when the host name of the requested URL is different than the host name of the (Azure AD) redirect URI and inform the administrator to update the wp-config.php (see https://docs.wpo365.com/article/5-infinite-loop for details).
* Improvement: Added the needed prerequisites for l10n based translations for the text domain wpo365-login (a new .POT file has been added to the plugin's /languages folder that can be used e.g. to translate errors and the "Sign in with Microsoft" text on login button).
* Improvement: Thanks to customer feedback, the Teams integration will now automatically redirect the user to the Microsoft login.
* Fix: When using Azure AD customized claims the plugin must use a different endpoint to retrieve the public keys needed to decode the ID token.
* Fix: The Employee Directory now handles the auto-search flag as expected and does not ignore the query template, page and select properties configuration.
* Fix: Error messages now will show on the login page when the adminstrator optimized the plugin's performance (see https://docs.wpo365.com/article/36-authentication-scenario for details).

= v10.9 =
* Change: The PREMIUM and INTRANET edition now support mappings between Azure AD group memberships and (itthinx) Groups that you created with the [Groups plugin](wordpress.org/plugins/groups/).
* Improvement: The "Plugin self-test" will now allow you to inspect the ID token received during the execution of the test.
* Improvement: The WordPress Admin Notification now includes details of the last three errors plus useful links to help resolve those errors.
* Improvement: Several improvements have been made in an attempt to make a first-time installation / configuration successfull e.g. direct links to Azure Portal an an option to hide advanced configuration options.
* Improvement: Even when an administrator configured the global constant WPO_AUTH_SCENARIO and set its value as 'internet' to prevent the plugin from initializing when running in intranet authentication mode, the plugin will still initialize when: 1 - A Microsoft authentication response (= ID token) is detected or 2 - The login_init hook is triggered (which is the case for the default login page).
* Improvement: The Employee Directory / Contacts app now supports a query template that can include a '{searchterms}' placeholder and if it does it will override the default query e.g. startswith(department, '{searchterms}')
* Fix: Microsoft Teams integration accidently was not included in versions v10.6 - v10.8.
* Fix: "Express login" that can be togged for the PREMIUM and INTRANET edition now works as expected (adm)
* Fix: When an error occurs in one of the Microsoft Office 365 Apps, the error message now starts with Oops (instead of Ups).
* Deprecated: The "Nonce secret" option is no longer used (no action required).
* Deprecated: The "Default domain" option is now edited as a "Custom domain" instead (no action required). 

= v10.8 =
* Fix: Power BI Embed token was generated using the wrong scope.
= v10.7 =
* Change: All editions now feature the ability to embed Power BI artifacts such as reports and dashboard in any WordPress page or post. The INTRANET edition - in addition - allows administrators to directly edit the JSON source for generating tokens and embedding artifacts. See [website](https://www.wpo365.com/power-bi-for-wordpress/) for details.
* Change: The INTRANET edition now features a brand new Yammer app that can be embedded in any WordPress page or post. Users are authenticated when they sign into the WordPress website with Microsoft using the single sign-on experience. See [website](https://www.wpo365.com/yammer-for-wordpress/) for details.
* Improvement: The "wpo365_openid_token_processed" developer hook now receives the ID token as a third argument. See [website](https://docs.wpo365.com/article/82-developer-hooks) for details.
* Fix: The (Microsoft Graph) Employee Directory app now correctly clears the list of existing results when the user continues to type the search query.

= v10.6 =
* Change: All editions of the plugin will now always show a "Sign in with Microsoft" button on the (default) WordPress login form. Administrators, however, can choose to hide the button. See https://docs.wpo365.com/article/81-enable-dual-login for details.
* Change: The plugin no longer rejects the ID token of a user without a valid email address. This may result in premium editions of the plugin creating WordPress users without a valid address.
* Change: The plugin now provides 3 hooks for developers to respond when a user signs in with Microsoft, receives an access token and when the plugin analyzes reasons to skip authentication. These hooks are not enabled by default. See https://docs.wpo365.com/article/82-developer-hooks for details.
* Improvement: The (Helpscout) Support Beacon is now loaded whenever the plugin's configuration wizard is loaded. This makes it very easy to search the available documentation when configuring the plugin without the need to open a new browser window.
* Improvement: A new toolbar has been added to the plugin's configuration wizard the interacts with the (Helpscout) Support Beacon, making it really easy to contact WPO365 support.
* Improvement: The wizard now tries to load pages from the new (but still work-in-progress) documentation service https://docs.wpo365.com.

= v10.5 =
* Fix: The (PREMIUM and INTRANET editions of the) plugin now checks if the BuddyPress avatar is requested for a user (e.g. and not for a group).
* Fix: The (INTRANET edition's) Content by (SharePoint Online) Search app auto-search function did not automatically started a new search immediately after being loaded.
* Improvement: The (INTRANET edition's) Content by (SharePoint Online) Search app now injects a count property into the Handlebar template to make it possible e.g. to show a table header before the first row.

= v10.4 =
* Fix: The plugin now saves the request ID variable as a GLOBAL variable.
* Fix: A missing (global) namespace declaration in the plugin's update checker could cause a serious error.
* Fix: The Content by Search (SharePoint Online) and Documents (SharePoint Online / OneDrive) apps will now format dates based on the detected user's browser (language) preference.

= v10.3 =
* Fix: Accented characters e.g. é, è or ä would prevent the wizard from saving updated options (e.g. custom error messages, Office 365 profile field labels etc.).
* Fix: The PLUS+ edition's update checker was not tracking the correct item in the online store and therefore didn't show that updates were available.

= v10.2 =
* Fix: Usage of trailing comma's after method parameters is not supported before PHP 7.3 and hence for older PHP versions the plugin may not load as expected (affected the INTRANET edition v10.1).
* Fix: Usage of the PHP function get_file_contents to retrieve the WordPress gravatar for a user may cause a warning if the IT administrator had disallowed allow_url_fopen in php.ini (affected PREMIUM and INTRANET editions v10.1).
* Fix: The table that tracks the user synchronization results was only updated with the results of the last batch (affected the PREMIUM and INTRANET editions v10.0 and higher).

= v10.1 =
* New capability: An administrator (of the INTRANET edition of the plugin) can now configure Azure AD User provisioning by configuring the custom WPO365 SCIM endpoint for WordPress. See https://docs.wpo365.com/article/59-wordpress-user-provisioning-with-azure-ad-scim for details.
* Improvement: The plugin now tries to detect whether the requested WordPress page is loaded inside of Microsoft Teams e.g. as Content Page of a custom built Microsoft Teams App. If this is the case, the plugin will show a "Sign in with Microsoft" button that - when clicked - will then start the authentication workflow in a popup window that is controlled by Microsoft Teams. See https://docs.wpo365.com/article/70-adding-a-wordpress-tab-to-microsoft-teams-and-use-single-sign-on for details.
* Improvement: Additional Office 365 fields can now be mapped to BuddyPress Extended Profile Fields.
* Improvement: An administrator can now choose to stream the WPO365 log to a remote instance of Microsoft ApplicationInsights and by doing so benefit from the advanced search, analytics and alert functions the platform offers. See https://docs.wpo365.com/article/60-use-applicationinsights for details.
* Improvement: When synchronizing users (with the PREMIUM and / or INTRANET edition of the plugin) an Administrator can now choose to soft-delete users which will result in soft-deleted users no longer being able to sign into the WordPress. Instead those users will see an "Account deactivated" error message.
* Fix: The Documents app's breadcrumb navigation will now start with the folder name if a folder path has been provided.
* Fix: Checked PHP 7.3 compatibility with PHP Compatibility Checker and fixed two issues.

= v10.0 =
* New capability: An adminstrator (of the PREMIUM and INTRANET edition of the plugin) can now create a schedule to synchronize users between Azure AD and Wordpress at regular (daily or weekly) intervals. Please note that doing so requires you to have configured the (App-only) Application (client) ID and corresponding secret (see https://www.wpo365.com/use-app-only-token/ and https://www.wpo365.com/app-only-application-id/ for more details about app-only permissions). Please also note that scheduled user synchronization relies on WordPress cron jobs. 
* New capability: In addition to the Employee Directory the (INTRANET edition of the plugin) now offers an advanced Contacts app that allows users to search for users, view their contact details and see their direct reports as well as their managers in the form of an interactive clickable organization chart. The app uses Handlebar templates that can be used to further customize the user experience.
* Improvement: The Documents app (of the INTRANET edition of the plugin) can now be configured to only show the contents of a SharePoint Online / OneDrive folder. In addition it can be configured to show the "recently used" documents of the "logged-in" user.
* Improvement: Most apps now offer the ability to add translations for (most of) the user interface elements (error information not always included).
* Improvement: To optimize performance in case of the "Internet" authentication mode, administrators can now add the following line to the wp-config.php: "define( 'WPO_AUTH_SCENARIO', 'internet' );". This will prevent the plugin from loading for all requests that are not for WordPress administration pages. Please be aware that - if you add this line to your wp-config.php - you must ensure that the Redirect URI ends with "/wp-admin/". If this is not the case, the plugin won't be able to receive the authentication response sent by Microsoft and the plugin will not work as expected. Please also note that the following Login / Logout capabilities won't work and must be de-activated in advance: Dual Login, Error Page.
* Improvement: All apps have been refactored from the ground up and have been greatly simplified from a technical / maintenance point of view by utilizing Function Components combined with React Hooks and removing React Redux alltogether. Administrators are advised to test the apps before upgrading in production.
* Fix: Previously, the plugin would overwrite the array containing a user's (Azure AD) groups with an empty array when it tried to retrieve missing profile fields from Microsoft Graph.

= v9.6 =
* Improvement: The plugin will now try to request data from Microsoft Graph for the current user if essential information (user principal name, email, first or last name) is not included in the initial authentication response (ID token) (PROFESSIONAL, PREMIUM and INTRANET editions only).
* Improvement: The WordPress session will expire automatically whenever the user closes the browser. A new setting has been added (on the Single Sign-on tab of the plugin's wizard) to remember the user.
* Improvement: The (INTRANET edition of the) Employee Directory now includes an Org Chart template that allows users to see an employee's manager and direct reports.
* Improvement: You can now customize the appearance of the (INTRANET edition of the) Documents app by adding your own translations for the available columns (or choose not to show a column at all).
* Improvement: The plugin is now capable of running a self-test sequence that validates core configuration and received ID and access tokens. Test results include hints and recommendations for improvement.
* Improvement: The debug log now shows an ID for each request, making it easier to understand the program flow when executing multiple requests simultaneously.
* Improvement: The (PREMIUM and INTRANET) edition of the plugin now allows storing Office 365 profile images as avatars in the wp-content folder without the need to configure a secondary App registation for app-only tokens.
* Tested: Compatibility with WordPress 5.3.
* Fix: PREMIUM and INTRANET edition of the plugin do not retrieve Avatar for another user when synchronizing.
* Fix: PREMIUM and INTRANET edition of the plugin do not update extra O365 fields if that field is a boolean and changes from true to false.
* Fix: Compatibility with PHP 7.4 (create_func deprecation).
* Fix: By default the plugin now starts validation of the current session on WordPress' init hook. Administrators can, however, override this and choose to start validation earlier on the plugins_loaded hook.

= v9.5 =
* Improvement: An administrator can now configure to save the retrieved O365 user profile images in wp-content/uploads/wpo365/profile-images (instead of in the database), helping boost performance significantly.
* Improvement: An administrator can now configure a 2nd Azure AD App registration for so-called application permissions. Doing so eliminates the need for sensitve permissions such as Groups.Read.All and User.Read.All being granted for all users.
* Improvement: Apps can now be customized with the help of (<a href="https://handlebarsjs.com/" target="_blank">Handlebars.js</a>)templates</a> (Employee Directory, Content by Search).
* Improvement: Using (colorful) branded icons for Office products (Content by Search).
* Improvement: Specify the (custom Azure AD extension) properties that should return from a Microsoft Graph users query e.g. employeeId (Employee Directory).
* Improvement: Specify to use the current user's OneDrive as the library source instead of entering the OneDrive site address and library title (Documents).
* Fix: IE 11 compatibility (all apps).
* Fix: Rendering of (user profile) images in search results (Employee Directory, Content by Search).
* Fix: Increased time-out waiting to start searching after a user entered a query (Employee Directory).

= v9.4 =
* Improvement: An administrator can now configure the plugin to automatically assign users a WordPress role by creating one or more mappings between a (username's login) domain on the one side and a WordPress role on the other side. Visit https://www.wpo365.com/domain-roles-mappings/.
* Improvement: Added support for so-called Azure single sign out. Visit https://www.wpo365.com/enable-logout-without-confirmation/.
* Improvement: An administrator can now configure a domain hint to prevent users that are already logged on toanother Azure AD / Office 365 tenant from signing in with possibly the wrong Microsoft work or school account. Visit https://www.wpo365.com/domain-hint/.
* Improvement: The plugin, when receiving the authentication response from Microsoft, will now additionally search in WordPress for users by account name i.e. the user's principal name (= Office 365 login name) without the domain suffix. However, please be aware that some plugin features expect a WordPress username to be a legitimate Azure AD login name. Features not working when the WordPress user name is not a fully qualified Azure AD user principal name are the Avatar synchronization, mapping of Azure AD group memberships to WordPress roles and adding additional Office 365 user profile properties to a user's WordPress and / or BuddyPress profile as well as the deep integration in MS Graph and SharePoint Online.
* Improvement: Some 3rd party themes and plugins that hook into the user_register action e.g. to send an email with a confirmation link, would run into a fatal error when the action was triggered. This new configuration setting (on the Miscellaneous tab) - when checked - is a work-around to disable the action from being triggered (when a new user is created automatically by the plugin). Visit https://www.wpo365.com/skip-user-register-action/.
* Fix: Error "Undefined variable: resource Auth.php on line 774".

= v9.3 =
* Change: The plugin now ships with a built-in SharePoint Online Documents app (see https://www.wpo365.com/documents/). 
* Improvement: A new setting "Retrieve all group memberships" allows you to retrieve all sorts of groups memberships when synchronizing users instead of only the security-enabled group memberships.

= v9.2 =
* Fix: Now getting / setting WordPress transients take into account WordPress multisite to prevent "Your login has been tampered with" error when signing into a subsite (when authentication configuration is shared between all sites in the network).

= v9.1 =
* Improvement: Optionally you can specify your custom query when synchronizing users.
* Improvement: Optionally you can specify a Welcome Page URL where new users are sent after they signed on with Microsoft the very first time.
* Improvement: You can now (try to) activate your license.
* Fix: When redirecting, the plugin now writes a proper HTML document incl. doctype.
* Fix: The plugin now tries to obtain the initial URL the users intended to load on the client to preserve query parameters and fragments (hash).

= v9.0 =
* Change: The plugin now ships with a built-in SharePoint Online Search app (see https://www.wpo365.com/content-by-search/). 
* Change: The plugin now ships with a built-in Employee Directory app that queries Microsoft Graph (see https://www.wpo365.com/employee-directory/).
* Change: When using BuddyPress you can now instruct the plugin to show the Office 365 profile picture instead.
* Fix: When synchronizing users the plugin will now also update core user fields (email, first name, last name, display name).
* Fix: When synchronizing users the plugin will now also retrieve a user's Office 365 profile picture (if this feature is enabled and if an older version that has not yet expired is not found).
* Fix: If the plugin detects a different scheme between the Azure AD redirect URL and the URL the user navigated to before the SSO workflow started the plugin autocorrects the scheme (changes http:// to https://) to avoid infinite loops. An error will be generated in the log and the admin should take appropriate measures e.g. updating .htaccess to ensure the site automatically redirects to its secure version.

= 8.6 =
* Fix: The plugin will only (try to) retrieve additional user fields (from O365) if the user signed in with Microsoft (assumption made by analyzing the email domain).
* Fix: When the Dual Login feature is activated, the plugin now redirects the user to the WordPress site instead to initiate the login workflow.
* Fix: A typo caused the BASIC edition to cause a warning when trying to show the discount banner.
* Fix: When redirecting to Microsoft the plugin would sometimes not remember the state correctly, resulting in a login error.
* Fix: Cache buster for the wizard was not set correctly and therefore wizard updates were not immediately visible after an upgrade.
* Fix: More robust detection whether WordPress is loaded in an iframe.

= 8.5 =
* Change: Now the plugin will no longer require access to WP REST API or WP AJAX API. Instead the plugin adds an additional POST request to trigger the Single Sign-on workflow. This request uses a cache breaker to work-around server-side cache, allowing admins to configure the home url (instead of the WP Admin url) as a Redirect URI for the Azure AD App registration.
* Change: User synchronization no longer requires (unattended) access to the WP AJAX API. Instead the plugin will "loop" until all users found in Microsoft Graph have been processed. For the admin starting the synchronization this will appear as a synchronous action but in reality the synchronization is executed in batches of 10 users. By doing so the synchronization will not eventually time out (but as a drawback can also not be executed unattended).

= 8.4 =
* Fix: Removed the "too" opinionated validation of schemes used for redirect URI and WordPress URL.
* Fix: Improved the detection of HTTPS (but it is up to the administrator to ensure SSL is being enforced for the front and back end).
* Fix: Removed dead code.

= 8.3 =
* Change: Moved the custom API for users to obtain the Microsoft authentication endpoint e.g. login.microsoftonline.com to the WordPress REST API. Please ensure that this endpoint i.e. https://www.example.com/wp-json/wpo365/ is not blocked e.g. by basic auth, another plugin or your firewall.
* Change: If the custom (WP REST) API is not available to end users (e.g. because it is disabled or blocked) the user will see an error message and instructions on how to resolve the issue are printed to the developer console (F12).
* Change: The option to bypass the NONCE verification (at your own risk) to work around server-side cache has been re-activated. This options should only be used in combination with SSL.
* Change: The client-side redirect script will try and detect if it's being loaded in an iframe (which is by default not supported by Microsoft) and if this is the case it will try and open a popup instead. Please make sure popup blockers are disable for your domain, if you are trying to place your website in an iframe. For Internet Explorer / Edge please make sure that login.microsoftonline.com and your website are both added to the same security zone.
* Change: Logging has been improved with a filter to only show errors and error descriptions now offer more guidance on how they can be resolved.
* Fix: When WordPress multisite has been installed, the plugin will detect when the user changes the (sub) site (when the admin configured WPO_MU_USE_SUBSITE_OPTIONS (true)) and if this is the case signs out the user and eventually redirects the user to Microsoft to authenticate for the new (sub) site.

= 8.2 =
* Fix: WPO365 admin menu not available when WPO_MU_USE_SUBSITE_OPTIONS (true) has been configured.
* Fix: O365 user fields now requested using the user's principal name (upn) instead of email address.

= 8.1 =
* Fix: Compatibility with older browsers, specifically IE11.
* Fix: Added a plugcache breaker when loading pintra-redirectjs.

= 8.0 =
* Change: To work-around server-side caching the previous solution to redirect via /wp-admin has been discontinued. Instead the plugin will now output a short (cachable) JavaScript that will request the authentication URL from a custom WordPress AJAX service and redirect the user accordingly.
* Change: The way nonces are generated and validated has been changed to ensure that nonces are really used only once.
* Change: A version 2 of the "Sign-in with Microsoft" shortcode has been added to take advantage of the beforementioned client-side redirection to prevent server-side caching. Older "Sign-in with Microsoft" shortcode templates will continue to work but it is recommended that they are updated accordingly.
* Change: A version 2 of the "Dual Login" feature (= previously referred to as "Redirect to login")  has been added to take advantage of the beforementioned client-side redirection to prevent server-side caching. Older Dual Login templates will continue to work but it is recommended that they are updated accordingly.
* Change: The plugin now requires that the Azure AD "Redirect URI" and your WordPress (Site) Address use the same scheme e.g. http(s). If this is not the case it will show a "Plugin is not configured" error and will basically disable it self, to prevent infinite loops.
* Change: Debug log will now show the debug in descending order (latest entries first).
* Change: The plugin will now try and automatically add a trailing slash whenever it tries to redirect the user.
* Change: When using the "Dual Login" feature (= previously referred to as Redirect to login) the plugin will now remember the URL the user initially requested and redirect the user accordingly upon successful authentication.
* Change: The plugin's wizard "Test authentication" button has been removed. Instead the configuration is always saved and then tested. The authentication URL used for testing will now appear after clicking "Save configuration" since this URL (and the corresponding nonce) is generated server-side and must be unique.
* Fix: A legacy function to prevent client-side caching that generated unnecessary error log entries (and thus unnecessary warnings in WP admin) has been removed.

= 7.18 =
* Change: The plugin will regularly check the error log to see if recently new errors were logged and if so show a dismissable notice in the WordPress admin area.
* Change: The administrator can choose to surpress the error notice in the WordPress admin area.
* Fix: Improved the improved way of parsing the ID token (trying to get the user principal name first if available).
* Fix: The plugin would throw an previously uncaught exception when trying to log an event when the synchronization of users would fail.

= 7.17 =
* Change: Now that Microsoft has made the new Azure App registration portal General Available, the recommended Azure AD endpoint to use is v2.0 (see https://www.wpo365.com/azure-application-registration/)
* Change: The plugin now supports retrieving manager data (display name, email, telephone number(s), office location, country) of an O365 user through Microsoft Graph.
* Change: When configuring "Redirect to login" you can now choose to hide the SSO link which is otherwise shown above the login form.
* Change: You can now configure a custom login URL (which is automatically added to the Pages Blacklist).
* Fix: Improved way of parsing the ID token, avoiding unexpected WP user names, especially for Azure AD guests and users from other tenants.
* Fix: Display name property now correctly set when creating a new WP user using the information from the parsed ID token.
* Fix: Now the plugin will check - when multisite is activated - whether the logged in user autenticated for the current site and if not the user will be logged out and forced to authenticate again.
* Fix: WP user now created with a stronger default password.

= 7.16 =
* Fix: Improved caching of license check result to prevent it from impacting the overall website performance.
* Fix: Now the wizard is loaded with a cache breaker to ensure with each new plugin version the latest version shows immediately.
* Fix: White spaces at the beginning and end of configuration options that are strings are now properly trimmed.

= 7.15 =
* Change: Added software licensing and replaced automated upgrade with license key based solution (professional and premium version).
* Fix: Additional logging when synchronizing user (premium version).

= 7.14 =
* Change: Added an extra option (see Miscellaneous tab of the plugin's configuration wizard) to prevent the wp-login hook from being fired as it may cause an error in combination with some 3rd party themes.
* Fix: The plugin now recognize the super administrator (available only for WordPress multisite) as an administrator of (any) subsite.

= 7.13 =
* Fix: The plugin now checks whether a user is an administrator by verifying roles instead of capabilities.
* Fix: The plugin's URL cache now resolves the WordPress home URL instead of the site address for the website's front end home.
* Fix: The plugin now correctly recognizes a "bounced" request when preparing to redirect the user to Microsoft's authentication endpoint.

= 7.12 =
* Change: The plugin can be configured to skip authentication when requesting data from the WordPress REST API when a Basic authentication header is present (professional and premium editions only).
* Change: You can configure the plugin to skip nonce verification (however, it is not recommended to do so but instead find the root cause e.g. an aggressive server-side caching strategy).
* Change: User synchronization is now supported at the level of a (sub) site in a WordPress Multisite WPMU network (premium edition only).
* Change: User synchronization now checks user capabilities and won't delete users that have the administrator capability (premium edition only).
* Fix: Check for admin capabilities would not always return true for a WordPress Multisite WPMU Network.
* Fix: Due to a regression the number of user synced per batch was set to 1 instead of 10 (premium edition only).
* Fix: Manual login attempts will now be intercepted even when redirect to login is checked (professional and premium editions only).

= 7.11 =
* Change: User Synchronization is now executed in asynchronous batches of 25 users each until finished to prevent a timeout exception. As soon as the asynchronous user synchronization has finished the plugin will (try and) send an email to website's administrator (premium version only).
* Change: When you have selected the Intranet (Authentication) Scenario, you can check the "Public Homepage" option to allow anonymous access to the WordPress frontpage i.e. your website's home page (premium and professional version only).
* Change: A direct link to the WPO365 Wizard has been added to the Admin Dashboard Menu.
* Change: You can now toggle debug mode comfortably from the "Debug" tab that has been added to the plugin's configuration wizard. The debug log can now be viewed on that tab as well and you can copy the log to the clipboard.
* Change: The plugin now partially obscures a number of configuration secrets e.g. application ID, application secret, nonce etc.
* Change: The plugin's wizard has been enhanced with a number of warnings in the form of popups to provide more guidance when configuring the plugin.
* Fix: Synchronizing external users has been improved and the user name configured by the plugin is the external user's own email address (instead of the - sanitized - Azure AD User Principal Name) (premium version only).
* Fix: When a user - for any reason - cannot be created, the plugin would try and log that user's ID, causing an irrecoverable exception, which is now caught and logged adequately.

= 7.10 =
* Fix: Stricter validation of the Error Page URL and Pages Blacklist entries to ensure that the website is not accidently added (causing the plugin to skip authentication alltogether).
* Fix: Automatic update for the PROFESSIONAL edition failed.

= 7.9 =
* Fix: Custom error messages were ignored due to an error with the property's casing.
* Change: The professional and premium version now offer a "Redirect to login" option that when checked will send the user to the default WordPress login form (instead of the Microsoft) and on the login form a message will inform the user that he / she can also sign into the website using his / her Microsoft Office 365 / Azure AD account (and provide a link that when clicked will sign in the user with Microsoft)

= 7.8 =
* Fix: Auto-fix for bypassing server-side cache dind't work as expected.
* Change: The BASIC edition will now show an appropriate error message when user not found.
* Change: Added a short code that can be used on a custom error page to display the plugin's error message (professional / premium only).

= 7.7 =
* Fix: Removed "Plugin not configured" error redirection which prevented users to logon with their WordPress-only admin account when then plugin was not yet configured.
* Fix: (Smoke) Tested against PHP 7.3.3 and replaced deprecated create_function call.

= 7.6 =
* Change: When you change the authentication scenario to "Internet" the Pages Blacklist will be replaced by a Private Pages list. Posts and Pages added to the new Private Pages list will only be accessible for authenticated users. If the user is authenticated, the plugin will try and sign in the user with Microsoft.
* Change: You can now configure an Error Page. When configured, the plugin will redirect the user to this page each time it runs into an error e.g. user not found, plugin not configured etc. If no Error Page is configured, the plugin will instead redirect the user to the default WordPress login form. The plugin will automatically skip the Error Page when authenticate a request (to avoid an infinite loop). The error code will be sent along as query string parameter and can be used to customize your own Error Page.
* Fix: Added MIME Type and Content Headers to the New User Notification email template.

= 7.5 =
* Change: Sending a customized new user registration email is not supported by the basis (free) version. See <a href="https://www.wpo365.com/new-user-email/">online documentation</a> for details.

= 7.4 =
* Fix: If a user is not manually registered prior to trying to sign into the WordPress site with Microsoft, the user would end up in an infinite loop (only impacts basic version).
* Fix: Remove crossorigin from Pintra Fx template since this was causing an issue downloading react files from UNPKG CDN.

= 7.3 =
* Fix: A new setting "Don't try bypass (server side) cache" on the Miscellaneous Tab now controls whether the plugin will try and bypass the server side cache by redirecting the user first to /wp-admin before redirecting the user to Microsoft's Identity Provider.
* Fix: A new global constant WPO_MU_USE_SUBSITE_OPTIONS allows administrators of a WordPress multisite network to toggle between a "shared" scenario in which all subsites in the network share the same Azure AD application registration and a "dedicated" scenario in which all sites in the network will have to be configured individually. 

= 7.2 =
* Fix: Missing namespace import causing server error when user cannot be added successfully [professional, premium]

= 7.1 =
* Change: Now the plugin can redirect users based on their Azure AD Group Membership [premium]
* Fix: User synchronization would not work correctly with Graph Version set to beta
* Fix: Added support for wp_login hook
* Fix: Lowered priority when hooking into the wp_authenticate hook

= 7.0 =
* Plugin options are now managed through a new Wizard app that can be opened from the WordPress Plugins page where a new action link has been added to the wpo365-login plugin
* Support for configuring options through wp-config.php and Redux Platform has been discontinued (existing options will be upgraded automatically)
* Harmonized version number across all versions

= 6.1 =
* Change: Removed the (Redux) WPO365 Option for scope
* Change: Support for Azure AD v2.0 authentication and access token requests (preview, more information will follow in a separate upcoming post)
* Change: Updated the access token (AJAX) service API to support Azure AD v2.0 scope based token requests
* Change: Authorization, access and refresh codes and tokens are now stored as JSON encoded classes
* Change: Previously deprecated methods have been removed (other / third party plugins and apps must integrate using the API now)

= 6.0 =
* Change: A configuration option has been added to always redirect a user to a designated page upon signin into the website
* Change: A client (side) application can now request an oauth access token for any Azure AD secured resource e.g. Graph and SharePoint Online
* Change: A configuration section has been added to configure / disable the aforementioned AJAX service for Azure AD oauth access tokens
* Change: A Configuration section has been added that allows administrators to define custom login error messages
* Change: Refresh tokens e.g. for Graph and SharePoint Online are now set to expire after 14 days
* Change: The plugin will now cache the Microsoft signin keys used to verify the incoming ID token for 6 hours to improve overall performance
* Change: The flow to obtain access tokens has been refactored and greatly simplied (existing methods have been marked deprecated)
* Fix: Dynamic role assignment will not add default role when user has existing role(s)

= 5.3 =
* Change: Pages Blacklist can now include query string parts e.g. "?api=" but administrators need to be aware that this can potentially weaken overall security [read more](https://www.wpo365.com/pages-blacklist/)

= 5.2 =
* Fix: user_nicename - a WP_User field that is limited to 50 characters - was wrongly set to a user's full name which under circumstances prevented a user from being created successfully

= 5.1 =
* Fix: When searching for O365 users search both in email and login name
* Fix: Check before redirecting whether headers are sent and if yes falls back to an alternative method to redirect
* Fix: search_columns argument for WP_User_Query must be an array

= 5.0 =
* Moved the JWT class into the Wpo namespace (to avoid class loading issues)
* Added psr-4 type auto class loading
* Code refactoring to allow for tighter integration e.g. with [SharePoint Online Plugin](https://wordpress.org/plugins/wpo365-spo/)
