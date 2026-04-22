<?php
require __DIR__ . '/../includes/init.php';
$pageTitle = 'Contact Us | ' . $siteName;

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($fullName === '' || $email === '' || $subject === '' || $message === '') {
        $errorMessage = 'Please fill out all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        $to = 'pa8672@rit.edu';
        $mailSubject = 'Website Contact: ' . $subject;

        $body = "You received a new message from your website.\n\n";
        $body .= "Full Name: {$fullName}\n";
        $body .= "Email: {$email}\n";
        $body .= "Subject: {$subject}\n\n";
        $body .= "Message:\n{$message}\n";

        $headers = "From: noreply@rit.edu\r\n";
        $headers .= "Reply-To: {$email}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($to, $mailSubject, $body, $headers)) {
            $successMessage = 'Your message was sent successfully.';
            $_POST = []; // clears form
        } else {
            $errorMessage = 'Form submitted, but email could not be sent.';
        }
    }
}

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

        <?php if ($successMessage): ?>
            <p class="form-success"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <p class="form-error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <form class="detail-form contact-form" method="post" action="">
            <div class="form-row">
                <label>
                    Full Name
                    <input
                            type="text"
                            name="full_name"
                            placeholder="Enter your full name"
                            value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                    >
                </label>

                <label>
                    Email
                    <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    >
                </label>
            </div>

            <label>
                Subject
                <input
                        type="text"
                        name="subject"
                        placeholder="What is this about?"
                        value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                >
            </label>

            <label>
                Message
                <textarea
                        name="message"
                        rows="7"
                        placeholder="Write your message here"
                ><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
            </label>

            <button class="button contact-button" type="submit">Send Message</button>
        </form>
    </section>

<?php require __DIR__ . '/../includes/footer.php'; ?>