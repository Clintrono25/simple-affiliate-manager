<?php
if (!defined('ABSPATH')) exit;

add_action('init', function() {
    if (isset($_POST['sam_affiliate_register'])) {
        $username = sanitize_user($_POST['username']);
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $email = sanitize_email($_POST['email']);
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $commission_rate = isset($_POST['commission_rate']) ? intval($_POST['commission_rate']) : null;
        $errors = [];

        // Username validation
        if (empty($username)) $errors[] = 'Username is required.';
        elseif (username_exists($username) && !is_user_logged_in()) $errors[] = 'Username already exists.';

        // First/Last name validation
        if (empty($first_name)) $errors[] = 'First name is required.';
        if (empty($last_name)) $errors[] = 'Last name is required.';

        // Email validation
        if (!is_email($email)) $errors[] = 'Invalid email.';

        // Password validation for new users only
        if (!is_user_logged_in()) {
            if (empty($password)) {
                $errors[] = 'Password required.';
            } else {
                if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
                if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Password must contain at least one uppercase letter.';
                if (!preg_match('/[a-z]/', $password)) $errors[] = 'Password must contain at least one lowercase letter.';
                if (!preg_match('/\d/', $password)) $errors[] = 'Password must contain at least one number.';
                if (!preg_match('/[\W_]/', $password)) $errors[] = 'Password must contain at least one special character.';
            }
        }

        // Subscriber check for commission rate
        $is_subscriber = false;
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $is_subscriber = in_array('subscriber', (array)$user->roles);
        } else if ($email && email_exists($email)) {
            $user = get_user_by('email', $email);
            $is_subscriber = (bool)$user && in_array('subscriber', (array)$user->roles);
        }

        if ($is_subscriber) {
            if ($commission_rate < 5 || $commission_rate > 50 || $commission_rate % 5 !== 0) {
                $errors[] = 'Commission rate must be between 5 and 50 in steps of 5.';
            }
        }

        if (empty($errors)) {
            if (is_user_logged_in()) {
                $user = wp_get_current_user();
                if (!in_array('affiliate', (array)$user->roles)) {
                    $user->add_role('affiliate');
                }
                update_user_meta($user->ID, 'first_name', $first_name);
                update_user_meta($user->ID, 'last_name', $last_name);
                if ($is_subscriber) {
                    update_user_meta($user->ID, 'affiliate_commission_rate', $commission_rate);
                }
            } else if (email_exists($email)) {
                $user = get_user_by('email', $email);
                if (!in_array('affiliate', (array)$user->roles)) {
                    $user->add_role('affiliate');
                }
                update_user_meta($user->ID, 'first_name', $first_name);
                update_user_meta($user->ID, 'last_name', $last_name);
                if ($is_subscriber) {
                    update_user_meta($user->ID, 'affiliate_commission_rate', $commission_rate);
                }
                wp_set_auth_cookie($user->ID);
            } else {
                // Create new user with username
                $user_id = wp_create_user($username, $password, $email);
                if (!is_wp_error($user_id)) {
                    $user = get_user_by('id', $user_id);
                    $user->add_role('affiliate');
                    update_user_meta($user_id, 'first_name', $first_name);
                    update_user_meta($user_id, 'last_name', $last_name);
                    if ($commission_rate && $commission_rate >= 5 && $commission_rate <= 50 && $commission_rate % 5 === 0) {
                        update_user_meta($user_id, 'affiliate_commission_rate', $commission_rate);
                    }
                    wp_set_auth_cookie($user_id);
                } else {
                    $errors[] = $user_id->get_error_message();
                }
            }
        }

        if (!empty($errors)) {
            set_transient('sam_affiliate_errors', $errors, 30);
        } else {
            wp_redirect(home_url('/affiliate-dashboard'));
            exit;
        }
    }
});