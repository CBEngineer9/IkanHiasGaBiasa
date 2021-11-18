<?php
    // This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
    // Please refer to this docs for sample HTTP notifications:
    // https://docs.midtrans.com/en/after-payment/http-notification?id=sample-of-different-payment-channels

    namespace Midtrans;

    require_once dirname(__FILE__) . '/Midtrans.php';


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

    if ($transaction == 'capture') {
        // For credit card transaction, we need to check whether transaction is challenge by FDS or not
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                // TODO set payment status in merchant's database to 'Challenge by FDS'
                // TODO merchant should decide whether this transaction is authorized or not in MAP
                // echo "Transaction order_id: " . $order_id ." is challenged by FDS"
                echo "200";
            } else {
                // TODO set payment status in merchant's database to 'Success'
                // echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
                echo "200";
            }
        }
    } else if ($transaction == 'settlement') {
        // TODO set payment status in merchant's database to 'Settlement'
        // echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
        echo "200";
    } else if ($transaction == 'pending') {
        // TODO set payment status in merchant's database to 'Pending'
        // echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
        echo "200";
    } else if ($transaction == 'deny') {
        // TODO set payment status in merchant's database to 'Denied'
        // echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        echo "200";
    } else if ($transaction == 'expire') {
        // TODO set payment status in merchant's database to 'expire'
        // echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is expired.";
        echo "200";
    } else if ($transaction == 'cancel') {
        // TODO set payment status in merchant's database to 'Denied'
        // echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is canceled.";
        echo "200";
    }

?>