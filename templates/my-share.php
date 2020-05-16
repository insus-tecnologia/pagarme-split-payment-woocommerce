<div class="wrap metabox-holder">
    <h2><?php _e('My Share'); ?></h2>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <th scope="col"><?php _e('Partner') ?></th>
            <th scope="col"><?php _e('Order') ?></th>
            <th scope="col"><?php _e('Date') ?></th>
            <th scope="col"><?php _e('Product') ?></th>
            <th scope="col"><?php _e('Percentage') ?></th>
            <th scope="col"><?php _e('Amount') ?></th>
        </thead>
        <tbody>
            <?php foreach ($partner_orders as $order): ?>
                <tr>
                    <td>
                        <?= $order['user']->data->display_name ?>
                    </td>
                    <td>
                        <a href="<?= get_edit_post_link($order['order_id']) ?>">
                            #<?= $order['order_id'] ?>
                        </a>
                    </td>
                    <td>
                        <?= get_the_date(
                            get_option('date_format') . ' ' . get_option('time_format'), 
                            $order['order_id']
                        ) ?>
                    </td>
                    <td>
                        <?= get_the_title($order['product_id']) ?>
                        (x<?= $order['quantity'] ?>)
                    </td>
                    <td><?= $order['percentage'] ?>%</td>
                    <td><?= wc_price($order['amount']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <h3>
            <?= __('Total') . ': ' . wc_price($partner_total_amount) ?>
        </h3>
    </div>
</div>