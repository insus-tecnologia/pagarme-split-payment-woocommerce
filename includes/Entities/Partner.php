<?php

namespace PagarmeSplitPayment\Entities;

use PagarmeSplitPayment\Helper;
use WC_Order_Item;

class Partner
{
    public function __construct(array $partnerData)
    {
        $this->partnerData = $partnerData;
    }

    public function calculateValue(WC_Order_Item $item): float
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

    protected function getPercentageAmount(WC_Order_Item $item, array $partnerData): float
    {
        $percentage = (float) $partnerData['psp_percentage'];

        return round($item->get_data()['total'] * ($percentage / 100), 2);
    }

    protected function getFixedAmount(WC_Order_Item $item, array $partnerData): float
    {
        $comission = Helper::priceInCents((float) $partnerData['psp_fixed_amount']);
        $productPrice = Helper::priceInCents((float) $item->get_data()['total']);

        return $comission > $productPrice ? (float) $item->get_data()['total'] : (float) $partnerData['psp_fixed_amount'];
    }
}
