<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Get the domain from Railway's environment variable
$domain = getenv('RAILWAY_PUBLIC_DOMAIN');

if (!$domain) {
    // Fallback to current host if not on Railway
    $domain = $_SERVER['HTTP_HOST'];
}

// Set webhook URL
$webhookUrl = "https://" . $domain . "/bot.php";

// Set webhook URL
$apiUrl = "https://api.telegram.org/bot" . BOT_TOKEN . "/setWebhook";
$data = [
    'url' => $webhookUrl,
    'allowed_updates' => ['message', 'callback_query']
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

// Display results
echo "<pre>";
echo "Setting webhook to: " . $webhookUrl . "\n\n";
echo "Response: " . $response . "\n";
if ($error) {
    echo "Error: " . $error . "\n";
}

// Get current webhook info
$info = file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/getWebhookInfo");
echo "Webhook Info:\n" . $info;
echo "</pre>";
?>
