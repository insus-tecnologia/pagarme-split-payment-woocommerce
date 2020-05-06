<?php

namespace PagarmeSplitPayment\Cpts;

use \Carbon_Fields\Field\Field;

class CustomPostTypePartner extends CustomPostType {
    public function __construct()
    {
        parent::__construct(
            'Partners',
            'Partner',
            'partner',
            [
                Field::make(
                    'text', 
                    'psp_recipient_id', 
                    __('Pagar.me Recipient ID')
                )->set_attributes([
                    'readOnly' => 'readOnly',
                    'placeholder' => __('This field will be filled by Pagar.me')
                ]),
                Field::make(
                    'text', 
                    'psp_bank_code', 
                    __('Bank Code')
                )->set_attribute('type', 'number')
                ->set_required(true),
                Field::make(
                    'text', 
                    'psp_agency', 
                    __('Agency number')
                )->set_width(50)
                ->set_attribute('type', 'number')
                ->set_required(true),
                Field::make(
                    'text', 
                    'psp_agency_digit', 
                    __('Agency digit')
                )->set_width(50)
                ->set_attribute('type', 'number'),
                Field::make(
                    'text', 
                    'psp_account', 
                    __('Account number')
                )->set_width(50)
                ->set_attribute('type', 'number')
                ->set_required(true),
                Field::make(
                    'text', 
                    'psp_account_digit', 
                    __('Account digit')
                )->set_width(50)
                ->set_attribute('type', 'number')
                ->set_required(true),
                Field::make(
                    'select', 
                    'psp_account_type', 
                    __('Account type')
                )->add_options([
                    'conta_corrente' => __('Current account'),
                    'conta_poupanca' => __('Savings account'), 
                    'conta_corrente_conjunta' => __('Joint current account'), 
                    'conta_poupanca_conjunta' => __('Joint savings account'),
                ])->set_width(50)
                ->set_required(true),
                Field::make(
                    'text', 
                    'psp_document_number', 
                    __('Document number')
                )->set_width(50)
                ->set_attribute('type', 'number')
                ->set_required(true),
                Field::make(
                    'text', 
                    'psp_legal_name', 
                    __('Legal name')
                )->set_attribute('maxLength', 30)
                ->set_required(true),
            ]
        );
    }
}