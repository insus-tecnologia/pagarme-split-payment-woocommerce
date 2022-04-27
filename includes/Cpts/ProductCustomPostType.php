<?php

namespace PagarmeSplitPayment\Cpts;

use Carbon_Fields\Field\Field;
use PagarmeSplitPayment\Entities\Partner;
use PagarmeSplitPayment\Fields\PartnersFieldGroup;
use PagarmeSplitPayment\Helper;

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

        add_filter('carbon_fields_should_save_field_value', [$this, 'validateFixedPartnerComission'], 10, 3);
    }

    public function validateFixedPartnerComission(bool $save, $value, Field $field)
    {
        global $post;
        $comissionType = $_POST['carbon_fields_compact_input']['_psp_comission_type'];

        if ($field->get_name() !== '_psp_comission_value' || 'fixed_amount' !== $comissionType || empty($value)) {
            return $save;
        }

        $comission = (float) str_replace(wc_get_price_decimal_separator(), '.', $value);
        $product = wc_get_product($post->ID);

        if (Helper::priceInCents($comission) > Helper::priceInCents((float) $product->get_price())) {
            set_transient('psp_comission_error_' . $post->ID, __('Partners comissions has value greater than product price.', 'pagarme-split-payment'), YEAR_IN_SECONDS);
            return false;
        }

        delete_transient('psp_comission_error_' . $post->ID);
        return $save;
    }
}
