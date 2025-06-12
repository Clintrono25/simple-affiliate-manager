<?php
$errors = get_transient('sam_affiliate_errors');
if ($errors) {
    echo '<div class="notice notice-error"><ul>';
    foreach ($errors as $e) echo '<li>' . esc_html($e) . '</li>';
    echo '</ul></div>';
    delete_transient('sam_affiliate_errors');
}

$is_subscriber = false;
$user = null;
$username = $first_name = $last_name = $email = '';
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    $is_subscriber = in_array('subscriber', (array)$user->roles);
    $username = $user->user_login;
    $email = $user->user_email;
    $first_name = get_user_meta($user->ID, 'first_name', true);
    $last_name = get_user_meta($user->ID, 'last_name', true);
}
?>
<form method="post">
    <label>Username:<br>
        <input type="text" name="username" required value="<?php echo esc_attr($username); ?>" <?php if ($username) echo 'readonly'; ?>>
    </label><br>
    <label>First Name:<br>
        <input type="text" name="first_name" required value="<?php echo esc_attr($first_name); ?>">
    </label><br>
    <label>Last Name:<br>
        <input type="text" name="last_name" required value="<?php echo esc_attr($last_name); ?>">
    </label><br>
    <label>Email:<br>
        <input type="email" name="email" required value="<?php echo esc_attr($email); ?>" <?php if ($email) echo 'readonly'; ?>>
    </label><br>
<?php if (!is_user_logged_in()) : ?>
    <label>Password:<br>
        <div style="position:relative;display:inline-block;">
            <input type="password" name="password" id="sam_password" required style="padding-right:30px;">
            <span style="position:absolute;top:8px;right:8px;cursor:pointer;" onclick="togglePassword()">
                <!-- Simple SVG eye icon -->
                <svg id="sam_eye_icon" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M10 4C5 4 1.73 7.11 0 10c1.73 2.89 5 6 10 6s8.27-3.11 10-6c-1.73-2.89-5-6-10-6zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" fill="#555"/>
                </svg>
            </span>
        </div>
        
    </label><br>
    <!--<small>Minimum 6 characters, 1 number, 1 special character, 1 lower and 1 upper case letter.</small>-->
    <script>
    function togglePassword() {
        var pwd = document.getElementById('sam_password');
        var eye = document.getElementById('sam_eye_icon');
        if (pwd.type === "password") {
            pwd.type = "text";
            eye.style.opacity = 0.5;
        } else {
            pwd.type = "password";
            eye.style.opacity = 1;
        }
    }
    </script>
<?php endif; ?>
    <?php if ($is_subscriber): ?>
    <label>Commission Rate (%):<br>
        <select name="commission_rate" required>
            <?php for($i=5; $i<=50; $i+=5): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?>%</option>
            <?php endfor; ?>
        </select>
    </label><br>
    <?php endif; ?>
    <button type="submit" name="sam_affiliate_register" value="1">Register as Affiliate</button>
</form>
