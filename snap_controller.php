<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs for snap popup:
// https://docs.midtrans.com/en/snap/integration-guide?id=integration-steps-overview

namespace Midtrans;

require_once dirname(__FILE__) . '/Midtrans.php';
Config::$serverKey = 'SB-Mid-server-xZplK3pXfb9G72CfNcJCzoXp';
Config::$clientKey = 'SB-Mid-client-lWw1rHND0roMX3yv';

// Enable sanitization
Config::$isSanitized = true;

// Enable 3D-Secure
Config::$is3ds = true;

// Uncomment for append and override notification URL
// Config::$appendNotifUrl = "https://example.com";
// Config::$overrideNotifUrl = "https://example.com";

// get cart
$cart = json_decode($_REQUEST['cart'],true);
$user = json_decode($_REQUEST['user'],true);
$total = $_REQUEST['total'];

// Required
$transaction_details = array(
    'order_id' => rand(),
    'gross_amount' => $total, 
);

// Optional
// $item1_details = array(
//     'id' => 'a1',
//     'price' => 18000,
//     'quantity' => 3,
//     'name' => "Apple"
// );

// Optional
// $item2_details = array(
//     'id' => 'a2',
//     'price' => 20000,
//     'quantity' => 2,
//     'name' => "Orange"
// );

// Optional
// $item_details = array ($item1_details, $item2_details);

// Optional
// $billing_address = array(
//     'first_name'    => "Andri",
//     'last_name'     => "Litani",
//     'address'       => "Mangga 20",
//     'city'          => "Jakarta",
//     'postal_code'   => "16602",
//     'phone'         => "081122334455",
//     'country_code'  => 'IDN'
// );

// Optional
// $shipping_address = array(
//     'first_name'    => "Obet",
//     'last_name'     => "Supriadi",
//     'address'       => "Manggis 90",
//     'city'          => "Jakarta",
//     'postal_code'   => "16601",
//     'phone'         => "08113366345",
//     'country_code'  => 'IDN'
// );

// Optional
$customer_details = array(
    'first_name'    => "Andri",
    'last_name'     => "Litani",
    'email'         => "andri@litani.com",
    'phone'         => "081122334455",
    // 'billing_address'  => $billing_address,
    // 'shipping_address' => $shipping_address
);

// Optional, remove this to display all available payment methods
$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

// Fill transaction details
$transaction = array(
    'enabled_payments' => $enable_payments,
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $cart,
);

$snap_token = '';
try {
    $snap_token = Snap::getSnapToken($transaction);
}
catch (\Exception $e) {
    echo $e->getMessage();
}

echo $snap_token;


?>