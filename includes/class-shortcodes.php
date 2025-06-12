<?php
if (!defined('ABSPATH')) exit;

class SAM_Shortcodes {
    public function __construct() {
        add_shortcode('affiliate_register', [$this, 'register_form']);
        add_shortcode('affiliate_dashboard', [$this, 'dashboard']);
    }

    public function register_form() {
        if (is_user_logged_in() && $this->is_affiliate(get_current_user_id())) {
            return '<div class="notice notice-success">You are already an affiliate. <a href="' . esc_url(home_url('/affiliate-dashboard')) . '">Go to Dashboard</a></div>';
        }
        ob_start();
        include SAM_PLUGIN_PATH . 'templates/register-form.php';
        return ob_get_clean();
    }

    public function dashboard() {
        if (!is_user_logged_in() || !$this->is_affiliate(get_current_user_id())) {
            return '<div class="notice notice-error">Please login as an affiliate to access the dashboard.</div>';
        }
        ob_start();
        include SAM_PLUGIN_PATH . 'templates/dashboard.php';
        return ob_get_clean();
    }

    public function is_affiliate($user_id) {
        $user = get_userdata($user_id);
        return $user && in_array('affiliate', (array)$user->roles);
    }
}