<?php

namespace PagarmeSplitPayment\Pagarme;

use WC_Order_Item;

class SplitRules {
    public function split( $data, $order ) {
        $partners = $this->partnersPercentageOverOrder($order);
        $mainRecipientData = carbon_get_theme_option('psp_partner');

        if (
            empty($partners) ||
            empty($mainRecipientData[0]) || 
            empty($mainRecipientData[0]['psp_recipient_id'])
        ) {
            return $data;
        }

        $partnersPercentage = 0;
        foreach($partners as $id => $partner) {
            $partnerData = carbon_get_user_meta($id, 'psp_partner')[0];
            
            if (empty($partnerData['psp_recipient_id'])) {
                continue;
            }

            // Count the percentage splited to partners
            $partnersPercentage += $partner['percentage'];

            $data['split_rules'][] = [
                'recipient_id' => $partnerData['psp_recipient_id'],
                'percentage' => $partner['percentage'],
                'liable' => true,
                'charge_processing_fee' => true,
            ];
        }

        // If there is no percentage to split return original data
        if (!$partnersPercentage) {
            return $data;
        }

        $data['split_rules'][] = [
            'recipient_id' => $mainRecipientData[0]['psp_recipient_id'],
            'percentage' => 100 - $partnersPercentage,
            'liable' => true,
            'charge_processing_fee' => true,
        ];

        $this->log($order);

        return $data;
    }

    /**
	 * Calculate the percentage that each partner should receive over the order 
     * based on the values he should receive over each product
	 *
	 * @param mixed $order WooCommerce Order object.
	 * @return array
	 */
    // 
    private function partnersPercentageOverOrder(\WC_Order $order)
    {
        $items = $order->get_items();
        $partners = [];

        foreach ( $items as $item ) {
            $productPartners = carbon_get_post_meta(
                $item->get_product_id(), 
                'psp_partners'
            );

            // Sum the total amount to be given to each partner on the order
            foreach ($productPartners as $partner) {
                $partners[$partner['psp_partner_user'][0]['id']]['value'] += (
                    $item->get_data()['total'] * ($partner['psp_percentage']/100)
                );
            }
        }

        $productsTotal = $order->get_total();

        foreach($partners as $id => $partner) {
            // Round the value because pagarme doesnt allow float
            $partners[$id]['percentage'] = round(
                ($partner['value'] * 100) / $productsTotal
            );
        }

        return $partners;
    }

    private function log($order)
    {
        // Remove all partners related to order to be sure this info will be updated
        delete_post_meta($order->get_ID(), 'psp_order_partner');

        $items = $order->get_items();
        $partners = [];

        $partners_ids = [];

        foreach ( $items as $item ) {
            $productId = $item->get_product_id();
            $productPartners = carbon_get_post_meta(
                $productId,
                'psp_partners'
            );

            // Get data for all partners related to this order
            foreach ($productPartners as $partner) {
                $partners[] = [
                    'user_id' => $partner['psp_partner_user'][0]['id'],
                    'product_id' => $productId,
                    'quantity' => $item->get_quantity(),
                    'amount' => $item->get_data()['total'] * ($partner['psp_percentage']/100),
                    'percentage' => $partner['psp_percentage'],
                ];

                // Register the different partners at this order
                if (!in_array($partner['psp_partner_user'][0]['id'], $partners_ids)) {
                    $partners_ids[] = $partner['psp_partner_user'][0]['id'];
                }
            }
        }

        // Turn order queriable by partner id
        foreach($partners_ids as $partner_id) {
            add_post_meta($order->get_ID(), 'psp_partner_id', $partner_id);
        }

        update_post_meta($order->get_ID(), 'psp_order_split', $partners);
    }

    public function addSplit()
    {
        add_filter( 'wc_pagarme_transaction_data', array($this, 'split'), 10, 2 );
    }
}
