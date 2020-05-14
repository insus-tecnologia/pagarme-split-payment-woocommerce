<?php

namespace PagarmeSplitPayment\Pagarme;

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
    private function partnersPercentageOverOrder($order)
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

        $productsTotal = $order->get_subtotal();

        foreach($partners as $id => $partner) {
            // Round the value because pagarme doesnt allow float
            $partners[$id]['percentage'] = round(
                ($partner['value'] * 100) / $productsTotal
            );
        }

        return $partners;
    }

    public function addSplit()
    {
        add_filter( 'wc_pagarme_transaction_data', array($this, 'split'), 10, 2 );
    }
}
