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

        add_action('save_post_product', [$this, 'validatePartnersComission'], 10);
    }

    public function validatePartnersComission(int $postID)
    {
        $partners = $_POST['carbon_fields_compact_input']['_psp_partners'];
        $product = wc_get_product($postID);

        $totalPartnersComission = array_reduce($partners, function (float $total, array $partner) use ($product) {
            $partner = new Partner([
                'psp_comission_type' => $partner['_psp_comission_type'],
                'psp_percentage' => $partner['_psp_percentage'] ?? 0,
                'psp_fixed_amount' => $partner['_psp_fixed_amount'] ?? 0,
            ]);

            return $partner->calculateValue($product) + $total;
        }, 0);

        if (Helper::priceInCents($totalPartnersComission) > Helper::priceInCents((float) $product->get_price())) {
            set_transient('psp_comission_error_' . $postID, __('Partners comissions has a bigger value than product price.', 'pagarme-split-payment'), YEAR_IN_SECONDS);
            return;
        }

        delete_transient('psp_comission_error_' . $postID);
    }
}
