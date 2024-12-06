<?php 
# @*************************************************************************@
# @ Software author: JOOJ Team (JOOJ.us)                           @
# @ Author_url 1: https://jooj.us                       @
# @ Author_url 2: http://jooj.us/twitter-clone                      @
# @ Author E-mail: sales@jooj.us                                    @
# @*************************************************************************@
# @ JOOJ Talk - The Ultimate Modern Social Media Sharing Platform           @
# @ Copyright (c) 2020 - 2023 JOOJ Talk. All rights reserved.               @
# @*************************************************************************@

if (empty($cl["is_logged"])) {
    $data['status'] = 400;
    $data['error']  = 'Invalid access token';
}

else if($action == 'topup_wallet') {
    $data['status']   = 400;
    $data['err_code'] = 0;
    $topup_amount     = fetch_or_get($_POST['amount'], false);
    $topup_method     = fetch_or_get($_POST['method'], false);
    $topup_min_amount = intval($cl["config"]["wallet_min_amount"]);

    if (empty($topup_amount) || is_numeric($topup_amount) != true) {
        $data['err_code'] = 'invalid_topup_amount';
    }

    else if ($topup_amount < $topup_min_amount || $topup_amount > 15000) {
        $data['err_code'] = 'invalid_topup_amount';
    }

    else if (empty($topup_method) || in_array($topup_method, array("paypal", "stripe", "razorpay", "paystack", "stripe_alipay", "bank","square")) != true) {
        $data['err_code'] = 'invalid_topup_method';
    }

    else {
        if ($topup_method == "paypal" && $cl['config']['paypal_method_status'] == 'on') {

            try {
                require_once("core/libs/configs/paypal.php");

                $currency       = strtoupper($cl['config']['site_currency']);
                $payer          = new \PayPal\Api\Payer();
                $itemList       = new \PayPal\Api\ItemList();
                $details        = new \PayPal\Api\Details();
                $amount         = new \PayPal\Api\Amount();
                $transaction    = new \PayPal\Api\Transaction();
                $redirectUrls   = new \PayPal\Api\RedirectUrls();
                $payment        = new \PayPal\Api\Payment();
                $line_item      = new \PayPal\Api\Item();
                $inputFields    = new \PayPal\Api\InputFields();
                $webProfile     = new \PayPal\Api\WebProfile();
                $payer          = $payer->setPaymentMethod('paypal');
                $subtotal       = $topup_amount;
                $url_success    = cl_link("native_api/wallet/pgw1_wallet_tup_success");
                $url_cancel     = cl_link("native_api/wallet/pgw1_wallet_tup_cancel");
                $inputFields    = $inputFields->setAllowNote(true);
                $inputFields    = $inputFields->setNoShipping(1);
                $inputFields    = $inputFields->setAddressOverride(0);
                $webProfile     = $webProfile->setName(uniqid());
                $webProfile     = $webProfile->setInputFields($inputFields);
                $webProfile     = $webProfile->setTemporary(true);
                $createProfile  = $webProfile->create($paypal);
                $profileID      = $createProfile->getId();
                $payment        = $payment->setExperienceProfileId($profileID); 
                $redirectUrls   = $redirectUrls->setReturnUrl($url_success);
                $redirectUrls   = $redirectUrls->setCancelUrl($url_cancel); 
                $line_item      = $line_item->setName(cl_translate('Top up your account balance'));
                $line_item      = $line_item->setQuantity(1);
                $line_item      = $line_item->setPrice($topup_amount);
                $line_item      = $line_item->setCurrency($currency);
                $itemList       = $itemList->setItems(array($line_item)); 
                $details        = $details->setSubtotal($subtotal);
                $amount         = $amount->setCurrency($currency);
                $amount         = $amount->setTotal($subtotal);
                $amount         = $amount->setDetails($details);
                $transaction    = $transaction->setAmount($amount);
                $transaction    = $transaction->setItemList($itemList);
                $transaction    = $transaction->setDescription(cl_translate('Pay to: {@site_name@}', array('site_name' => $cl['config']['name'])));
                $transaction    = $transaction->setInvoiceNumber(time());
                $payment        = $payment->setIntent('sale');
                $payment        = $payment->setPayer($payer);
                $payment        = $payment->setRedirectUrls($redirectUrls);
                $payment        = $payment->setTransactions(array($transaction));
                $payment        = $payment->create($paypal);
                $data['url']    = $payment->getApprovalLink();
                $data['status'] = 200;
                
                cl_session('tup_amount', $topup_amount);
            }

            catch (Exception $ex) {
                $data['status']  = 500;
                $data['message'] = $ex->getMessage();
            }
        }

        else if($topup_method == "paystack" && $cl['config']['paystack_method_status'] == 'on') {
            try {
                require_once(cl_full_path("core/libs/PayStack-PHP/vendor/autoload.php"));

                $paystack       = new \Yabacon\Paystack($cl["config"]["paystack_api_pass"]);
                $reference      = sha1(microtime());
                $tranx          = $paystack->transaction->initialize([
                    'amount'    => ($topup_amount * 100),
                    'email'     => $me["email"],
                    'reference' => $reference,
                    'callback'  => cl_link("native_api/wallet/pgw2_wallet_tup_verification"),
                    'currency'  => strtoupper($cl['config']['site_currency'])
                ]);

                cl_session('paystack_reference', $reference);
                cl_session('tup_amount', $topup_amount);

                $data['url']    = $tranx->data->authorization_url;
                $data['status'] = 200;
            }

            catch(Exception $ex){
                $data['status']  = 500;
                $data['message'] = $ex->getMessage();
            }
        }

        else if($topup_method == "razorpay" && $cl['config']['rzp_method_status'] == 'on') {
            try {
                require_once(cl_full_path("core/libs/RazorPay/vendor/autoload.php"));


                $razorpay_api = new Razorpay\Api\Api($cl['config']['rzp_api_key'], $cl['config']['rzp_api_secret']);

                $rzp_order_id = $razorpay_api->order->create(array(
                    'receipt' => sha1(microtime()),
                    'amount' => ($topup_amount * 100),
                    'currency' => strtoupper($cl['config']['site_currency']),
                    'partial_payment' => false,
                    'notes'=> array(
                        'key1'=> 'value3',
                        'key2'=> 'value2'
                    )
                ));


                cl_session('tup_amount', $topup_amount);
                cl_session('razorpay_order_id', $rzp_order_id->id);

                $data['order_id'] = $rzp_order_id->id;
                $data['status']   = 200;
            }

            catch(Exception $ex){
                $data['status']  = 500;
                $data['message'] = $ex->getMessage();
            }
        }

        else if($topup_method == "bank" && $cl['config']['bank_method_status'] == 'on') {
            try {

                $bank_trans_session_id = cl_strf("SID_%s", cl_genkey(32,32));

                cl_session("bank_trans_session", array(
                    "amount" => $topup_amount,
                    "currency" => strtoupper($cl['config']['site_currency']),
                    "sess_id" => $bank_trans_session_id
                ));

                $data['url']    = cl_link(cl_strf("wallet_bank_transfer/%s", $bank_trans_session_id));
                $data['status'] = 200;
            }

            catch(Exception $ex){
                $data['status']  = 500;
                $data['message'] = $ex->getMessage();
            }
        }

        else if(in_array($topup_method, array("stripe", "stripe_alipay")) && $cl['config']['stripe_method_status'] == 'on') {
            try {
                require_once(cl_full_path("core/libs/Stripe/vendor/autoload.php"));

                $stripe_methods = array("stripe" => "card", "stripe_alipay" => "alipay");
                $stripe         = new \Stripe\StripeClient($cl["config"]["stripe_api_pass"]);
                $stripe_session = $stripe->checkout->sessions->create([
                    
                    "payment_method_types" => array($stripe_methods[$topup_method]),
                    
                    "line_items" => [[
                        
                        "price_data" => [
                            
                            "currency"  => strtoupper($cl["config"]["site_currency"]),
                            "unit_amount"    => ($topup_amount * 100),
                            
                            "product_data" => [
                                "name"      => cl_translate('Top up your account balance'),
                                "description" => cl_translate('Top up your account balance'),
                            ],
                            
                        ],
                        
                        "quantity"  => 1
                        
                    ]],
                    
                    "mode" => 'payment',
                    "success_url"  => cl_link("native_api/wallet/pgw3_wallet_tup_success"),
                    "cancel_url"   => cl_link("wallet"),
                    
                ]);

                if (not_empty($stripe_session)) {
                    $data["status"]  = 200;
                    $data["sess_id"] = $stripe_session->id;
                    $data['url'] = $stripe_session->url;

                    cl_session('stripe_session', $data["sess_id"]);
                    cl_session('tup_amount', $topup_amount);
                }
            }

            catch(Exception $ex){
                $data['status']  = 500;
                $data['message'] = $ex->getMessage();
            }
        }
        
        else if($topup_method == "square" && $cl['config']['square_method_status'] == 'on') {
            try {
                
                $sq = sqaure([
                    
                            'checkoutOptions' => [
                                "accepted_payment_methods" => [
                                    "apple_pay" => true,
                                    "cash_app_pay" => true,
                                    "google_pay" => true,
                                    "afterpay_clearpay" => true
                                ],
                                "allow_tipping" => false,
                                "ask_for_shipping_address" => false,
                                "enable_coupon" => false,
                                "enable_loyalty" => false,
                                "redirect_url" => cl_link("native_api/wallet/squareup_webhook?a=" . intval($topup_amount))
                            ],
                            
                            'description' => "P-tweet Credit",
                            
                            'order' => [
                                "location_id" => "L44HTDYCSQ3DM",
                                "pricing_options" => [
                                    "auto_apply_discounts" => false,
                                    "auto_apply_taxes" => false
                                ],
                                "line_items" => [
                                    [
                                        "quantity" => "1",
                                        "base_price_money" => [
                                            "amount" => intval($topup_amount)*100,
                                            "currency" => "USD"
                                        ],
                                        "name" => "Credit"
                                    ]
                                ]
                            ]
                    
                        
                    ]);
                    
                $sq_json = json_decode($sq);
              
                $data["status"]  = 200;
                $data["type"] = 'square';
                $data["message"]  = 'successfully !';
                $data['url'] = $sq_json->payment_link->long_url;
            }

            catch(Exception $ex){
                $data['status']  = 500;
                $data['message'] = $ex->getMessage();
            }
        }
        
    }
}

else if($action == 'pgw1_wallet_tup_success') {
    if (not_empty($_GET['paymentId']) && not_empty($_GET['token']) && not_empty($_GET['PayerID'])) {
        try{

            require_once("core/libs/configs/paypal.php");

            $paym_id    = fetch_or_get($_GET['paymentId'], false);
            $paym_tok   = fetch_or_get($_GET['token'], false);
            $payer_id   = fetch_or_get($_GET['PayerID'], false);
            $payment    = \PayPal\Api\Payment::get($paym_id, $paypal);
            $execute    = new \PayPal\Api\PaymentExecution();
            $execute    = $execute->setPayerId($payer_id);
            $tup_amount = cl_session('tup_amount');

            if ($tup_amount) {
                $result = $payment->execute($execute, $paypal);
                
                cl_update_user_data($me['id'], array(
                    'wallet' => ($me['wallet'] += $tup_amount)
                ));

                cl_db_insert(T_WALLET_HISTORY, array(
                    'user_id'   => $me['id'],
                    'operation' => 'paypal_wallet_tup',
                    'amount'    => $tup_amount,
                    'json_data' => json(array(
                        'paypal_pid' => $result->id
                    ), true),
                    'time' => time()
                ));

                cl_session_unset('tup_amount');

                cl_redirect('wallet');
            }
            else {
                throw new Exception('The current payment is duplicated of already processed payment. Please check your details');
            }
        }

        catch (Exception $e) {
            cl_session_unset('tup_amount');

            cl_session('err500_message', array(
                'title' => "Transaction failed!",
                'desc' => $e->getMessage()
            ));

            cl_redirect('500');
        }
    }
}

else if($action == 'pgw1_wallet_tup_cancel') {
    cl_session_unset('tup_amount');
    cl_redirect('wallet');
}

else if($action == 'pgw2_wallet_tup_verification') {
    if (not_empty($_GET['reference'])) {

        try{
            $reference1 = fetch_or_get($_GET['reference'], false);
            $tup_amount = cl_session('tup_amount');
            $reference2 = cl_session('paystack_reference');

            if ($tup_amount && ($reference1 == $reference2)) {
                
                require_once(cl_full_path("core/libs/PayStack-PHP/vendor/autoload.php"));

                $paystack = new \Yabacon\Paystack($cl["config"]["paystack_api_pass"]);
                $tranx    = $paystack->transaction->verify(array(
                    'reference' => $reference1
                ));
                
                cl_update_user_data($me['id'], array(
                    'wallet' => ($me['wallet'] += $tup_amount)
                ));

                cl_db_insert(T_WALLET_HISTORY, array(
                    'user_id'   => $me['id'],
                    'operation' => 'paystack_wallet_tup',
                    'amount'    => $tup_amount,
                    'json_data' => json(array(
                        'paystack_ref' => $reference1
                    ), true),
                    'time' => time()
                ));

                cl_session_unset('tup_amount');
                cl_session_unset('paystack_reference');

                cl_redirect('wallet');
            }
            else {
                throw new Exception('An error occurred while processing your request. Please try again later. Please contact our support team');
            }
        }

        catch (Exception $e) {
            cl_session_unset('tup_amount');

            cl_session('err500_message', array(
                'title' => "Transaction failed!",
                'desc' => $e->getMessage()
            ));

            cl_redirect('500');
        }
    }
}

else if($action == 'pgw3_wallet_tup_success') {
    $tup_amount     = cl_session('tup_amount');
    $stripe_session = cl_session('stripe_session');

    if ($tup_amount && $stripe_session) {

        try{

            require_once(cl_full_path("core/libs/Stripe/vendor/autoload.php"));

            $stripe         = new \Stripe\StripeClient($cl["config"]["stripe_api_pass"]);
            $session_object = $stripe->checkout->sessions->retrieve($stripe_session);

            if ($session_object && not_empty($session_object->payment_status) && $session_object->payment_status == "paid") {
                cl_update_user_data($me['id'], array(
                    'wallet' => ($me['wallet'] += $tup_amount)
                ));

                cl_db_insert(T_WALLET_HISTORY, array(
                    'user_id'   => $me['id'],
                    'operation' => 'stripe_wallet_tup',
                    'amount'    => $tup_amount,
                    'json_data' => json(array(
                        'sess_id' => $session_object->id,
                        'payment_intent' => $session_object->payment_intent
                    ), true),
                    'time' => time()
                ));

                cl_session_unset('tup_amount');
                cl_session_unset('stripe_session');

                cl_redirect('wallet');
            }

        }
        catch (Exception $e) {
            cl_session_unset('tup_amount');
            cl_session_unset('stripe_session');

            cl_session('err500_message', array(
                'title' => "Transaction failed!",
                'desc' => $e->getMessage()
            ));

            cl_redirect('500');
        }
    }
}

else if($action == 'banktrans_receipt_submit') {
    $data['status']   = 400;
    $data['err_code'] = 0;

    $bank_trans_session = cl_session("bank_trans_session");

    if (empty($bank_trans_session) || is_array($bank_trans_session) != true) {
        $data['err_code'] = 'invalid_session_id';
    }

    else if (not_empty($_POST['text_message']) && len($_POST['text_message']) > 1200) {
        $data['err_code'] = "invalid_text_message";
    }

    else if(empty($_FILES['receipt']) || empty($_FILES['receipt']['tmp_name'])) {
        $data['err_code'] = "invalid_receipt_photo";
    }

    else {
        $file_info      = array(
            'file'      => $_FILES['receipt']['tmp_name'],
            'size'      => $_FILES['receipt']['size'],
            'name'      => $_FILES['receipt']['name'],
            'type'      => $_FILES['receipt']['type'],
            'file_type' => 'image',
            'folder'    => 'videos',
            'slug'      => 'image_receipt',
            'allowed'   => 'jpg,png,jpeg,gif'
        );

        $file_upload = cl_upload($file_info);

        if (not_empty($file_upload['filename'])) {
            $text_message = cl_text_secure($_POST['text_message']);
            $trans_id = cl_strf("TID_%s", sha1(microtime()));
            $insert_data  = array(
                'user_id' => $me['id'],
                'message' => $text_message,
                'receipt_img' => $file_upload['filename'],
                'time' => time(),
                'amount' => $bank_trans_session["amount"],
                'currency' => $bank_trans_session["currency"],
                'trans_id' => $trans_id
            );

            $req_id = $db->insert(T_BANKTRANS_REQS, $insert_data);

            $history_rec_id = $db->insert(T_WALLET_HISTORY, array(
                "user_id" => $me["id"],
                "operation" => "banktrans_wallet_tup",
                "amount" => $bank_trans_session["amount"],
                "time" => time(),
                "status" => "pending_approval",  
                "trans_id" => $trans_id
            ));

            if (is_posnum($req_id) && is_posnum($history_rec_id)) {
                $data['err_code'] = 0;
                $data['status']   = 200;

                cl_session_unset("bank_trans_session");
            }
        }
    }
}

else if($action == 'search_recipients') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $username         = fetch_or_get($_GET["query"], false);
    $username         = cl_text_secure($username);
    $username         = cl_croptxt($username, 32);
    $users_list       = cl_money_recipients_search($username);

    if (not_empty($users_list)) {
        $data["status"] = 200;
        $data["recipients_list"] = $users_list;
    }
}

else if($action == 'send_money') {
    $data['err_code'] = 0;
    $data['status']   = 400;
    $send_amount      = fetch_or_get($_POST["amount"], false);
    $user_id          = fetch_or_get($_POST["user_id"], false);

    if (is_numeric($send_amount) && is_posnum($user_id)) {

        $send_amount = ($send_amount > $me["wallet"]) ? $me["wallet"] : $send_amount;

        $recipient_data = cl_raw_user_data($user_id);

        if (not_empty($recipient_data)) {

            $trans_id = cl_strf("TID_%s", sha1(microtime()));
            $data['status'] = 200;
            
            cl_update_user_data($user_id, array(
                "wallet" => ($recipient_data["wallet"] += $send_amount)
            ));

            cl_update_user_data($me["id"], array(
                "wallet" => ($me["wallet"] -= $send_amount)
            ));

            $db->insert(T_WALLET_HISTORY, array(
                "user_id" => $me["id"],
                "operation" => "wallet_local_transfer",
                "amount" => $send_amount,
                "time" => time(),
                "status" => "success",  
                "trans_id" => $trans_id
            ));

            $db->insert(T_WALLET_HISTORY, array(
                "user_id" => $user_id,
                "operation" => "wallet_local_receipt",
                "amount" => $send_amount,
                "time" => time(),
                "status" => "success",  
                "trans_id" => $trans_id
            ));
        }
    }
}

else if($action == 'verify_rzp_payment') {
    $data['err_code'] = 0;
    $data['status']   = 400;

    $tup_amount = cl_session('tup_amount');
    $save_razorpay_order_id = cl_session('razorpay_order_id');

    $payment_id = fetch_or_get($_POST["payment_id"], false);
    $order_id = fetch_or_get($_POST["order_id"], false);
    $signature = fetch_or_get($_POST["signature"], false);

    if ($save_razorpay_order_id && $tup_amount && $payment_id && $order_id && $signature) {
        if ($save_razorpay_order_id == $order_id) {
            try {
                require_once(cl_full_path("core/libs/RazorPay/vendor/autoload.php"));


                $razorpay_api = new Razorpay\Api\Api($cl['config']['rzp_api_key'], $cl['config']['rzp_api_secret']);

                $verify_checkout = $razorpay_api->utility->verifyPaymentSignature(array(
                    "razorpay_signature" => $signature,
                    "razorpay_payment_id" => $payment_id,
                    "razorpay_order_id" => $order_id
                ));

                cl_update_user_data($me['id'], array(
                    'wallet' => ($me['wallet'] += $tup_amount)
                ));

                cl_db_insert(T_WALLET_HISTORY, array(
                    'user_id'   => $me['id'],
                    'operation' => 'razorpay_wallet_tup',
                    'amount'    => $tup_amount,
                    'json_data' => json(array(
                        "razorpay_signature" => $signature,
                        "razorpay_payment_id" => $payment_id,
                        "razorpay_order_id" => $order_id
                    ), true),
                    'time' => time()
                ));

                cl_session_unset('tup_amount');
                cl_session_unset('razorpay_order_id');
                
                $data['status'] = 200;
            }

            catch(Exception $ex){
                $data['status']  = 500;
                $data['message'] = $ex->getMessage();
            }
        }
    }
}

else if($action == 'squareup_webhook') {
    if(not_empty($_GET['a'])) {
    
        $amount = base64_decode($_GET['a']);
        
        $amount = intval($amount);

        cl_update_user_data($me['id'], array(
                'wallet' => ($me['wallet'] += $amount*1000)
        ));

        cl_db_insert(T_WALLET_HISTORY, array(
                'user_id'   => $me['id'],
                'operation' => 'squareup',
                'amount'    => $amount,
                'json_data' => json(array(
                    'success' => true
                ), true),
                    'time' => time()
            ));

        cl_redirect('wallet');
        
    }
}

function sqaure($data) {
                    global $cl;
                        // Initialize cURL
                        $ch = curl_init();
                    
                        // Set cURL options
                        curl_setopt($ch, CURLOPT_URL, 'https://connect.squareup.com/v2/online-checkout/payment-links');
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    
                        // Set headers
                        $headers = [
                            "Square-Version: 2024-04-17",
                            "Authorization: Bearer " .  $cl['config']['square_api_key'],
                            "Content-Type: application/json"
                        ];
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    
                        // Create the request payload
                        $payload = [
                            "checkout_options" => $data['checkoutOptions'],
                            "description" => $data['description'],
                            "order" => $data['order']
                        ];
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                    
                        // Execute the request and get the response
                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $error = curl_error($ch);
                    
                        // Close cURL
                        curl_close($ch);
                    
                        // Check for errors
                        if ($response === false || $httpCode >= 400) {
                            throw new Exception("cURL Error: " . $error);
                        }
                    
                        return $response;
                    }