<?php

namespace PagarmeSplitPayment\Entities;

use PagarmeSplitPayment\Helper;
use WC_Data;
use WC_Order_Item;
use WC_Product;

class Partner
{
    public function __construct(array $partnerData)
    {
        $this->partnerData = $partnerData;
    }

    public function calculateValue(WC_Data $item): float
    {
        $value = [
            'percentage' => $this->getPercentageAmount($item, $this->partnerData),
            'fixed_amount' => $this->getFixedAmount($item, $this->partnerData),
        ];

        $comissionType = $this->partnerData['psp_comission_type'];

        if (!isset($value[$comissionType])) {
            throw new \Exception(__('Invalid comission type.', 'pagarme-split-payment'));
        }

        return $value[$comissionType];
    }

    public function getID()
    {
        return $this->partnerData['psp_partner_user'][0]['id'];
    }

    protected function getPercentageAmount(WC_Data $item, array $partnerData): float
    {
        $percentage = (float) $partnerData['psp_percentage'];
        $price = 0;
    
        if (is_a($item, WC_Order_Item::class)) {
            $price = (float) $item->get_data()['total'];
        } else if(is_a($item, WC_Product::class)) {
            $price = (float) $item->get_data()['price'];
        }

        return round($price * ($percentage / 100), 2);
    }

    protected function getFixedAmount(WC_Data $item, array $partnerData): float
    {
        $price = 0;
    
        if (is_a($item, WC_Order_Item::class)) {
            $price = (float) $item->get_data()['total'];
        } else if(is_a($item, WC_Product::class)) {
            $price = (float) $item->get_data()['price'];
        }

        $comission = (float) $partnerData['psp_fixed_amount'];

        return Helper::priceInCents($comission) > Helper::priceInCents($price) ? $price : $comission;
    }
}
