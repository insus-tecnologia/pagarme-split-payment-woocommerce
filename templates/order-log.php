<?php if (is_iterable($logLine)) : ?>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <th scope="col"><?= __('Partner') ?></th>
            <th scope="col"><?= __('Product') ?></th>
            <th scope="col"><?= __('Amount') ?></th>
            <th scope="col"><?= __('Percentage') ?></th>
        </thead>
        <tbody>
            <?php foreach ($logLine as $log) : ?>
                <?php $user = get_userdata($log['user_id']); ?>
                <tr>
                    <td><?= $user->display_name ?></td>
                    <td><?= get_the_title($log['product_id']) . "(x{$log['quantity']})" ?></td>
                    <td><?= wc_price($log['amount']) ?></td>
                    <td><?= $log['percentage'] ? $log['percentage'] . '%' : '-' ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php endif; ?>