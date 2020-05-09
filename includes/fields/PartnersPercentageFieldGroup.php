<?php

namespace PagarmeSplitPayment\Fields;

use PagarmeSplitPayment\Fields\FieldGroup;
use \Carbon_Fields\Field\Field;

class PartnersPercentageFieldGroup implements FieldGroup
{
    public static function get()
    {
        return [
            Field::make(
                'complex', 
                'psp_partners', 
                __('Partners payment split')
            )
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
                    'text',
                    'psp_percentage',
                    __('Partner percentage')
                )->set_width(50)
                ->set_attribute('type', 'number')
            ])
        ];
    }
}
