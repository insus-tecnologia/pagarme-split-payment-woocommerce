<?php

namespace PagarmeSplitPayment\Fields;

use PagarmeSplitPayment\Fields\FieldGroup;
use \Carbon_Fields\Field\Field;

class PartnersFieldGroup implements FieldGroup
{
    public static function get()
    {
        $comission_type_logic = [ 'field' => 'psp_comission_type', 'compare' => '=', 'value' => '' ];

        $fields = [
            Field::make('radio', 'psp_comission_type', __('Comission Type', 'pagarme-split-payment'))
                ->set_options([
                    'percentage' => __('Percentage', 'pagarme-split-payment'),
                    'fixed_amount' => __('Fixed Amount', 'pagarme-split-payment'),
                ]),
            self::user_association_field('psp_fixed_partner', __('Partner'))
                ->set_conditional_logic([
                    array_merge($comission_type_logic, ['value' => 'fixed_amount'])
                ]),
            Field::make('text', 'psp_fixed_amount', __('Partner Amount', 'pagarme-split-payment'))
                ->set_width(50)
                ->set_help_text(sprintf( __( 'Please enter with one monetary decimal point (%s) without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ))
                ->set_conditional_logic([
                    array_merge($comission_type_logic, ['value' => 'fixed_amount'])
                ]),
            Field::make('complex', 'psp_percentage_partners', __('Partners payment split'))
            ->add_fields([
                self::user_association_field('psp_partner', __('Partner')),
                Field::make('text', 'psp_percentage', __('Partner Percentage', 'pagarme-split-payment'))
                    ->set_width(50)
                    ->set_attribute('type', 'number')
            ])->set_conditional_logic([
                array_merge($comission_type_logic, ['value' => 'percentage'])
            ])
            ->set_layout('tabbed-horizontal'),
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

    protected static function user_association_field($name, $label) {
        return Field::make('association', $name, $label)
        ->set_types([
            [
                'type' => 'user',
            ]
        ])->set_min(1)
        ->set_max(1)
        ->set_width(50);
    }
}
