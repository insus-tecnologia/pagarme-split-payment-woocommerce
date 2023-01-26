<?php

namespace PagarmeSplitPayment\Admin;

use PagarmeSplitPayment\Pagarme\Recipients;

class Actions
{
    public function createRecipients()
    {
        add_action(
            'carbon_fields_user_meta_container_saved',
            array($this, 'createPartnerRecipient')
        );

        add_action(
            'carbon_fields_theme_options_container_saved',
            array($this, 'createMainRecipient')
        );

        return $this;
    }

    public function createAdminNotices()
    {
        add_action('admin_notices', [$this, 'showComissionErrors']);

        return $this;
    }

    public function logExternalRequests()
    {
        add_action('http_api_debug', [$this, 'logPagarmeApiRequests'], 10, 5);
        return $this;
    }

    public function createMainRecipient()
    {
        $partnerData = carbon_get_theme_option(
            'psp_partner'
        )[0];

        $recipientService = new Recipients();
        $recipient = $recipientService->createOrUpdate($partnerData);

        carbon_set_theme_option(
            'psp_partner[0]/psp_recipient_id',
            $recipient->id
        );
    }

    public function createPartnerRecipient($partnerId)
    {
        $user = get_userdata($partnerId);

        if (!in_array('partner', $user->roles)) {
            return;
        }

        $partnerData = carbon_get_user_meta(
            $partnerId,
            'psp_partner'
        );

        if (empty($partnerData)) {
            return;
        }

        $partnerData = array_shift($partnerData);

        $recipientService = new Recipients();
        $recipient = $recipientService->createOrUpdate($partnerData);

        carbon_set_user_meta(
            $partnerId,
            'psp_partner[0]/psp_recipient_id',
            $recipient->id
        );
    }

    public function showComissionErrors()
    {
        global $post, $pagenow;

        if (
            empty($post->ID)
            || (!$error = get_transient('psp_comission_error_' . $post->ID))
            || 'post.php' !== $pagenow
        ) {
            return;
        }

?>
        <div class="notice notice-error">
            <p><?php echo $error; ?></p>
        </div>
<?php
    }

    public function logPagarmeApiRequests($response, $hookContext, $httpTransportUsed, $httpArgs, $requestedUrl)
    {
        if (
            !function_exists('wc_get_logger')
            || !class_exists('WC_Pagarme_API')
            || !str_contains($requestedUrl, \WC_Pagarme_API::API_URL)
        ) {
            return;
        }

        $allowedBodyItems = [
            'amount',
            'postback_url',
            'metadata',
            'payment_method',
            'split_rules'
        ];

        $filteredHttpBody = array_filter($httpArgs['body'], function ($key) use ($allowedBodyItems) {
            return in_array($key, $allowedBodyItems);
        }, ARRAY_FILTER_USE_KEY);

        $filteredHttpHeaders = array_filter($httpArgs['headers'], function ($key) {
            return !str_contains($key, 'api_key');
        }, ARRAY_FILTER_USE_KEY);

        $allowedResponseItems = [
            'body',
            'response'
        ];

        $filteredResponse = array_filter($response, function ($key) use ($allowedResponseItems) {
            return in_array($key, $allowedResponseItems);
        }, ARRAY_FILTER_USE_KEY);

        $logEntry = var_export([
            'requested_url' => sprintf('[%s] %s', $httpArgs["method"], $requestedUrl),
            'request_headers' => $filteredHttpHeaders,
            'request_body' => $filteredHttpBody,
            'response_body' => $filteredResponse,
        ], true);

        $logger = wc_get_logger();

        if ($response['http_response']->get_status() === 200) {
            $logger->info(sprintf('Pagar.me API - Request sent successfully: %s', $logEntry));
        } else {
            $logger->error(sprintf('Pagar.me API - Request sent with error: %s', $logEntry));
        }
    }
}
