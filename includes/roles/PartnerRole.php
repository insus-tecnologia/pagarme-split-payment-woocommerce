<?php

namespace PagarmeSplitPayment\Roles;

use PagarmeSplitPayment\Fields\RecipientFieldGroup;

class PartnerRole extends Role {
    public function __construct()
    {
        parent::__construct(
            'partner',
            'Partner',
            [
                'read' => true,
                'psp_my_share' => true,
            ],
            RecipientFieldGroup::get(),
            false,
            true
        );
    }
}
