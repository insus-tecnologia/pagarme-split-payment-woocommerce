<?php

namespace PagarmeSplitPayment\Fields;

use PagarmeSplitPayment\Fields\FieldGroup;
use \Carbon_Fields\Field\Field;

class PartnersPercentageFieldGroup implements FieldGroup
{
    public static function get()
    {
        $fields = [
            Field::make(
                'complex', 
                'psp_partners', 
                __('Partners payment split')
            )
            ->add_fields([
                Field::make(
                    'association',
                    'psp_partner_user',
                    __('Partner')
                )->set_types([
                    [
                        'type' => 'user',
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

        self::list_only_partners();

        return $fields;
    }

    public static function list_only_partners()
    {
        add_filter(
            'carbon_fields_association_field_options_psp_partner_user_user',
            array(__CLASS__, 'apply_partners_filter')
        );
    }
    
    public static function apply_partners_filter($options){
        $options['role'] = 'partner';
        return $options;
    }
}
