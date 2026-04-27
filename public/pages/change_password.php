<?php
require __DIR__ . '/../includes/init.php';

if (!isLoggedIn()) {
    header("Location: {$basepath}pages/login.php");
    exit;
}

$customer_id = (int) $_SESSION['customer_id'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($current) || empty($new) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters long.";
    } elseif ($new !== $confirm) {
        $error = "New password and confirmation do not match.";
    } elseif ($new === $current) {
        $error = "New password must be different from your current password.";
    } else {
        $stmt = $pdo->prepare("SELECT password_hash FROM customers WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($current, $row['password_hash'])) {
            $error = "Current password is incorrect.";
        } else {
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE customers SET password_hash = ? WHERE customer_id = ?");
            $upd->execute([$new_hash, $customer_id]);
            $success = "Your password has been updated.";
        }
    }
}

$pageTitle = 'Change Password | ' . $siteName;
require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Change Password</h1>

    <?php if ($success): ?>
        <p style="color:#060; font-weight:bold;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color:#c00; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" class="detail-form" style="max-width:400px;">
        <label>Current Password<br>
            <input type="password" name="current_password" required>
        </label><br><br>

        <label>New Password (min 6 characters)<br>
            <input type="password" name="new_password" required>
        </label><br><br>

        <label>Confirm New Password<br>
            <input type="password" name="confirm_password" required>
        </label><br><br>

        <button type="submit" class="button">Update Password</button>
    </form>

    <p><a href="<?= $basepath ?>pages/profile.php">Back to Profile</a></p>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
