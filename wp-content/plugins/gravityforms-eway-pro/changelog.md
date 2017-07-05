# Gravity Forms eWAY Pro

## Changelog

### 1.1.3, 2017-01-30

* fixed: Recurring Payments start dates sometimes break form processing
* fixed: Recurring Payments end dates are calculated as day after start date when interval is in weeks
* fixed: Recurring Payments could not send formatted phone numbers with () characters
* changed: requires minimum PHP 5.3; recommended version is PHP 7.0 or higher

### 1.1.2, 2017-01-04

* fixed: token customer payments can now be processed with method Authorise (PreAuth), not just Capture
* fixed: admin performance problem when website uses W3 Total Cache with Object Caching ([read more](https://easydigitaldownloads.com/development/2016/12/21/edd_sl_plugin_updater-class-updated-to-version-1-6-7/))
* fixed: add-on links disappear from Settings and Form Settings menus when roles/permissions edited e.g. with Members plugin
* added: bulk actions for setting feeds to Live or Test

### 1.1.1, 2016-12-12

* fixed: delayed actions (MailChimp, User Registration, Zapier) would not trigger when creating a new token customer without a transaction
* added: trigger notification event "Subscription Created" when creating a new token customer without a transaction
* added: support free [Gravity Forms Saleforce add-on](https://wordpress.org/plugins/gravity-forms-salesforce/) feeds with delayed actions

### 1.1.0, 2016-12-08

* fixed: conflict between eWAY's Client Side Encryption script and File Upload fields
* fixed: undefined index errors on `gform_replace_merge_tags` hook, e.g. with GF User Registration login widget
* changed: minimum required version of Gravity Forms is 2.0
* changed: accept number fields for Recurring Payments feed fields
* changed: report any recurring payments initial fee amount in the entry note
* changed: don't attempt card processing if form validation fails (validation in other plugins; honeypot failure; save and continue heartbeat)
* added: support for token payments and token customer creation
* added: support for creating token customers without a transaction
* added: support for notification actions on payment events (payment completed, pending, failed; subscription created)

### 1.0.6, 2016-11-05

* added: support Zapier feeds with delayed action, and force transaction ID send by duplicating it as entry meta

### 1.0.5, 2016-09-09

* fixed: InvoiceReference and InvoiceNumber had each other's values (NB: please update your filters if you modify these values!)
* fixed: `do_parse_request` is a filter, not an action
* fixed: indirect expressions incompatible with PHP 7
* fixed: one Gravity Forms field could not be mapped to multiple eWAY fields
* fixed: prevent Free eWAY add-on from processing form when Pro add-on has an active feed
* fixed: email address fields with confirmation enabled failed
* changed: use `wp_remote_retrieve_*()` functions instead of response array access (WP4.6 compatibility)
* changed: Shipping Address defaults to Left Empty (was: Same as Billing Address)
* changed: use Gravity Forms `get_order_total()` to calculate form total (fixes T2T Toolkit conflict with Coupons add-on)
* changed: use the minified version of the eWAY Client Side Encryption script, unless `SCRIPT_DEBUG` is enabled
* added: default value for Invoice Reference is the form ID for Direct / Recurring, and form ID + entry ID for Responsive Shared Page
* added: pre-fill some field mappings in new feeds
* added: support for Beagle Verify on Responsive Shared Page feeds
* added: smart Client Side Encryption when mixing Direct Connection and Recurring Payments feeds on one form
* added: check for PCRE (regular expression library) minimum version
* added: new error message strings for eWAY Rapid API response codes
* added: feeds list now shows mode (test/live) and method (direct/shared page/recurring)
* added: record partial card number and type for Direct Connection transactions

### 1.0.4, 2016-05-06

* fixed: v1.0.3 broke Client Side Encryption for Direct Connection feeds (sorry!)

### 1.0.3, 2016-05-02

* fixed: send customer title to eWAY when mapped in a feed
* fixed: recurring payments using the form_total as the recurring amount
* fixed: T2T Toolkit breaks posted Gravity Forms total field when products have options

### 1.0.2, 2016-04-20

* changed: when overriding the global Gravity Forms currency, override for all forms on page (or they'll override our form)
* changed: minimum required version of Gravity Forms is 1.9.15
* fixed: saving feed settings in Gravity Forms 2.0
* fixed: Client Side Encryption works on form previews now too
* fixed: delayed notifications are always processed, regardless of transaction status (use conditional logic on AuthCode for success/fail notifications)
* fixed: some en-GB, en-AU, en-NZ translation strings
* added: separate sandbox configuration, making it easier to switch between Live and Sandbox modes

### 1.0.1, 2016-03-08

* fixed: was always using sandbox for Responsive Shared Page, regardless of feed settings

### 1.0.0, 2016-02-27

* initial public release
