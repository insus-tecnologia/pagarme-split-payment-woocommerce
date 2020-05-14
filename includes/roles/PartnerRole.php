<?php

namespace PagarmeSplitPayment\Roles;

use PagarmeSplitPayment\Fields\RecipientFieldGroup;

class PartnerRole extends Role {
    public function __construct()
    {
        parent::__construct(
            'partner',
            'Partner',
            [],
            RecipientFieldGroup::get()
        );
    }
}
