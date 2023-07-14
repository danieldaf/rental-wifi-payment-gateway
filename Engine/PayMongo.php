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

        if (!empty($userdata['firstname'])){
            $fullname = $userdata['firstname'] . ' ' . $userdata['lastname'];
        } else {
            $fullname = $userdata['username'];
        }

        $email = $userdata['email'];
        $reference_number = 'REF' . time();

        $payload = [
            'data' => [
                'attributes' => [
                    'billing' => [
                        'name' => $fullname,
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
                    'send_email_receipt' => true,
                    'show_description' => false,
                    'show_line_items' => true,
                    'reference_number' => $reference_number,
                    'success_url' => 'http://localhost/payment-gateway/success.php'
                ]
            ]
        ];


        $response = $this->client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions', [
            'body' => json_encode($payload),
            'headers' => [
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
                'authorization' => 'Basic c2tfdGVzdF93aUNjMVNacEt3TG5KeXhGYkZaWkZtTHQ6',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        $checkoutId = $data['data']['id'];
        $checkoutType = $data['data']['type'];
        $checkoutUrl = $data['data']['attributes']['checkout_url'];
        $referenceNumber = $data['data']['attributes']['reference_number'];
        $paymentIntent = $data['data']['attributes']['payment_intent']['id'];

            Session::setSession('checkout_session_id', $checkoutId);
        $this->processPurchase($referenceNumber, $voucher, $checkoutId);


        $out = array(
            "checkout_id" => $checkoutId,
            "checkout_type" => $checkoutType,
            "checkout_url" => $checkoutUrl,
            "reference_number" => $referenceNumber,
            "payment_intent" => $paymentIntent
        );


        $json_body = json_encode($out, JSON_UNESCAPED_UNICODE);

        echo $json_body;

    }

    /**
     * @throws GuzzleException
     */
    public function retrieveCheckout($checkout_session_id) {

        $uri = 'https://api.paymongo.com/v1/checkout_sessions/' .urlencode($checkout_session_id);

        $response = $this->client->request('GET', $uri, [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic c2tfdGVzdF93aUNjMVNacEt3TG5KeXhGYkZaWkZtTHQ6',
            ],
        ]);

        $json_response = $response->getBody();

        $data = json_decode($json_response, true);

        $checkoutId = $data['data']['id'];
        $referenceNumber = $data['data']['attributes']['reference_number'];
        $status = $data['data']['attributes']['payments']['attributes']['status'];

        return array (
            "checkoutId" => $checkoutId,
            "referenceNumber" => $referenceNumber,
            "status" => $status
        );
    }

    /**
     * @throws GuzzleException
     */
    public function expireCheckout($checkout_session_id) {
        $response = $this->client->request('POST', 'https://api.paymongo.com/v1/checkout_sessions/'. urlencode($checkout_session_id) .'/expire', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic c2tfdGVzdF93aUNjMVNacEt3TG5KeXhGYkZaWkZtTHQ6'
            ],
        ]);

        echo $response->getBody();
    }

    public function processPurchase($reference_number, $voucher_id, $checkout_session_id): bool
    {

        $sql = "INSERT INTO `purchased_voucher` (reference_number, user_id, voucher_id) VALUES (:reference_number, :user_id, :voucher_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':reference_number', $reference_number );
        $stmt->bindParam(':user_id', $this->loggedInUser);
        $stmt->bindParam(':voucher_id', $voucher_id);
        if($stmt->execute()) {

            $purchased_id = $this->db->lastInsertId();

            $sql = "INSERT INTO `purchase_history` (`checkout_session_id`, `purchased_id`) VALUES (:checkout_session_id, :purchased_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':checkout_session_id', $checkout_session_id);
            $stmt->bindParam(':purchased_id', $purchased_id);

            if ($stmt->execute()) {
                return true;
            }
        }
        return true;
    }




}