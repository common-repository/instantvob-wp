=== instantvob® WP ===
Contributors: instantvob
Tags: shortcode, forms, medical, health, insurance
Requires at least: 6.4.1
Tested up to: 6.4
Stable tag: 1.0.11
Requires PHP: 8.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Provide your patients real-time access to verification of benefits (VOB) and patient coverage information.

== Description ==

Introducing instantvob® WP, a dynamic WordPress plugin tailored for medical providers seeking to streamline their
insurance verification process. This plugin effortlessly connects your WordPress website with the powerful [instantvob®](https://instantvob.com/)
platform, ensuring your site visitors can verify their insurance benefits with ease and precision.

Once installed, instantvob® WP allows you to place a customizable form on any page of your site using a simple
shortcode. This form invites patients to submit their insurance details, facilitating a seamless verification of their
benefits before their treatment commences. The result is an enhanced patient experience, ensuring they are well-informed
about their coverage well in advance of their appointment.

Key Features:

* Seamless Integration: Effortlessly connects with the instantvob® service, blending smoothly into your existing website design.
* Customizable Forms: Tailor the insurance verification form to meet the specific needs of your clinic or practice.
* Instant Verification: Offers real-time insurance benefit verifications, directly on your website.
* API Key Requirement: Secure and reliable, requiring a valid instantvob® API key for operation.
* Email Notifications: Designate an email address to receive notifications and details of the verifications conducted through your site.
* Spam Protection: Integrates with the [Cloudflare Turnstile](https://www.cloudflare.com/products/turnstile/) smart CAPTCHA service.

instantvob® WP is more than just a plugin; it's a step towards modernizing the way medical providers interact with their
patients' insurance. With just a few clicks, transform your website into a hub of efficiency and patient satisfaction.

== Requirements and Disclosures ==

= instantvob® =

This plugin requires a valid [instantvob®](https://instantvob.com/) API key to utilize this plugin. This plugin will interface
with instantvob®'s servers and sends requests to our API server URL at https://portal.instantvob.com.

By using this plugin, you agree to adhere to the instanvob terms and conditions and accept the privacy policy linked below:

* Terms: https://instantvob.com/terms-of-use/
* Privacy policy: https://instantvob.com/privacy-policy/

= Cloudflare Turnstile =

To use this plugin, you will need a free [Cloudflare](https://www.cloudflare.com/products/turnstile/) account, through
which you can obtain a Cloudflare Turnstile API key. This plugin validates all form submissions through Cloudflare's
servers at https://challenges.cloudflare.com.

By using this plugin, you agree to adhere to the Cloudflare terms and conditions and accept the privacy policy linked below:

* Terms: https://www.cloudflare.com/website-terms/
* Privacy policy: https://www.cloudflare.com/privacypolicy/

== Changelog ==

= 1.0.4 =

* Fixed an error validating instantvob® API keys

= 1.0.3 =

* Update instantvob® logo

= 1.0.2 =

* Adjust how branding image URLs are resolved

= 1.0.0 =

* Adds Cloudflare Turnstile integration
* Adds optional instantvob attribution credit
