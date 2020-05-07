<?php

namespace PagarmeSplitPayment\Pagarme;

use PagarMe\Client;

class ClientSingleton {
    private static $client;

    const PAGARME_PLUGIN_CREDIT_CARD_SETTINGS = 'woocommerce_pagarme-credit-card_settings';
    const PAGARME_PLUGIN_BANKING_TICKET_SETTINGS = 'woocommerce_pagarme-banking-ticket_settings';
    
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance()
    {
        if (self::$client === null) {
            if (!$paymentSettings = get_option(self::PAGARME_PLUGIN_CREDIT_CARD_SETTINGS)) {
                $paymentSettings = get_option(self::PAGARME_PLUGIN_BANKING_TICKET_SETTINGS);
            }
            
            if (empty($paymentSettings['api_key'])) {
                throw new \Exception(__('Configure Pagar.me API key'));
            }
    
            self::$client = new Client($paymentSettings['api_key']);
        }

        return self::$client; 
    }
}
