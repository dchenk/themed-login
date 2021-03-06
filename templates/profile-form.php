<?php
// If you would like to edit this file, copy it to your current theme's directory and edit it there.
// This plugin will always look in your theme's directory first, before using this default template.

global $themedLoginInstance;
$template = $themedLoginInstance->current_instance;

?>
<div class="tml tml-profile" id="themed-login<?php $template->instance_id(); ?>">
	<?php $template->the_action_template_message('profile'); ?>
	<?php $template->the_errors(); ?>
	<form id="your-profile" action="<?php $template->the_action_url('profile', 'login_post'); ?>" method="post">
		<?php wp_nonce_field('update-user_' . $current_user->ID); ?>

		<input type="hidden" name="from" value="profile">
		<input type="hidden" name="checkuser_id" value="<?php echo $current_user->ID; ?>">

		<?php
		if (apply_filters('show_admin_bar', true) || has_action('personal_options')) {
			?>
			<h3><?php _e('Personal Options', 'themed-login'); ?></h3>

			<table class="tml-form-table">
			<?php
			if (apply_filters('show_admin_bar', true)) {
				?>
				<tr class="tml-user-admin-bar-front-wrap">
					<th><label for="admin_bar_front"><?php _e('Toolbar', 'themed-login'); ?></label></th>
					<td>
						<label for="admin_bar_front"><input type="checkbox" name="admin_bar_front" id="admin_bar_front" value="1"<?php checked(_get_admin_bar_pref('front', $profileuser->ID)); ?>>
						<?php _e('Show Toolbar when viewing site', 'themed-login'); ?></label>
					</td>
				</tr><?php
			} ?>
			<?php do_action('personal_options', $profileuser); ?>
			</table>
			<?php
		}

		do_action('profile_personal_options', $profileuser); ?>

		<h3><?php _e('Name', 'themed-login'); ?></h3>

		<table class="tml-form-table">
		<tr class="tml-user-login-wrap">
			<th><label for="user_login"><?php _e('Username', 'themed-login'); ?></label></th>
			<td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr($profileuser->user_login); ?>" disabled="disabled" class="regular-text"> <span class="description"><?php _e('Usernames cannot be changed.', 'themed-login'); ?></span></td>
		</tr>

		<tr class="tml-first-name-wrap">
			<th><label for="first_name"><?php _e('First Name', 'themed-login'); ?></label></th>
			<td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($profileuser->first_name); ?>" class="regular-text"></td>
		</tr>

		<tr class="tml-last-name-wrap">
			<th><label for="last_name"><?php _e('Last Name', 'themed-login'); ?></label></th>
			<td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($profileuser->last_name); ?>" class="regular-text"></td>
		</tr>

		<tr class="tml-nickname-wrap">
			<th><label for="nickname"><?php _e('Nickname', 'themed-login'); ?> <span class="description"><?php _e('(required)', 'themed-login'); ?></span></label></th>
			<td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr($profileuser->nickname); ?>" class="regular-text"></td>
		</tr>

		<tr class="tml-display-name-wrap">
			<th><label for="display_name"><?php _e('Display name publicly as', 'themed-login'); ?></label></th>
			<td>
				<select name="display_name" id="display_name">
				<?php
					$public_display = [];
					$public_display['display_nickname']  = $profileuser->nickname;
					$public_display['display_username']  = $profileuser->user_login;

					if (!empty($profileuser->first_name)) {
						$public_display['display_firstname'] = $profileuser->first_name;
					}

					if (!empty($profileuser->last_name)) {
						$public_display['display_lastname'] = $profileuser->last_name;
					}

					if (!empty($profileuser->first_name) && ! empty($profileuser->last_name)) {
						$public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
						$public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
					}

					if (!in_array($profileuser->display_name, $public_display, true)) {// Only add this if it isn't duplicated elsewhere
						$public_display = ['display_displayname' => $profileuser->display_name] + $public_display;
					}

					$public_display = array_map('trim', $public_display);
					$public_display = array_unique($public_display);

					foreach ($public_display as $id => $item) {
						?>
						<option <?php selected($profileuser->display_name, $item); ?>><?php echo $item; ?></option>
						<?php
					}
				?>
				</select>
			</td>
		</tr>
		</table>

		<h3><?php _e('Contact Info', 'themed-login'); ?></h3>

		<table class="tml-form-table">
		<tr class="tml-user-email-wrap">
			<th><label for="email"><?php _e('E-mail', 'themed-login'); ?> <span class="description"><?php _e('(required)', 'themed-login'); ?></span></label></th>
			<td><input type="text" name="email" id="email" value="<?php echo esc_attr($profileuser->user_email); ?>" class="regular-text"></td>
			<?php
			$new_email = get_option($current_user->ID . '_new_email');
			if ($new_email && $new_email['newemail'] != $current_user->user_email) {
				?>
			<div class="updated inline">
			<p><?php
				printf(
					__('There is a pending change of your e-mail to %1$s. <a href="%2$s">Cancel</a>', 'themed-login'),
					'<code>' . $new_email['newemail'] . '</code>',
					esc_url(self_admin_url('profile.php?dismiss=' . $current_user->ID . '_new_email'))
			); ?></p>
			</div>
			<?php
			} ?>
		</tr>

		<tr class="tml-user-url-wrap">
			<th><label for="url"><?php _e('Website', 'themed-login'); ?></label></th>
			<td><input type="text" name="url" id="url" value="<?php echo esc_attr($profileuser->user_url); ?>" class="regular-text code"></td>
		</tr>

		<?php
		foreach (wp_get_user_contact_methods() as $name => $desc) {
			?>
			<tr class="tml-user-contact-method-<?php echo $name; ?>-wrap">
				<th><label for="<?php echo $name; ?>"><?php echo apply_filters('user_' . $name . '_label', $desc); ?></label></th>
				<td><input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr($profileuser->{$name}); ?>" class="regular-text"></td>
			</tr>
			<?php
		}
		?>
		</table>

		<h3><?php _e('About Yourself', 'themed-login'); ?></h3>

		<table class="tml-form-table">
		<tr class="tml-user-description-wrap">
			<th><label for="description"><?php _e('Biographical Info', 'themed-login'); ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="30"><?php echo esc_html($profileuser->description); ?></textarea><br>
			<span class="description"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.', 'themed-login'); ?></span></td>
		</tr>
		</table>

		<?php
		$show_password_fields = apply_filters('show_password_fields', true, $profileuser);
		if ($show_password_fields) {
			?>
			<h3><?php _e('Account Management', 'themed-login'); ?></h3>
			<table class="tml-form-table">
			<tr id="password" class="user-pass1-wrap">
				<th><label for="pass1"><?php _e('New Password', 'themed-login'); ?></label></th>
				<td>
					<input class="hidden" value=" "><!-- #24364 workaround -->
					<button type="button" class="button button-secondary wp-generate-pw hide-if-no-js"><?php _e('Generate Password', 'themed-login'); ?></button>
					<div class="wp-pwd hide-if-js">
						<span class="password-input-wrapper">
							<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr(wp_generate_password(24)); ?>" aria-describedby="pass-strength-result">
						</span>
						<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
						<button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e('Hide password', 'themed-login'); ?>">
							<span class="dashicons dashicons-hidden"></span>
							<span class="text"><?php _e('Hide', 'themed-login'); ?></span>
						</button>
						<button type="button" class="button button-secondary wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e('Cancel password change', 'themed-login'); ?>">
							<span class="text"><?php _e('Cancel', 'themed-login'); ?></span>
						</button>
					</div>
				</td>
			</tr>
			<tr class="user-pass2-wrap hide-if-js">
				<th scope="row"><label for="pass2"><?php _e('Repeat New Password', 'themed-login'); ?></label></th>
				<td>
					<input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="off">
					<p class="description"><?php _e('Type your new password again.', 'themed-login'); ?></p>
				</td>
			</tr>
			<tr class="pw-weak">
				<th><?php _e('Confirm Password', 'themed-login'); ?></th>
				<td>
					<label>
						<input type="checkbox" name="pw_weak" class="pw-checkbox">
						<?php _e('Confirm use of weak password', 'themed-login'); ?>
					</label>
				</td>
			</tr>
			</table>
			<?php
		}

		do_action('show_user_profile', $profileuser); ?>

		<p class="tml-submit-wrap">
			<input type="hidden" name="action" value="profile">
			<input type="hidden" name="instance" value="<?php $template->instance_id(); ?>">
			<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr($current_user->ID); ?>">
			<input type="submit" class="button-primary" value="<?php esc_attr_e('Update Profile', 'themed-login'); ?>" name="submit" id="submit">
		</p>
	</form>
</div>
