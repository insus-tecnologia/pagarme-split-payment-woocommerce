<?php

namespace PagarmeSplitPayment\Pagarme;

use PagarmeSplitPayment\Entities\Partner;
use PagarmeSplitPayment\Helper;

class SplitRules
{
    public function split($data, $order)
    {
        $partners = $this->partnersAmountOverOrder($order);
        $mainRecipientData = carbon_get_theme_option('psp_partner');
        $data['split_rules'] = [];

        if (
            empty($partners) ||
            empty($mainRecipientData[0]) ||
            empty($mainRecipientData[0]['psp_recipient_id'])
        ) {
            return $data;
        }

        $partnersAmount = 0;

        foreach ($partners as $id => $partner) {
            $partnerData = carbon_get_user_meta($id, 'psp_partner')[0];

            if (empty($partnerData['psp_recipient_id'])) {
                continue;
            }

            $partnersAmount += Helper::priceInCents($partner['value']);

            $data['split_rules'][] = [
                'recipient_id' => $partnerData['psp_recipient_id'],
                'amount' => Helper::priceInCents($partner['value']),
                'liable' => true,
                'charge_processing_fee' => true,
            ];
        }

        // If there is no percentage to split return original data
        if (!$partnersAmount) {
            return $data;
        }

        $data['split_rules'][] = [
            'recipient_id' => $mainRecipientData[0]['psp_recipient_id'],
            'amount' => Helper::priceInCents($order->get_total()) - $partnersAmount,
            'liable' => true,
            'charge_processing_fee' => true,
        ];

        $this->log($order);

        return $data;
    }

    /**
     * Calculate the amount that each partner should receive over the order
     *
     * @param mixed $order WooCommerce Order object.
     * @return array
     */
    private function partnersAmountOverOrder(\WC_Order $order)
    {
        $items = $order->get_items();
        $partners = [];

        if (!$items) {
            return $partners;
        }

        foreach ($items as $item) {
            foreach ($this->getPartnersFromProduct($item->get_product_id()) as $partner) {
                $userId = (int) $partner['psp_partner'][0]['id'];
                $partner = new Partner($userId);

                $orderItemPartnerComission = $partner->calculateComission($item)->getComission();

                if (empty($partners[$userId])) {
                    $partners[$userId] = ['value' => 0];
                }

                $partners[$userId]['value'] += $orderItemPartnerComission;
            }
        }

        return $partners;
    }

    private function getPartnersFromProduct(int $productId): array
    {
        $partners = [
            'percentage' => carbon_get_post_meta($productId, 'psp_percentage_partners'),
            'fixed_amount' => [[
                'psp_partner' => carbon_get_post_meta($productId, 'psp_fixed_partner'),
                'psp_comission_value' => carbon_get_post_meta($productId, 'psp_comission_value')
            ]]
        ];

        $comissionType = carbon_get_post_meta($productId, 'psp_comission_type');

        return $partners[$comissionType];
    }

    private function log($order)
    {
        // Remove all partners related to order to be sure this info will be updated
        delete_post_meta($order->get_ID(), 'psp_order_partner');

        $items = $order->get_items();
        $partners_ids = [];

        $partners = [];
        $orderPartners = $this->partnersAmountOverOrder($order);

        if ($items && is_array($items)) {
            foreach ($items as $item) {
                $productId = $item->get_product_id();
                $productPartners = carbon_get_post_meta($productId, 'psp_percentage_partners');

                // Get data for all partners related to this order
                foreach ($productPartners as $partner) {
                    $partners[] = [
                        'user_id' => $partner['psp_partner'][0]['id'],
                        'product_id' => $productId,
                        'quantity' => $item->get_quantity(),
                        'amount' => $orderPartners[$partner['psp_partner'][0]['id']]['value'],
                        'percentage' => !empty($partner['psp_comission_value']) ? $partner['psp_comission_value'] : null,
                    ];

                    // Register the different partners at this order
                    if (!in_array($partner['psp_partner'][0]['id'], $partners_ids)) {
                        $partners_ids[] = $partner['psp_partner'][0]['id'];
                    }
                }
            }
        }

        // Turn order queriable by partner id
        foreach ($partners_ids as $partner_id) {
            add_post_meta($order->get_ID(), 'psp_partner_id', $partner_id);
        }

        update_post_meta($order->get_ID(), 'psp_order_split', $partners);
    }

    public function addSplit()
    {
        add_filter('wc_pagarme_transaction_data', array($this, 'split'), 10, 2);
        add_filter('wc_pagarme_transaction_data_for_renewal', array($this, 'split'), 10, 2);
    }
}
