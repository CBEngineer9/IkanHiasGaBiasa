<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs for snap popup:
// https://docs.midtrans.com/en/snap/integration-guide?id=integration-steps-overview

namespace Midtrans;

require_once dirname(__FILE__) . '/../Midtrans.php';
require_once("../proyekpw_lib.php");

// get cart
$cart = json_decode($_REQUEST['cart'],true);
$user = json_decode($_REQUEST['user'],true);
$total = $_REQUEST['total'];
$shipping = json_decode($_REQUEST['shipping'],true);
$orderId = '';



//conn
try {
    $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
    // set the PDO error mode to exception
    $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO `htrans` (`id_user`, `id_shipping`, `status`) VALUES ((SELECT id FROM users WHERE username = :username), :shipid, 'attempted');";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(":username",$_SESSION['currUsername']);
    $stmt -> bindValue(":shipid", $shipping['id']);
    $succInsert = $stmt->execute();

    $sql = "SELECT mid_order_id FROM `htrans` WHERE `id_user` = (SELECT id FROM users WHERE username = :username) ORDER BY `trans_time` DESC LIMIT 1;";
    $stmt = $conn -> prepare($sql);
    $stmt -> bindValue(":username",$_SESSION['currUsername']);
    $stmt->execute();
    $htrans_id = $stmt -> fetch(\PDO::FETCH_ASSOC);
    $orderId = $htrans_id['mid_order_id'];

    // copy cart to dtrans
    foreach ($cart as $cartRow) {
        $sql = "INSERT INTO `dtrans` (`id_htrans`, `ikan_id`, `qty`) VALUES (:id_htrans, :ikanid, :qty);";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindValue(":id_htrans",$orderId);
        $stmt -> bindValue(":ikanid", $cartRow['id']);
        $stmt -> bindValue(":qty", $cartRow['quantity']);
        $succInsertCart = $stmt->execute();
    }

} catch(\PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$conn=null;

//add shipping
array_push($cart,[
    "id" => $shipping['id'],
    "name" => $shipping['name'],
    "quantity" => $shipping['quantity'],
    "price" => $shipping['price'],
]);
// echo $orderId;
// Required
$transaction_details = array(
    'order_id' => $orderId,
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
// $customer_details = array(
//     'first_name'    => "Andri",
//     'last_name'     => "Litani",
//     'email'         => "andri@litani.com",
//     'phone'         => "081122334455",
//     'billing_address'  => $billing_address,
//     'shipping_address' => $shipping_address
// );

// $customer_details = array(
//     'first_name'    => $user['first_name'],
//     'last_name'     => $user['last_name'],
//     'email'         => $user['email'],
//     'phone'         => $user['phone']
// );

// Optional, remove this to display all available payment methods
$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

// Fill transaction details
$transaction = array(
    'enabled_payments' => $enable_payments,
    'transaction_details' => $transaction_details,
    'customer_details' => $user,
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