<?php
$user = wp_get_current_user();
echo '<h2>Affiliate Dashboard</h2>';
echo '<p>Welcome, ' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</p>';
?>
<p>You are now an affiliate!</p>