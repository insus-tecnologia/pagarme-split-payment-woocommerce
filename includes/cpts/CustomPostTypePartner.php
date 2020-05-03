<?php

namespace PagarmeSplitPayment\Cpts;

use \Carbon_Fields\Field\Field;

class CustomPostTypePartner extends CustomPostType {
    public function __construct()
    {
        parent::__construct(
            'Partners',
            'Partner',
            'partner',
            [
                Field::make(
                    'number', 
                    'psp_recipient_id', 
                    __('Pagar.me Recipient ID')
                )
            ]
        );
    }
}