<?php

namespace PagarmeSplitPayment\Admin;

use Carbon_Fields\Container;
use PagarmeSplitPayment\Fields\RecipientFieldGroup;

class PluginOptions {

    public function create()
    {
        $plugin_options = Container::make(
            'theme_options', 
            __('Pagar.me Split Payment')
        )->set_page_file('psp-plugin-options')
        ->set_icon('dashicons-cart')
        ->add_fields(RecipientFieldGroup::get());

        self::loadMenus();
    }

    public static function loadMenus()
    {
        add_action(
            'admin_menu',
            array(__CLASS__, 'addMySharePage')
        );
    }

    public static function addMySharePage()
    {
        add_menu_page(
            __('My Share'),
            __('My Share'),
            'psp_my_share',
            'psp-my-share',
            array(__CLASS__, 'getMyShareContent'),
            'dashicons-chart-bar'
        );
    }

    public static function getMyShareContent()
    {
        $user = wp_get_current_user();
        $user_id = $user->data->ID;
        $current_user_is_admin = current_user_can('administrator');

        $args = array(
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        if (!$current_user_is_admin) {
            $args['meta_key'] = 'psp_partner_id';
            $args['meta_value'] = $user_id;
        }

        $query = new \WC_Order_Query($args);

        $orders = $query->get_orders();

        $split_data = [];
        $total_amount = 0;
        foreach ($orders as $order) {
            $split_data = $order->get_meta('psp_order_split');

            if (empty($split_data)) {
                continue;
            }

            foreach ($split_data as $key => $register) {
                if ($register['user_id'] != $user_id && !$current_user_is_admin) {
                    unset($split_data[$key]);
                    continue;
                }

                $total_amount += $register['amount'];
                $split_data[$key]['order_id'] = $order->get_ID();

                $split_data[$key]['user'] = get_userdata($split_data[$key]['user_id']);
                unset($split_data[$key]['user_id']);
            }
        }

        set_query_var('partner_orders', $split_data);
        set_query_var('partner_total_amount', $total_amount);
        load_template(plugin_dir_path(__FILE__) . '../../templates/my-share.php');
    }
}
