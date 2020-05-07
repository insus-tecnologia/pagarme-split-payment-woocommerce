<?php

namespace PagarmeSplitPayment\Pagarme;

use PagarmeSplitPayment\Pagarme\ClientSingleton;

class Receivers {
    private $partnerData, $client;

    public function __construct()
    {
        $this->client = ClientSingleton::getInstance();
    }

    public function createOrUpdate( $partnerId )
    {
        if ('partner' !== get_post_type($partnerId)) {
            return;
        }

        $this->partnerData = carbon_get_post_meta(
            $partnerId, 
            'psp_partner'
        )[0];

        try {
            if ($this->partnerData['psp_recipient_id']) {
                $this->update();
                return;
            }

            $recipients = $this->create();
        } catch (\Exception $e) {
            //TODO: Add a log or something like that
            var_dump($e->getMessage());die;
        }

        carbon_set_post_meta(
            $partnerId, 
            'psp_partner[0]/psp_recipient_id', 
            $recipients->id
        );
    }

    private function create()
    {
        return $this->client->recipients()->create(
            $this->getPartnerDataFormatted()
        );
    }

    private function update()
    {
        return $this->client->recipients()->update(
            $this->getPartnerDataFormatted(true)
        );
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

    public function addReceiversCreation()
    {
        add_action(
            'carbon_fields_post_meta_container_saved',
            array($this, 'createOrUpdate')
        );
    }
}
