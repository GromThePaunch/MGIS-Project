<?php
require __DIR__ . '/../includes/init.php';
$pageTitle = 'Login | ' . $siteName;

$error = "";
$notice = "";

if (isset($_GET['registered'])) {
    $notice = "Account created. Please log in.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT customer_id, first_name, password_hash
             FROM customers WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['customer_id'] = (int) $user['customer_id'];
            $_SESSION['first_name']  = $user['first_name'];
            header("Location: {$basepath}pages/profile.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>
<section class="content-panel">
    <h1>Login</h1>

    <?php if ($notice): ?>
        <p style="color: #060; font-weight: bold;"><?= htmlspecialchars($notice) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color: #c00; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="detail-form" method="POST">
        <label>
            Email
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button class="button" type="submit">Login</button>
        <p><a href="<?= $basepath ?>pages/register.php">Create account</a></p>
    </form>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
