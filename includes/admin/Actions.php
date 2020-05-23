<?php

namespace PagarmeSplitPayment\Admin;

use PagarmeSplitPayment\Pagarme\Recipients;

class Actions {
    public function createRecipients()
    {
        add_action(
            'carbon_fields_user_meta_container_saved',
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
        $user = get_userdata($partnerId);
        
        if (!in_array('partner', $user->roles)) {
            return;
        }

        $partnerData = carbon_get_user_meta(
            $partnerId, 
            'psp_partner'
        );

        if (empty($partnerData)) {
            return;
        }

        $partnerData = array_shift($partnerData);

        $recipientService = new Recipients();
        $recipient = $recipientService->createOrUpdate($partnerData);

        carbon_set_user_meta(
            $partnerId, 
            'psp_partner[0]/psp_recipient_id', 
            $recipient->id
        );
    }
}
