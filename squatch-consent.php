<?php
/*
Plugin Name: Squatch Consent
Plugin URI: https://squatchcreative.com
Description: A simple cookie consent banner that allows visitors to opt in to cookies and tracking.
Version: 1.003
Author: Squatch Creative
Author URI: https://squatchcreative.com
*/

$plugin_data = get_file_data(__FILE__,array('Version' => 'Version'));
$plugin_version = $plugin_data['Version'];

define('SQUATCH_CONSENT_PLUGIN', plugin_dir_url(__FILE__));
define('SQUATCH_CONSENT_PATH', plugin_dir_path(__FILE__));

add_action('wp_enqueue_scripts', 'squatch_consent_enqueue_assets');
function squatch_consent_enqueue_assets() {	
	wp_enqueue_style( 'squatch-consent', SQUATCH_CONSENT_PLUGIN . 'assets/squatch-consent.css',	array(), $plugin_version);
	wp_enqueue_script('squatch-consent', SQUATCH_CONSENT_PLUGIN . 'assets/squatch-consent.js', array(), $plugin_version, true);	
	wp_localize_script(
		'squatch-consent',
		'squatchConsentData',
		array(
			'cookieName' => 'squatch_consent',
			'privacyUrl' => get_privacy_policy_url()
		)
	);
}

function squatch_consent_menu() {
	add_submenu_page(
		'tools.php',
		'Squatch Consent',
		'Squatch Consent',
		'manage_options',
		'squatch-consent',
		'squatch_consent_page'
	);
}
add_action('admin_menu', 'squatch_consent_menu');

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'squatch_consent_settings_link');
function squatch_consent_settings_link($links) {
	$url = admin_url('tools.php?page=squatch-consent');
	$settings_link = '<a href="' . esc_url($url) . '">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}

add_filter('admin_footer_text', 'squatch_admin_footer_text_consent');
function squatch_admin_footer_text_consent($footer_text) {
	$screen = get_current_screen();
	if ($screen && $screen->id === 'tools_page_squatch-consent') {
		$img_url = SQUATCH_CONSENT_PLUGIN . 'assets/built-by-squatch.svg'; 
		ob_start();
		?>
		<span id="footer-thankyou">
			<a href="https://squatchcreative.com" title="Built By Squatch Creative" target="_blank">
				<img src="<?php echo esc_url($img_url); ?>" alt="Built By Squatch Creative">
			</a>
		</span>
		<?php
		return ob_get_clean();
	}

	return $footer_text;
}

add_action('admin_head', function() {
	$screen = get_current_screen();
	if($screen && $screen->id === 'tools_page_squatch-consent') {
		?>
		<style>
		.squatch-plugin-header {
			display: flex;
			gap: 18px;
			align-items: center;
			padding: 18px 0;
		}
		.squatch-plugin-header img {
			display: block;
			margin: 0;
			width: 54px;
			height: 54px;
			background: black;
			border-radius: 50%;
			padding: 2px;
		}
		.squatch-header-text * {
			margin: 0 !important;
			padding: 0 !important;
		}
		#squatch_consent_form {
			transition: 180ms ease all;
			position: relative;
			display: flex;
			gap: 24px;
			flex-flow: column;
			align-items: flex-start;
		}
		#footer-thankyou img {
			height:28px;vertical-align:middle; 
		}
		#squatch_consent_form label {
			display: block;
			min-width: 120px;
		}
		.form-field {
			display: flex;
			gap: 18px;
			width: 540px;
			max-width: 100%;
		}
		.form-field input, .form-field select {
			display: block;
			flex: 1;
		}
		.form-field .checkbox-holder {
			display: flex;
			flex-flow: row;
			gap: 6px;
			align-items: center;
		}
		.form-field input[type="checkbox"] {
			max-width: 18px;
			margin:0;
		}
		</style>
		<?php
	}
});



function squatch_consent_page() {
	
	if(!current_user_can('manage_options')) {
		wp_die('Unauthorized');
	}
	
	if(
		isset($_POST['_wpnonce']) &&
		wp_verify_nonce($_POST['_wpnonce'], 'squatch_consent_save')
	) {

		update_option(
			'squatch_consent_text',
			sanitize_textarea_field($_POST['consent_text'] ?? '')
		);

		update_option(
			'squatch_consent_background_color',
			sanitize_hex_color($_POST['background_color'] ?? '#000000')
		);

		update_option(
			'squatch_consent_text_color',
			sanitize_hex_color($_POST['text_color'] ?? '#ffffff')
		);

		update_option(
			'squatch_consent_button_color',
			sanitize_hex_color($_POST['button_color'] ?? '#ffd747')
		);

		update_option(
			'squatch_consent_privacy_link',
			isset($_POST['privacy_link']) ? 1 : 0
		);

		update_option(
			'squatch_consent_scripts',
			wp_unslash($_POST['consent_scripts'] ?? '')
		);

		echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
	}

	$img_url = SQUATCH_CONSENT_PLUGIN . 'assets/squatch-mark-yellow.svg';

	$consent_text = get_option(
		'squatch_consent_text',
		'We use cookies and similar technologies to improve your experience, analyze traffic, and better understand how visitors use our website.'
	);

	$background_color = get_option('squatch_consent_background_color', '#000000');
	$text_color = get_option('squatch_consent_text_color', '#ffffff');
	$button_color = get_option('squatch_consent_button_color', '#ffd747');
	$privacy_link = get_option('squatch_consent_privacy_link', 1);
	$consent_scripts = get_option('squatch_consent_scripts', '');

	echo '<div class="wrap">';

	echo '<div class="squatch-plugin-header">';
	echo '<img src="' . esc_url($img_url) . '" alt="Built By Squatch Creative">';
	echo '<div class="squatch-header-text">';
	echo '<h1>Squatch Consent</h1>';
	echo '<p>A lightweight cookie consent banner that allows visitors to opt in to cookies and tracking. Configure the banner appearance, consent message, and any tracking scripts that should only load after consent is granted. <a href="https://github.com/RCNeil/squatch-consent" target="_blank">View Details</a></p>';
	echo '</div>';
	echo '</div>';

	echo '<form id="squatch_consent_form" method="post">';

	echo '<div class="form-field">';
	echo '<label for="consent_text"><strong>Consent Message</strong></label>';
	echo '<textarea id="consent_text" name="consent_text" rows="4">' . esc_textarea($consent_text) . '</textarea>';
	echo '</div>';

	echo '<div class="form-field">';
	echo '<label for="background_color"><strong>Background Color</strong></label>';
	echo '<input type="color" id="background_color" name="background_color" value="' . esc_attr($background_color) . '">';
	echo '</div>';

	echo '<div class="form-field">';
	echo '<label for="text_color"><strong>Text Color</strong></label>';
	echo '<input type="color" id="text_color" name="text_color" value="' . esc_attr($text_color) . '">';
	echo '</div>';

	echo '<div class="form-field">';
	echo '<label for="button_color"><strong>Button Color</strong></label>';
	echo '<input type="color" id="button_color" name="button_color" value="' . esc_attr($button_color) . '">';
	echo '</div>';

	echo '<div class="form-field">';
	echo '<label for="privacy_link"><strong>Privacy Policy Link</strong></label>';
	echo '<div class="checkbox-holder">';
	echo '<input type="checkbox" id="privacy_link" name="privacy_link" value="1" ' . checked($privacy_link, 1, false) . '>';
	echo '<span>Include a link to the site privacy policy page.</span>';
	echo '</div>';
	echo '</div>';

	echo '<div class="form-field">';
	echo '<label for="consent_scripts"><strong>Tracking Scripts</strong></label>';
	echo '<textarea id="consent_scripts" name="consent_scripts" rows="12" placeholder="Paste Google Analytics, Google Tag Manager, Meta Pixel, or other tracking scripts that should only load after a visitor grants consent.">' . esc_textarea($consent_scripts) . '</textarea>';
	echo '</div>';

	echo '<input type="hidden" name="_wpnonce" value="' . esc_attr(wp_create_nonce('squatch_consent_save')) . '">';

	echo '<button type="submit" class="button button-primary">Save Settings</button>';

	echo '</form>';

	echo '</div>';
}








add_action('wp_footer', 'squatch_consent_render_banner', 100);
function squatch_consent_render_banner() {
	$text = get_option('squatch_consent_text', '');
	$bg = get_option('squatch_consent_background_color', '#000000');
	$color = get_option('squatch_consent_text_color', '#ffffff');
	$button = get_option('squatch_consent_button_color', '#ffd747');
	$button_text_color = squatch_consent_contrast_color($button);
	$privacy = get_option('squatch_consent_privacy_link', 1);
	?>
	<div id="squatch-consent-banner" style="display:none;--sq-bg: <?php echo esc_attr($bg); ?>;--sq-text: <?php echo esc_attr($color); ?>;--sq-button: <?php echo esc_attr($button); ?>;--sq-button-text: <?php echo esc_attr($button_text_color); ?>;">
		<div class="squatch-consent-inner">
			<p>
				<?php echo esc_html($text); ?>
				<?php if($privacy == 1): ?>
					<a href="<?php echo esc_url(get_privacy_policy_url()); ?>">Privacy Policy</a>
				<?php endif; ?>
			</p>			

			<div class="squatch-consent-actions">
				<button id="squatch-consent-accept">Accept</button>
				<button id="squatch-consent-reject">Reject</button>
			</div>
		</div>
	</div>
	<?php 
}





function squatch_consent_prepare_scripts($scripts) {
	$scripts = preg_replace_callback(
		'#<script([^>]*)>#i',
		function($matches) {
			$attributes = $matches[1];
			if(stripos($attributes, 'type=') !== false) {
				$attributes = preg_replace(
					'/type\s*=\s*([\'"]).*?\1/i',
					'type="text/plain"',
					$attributes
				);
			} else {
				$attributes .= ' type="text/plain"';
			}
			if(stripos($attributes, 'data-squatch-consent') === false) {
				$attributes .= ' data-squatch-consent';
			}
			return '<script' . $attributes . '>';
		},
		$scripts
	);
	return $scripts;
}

add_action('wp_footer', 'squatch_consent_render_scripts', 20);
function squatch_consent_render_scripts() {
	$scripts = get_option('squatch_consent_scripts', '');
	if(empty($scripts)) {
		return;
	}
	echo squatch_consent_prepare_scripts($scripts);
}
















function squatch_consent_contrast_color($hex) {
	$hex = ltrim($hex, '#');
	if(strlen($hex) === 3) {
		$hex =
			$hex[0] . $hex[0] .
			$hex[1] . $hex[1] .
			$hex[2] . $hex[2];
	}
	$r = hexdec(substr($hex, 0, 2));
	$g = hexdec(substr($hex, 2, 2));
	$b = hexdec(substr($hex, 4, 2));
	$brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
	return $brightness > 128 ? '#000000' : '#ffffff';
}