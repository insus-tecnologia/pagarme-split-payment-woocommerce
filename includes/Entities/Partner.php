<?php

namespace PagarmeSplitPayment\Entities;

use PagarmeSplitPayment\Helper;
use WC_Order_Item_Product;

class Partner
{
    protected $id;
    protected $comission = 0;
    protected $partnerPercentage = 0;
    protected $partnerFixedComission = 0;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function calculateComission(WC_Order_Item_Product $item): self
    {
        $comissionType = carbon_get_post_meta($item->get_product_id(), 'psp_comission_type');

        $value = [
            'percentage' => $this->calculatePercentageComission($item),
            'fixed_amount' => $this->calculateFixedComission($item),
        ];

        if (!isset($value[$comissionType])) {
            throw new \Exception(__('Invalid comission type.', 'pagarme-split-payment'));
        }

        $this->comission = $value[$comissionType];

        return $this;
    }

    public function getComission(): float
    {
        return $this->comission;
    }

    protected function calculatePercentageComission(WC_Order_Item_Product $item): float
    {
        $percentage = $this->getPercentage($item);

        $price = $item->get_data()['total'];

        return round($price * ($percentage / 100), 2);
    }

    protected function calculateFixedComission(WC_Order_Item_Product $item): float
    {
        $fixedComission = $this->getFixedComission($item);

        $price = (float) (is_a($item, WC_Order_Item_Product::class) ? $item->get_data()['total'] : $item->get_data()['price']);

        return Helper::priceInCents($fixedComission) > Helper::priceInCents($price) ? $price : $fixedComission;
    }

    protected function getPercentage(WC_Order_Item_Product $item): float
    {
        $partners = carbon_get_post_meta($item->get_product_id(), 'psp_percentage_partners');
        $id = $this->id;

        $partner = array_filter($partners, function (array $partner) use ($id) {
            return $partner['psp_partner'][0]['id'] == $id;
        });

        return (float) $partner[0]['psp_comission_value'];
    }

    protected function getFixedComission(WC_Order_Item_Product $item): float
    {
        $fixedComission = carbon_get_post_meta($item->get_product_id(), 'psp_comission_value');

        if (empty($fixedComission)) {
            return 0;
        }

        return (float) str_replace(wc_get_price_decimal_separator(), '.', $fixedComission);
    }
}
