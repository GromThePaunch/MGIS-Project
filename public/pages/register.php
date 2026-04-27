<?php
// register.php
require __DIR__ . '/../includes/init.php';

$pageTitle = 'Register | ' . $siteName;
require __DIR__ . '/../includes/header.php';

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = strtolower(trim($_POST['email'] ?? ''));
    $password   = $_POST['password'] ?? '';

    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $check = $pdo->prepare("SELECT customer_id FROM customers WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "An account with that email already exists. Please log in instead.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO customers (first_name, last_name, email, password_hash, created_at)
                 VALUES (?, ?, ?, ?, NOW())"
            );

            if ($stmt->execute([$first_name, $last_name, $email, $password_hash])) {
                header("Location: {$basepath}pages/login.php?registered=1");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<h1>Create Account</h1>

<?php if ($error): ?>
    <p style="color: #c00; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" style="max-width: 400px;">
    <label>First Name<br>
        <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
    </label><br><br>

    <label>Last Name<br>
        <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
    </label><br><br>

    <label>Email<br>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </label><br><br>

    <label>Password (min 6 characters)<br>
        <input type="password" name="password" required>
    </label><br><br>

    <button type="submit" class="button">Sign Up</button>
</form>

<p>Already have an account? <a href="<?= $basepath ?>pages/login.php">Login here</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>