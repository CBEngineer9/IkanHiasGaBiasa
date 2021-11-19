<?php
    // This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
    // Please refer to this docs for sample HTTP notifications:
    // https://docs.midtrans.com/en/after-payment/http-notification?id=sample-of-different-payment-channels

    namespace Midtrans;

    require_once dirname(__FILE__) . '/Midtrans.php';
    require_once("proyekpw_lib.php");


    try {
        $notif = new Notification();
    }
    catch (\Exception $e) {
        exit($e->getMessage());
    }

    $notif = $notif->getResponse();
    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $order_id = $notif->order_id;
    $fraud = $notif->fraud_status;

    $newStatus = '';

    if ($transaction == 'capture') {
        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                // TODO merchant should decide whether this transaction is authorized or not in MAP
                // echo "Transaction order_id: " . $order_id ." is challenged by FDS"
                $newStatus = 'Challenge by FDS';
                echo "200";
            } else {
                // echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
                $newStatus = 'Success';
                echo "200";
            }
        }
    } else if ($transaction == 'settlement') {
        // echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
        $newStatus = 'Settlement';
        echo "200";
    } else if ($transaction == 'pending') {
        // echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
        $newStatus = 'Pending';
        echo "200";
    } else if ($transaction == 'deny') {
        // echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        $newStatus = 'Denied';
        echo "200";
    } else if ($transaction == 'expire') {
        // echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        $newStatus = 'Expire';
        echo "200";
    } else if ($transaction == 'cancel') {
        // echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        $newStatus = 'Denied';
        echo "200";
    }

    //conn
    try {
        $conn = new \PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpass);
        // set the PDO error mode to exception
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE `htrans` SET `status` = :newStatus WHERE `mid_order_id` = :mid_order_id;";
        $stmt = $conn -> prepare($sql);
        $stmt -> bindValue(":mid_order_id",$order_id);
        $stmt -> bindValue(":newStatus", $newStatus);
        $succInsert = $stmt->execute();

    } catch(\PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    $conn=null;

?>