<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HitPay API Credentials
    |--------------------------------------------------------------------------
    |
    | These are required to authenticate with the HitPay API.
    | You can find these in your HitPay Dashboard > Settings > Payment Gateway > API Keys.
    |
    */
    'api_key' => env('HITPAY_API_KEY'),
    'salt' => env('HITPAY_SALT'),
    'env' => env('HITPAY_ENV', 'sandbox'), // 'sandbox' or 'production'

    /*
    |--------------------------------------------------------------------------
    | HitPay Payment Methods Toggles
    |--------------------------------------------------------------------------
    |
    | Turn specific payment methods on (true) or off (false).
    | IMPORTANT: A payment method MUST also be enabled in your HitPay 
    | Dashboard. If you set a method to true here but it's disabled 
    | in HitPay, the checkout request will fail with an error.
    |
    */
    'payment_methods' => [
        'card'          => env('HITPAY_METHOD_CARD', true),
        'gcash'         => env('HITPAY_METHOD_GCASH', true),
        'paymaya'       => env('HITPAY_METHOD_PAYMAYA', true),
        'ph_qr_ph'      => env('HITPAY_METHOD_QRPH', true),
        
        'grabpay'       => env('HITPAY_METHOD_GRABPAY', false),
        'ph_billease'   => env('HITPAY_METHOD_BILLEASE', false),
        'ph_shopeepay'  => env('HITPAY_METHOD_SHOPEEPAY', false),
        'paynow_online' => env('HITPAY_METHOD_PAYNOW', false),
        'shopeepay'     => env('HITPAY_METHOD_SHOPEEPAY_SG', false),
        'fpx'           => env('HITPAY_METHOD_FPX', false),
        'alipay'        => env('HITPAY_METHOD_ALIPAY', false),
        'wechat'        => env('HITPAY_METHOD_WECHAT', false),
    ],
];
