<?php
require __DIR__ . '/../includes/init.php';
$pageTitle = 'Contact Us | ' . $siteName;

require __DIR__ . '/../includes/header.php';
?>
    <section class="content-panel contact-panel">
        <div class="contact-header">
            <p class="eyebrow">We'd love to hear from you</p>
            <h1>Contact Us</h1>
            <p class="contact-intro">
                Have a question about an order, design, or custom print request? Send us a message and we’ll get back to you soon.
            </p>
        </div>

        <form class="detail-form contact-form">
            <div class="form-row">
                <label>
                    Full Name
                    <input type="text" name="full_name" placeholder="Enter your full name">
                </label>
                <label>
                    Email
                    <input type="email" name="email" placeholder="Enter your email">
                </label>
            </div>

            <label>
                Subject
                <input type="text" name="subject" placeholder="What is this about?">
            </label>

            <label>
                Message
                <textarea name="message" rows="7" placeholder="Write your message here"></textarea>
            </label>

            <button class="button contact-button" type="button">Send Message</button>
        </form>
    </section>
<?php require __DIR__ . '/../includes/footer.php'; ?>