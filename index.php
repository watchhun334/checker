<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'debug.log');

// Health check endpoint
if ($_SERVER['REQUEST_URI'] === '/') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
    exit;
}

require_once 'config.php';
require_once 'bot.php';

// Get and log raw input
$raw_input = file_get_contents('php://input');
file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " Raw input: " . $raw_input . "\n", FILE_APPEND);

// Get the update from Telegram
$update = json_decode($raw_input, true);

// Process incoming message
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id'];
    $message = $update['message']['text'] ?? '';
    
    // Handle the command
    try {
        handleCommand($message, $chat_id);
    } catch (Exception $e) {
        file_put_contents('debug.log', date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

// Always respond with 200 OK to Telegram
http_response_code(200);
echo "OK";
?>
