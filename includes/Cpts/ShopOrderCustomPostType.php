<?php

namespace PagarmeSplitPayment\Cpts;

use PagarmeSplitPayment\Fields\ShopOrderPartnersFieldGroup;

class ShopOrderCustomPostType extends CustomPostType
{
    public function __construct()
    {
        parent::__construct(
            'Orders',
            'Order',
            'shop_order',
            [],
            true
        );

        add_action('add_meta_boxes', array($this, 'addPartnersMetaBox'));
    }

    public function createPartnersMetaBox()
    {
        set_query_var('logLine', get_post_meta(get_the_ID(), 'psp_order_split', true));
        load_template(plugin_dir_path(__FILE__) . '../../templates/order-log.php');
    }

    public function addPartnersMetaBox()
    {
        add_meta_box(
            'partnersPercentage',
            __('Partners percentage'),
            array($this, 'createPartnersMetaBox'),
            $this->slug
        );
    }
}
