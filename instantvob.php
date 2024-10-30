<?php
/*
 * Plugin Name: instantvob® WP
 * Plugin URI:        https://instantvob.com/
 * Description:       Manage VOB submissions right from the WordPress admin
 * Version:           1.0.11
 * Requires at least: 5.8
 * Requires PHP:      7.2.0
 * Author:            instantvob
 * Author URI:        https://instantvob.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       instantvob
 */

if (!defined('ABSPATH')) {
  exit();
}

$deps_version = '1.0.11';

wp_register_style(
  'instantvob_styles',
  plugin_dir_url(__FILE__) . 'assets/instantvob-styles.css',
  [],
  $deps_version
);

wp_enqueue_style('instantvob_styles');

wp_enqueue_script(
  'instantvob_script_just_validate',
  plugin_dir_url(__FILE__) . 'assets/just-validate.production.min.js',
  [],
  $deps_version,
  [
    'in_footer' => true,
  ]
);

wp_enqueue_script(
  'instantvob_script_cloudflare_turnstile',
  'https://challenges.cloudflare.com/turnstile/v0/api.js',
  [],
  null,
  [
    'strategy' => 'defer',
  ]
);

wp_enqueue_script(
  'instantvob_script_submit',
  plugin_dir_url(__FILE__) .
    'assets/instantvob-submit.js?' .
    'hash=' .
    $deps_version,
  ['jquery'],
  null,
  [
    'in_footer' => true,
  ]
);

wp_localize_script(
  'instantvob_script_submit',
  'instantvob_submit_ajax_script',
  ['ajaxurl' => '/index.php?rest_route=/instantvob/v1/submit-form']
);

function instantvob_settings_init()
{
  add_option('instantvob_api_url', 'https://portal.instantvob.com/api/');

  add_settings_section(
    'instantvob_section_developers',
    __('General Settings', 'instantvob'),
    'instantvob_section_developers_callback',
    'instantvob'
  );

  register_setting('instantvob', 'instantvob_api_key', [
    'sanitize_callback' => 'instantvob_field_api_key_validate',
  ]);

  add_settings_field(
    'instantvob_field_api_key',
    __('API Key', 'instantvob'),
    'instantvob_field_api_key_cb',
    'instantvob',
    'instantvob_section_developers',
    [
      'label_for' => 'instantvob_api_key',
      'class' => 'instantvob_row',
      'instantvob_custom_data' => 'custom',
    ]
  );

  register_setting('instantvob', 'instantvob_notification_email');

  add_settings_field(
    'instantvob_field_notification_email',
    __('Notification Email', 'instantvob'),
    'instantvob_field_notification_email_cb',
    'instantvob',
    'instantvob_section_developers',
    [
      'label_for' => 'instantvob_notification_email',
      'class' => 'instantvob_row',
      'instantvob_custom_data' => 'custom',
    ]
  );

  register_setting('instantvob', 'instantvob_cf_turnstile_secret');

  add_settings_field(
    'instantvob_cf_turnstile_secret',
    __('CloudFlare Turnstile Secret', 'instantvob'),
    'instantvob_field_cf_secret_cb',
    'instantvob',
    'instantvob_section_developers',
    [
      'label_for' => 'instantvob_cf_turnstile_secret',
      'class' => 'instantvob_row',
      'instantvob_custom_data' => 'custom',
    ]
  );

  register_setting('instantvob', 'instantvob_cf_turnstile_sitekey');

  add_settings_field(
    'instantvob_cf_turnstile_sitekey',
    __('CloudFlare Turnstile Sitekey', 'instantvob'),
    'instantvob_field_cf_sitekey_cb',
    'instantvob',
    'instantvob_section_developers',
    [
      'label_for' => 'instantvob_cf_turnstile_sitekey',
      'class' => 'instantvob_row',
      'instantvob_custom_data' => 'custom',
    ]
  );

  register_setting('instantvob', 'instantvob_show_branding');

  add_settings_field(
    'instantvob_field_show_branding',
    __('Show instantvob® credit?', 'instantvob'),
    'instantvob_field_show_branding_cb',
    'instantvob',
    'instantvob_section_developers',
    [
      'label_for' => 'instantvob_show_branding',
      'class' => 'instantvob_row',
      'instantvob_custom_data' => 'custom',
    ]
  );
}

add_action('admin_init', 'instantvob_settings_init');

function instantvob_section_developers_callback($args)
{
  ?>
    <p id="<?php echo esc_attr(
      $args['id']
    ); ?>"><?php esc_html_e('', 'instantvob'); ?></p>
	<?php
}

function instantvob_get_show_branding_option()
{
  $option = get_option('instantvob_show_branding');

  return $option ?? null;
}

function instantvob_get_api_url()
{
  return get_option('instantvob_api_url');
}

function instantvob_get_api_key()
{
  $api_key = get_option('instantvob_api_key');
  return $api_key ?? null;
}

function instantvob_cf_secret()
{
  return get_option('instantvob_cf_turnstile_secret');
}

function instantvob_field_cf_secret_cb($args)
{
  $secret = instantvob_cf_secret(); ?>
  <input
    id="<?php echo esc_attr($args['label_for']); ?>"
    name="instantvob_cf_turnstile_secret"
    value="<?php echo esc_attr($secret != null ? $secret : ''); ?>"
    type="password"
  />
  <p class="description">
	  <?php esc_html_e(
     'Please enter your Cloudflare Turnstile secret.',
     'instantvob'
   ); ?>
  </p>
	<?php
}

function instantvob_cf_sitekey()
{
  return get_option('instantvob_cf_turnstile_sitekey');
}

function instantvob_field_cf_sitekey_cb($args)
{
  $site_key = instantvob_cf_sitekey(); ?>
  <input
    id="<?php echo esc_attr($args['label_for']); ?>"
    name="instantvob_cf_turnstile_sitekey"
    value="<?php echo esc_attr($site_key != null ? $site_key : ''); ?>"
    type="password"
  />
  <p class="description">
	  <?php esc_html_e(
     'Please enter your Cloudflare Turnstile sitekey.',
     'instantvob'
   ); ?>
  </p>
	<?php
}

// validates that the API key is valid.
// NOTE: this method runs twice, so the implementation here contains
// handling to make sure we avoid calling the validation API twice
function instantvob_field_api_key_validate($args)
{
  $instantvob_api_url = instantvob_get_api_url();

  static $pass_count = 0;
  $pass_count++;

  if ($pass_count <= 1) {
    $response = wp_remote_get($instantvob_api_url . 'verify-api-key', [
      'headers' => [
        'Content-Type' => 'application/json',
        'x-api-key' => $args,
      ],
    ]);

    if ($response['response']['code'] !== 200) {
      $args = 'INVALID';
    }
  }

  return $args;
}

function instantvob_field_api_key_error()
{
  $class = 'notice notice-error';
  $message = __(
    'The provided API key was not valid. Please enter a valid key.',
    'instantvob'
  );

  printf(
    '<div class="%1$s"><p>%2$s</p></div>',
    esc_attr($class),
    esc_html($message)
  );
}

function instantvob_field_show_branding_cb($args)
{
  $option = instantvob_get_show_branding_option(); ?>
  <input
    id="<?php echo esc_attr($args['label_for']); ?>"
    name="instantvob_show_branding"
    <?php echo esc_html($option === '1' ? 'checked="true"' : ''); ?>
    value="1"
    type="checkbox"
  />
    <label for="<?php echo esc_attr(
      $args['label_for']
    ); ?>"><?php esc_html_e('Displays instantvob® branding below to VOB form.', 'instantvob'); ?></label>
  <?php
}

function instantvob_field_api_key_cb($args)
{
  $api_key = instantvob_get_api_key();

  if ($api_key === 'INVALID') {
    instantvob_field_api_key_error();

    // empty the API key to prevent it from populating the field
    $api_key = '';
  }
  ?>
	<input
		id="<?php echo esc_attr($args['label_for']); ?>"
		name="instantvob_api_key"
        value="<?php echo esc_attr($api_key != null ? $api_key : ''); ?>"
        type="password"
	/>
	<p class="description">
		<?php esc_html_e('Please enter your instantvob® API key.', 'instantvob'); ?>
	</p>
	<?php
}

function instantvob_get_notification_email()
{
  $notification_email = get_option('instantvob_notification_email');
  return $notification_email ?? null;
}

function instantvob_field_notification_email_cb($args)
{
  $field_value = instantvob_get_notification_email(); ?>
  <input
    id="<?php echo esc_attr($args['label_for']); ?>"
    name="instantvob_notification_email"
    value="<?php echo esc_attr($field_value != null ? $field_value : ''); ?>"
    type="text"
  />
  <p class="description">
	  <?php esc_html_e(
     'Please enter an email address for instantvob® notifications.',
     'instantvob'
   ); ?>
  </p>
	<?php
}

/**
 * Add the top level menu page.
 */
function instantvob_api_key_page()
{
  add_menu_page(
    'instantvob® WP',
    'instantvob® WP',
    'manage_options',
    'instantvob',
    'instantvob_api_key_page_html'
  );
}

add_action('admin_menu', 'instantvob_api_key_page');

function instantvob_api_key_page_html()
{
  if (!current_user_can('manage_options')) {
    return;
  }

  if (isset($_GET['settings-updated'])) {
    add_settings_error(
      'instantvob_messages',
      'instantvob_message',
      __('Settings Saved', 'instantvob'),
      'updated'
    );
  }

  settings_errors('instantvob_messages');

  $api_url = instantvob_get_api_url();
  ?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
   settings_fields('instantvob');
   do_settings_sections('instantvob');
   submit_button('Save Settings');?>
		</form>
	</div>
	<?php
}

/**
 * REST endpoint for form submissions
 */
function instantvob_submit_form(WP_REST_Request $request)
{
  $instantvob_cf_secret = instantvob_cf_secret();
  $instantvob_api_url = instantvob_get_api_url();
  $api_key = instantvob_get_api_key();
  $email = instantvob_get_notification_email();
  $parameters = $request->get_json_params();

  $payload = [
    'memberId' => $parameters['instantvob_form_insurance_id'],
    'firstName' => $parameters['instantvob_form_first_name'],
    'lastName' => $parameters['instantvob_form_last_name'],
    'dateOfBirth' => $parameters['instantvob_form_dob'],
    'phone' => $parameters['instantvob_form_phone'],
    'address' => $parameters['instantvob_form_address'],
    'city' => $parameters['instantvob_form_city'],
    'state' => $parameters['instantvob_form_state'],
    'zip' => $parameters['instantvob_form_zip'],
    'patientEmail' => $parameters['instantvob_form_email'],
    'vendor' => $parameters['instantvob_form_insurance'],
    'notificationEmail' => $email,
  ];

  $cloudflare_validation = wp_remote_post(
    'https://challenges.cloudflare.com/turnstile/v0/siteverify',
    [
      'timeout' => 30,
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      'body' => json_encode([
        'secret' => $instantvob_cf_secret,
        'response' => $parameters['cf-turnstile-response'],
      ]),
    ]
  );

  $cloudflare_result = json_decode($cloudflare_validation['body']);

  if (!$cloudflare_result->success) {
    return rest_ensure_response([
      'payload' => $payload,
      'validation' => $cloudflare_result,
    ]);
  }

  $response = wp_remote_post($instantvob_api_url . 'wp-plugin-vob', [
    'headers' => [
      'Content-Type' => 'application/json',
      'x-api-key' => $api_key,
    ],
    'body' => json_encode($payload),
  ]);

  // rest_ensure_response() wraps the data we want to return into a WP_REST_Response, and ensures it will be properly returned.
  return rest_ensure_response([
    'payload' => $payload,
    'response' => $response,
    'validation' => $cloudflare_result,
  ]);
}

/**
 * Register REST endpoints for the plugin
 */
function instantvob_register_api_routes(): void
{
  register_rest_route('instantvob/v1', '/submit-form', [
    'methods' => WP_REST_Server::EDITABLE,
    'callback' => 'instantvob_submit_form',
  ]);
}

add_action('rest_api_init', 'instantvob_register_api_routes');

function instantvob_shortcode($atts = [], $content = null, $tag = '')
{
  $api_key = instantvob_get_api_key();
  $instantvob_api_url = instantvob_get_api_url();
  $instantvob_cf_secret = instantvob_cf_secret();
  $instantvob_cf_sitekey = instantvob_cf_sitekey();
  $is_branding_shown = instantvob_get_show_branding_option();
  $branding_shown_css_class =
    $is_branding_shown === '1' ? 'instantvob_branding__shown' : '';
  $logo_url = plugins_url('assets/instantvob-logo.svg', __FILE__);

  $response = wp_remote_get($instantvob_api_url . 'vendors', [
    'headers' => [
      'Content-Type' => 'application/json',
      'x-api-key' => $api_key,
    ],
  ]);

  $vendors = json_decode(wp_remote_retrieve_body($response), true);

  $vendor_options = '';

  foreach ($vendors as $vendor) {
    $vendor_options .=
      '<option value="' . $vendor . '">' . $vendor . '</option>';
  }

  $allowed_html = [
    'option' => [
      'value' => [],
    ],
  ];

  $filtered_content = '';

  if (!is_null($content)) {
    $filtered_content .= apply_filters('the_content', $content);
  }

  // check fields that are required to be correctly set in the admin
  // for the form to display
  if (
    $api_key === 'INVALID' ||
    !$api_key ||
    !$instantvob_cf_secret ||
    !$instantvob_cf_sitekey
  ) {
    return;
  }

  $states = array(
    'Select State', 'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'DC', 'FL', 'GA', 'GU', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MH', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'MP', 'OH', 'OK', 'OR', 'PW', 'PA', 'PR', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VI', 'VA', 'WA', 'WV', 'WI', 'WY'
  );

  $states_select = '';

  foreach ($states as $state) {
    if ($state === 'Select State') {
      $states_select .= '<option>' . $state . '</option>';
    } else {
      $states_select .= '<option value="' . $state . '">' . $state . '</option>';
    }
  }

  ob_start();
  ?>
    <div>
        <form class="instantvob_form" id="instantvob_form">
          <?php echo esc_html($filtered_content); ?>
          <div class="instantvob_form_notes">
            <p>Fields marked with * are required.</p>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_first_name">First Name *</label>
              <input id="instantvob_form_first_name" name="instantvob_form_first_name" type="text" />
            </div>
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_last_name">Last Name *</label>
              <input id="instantvob_form_last_name" name="instantvob_form_last_name" type="text" />
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_dob">Date of Birth *</label>
              <input id="instantvob_form_dob" name="instantvob_form_dob" type="date" />
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_phone">Phone *</label>
              <input id="instantvob_form_phone" name="instantvob_form_phone" type="tel" />
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_address">Address</label>
              <input id="instantvob_form_address" name="instantvob_form_address" type="text" />
            </div>
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_city">City</label>
              <input id="instantvob_form_city" name="instantvob_form_city" type="text" />
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_state">State</label>
              <select id="instantvob_form_state" name="instantvob_form_state">
                <?php echo wp_kses($states_select, $allowed_html); ?>
              </select>
            </div>
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_zip">Zip</label>
              <input id="instantvob_form_zip" name="instantvob_form_zip" type="text" />
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_email">Email *</label>
              <input id="instantvob_form_email" name="instantvob_form_email" type="email" />
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_insurance">Insurance Company *</label>
              <select id="instantvob_form_insurance" name="instantvob_form_insurance">
                <?php echo wp_kses($vendor_options, $allowed_html); ?>
              </select>
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_insurance_id">Insurance ID *</label>
              <input id="instantvob_form_insurance_id" name="instantvob_form_insurance_id" type="text" />
            </div>
          </div>
          <div class="cf-turnstile" data-sitekey="<?php echo esc_attr(
            $instantvob_cf_sitekey
          ); ?>"></div>
          <div class="instantvob_form_row instantvob_form_row__highlight">
            <!--anti-spam honeypot field, hidden by CSS. If filled by a bot, the form will not submit-->
            <div class="instantvob_form_row_cell">
              <label for="instantvob_form_highlight">Enter a note</label>
              <input id="instantvob_form_highlight" name="instantvob_form_highlight" type="text" />
            </div>
          </div>
          <div class="instantvob_form_row">
            <div class="instantvob_form_row_cell">
              <button id="instantvob_form_insurance_submit" name="instantvob_form_insurance_submit" type="submit">Submit</button>
            </div>
          </div>
          <div class="instantvob_form_error"></div>
          <div class="instantvob_form_message"></div>
        </form>
        <div class="instantvob_branding <?php echo esc_attr(
          $branding_shown_css_class
        ); ?>">
          <div class="instantvob_branding_container">
            <p>Powered by instantvob®</p>
            <a href="https://instantvob.com/" target="_blank" rel="noreferrer">
              <img class="instantvob_branding_logo" src="<?php echo esc_attr(
                $logo_url
              ); ?>" alt="instantvob logo. The tagline reads: Stop wasting time.">
            </a>
          </div>
        </div>
    </div>
  <?php return ob_get_clean();
}

function instantvob_shortcodes_init()
{
  add_shortcode('INSTANTVOB', 'instantvob_shortcode');
}

add_action('init', 'instantvob_shortcodes_init');
