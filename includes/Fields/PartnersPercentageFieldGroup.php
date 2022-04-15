<?php

namespace PagarmeSplitPayment\Fields;

use PagarmeSplitPayment\Fields\FieldGroup;
use \Carbon_Fields\Field\Field;

class PartnersPercentageFieldGroup implements FieldGroup
{
    public static function get()
    {
        $comission_type_logic = [ 'field' => 'psp_comission_type', 'compare' => '=', 'value' => '' ];

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
                ->set_max(1),
                Field::make('radio', 'psp_comission_type', __('Comission Type', 'pagarme-split-payment'))
                    ->set_options([
                        'percentage' => __('Percentage', 'pagarme-split-payment'),
                        'fixed_amount' => __('Fixed Amount', 'pagarme-split-payment'),
                    ])
                    ->set_width(50),
                Field::make('text', 'psp_percentage', __('Partner Percentage', 'pagarme-split-payment'))
                    ->set_width(50)
                    ->set_attribute('type', 'number')
                    ->set_conditional_logic([
                        array_merge($comission_type_logic, ['value' => 'percentage'])
                    ]),
                Field::make('text', 'psp_fixed_amount', __('Partner Amount', 'pagarme-split-payment'))
                    ->set_width(50)
                    ->set_attribute('type', 'number')
                    ->set_conditional_logic([
                        array_merge($comission_type_logic, ['value' => 'fixed_amount'])
                    ]),
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
