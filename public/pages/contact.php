<?php
require __DIR__ . '/../includes/config.php';
$pageTitle = 'Contact Us | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Contact Us</h1>
    <form class="detail-form">
        <label>
            Full Name
            <input type="text" name="full_name">
        </label>
        <label>
            Email
            <input type="email" name="email">
        </label>
        <label>
            Subject
            <input type="text" name="subject">
        </label>
        <label>
            Message
            <textarea name="message" rows="6"></textarea>
        </label>
        <button class="button" type="button">Send Message</button>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
