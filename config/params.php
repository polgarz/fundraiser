<?php

return [
    'bsVersion' => '4.x',
    'senderEmail' => $_ENV['DEFAULT_SENDER_EMAIL'],
    'senderName' => $_ENV['DEFAULT_SENDER_NAME'],
    'campaignNotificationRecipients' => [
        [$_ENV['NEW_CAMPAIGN_NOTIFICATION_EMAIL'] => $_ENV['NEW_CAMPAIGN_NOTIFICATION_NAME']],
    ],
    'mailchimp' => [
        'key' => $_ENV['MAILCHIMP_KEY'],
        'list_id' => $_ENV['MAILCHIMP_LIST_ID'],
    ],
    'transfer' => [
        'name' => $_ENV['TRANSFER_NAME'],
        'bank_account_nr' => $_ENV['TRANSFER_BANK_ACCOUNT_NR'],
        'bank_name' => $_ENV['TRANSFER_BANK_NAME'],
    ],
    'recurringPaymentInfoUrl' => $_ENV['RECURRING_PAYMENT_INFO_URL'],
    'privacyPolicyUrl' => $_ENV['PRIVACY_POLICY_URL'],
    'cardRegistrationPolicyUrl' => $_ENV['CARD_REGISTRATION_POLICY_URL']
];
