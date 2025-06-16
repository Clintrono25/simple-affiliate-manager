<?php
/*
Plugin Name: Affiliate Managerr
Description:registration and dashboard.
Version: 1.0
Author: ClintRono
*/

if (!defined('ABSPATH')) exit;

// Register affiliate role on plugin activation
register_activation_hook(__FILE__, function() {
    add_role('affiliate', 'Affiliate', ['read' => true]);
});

// Remove affiliate role on plugin deactivation
register_deactivation_hook(__FILE__, function() {
    remove_role('affiliate');
});

// Define plugin path constant for easy includes
define('SAM_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Autoloader (simple)
spl_autoload_register(function($class) {
    if (strpos($class, 'SAM_') === 0) {
        require_once SAM_PLUGIN_PATH . 'includes/class-' . strtolower(str_replace('SAM_', '', $class)) . '.php';
    }
});

// Load registration handler!
require_once SAM_PLUGIN_PATH . 'includes/class-registration.php';

require_once SAM_PLUGIN_PATH . 'includes/class-woocommerce-integration.php';
// Register shortcodes
add_action('init', function() {
    new SAM_Shortcodes();
});
add_action('wp_footer', function() {
    ?>
    <script>
    (function() {
        var params = new URLSearchParams(window.location.search);
        if (params.has('ref')) {
            var ref = params.get('ref');
            document.cookie = "sam_affiliate_ref=" + encodeURIComponent(ref) + "; path=/; max-age=2592000"; // 30 days
        }
    })();
    </script>
    <?php
});