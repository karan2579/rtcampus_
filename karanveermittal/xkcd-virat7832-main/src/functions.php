<?php

function generateVerificationCode(): string {
    return str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
}

function sendVerificationEmail(string $email, string $code): bool {
    $subject = "Your Verification Code";
    $body = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    return mail($email, $subject, $body, $headers);
}

function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }
    return true;
}

function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return false;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);
    return true;
}

function verifyCode(string $email, string $code): bool {
    $codeFile = __DIR__ . "/codes/$email.txt";
    if (!file_exists($codeFile)) return false;
    $storedCode = trim(file_get_contents($codeFile));
    return $storedCode === $code;
}

function fetchAndFormatXKCDData(): string {
    $maxComicId = 2800; // Safe upper range
    $randomId = rand(1, $maxComicId);
    $url = "https://xkcd.com/$randomId/info.0.json";
    $json = @file_get_contents($url);
    if ($json === false) return "<p>Could not fetch XKCD comic.</p>";
    $data = json_decode($json, true);
    $imgUrl = $data['img'] ?? '';
    return "
        <h2>XKCD Comic</h2>
        <img src=\"$imgUrl\" alt=\"XKCD Comic\">
        <p><a href=\"http://localhost/src/unsubscribe.php\" id=\"unsubscribe-button\">Unsubscribe</a></p>
    ";
}

function sendXKCDUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $htmlContent = fetchAndFormatXKCDData();
    $subject = "Your XKCD Comic";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    foreach ($emails as $email) {
        mail($email, $subject, $htmlContent, $headers);
    }
}
