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
                    'number', 
                    'psp_recipient_id', 
                    __('Pagar.me Recipient ID')
                ),
                Field::make(
                    'number', 
                    'psp_bank_code', 
                    __('Bank Code')
                ),
                Field::make(
                    'number', 
                    'psp_agency', 
                    __('Agency number')
                )->set_width(50),
                Field::make(
                    'number', 
                    'psp_agency_digit', 
                    __('Agency digit')
                )->set_width(50),
                Field::make(
                    'number', 
                    'psp_account', 
                    __('Account number')
                )->set_width(50),
                Field::make(
                    'number', 
                    'psp_account_digit', 
                    __('Account digit')
                )->set_width(50),
                Field::make(
                    'select', 
                    'psp_account_type', 
                    __('Account type')
                )->add_options([
                    'conta_corrente' => __('Current account'),
                ])->set_width(50),
                Field::make(
                    'number', 
                    'psp_document_number', 
                    __('Document number')
                )->set_width(50),
                Field::make(
                    'text', 
                    'psp_legal_name', 
                    __('Legal name')
                ),
            ]
        );
    }
}