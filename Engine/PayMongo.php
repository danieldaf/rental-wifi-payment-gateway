<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PayMongo
{

    /**
     * @var Client
     */
    private $client;
    /**
     * @var User
     */
    private $user;
    /**
     * @var mixed|null
     */
    private $loggedInUser;

    private $db;

    public function __construct(){
        $this->client = new GuzzleHttp\Client();
        $this->user = new User();
        $this->db = Database::getInstance();
        $this->loggedInUser = Session::getSession('user_id');
    }

    /**

     * @throws GuzzleException
     */
    public function makeCheckout($price_amount, $quantity, $voucher_name, $voucher)
    {

        $userdata = $this->user->getUserData();

        if (!Session::getSession('isGuest')) {
            $full_name = !empty($userdata['firstname']) ? $userdata['firstname'] . ' ' . $userdata['lastname'] : $userdata['username'];
            $email = $userdata['email'];
            $emailReceipt = true;
        } else {
            $full_name = "Guest";
            $email = null;
            $emailReceipt = false;
        }


        $reference_number = 'REF' . time() . uniqid();

        $payload = [
            'data' => [
                'attributes' => [
                    'billing' => [
                        'name' => $full_name,
                        'email' => $email,
                    ],
                    'line_items' => [
                        [
                            'currency' => 'PHP',
                            'amount' => $price_amount,
                            'name' => $voucher_name,
                            'quantity' => $quantity
                        ]
                    ],
                    'payment_method_types' => [
                        'gcash', 'card', 'paymaya'
                    ],
                    'send_email_receipt' => $emailReceipt,
                    'show_description' => false,
                    'show_line_items' => true,
                    'reference_number' => $reference_number,
                    'success_url' => SUCCESS_URL
                ]
            ]
        ];


        $response = $this->client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
            'body' => json_encode($payload),
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'authorization' => AUTHORIZATION_VALUE,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        $checkoutId = $data['data']['id'];
        $checkoutType = $data['data']['type'];
        $checkoutUrl = $data['data']['attributes']['checkout_url'];
        $referenceNumber = $data['data']['attributes']['reference_number'];
        $paymentIntent = $data['data']['attributes']['payment_intent']['id'];

            Session::setSession('checkout_session_id', $checkoutId);


        $this->processPurchase($referenceNumber, $voucher, $checkoutId, $quantity);


        $out = array(
            "checkout_id" => $checkoutId,
            "checkout_type" => $checkoutType,
            "checkout_url" => $checkoutUrl,
            "reference_number" => $referenceNumber,
            "payment_intent" => $paymentIntent,
        );


        $json_body = json_encode($out, JSON_UNESCAPED_UNICODE);

        echo $json_body;

    }

    /**
     * @throws GuzzleException
     */
    public function retrieveCheckout($checkout_session_id): array
    {

        $uri = 'https://api.paymongo.com/v1/checkout_sessions/' .urlencode($checkout_session_id);

        $response = $this->client->request('GET', $uri, [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => AUTHORIZATION_VALUE,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        $checkoutId = $data['data']['id'];
        $referenceNumber = $data['data']['attributes']['reference_number'];
        $status = isset($data['data']['attributes']['payments'][0]['attributes']['status']) ? ($data['data']['attributes']['payments'][0]['attributes']['status'] === 'paid' ? 'paid' : 'not_paid') : 'not_paid';
        $checkoutUrl = $data['data']['attributes']['checkout_url'];

        return [
            "checkoutId" => $checkoutId,
            "referenceNumber" => $referenceNumber,
            "checkout_url" => $checkoutUrl,
            "status" => $status
        ];


    }

    /**
     * @throws GuzzleException
     */
    public function expireCheckout($checkout_session_id) {
        $response = $this->client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions/'. urlencode($checkout_session_id) .'/expire', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => AUTHORIZATION_VALUE
            ],
        ]);

        $data = json_decode($response->getBody()) ;
        $status = $data->data->attributes->status;

        echo $status;
        $this->updateVoucherandPurchase($checkout_session_id, true);

    }

    public function processPurchase($reference_number, $voucher_id, $checkout_session_id, $quantity): bool
    {


        $fingerprint = Session::getSession('login_fingerprint');

        $sql = "INSERT INTO `purchased_voucher` (reference_number, user_id, voucher_id, quantity) VALUES (:reference_number, :user_id, :voucher_id, :quantity)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':reference_number', $reference_number );
        $stmt->bindParam(':user_id', $this->loggedInUser);
        $stmt->bindParam(':voucher_id', $voucher_id);
        $stmt->bindParam(':quantity', $quantity);
        if($stmt->execute()) {

            $purchased_id = $this->db->lastInsertId();

            $sql = "INSERT INTO `purchase_history` (`checkout_session_id`, `purchased_id`, `fingerprint`) VALUES (:checkout_session_id, :purchased_id, :fp)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':checkout_session_id', $checkout_session_id);
            $stmt->bindParam(':purchased_id', $purchased_id);
            $stmt->bindParam(':fp', $fingerprint);

            if ($stmt->execute()) {
                return true;
            }
        }
        return true;
    }


    /**
     * Update purchase information and update voucher status
     * @param string $checkout_session_id
     * @param bool $cancelled
     * @return true
     */
    public function updateVoucherandPurchase(string $checkout_session_id, bool $cancelled = false)
    {

        if ($cancelled) {
            $sql = "UPDATE purchase_history
                        JOIN purchased_voucher ON purchase_history.purchased_id = purchased_voucher.purchase_id
                        JOIN vouchers ON purchased_voucher.voucher_id = vouchers.voucher_id
                        SET purchase_history.purchase_status = 'cancelled'
                        WHERE purchase_history.checkout_session_id = :csi";
        } else {
            $sql = "UPDATE purchase_history
                        JOIN purchased_voucher ON purchase_history.purchased_id = purchased_voucher.purchase_id
                        JOIN vouchers ON purchased_voucher.voucher_id = vouchers.voucher_id
                        SET purchase_history.purchase_status = 'paid',
                            vouchers.status = 'purchased'
                        WHERE purchase_history.checkout_session_id = :csi";
        }


        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":csi", $checkout_session_id);
        return $stmt->execute();

    }




}