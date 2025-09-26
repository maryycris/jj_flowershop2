<?php

namespace App\Services;

use GuzzleHttp\Client;

class PayMongoService
{
    protected $client;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->secretKey = env('PAYMONGO_SECRET_KEY');
        $this->baseUrl = env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1');
    }

    public function createSource($amount, $type, $redirectUrl)
    {
        try {
            $amountInCents = (int) ($amount * 100);
            \Log::info('PayMongo createSource', [
                'original_amount' => $amount,
                'amount_in_cents' => $amountInCents,
                'type' => $type,
                'redirect_url' => $redirectUrl
            ]);
            
            $response = $this->client->post($this->baseUrl . '/sources', [
                'auth' => [$this->secretKey, ''],
                'json' => [
                    'data' => [
                        'attributes' => [
                            'amount' => $amountInCents, // PayMongo expects cents as integer
                            'redirect' => [
                                'success' => $redirectUrl,
                                'failed' => $redirectUrl,
                            ],
                            'type' => $type, // 'gcash' or 'maya'
                            'currency' => 'PHP',
                        ]
                    ]
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $body = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';
            \Log::error('PayMongo createSource error', [
                'message' => $e->getMessage(),
                'body' => $body
            ]);
            throw new \Exception('PayMongo error: ' . $e->getMessage() . ' ' . $body);
        } catch (\Exception $e) {
            \Log::error('PayMongo createSource general error', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getSourceStatus($sourceId)
    {
        $response = $this->client->get($this->baseUrl . '/sources/' . $sourceId, [
            'auth' => [$this->secretKey, ''],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['data']['attributes']['status'] ?? null;
    }
} 