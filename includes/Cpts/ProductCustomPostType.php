<?php

namespace PagarmeSplitPayment\Cpts;

use PagarmeSplitPayment\Fields\PartnersFieldGroup;

class ProductCustomPostType extends CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            'Products',
            'Product',
            'product',
            PartnersFieldGroup::get(),
            true
        );
    }
}
