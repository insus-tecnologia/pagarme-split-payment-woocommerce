<?php

namespace PagarmeSplitPayment\Pagarme;

use PagarmeSplitPayment\Pagarme\ClientSingleton;

class Recipients {
    private $partnerData, $client;

    public function __construct()
    {
        $this->client = ClientSingleton::getInstance();
    }

    public function createOrUpdate($partnerData)
    {
        $this->partnerData = $partnerData;

        try {
            if ($this->partnerData['psp_recipient_id']) {
                return $this->update();
            }

            return $this->create();
        } catch (\Exception $e) {
            //TODO: Add a log or something like that.. one day.
            var_dump($e->getMessage());die;
        }
    }

    private function create()
    {
        return $this->client->recipients()->create(
            $this->getPartnerDataFormatted()
        );
    }

    private function update()
    {
        try {
            return $this->client->recipients()->update(
                $this->getPartnerDataFormatted(true)
            );
        } catch (\Exception $e) {
            // Update may fail if Pagar.me account change and recipient doesnt exists
            // In this case, create another one
            return $this->create();
        }
    }

    private function getPartnerDataFormatted($update = false)
    {
        $formattedPartner = [
            'bank_account' => [
                'bank_code' => $this->partnerData['psp_bank_code'],
                'agencia' => $this->partnerData['psp_agency'],
                'agencia_dv' => $this->partnerData['psp_agency_digit'],
                'conta' => $this->partnerData['psp_account'],
                'conta_dv' => $this->partnerData['psp_account_digit'],
                'type' => $this->partnerData['psp_account_type'],
                'document_number' => $this->partnerData['psp_document_number'],
                'legal_name' => $this->partnerData['psp_legal_name']
            ],
        ];

        if (!$this->partnerData['psp_agency_digit']) {
            unset($formattedPartner['bank_account']['agencia_dv']);
        }

        if (!$update) {
            return $formattedPartner;
        }

        $formattedPartner['id'] = $this->partnerData['psp_recipient_id'];

        return $formattedPartner;
    }
}
