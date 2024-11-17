<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


    // function __construct() {
    //     $CI = &get_instance();
    //     $CI->config->load('email');
    // }

    function sendVerifyEmail($to, $subject, $message, $from = NULL, $from_name = NULL) {
        $CI = &get_instance();
        $CI->config->load('email');
        
        $from = $from ? $from : $CI->config->item('smtp_user');
        $from_name = $from_name ? $from_name : 'DFM Trade';

        $CI->email->from($from, $from_name);
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($message);
        
        if ($CI->email->send()) {
            return true;
        } else {
            return $CI->email->print_debugger();
        }
    }

function sms_send($number,$message) {
    $url = "https://bulksmsbd.net/api/smsapi";
    $api_key = "yMlXW6GaKXhw2Sz1GXfq";
    $senderid = "03590740020";
    $number = $number;
    $message = $message;

    $data = [
        "api_key" => $api_key,
        "senderid" => $senderid,
        "number" => $number,
        "message" => $message
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}


function sendNotification($fcm, $title, $body, $img) {
    $serverKey = 'AAAAbV9YJAU:APA91bGv9gjjPoq8UTfOQVyi9Rkeb1UI9Rtj3ivP42rE0Z0dmY7SdCykJalf5Ej-Gnv73_GDutjJW5sB6Z3SJeT2VB4Tlgrkh3IFw6WHvvqlm_8bFn_tyKDJGPzrJ8As0djVYgQAP_3H';
    $url = 'https://fcm.googleapis.com/fcm/send';

    $notification = [
        'to' => $fcm,
        'notification' => [
            'title' => $title,
            'body' => $body,
            'image' => $img, // Specify the image URL
        ],
    ];

    $payload = json_encode($notification);
    $options = [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json',
        ],
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = false;
    if (curl_errno($ch)) {
        return 'cURL error: ' . curl_error($ch);
        //$error = true;
    }
    curl_close($ch);

    return $response;
}