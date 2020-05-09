<?php

namespace PagarmeSplitPayment\Cpts;

use PagarmeSplitPayment\Fields\RecipientFieldGroup;

class CustomPostTypePartner extends CustomPostType {
    public function __construct()
    {
        parent::__construct(
            'Partners',
            'Partner',
            'partner',
            RecipientFieldGroup::get()
        );
    }
}
