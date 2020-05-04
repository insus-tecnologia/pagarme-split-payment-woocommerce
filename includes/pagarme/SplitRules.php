<?php

namespace PagarmeSplitPayment\Pagarme;

class SplitRules {
    public function split( $data, $order ) {
        $partners = $this->partnersPercentageOverOrder($order);

        var_dump($partners);
        die('oi');
        $data['split_rules'] = array(
            array(
                'recipient_id'          => 'ID_do_primeiro_recebedor',
                'percentage'            => '50',
                'liable'                => true,
                'charge_processing_fee' => true,
            ),
            array(
                'recipient_id'          => 'ID_do_segundo_recebedor',
                'percentage'            => '50',
                'liable'                => true,
                'charge_processing_fee' => true,
            ),
        );
    
        return $data;
    }

    // Calculate the percentage that each partner should receive over the order based on the values he should receive over each product
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
                $partners[$partner['psp_partner'][0]['id']]['value'] += ($item->get_data()['total'] * ($partner['psp_percentage']/100));
            }
        }

        $productsTotal = $order->get_subtotal();

        foreach($partners as $id => $partner) {
            $partners[$id]['percentage'] = round(($partner['value'] * 100) / $productsTotal);
        }

        return $partners;
    }

    public function addSplit()
    {
        add_action( 'wc_pagarme_transaction_data', array($this, 'split'), 10, 2 );
    }
}
