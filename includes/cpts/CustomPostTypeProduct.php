<?php

namespace PagarmeSplitPayment\Cpts;

use \Carbon_Fields\Field\Field;

class CustomPostTypeProduct extends CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            'Products',
            'Product',
            'product',
            [
                Field::make('complex', 'psp_partners', __('Partners payment split'))
                    ->add_fields([
                        Field::make(
                            'association',
                            'psp_partner',
                            __('Partner')
                        )->set_types([
                            [
                                'type' => 'post',
                                'post_type' => 'partner'
                            ]
                        ])->set_min(1)
                        ->set_max(1)
                        ->set_width(50),
                        Field::make(
                            'number',
                            'psp_percentage',
                            __('Partner percentage')
                        )->set_width(50)
                    ])
            ],
            true
        );
    }
}
