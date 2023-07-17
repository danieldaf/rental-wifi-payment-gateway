<?php

use GuzzleHttp\Client;

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

    public function makeCheckout($price_amount, $quantity, $voucher_name, $voucher)
    {
        $userdata = $this->user->getUserData();
        $full_name = "Guest";
        $email = null;
        $emailReceipt = false;

        if (!Session::getSession('isGuest')) {
            $full_name = !empty($userdata['firstname']) ? $userdata['firstname'] . ' ' . $userdata['lastname'] : $userdata['username'];
            $email = $userdata['email'];
            $emailReceipt = true;
        }

        $reference_number = 'REF' . time();

        $payload = $this->createCheckoutPayload($full_name, $email, $price_amount, $quantity, $voucher_name, $emailReceipt, $reference_number);

        $data = $this->createCheckoutSession($payload);
        $checkoutId = $data['data']['id'];
        $checkoutType = $data['data']['type'];
        $checkoutUrl = $data['data']['attributes']['checkout_url'];
        $referenceNumber = $data['data']['attributes']['reference_number'];
        $paymentIntent = $data['data']['attributes']['payment_intent']['id'];

        Session::setSession('checkout_session_id', $checkoutId);
        Session::setSession('vid', $voucher);

        $this->processPurchase($referenceNumber, $voucher, $checkoutId, $quantity);

        $out = $this->prepareCheckoutResponse($checkoutId, $checkoutType, $checkoutUrl, $referenceNumber, $paymentIntent);

        echo json_encode($out, JSON_UNESCAPED_UNICODE);
    }

    public function retrieveCheckout($checkout_session_id): array
    {
        $uri = 'https://api.paymongo.com/v1/checkout_sessions/' . urlencode($checkout_session_id);
        $data = $this->getCheckoutSessionData($uri);

        $checkoutId = $data['data']['id'];
        $referenceNumber = $data['data']['attributes']['reference_number'];
        $status = $this->getCheckoutSessionStatus($data);
        $checkoutUrl = $data['data']['attributes']['checkout_url'];

        return [
            "checkoutId" => $checkoutId,
            "referenceNumber" => $referenceNumber,
            "checkout_url" => $checkoutUrl,
            "status" => $status
        ];
    }

    public function expireCheckout($checkout_session_id)
    {
        $uri = 'https://api.paymongo.com/v1/checkout_sessions/' . urlencode($checkout_session_id);
        $data = $this->expireCheckoutSession($uri);

        $status = $data->data->attributes->status;

        echo $status;

        $this->updateVoucherandPurchase($checkout_session_id, true);
    }

    private function createCheckoutPayload($full_name, $email, $price_amount, $quantity, $voucher_name, $emailReceipt, $reference_number)
    {
        return [
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
    }

    private function createCheckoutSession($payload)
    {
        $response = $this->client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
            'body' => json_encode($payload),
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'authorization' => AUTHORIZATION_VALUE,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }


    private function prepareCheckoutResponse($checkoutId, $checkoutType, $checkoutUrl, $referenceNumber, $paymentIntent)
    {
        return [
            "checkout_id" => $checkoutId,
            "checkout_type" => $checkoutType,
            "checkout_url" => $checkoutUrl,
            "reference_number" => $referenceNumber,
            "payment_intent" => $paymentIntent,
        ];
    }

    private function getCheckoutSessionData($uri)
    {
        $response = $this->client->request('GET', $uri, [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => AUTHORIZATION_VALUE,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    private function getCheckoutSessionStatus($data)
    {
        if (isset($data['data']['attributes']['payments'][0]['attributes']['status'])) {
            return $data['data']['attributes']['payments'][0]['attributes']['status'] === 'paid' ? 'paid' : 'not_paid';
        }

        return 'not_paid';
    }

    private function expireCheckoutSession($uri)
    {
        $response = $this->client->request('POST', $uri . '/expire', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => AUTHORIZATION_VALUE
            ],
        ]);

        return json_decode($response->getBody());
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
        $status = ($cancelled) ? 'cancelled' : 'paid';

        $sql = "UPDATE purchase_history
        JOIN purchased_voucher ON purchase_history.purchased_id = purchased_voucher.purchase_id
        JOIN vouchers ON purchased_voucher.voucher_id = vouchers.voucher_id
        SET purchase_history.purchase_status = :status";

        if (!$cancelled) {
            $sql .= ", vouchers.status = 'purchased'";
        }

        $sql .= " WHERE purchase_history.checkout_session_id = :csi";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":csi", $checkout_session_id);
        return $stmt->execute();


    }


    /**
     * @param $voucher_id
     * @return void
     */
    public function removeVoucherFromOtherCart($voucher_id){


            $sql = "
            DELETE FROM purchase_history 
            WHERE purchased_id IN (
                SELECT purchase_id 
                FROM purchased_voucher 
                WHERE voucher_id = :voucher_id 
                AND NOT user_id = :uid
            );
    
            DELETE FROM purchased_voucher 
            WHERE voucher_id = :voucher_id 
            AND NOT user_id = :uid
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":voucher_id", $voucher_id);
        $stmt->bindParam(":uid", $this->loggedInUser);
        $stmt->execute();
    }




}