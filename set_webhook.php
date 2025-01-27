<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get bot token from environment variable
$botToken = getenv('BOT_TOKEN');

if (!$botToken) {
    die("BOT_TOKEN environment variable is not set");
}

// Get Railway domain from environment variable
$domain = getenv('RAILWAY_STATIC_URL');

if (!$domain) {
    die("RAILWAY_STATIC_URL is not set. Make sure you're running on Railway");
}

// Set webhook URL using Railway domain
$webhookUrl = "https://" . $domain . "/bot.php";

// Set webhook URL
$apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook";

// Setup webhook
$data = [
    'url' => $webhookUrl,
    'allowed_updates' => ['message', 'callback_query']
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Enable SSL verification for Railway
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);    // Enable host verification

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Log everything
echo "<pre>";
echo "Setting webhook to: " . $webhookUrl . "\n\n";
echo "Response: " . $response . "\n";
if ($error) {
    echo "Error: " . $error . "\n";
}

// Get current webhook info
$info = file_get_contents("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
echo "\nWebhook Info:\n" . $info;
echo "</pre>";
?>
