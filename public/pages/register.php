<?php
require __DIR__ . '/../includes/config.php';
$pageTitle = 'Register | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Create Account</h1>
    <form class="detail-form">
        <label>
            First Name
            <input type="text" name="first_name">
        </label>
        <label>
            Last Name
            <input type="text" name="last_name">
        </label>
        <label>
            Email
            <input type="email" name="email">
        </label>
        <label>
            Password
            <input type="password" name="password">
        </label>
        <button class="button" type="button">Register</button>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
