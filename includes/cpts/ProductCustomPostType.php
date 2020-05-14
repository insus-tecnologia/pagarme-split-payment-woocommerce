<?php

namespace PagarmeSplitPayment\Cpts;

use PagarmeSplitPayment\Fields\PartnersPercentageFieldGroup;

class ProductCustomPostType extends CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            'Products',
            'Product',
            'product',
            PartnersPercentageFieldGroup::get(),
            true
        );
    }
}
