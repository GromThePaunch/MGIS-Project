<?php
require __DIR__ . '/../includes/config.php';
$pageTitle = 'Login | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Login</h1>
    <form class="detail-form">
        <label>
            Email
            <input type="email" name="email">
        </label>
        <label>
            Password
            <input type="password" name="password">
        </label>
        <button class="button" type="button">Login</button>
        <p><a href="/pages/register.php">Create account</a></p>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
