<?php

/**
 * Stripe Payment Gateway Library for OpenCart 4.x
 *
 * This library provides integration with Stripe's Payment Intents API
 * Compatible with Stripe PHP SDK 13.x+
 */

// Check if Stripe SDK is available via Composer
$composer_autoload = DIR_SYSTEM . '../vendor/autoload.php';
$legacy_autoload = DIR_SYSTEM . 'library/stripe-php/init.php';

if (file_exists($composer_autoload)) {
    // Modern approach: Use Composer autoloading
    require_once $composer_autoload;
} elseif (file_exists($legacy_autoload)) {
    // Fallback: Use legacy SDK structure
    require_once $legacy_autoload;
} else {
    // Manual loading for older installations
    $stripe_files = [
        'Stripe.php',
        'Util/AutoPagingIterator.php',
        'Util/RequestOptions.php',
        'Util/Set.php',
        'Util/Util.php',
        'HttpClient/ClientInterface.php',
        'HttpClient/CurlClient.php',
        'Error/Base.php',
        'Error/Api.php',
        'Error/ApiConnection.php',
        'Error/Authentication.php',
        'Error/Card.php',
        'Error/InvalidRequest.php',
        'Error/RateLimit.php',
        'ApiResponse.php',
        'JsonSerializable.php',
        'StripeObject.php',
        'ApiRequestor.php',
        'ApiResource.php',
        'SingletonApiResource.php',
        'AttachedObject.php',
        'ExternalAccount.php',
        'Account.php',
        'AlipayAccount.php',
        'ApplicationFee.php',
        'ApplicationFeeRefund.php',
        'Balance.php',
        'BalanceTransaction.php',
        'BankAccount.php',
        'BitcoinReceiver.php',
        'BitcoinTransaction.php',
        'Card.php',
        'Charge.php',
        'Collection.php',
        'CountrySpec.php',
        'Coupon.php',
        'Customer.php',
        'Dispute.php',
        'Event.php',
        'FileUpload.php',
        'Invoice.php',
        'InvoiceItem.php',
        'Order.php',
        'OrderReturn.php',
        'Plan.php',
        'Product.php',
        'Recipient.php',
        'Refund.php',
        'SKU.php',
        'Source.php',
        'Subscription.php',
        'ThreeDSecure.php',
        'Token.php',
        'Transfer.php',
        'TransferReversal.php',
        'PaymentIntent.php',
        'PaymentMethod.php',
        'Webhook.php',
        'WebhookEndpoint.php'
    ];

    foreach ($stripe_files as $file) {
        $file_path = DIR_SYSTEM . 'library/stripe-php/' . $file;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}

/**
 * Stripe Library Class
 * Provides initialization and utility methods for Stripe integration
 */
class StripeLibrary {

    /**
     * Initialize Stripe with API key
     *
     * @param string $api_key Stripe API key
     * @return bool
     */
    public static function init($api_key) {
        if (empty($api_key)) {
            return false;
        }

        try {
            \Stripe\Stripe::setApiKey($api_key);
            \Stripe\Stripe::setApiVersion('2023-10-16'); // Use latest stable API version
            return true;
        } catch (Exception $e) {
            error_log('Stripe initialization error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Stripe SDK version
     *
     * @return string
     */
    public static function getVersion() {
        return \Stripe\Stripe::VERSION ?? 'Unknown';
    }
}
