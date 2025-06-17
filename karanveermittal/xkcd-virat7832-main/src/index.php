<?php
require_once 'functions.php';

$step = 'email';
$message = '';
$email = '';
$code = '';

// Ensure `codes` folder exists
if (!file_exists(__DIR__ . '/codes')) {
    mkdir(__DIR__ . '/codes', 0777, true);
}

// Handle email submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !isset($_POST['verification_code'])) {
        $email = trim($_POST['email']);
        $code = generateVerificationCode();
        file_put_contents(__DIR__ . "/codes/$email.txt", $code);
        sendVerificationEmail($email, $code);
        $step = 'verify';
        $message = "Verification code sent to $email";

    } elseif (isset($_POST['verification_code']) && isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $code = trim($_POST['verification_code']);
        if (verifyCode($email, $code)) {
            registerEmail($email);
            $message = "Email verified and subscribed successfully!";
            unlink(__DIR__ . "/codes/$email.txt");
            $step = 'done';
        } else {
            $message = "Invalid verification code.";
            $step = 'verify';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>XKCD Email Subscription</title>
</head>
<body>
    <h2>XKCD Comic Subscription</h2>

    <?php if ($message): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <?php if ($step === 'email'): ?>
        <form method="POST">
            <label>Enter your email:</label><br>
            <input type="email" name="email" required>
            <button id="submit-email">Submit</button>
        </form>
    <?php elseif ($step === 'verify'): ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <label>Enter the 6-digit code sent to your email:</label><br>
            <input type="text" name="verification_code" maxlength="6" required>
            <button id="submit-verification">Verify</button>
        </form>
    <?php elseif ($step === 'done'): ?>
        <p>You are now subscribed! You’ll receive a daily XKCD comic.</p>
    <?php endif; ?>
</body>
</html>
