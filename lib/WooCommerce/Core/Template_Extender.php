<?php // phpcs:disable Squiz.Commenting.VariableComment.MissingVar
namespace Oblak\NPG\WooCommerce\Core;

use Oblak\WooCommerce\Core\Base_Template_Extender;
use Oblak\WP\Decorators\Hookable;

/**
 * Adds custom templates to WooCommerce
 */
#[Hookable( 'before_woocommerce_init', 99 )]
class Template_Extender extends Base_Template_Extender {
    /**
     * {@inheritDoc}
     */
    protected $base_path = WCNPG_ABSPATH . 'woocommerce/';

    /**
     * {@inheritDoc}
     */
    protected $path_tokens = array(
        'WCNPG_ABSPATH' => WCNPG_ABSPATH,
    );

    /**
     * {@inheritDoc}
     */
    protected $templates = array(
        'checkout/form-nestpay.php',
        'emails/customer-nestpay-status.php',
        'emails/email-nestpay-transaction-details.php',
    );
}
