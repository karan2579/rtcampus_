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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email']) && !isset($_POST['verification_code'])) {
        $email = trim($_POST['unsubscribe_email']);
        $code = generateVerificationCode();
        file_put_contents(__DIR__ . "/codes/$email.txt", $code);

        // Send unsubscribe confirmation email
        $subject = "Confirm Un-subscription";
        $body = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: no-reply@example.com\r\n";
        mail($email, $subject, $body, $headers);

        $step = 'verify';
        $message = "Unsubscription code sent to $email";

    } elseif (isset($_POST['verification_code']) && isset($_POST['email'])) {
        $email = trim($_POST['email']);
        $code = trim($_POST['verification_code']);
        if (verifyCode($email, $code)) {
            unsubscribeEmail($email);
            unlink(__DIR__ . "/codes/$email.txt");
            $message = "You have been unsubscribed successfully.";
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
    <title>Unsubscribe from XKCD</title>
</head>
<body>
    <h2>Unsubscribe from XKCD Emails</h2>

    <?php if ($message): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <?php if ($step === 'email'): ?>
        <form method="POST">
            <label>Enter your email to unsubscribe:</label><br>
            <input type="email" name="unsubscribe_email" required>
            <button id="submit-unsubscribe">Unsubscribe</button>
        </form>
    <?php elseif ($step === 'verify'): ?>
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <label>Enter the 6-digit code sent to your email:</label><br>
            <input type="text" name="verification_code" maxlength="6" required>
            <button id="submit-verification">Verify</button>
        </form>
    <?php elseif ($step === 'done'): ?>
        <p>You have successfully unsubscribed from XKCD comics.</p>
    <?php endif; ?>
</body>
</html>


