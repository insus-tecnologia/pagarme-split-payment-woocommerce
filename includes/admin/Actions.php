<?php

namespace PagarmeSplitPayment\Admin;

use PagarmeSplitPayment\Pagarme\Recipients;

class Actions {
    public function createRecipients()
    {
        add_action(
            'carbon_fields_post_meta_container_saved',
            array($this, 'createPartnerRecipient')
        );

        add_action(
            'carbon_fields_theme_options_container_saved',
            array($this, 'createMainRecipient')
        );
    }

    public function createMainRecipient()
    {
        $partnerData = carbon_get_theme_option( 
            'psp_partner'
        )[0];

        $recipientService = new Recipients();
        $recipient = $recipientService->createOrUpdate($partnerData);

        carbon_set_theme_option(
            'psp_partner[0]/psp_recipient_id', 
            $recipient->id
        );
    }

    public function createPartnerRecipient( $partnerId )
    {
        if ('partner' !== get_post_type($partnerId)) {
            return;
        }

        $partnerData = carbon_get_post_meta(
            $partnerId, 
            'psp_partner'
        )[0];

        $recipientService = new Recipients();
        $recipient = $recipientService->createOrUpdate($partnerData);

        carbon_set_post_meta(
            $partnerId, 
            'psp_partner[0]/psp_recipient_id', 
            $recipient->id
        );
    }
}
