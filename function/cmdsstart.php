<?php


$users = file_get_contents('Database/free.txt');
$freeusers = explode("\n", $users);

$videoURLStart = "https://t.me/BROKEN_CC/9";


if (preg_match('/^(\/start|\.start|!start)/', $text)) {
    if (in_array($userId, $freeusers)) {
        $caption = "𝘿𝙤𝙣'𝙩 𝙒𝙤𝙧𝙧𝙮, 𝙄'𝙢 𝙩𝙝𝙚 𝙨𝙩𝙧𝙤𝙣𝙜𝙚𝙨𝙩 💪
𝙃𝙚𝙮 <code>@$username</code> 𝙄'𝙢 𝙎𝙖𝙩𝙤𝙧𝙪 𝙂𝙤𝙟𝙤
𝘽𝙮 𝙏𝙝𝙚 𝙒𝙖𝙮 𝙔𝙤𝙪'𝙧𝙚 𝙄𝙙 - <code>$userId</code>

𝙏𝙝𝙖𝙩 𝙜𝙪𝙮 <code>@$username</code> 🤤… 𝙃𝙚’𝙨 𝙘𝙧𝙖𝙯𝙮 𝙪𝙥 𝙝𝙚𝙧𝙚. 𝙏𝙤𝙙𝙖𝙮, 𝙄 𝙬𝙖𝙣𝙣𝙖 𝙨𝙚𝙚 … 𝙃𝙤𝙬 𝙘𝙧𝙖𝙯𝙮 𝙨𝙝𝙚 𝙘𝙖𝙣 𝙜𝙚𝙩

𝙅𝙪𝙨𝙩 𝘾𝙡𝙞𝙘𝙠 '/cmds' 𝙏𝙤 𝙁𝙚𝙚𝙡 𝙈𝙮 𝙋𝙤𝙬𝙚𝙧 💠";
        sendVideox($chatId, $videoURLStart, $caption, $keyboard);
    } else {
        reply_tox($chatId,$message_id,$keyboard,"<code>You are not registered, Register first with</code> /register <code> to use me</code>");
    }
}
//=========START END========//
if (preg_match('/^(\/cmds|\.cmds|!cmds)/', $text)) {
  
    $videoUrl = "https://t.me/BROKEN_CC/9"; 

    $keyboard2 = json_encode([
        'inline_keyboard' => [
            [
                ['text' => '𝙂𝙖𝙩𝙚𝙬𝙖𝙮𝙨 ⚡', 'callback_data' => 'gates'],
                ['text' => '𝙏𝙤𝙤𝙡 𝙆𝙞𝙩 🔧', 'callback_data' => 'herr'],
                ['text' => '𝙋𝙧𝙞𝙘𝙚 💸', 'callback_data' => 'price'],
            ],
            [
                ['text' => '𝙈𝙮 𝙒𝙤𝙧𝙡𝙙 🌍', 'callback_data' => 'channel'],
            ],
        ]
    ]);

    $caption = "𝙃𝙚𝙮 @$username 𝙒𝙘𝙡𝙢 𝙏𝙤 𝙈𝙮 𝘾𝙪𝙧𝙨𝙚𝙙 𝙀𝙣𝙚𝙧𝙜𝙮 ☄️

𝘾𝙝𝙚𝙘𝙠 𝙈𝙮 𝘾𝙪𝙧𝙨𝙚𝙙 𝙀𝙣𝙚𝙧𝙜𝙮 𝘽𝙮 𝘾𝙡𝙞𝙘𝙠𝙞𝙣𝙜 𝘽𝙪𝙩𝙩𝙤𝙣 !";
    file_get_contents("https://api.telegram.org/bot$botToken/deleteMessage?chat_id=$chatId&message_id=$messageId");

    // Using sendVideo endpoint instead of sendPhoto
    file_get_contents("https://api.telegram.org/bot$botToken/sendVideo?chat_id=$chatId&video=$videoUrl&caption=" . urlencode($caption) . "&parse_mode=HTML&reply_markup=$keyboard2");
}