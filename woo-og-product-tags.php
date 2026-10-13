<?php

/**
 * Plugin Name:       WooCommerce Open Graph Product Tags
 * Plugin URI:        https://github.com/imagewize/woo-og-product-tags
 * Description:       Prints the Open Graph product tags — product:price:amount, product:price:currency and product:availability — on single WooCommerce product pages.
 * Version:           0.1.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Requires Plugins:  woocommerce
 * Author:            Imagewize
 * Author URI:        https://imagewize.com
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 *
 * WordPress and WooCommerce core print no Open Graph price tags (WooCommerce
 * prints JSON-LD only), and some SEO plugins set og:type=product without them.
 * Google Merchant Center and shopping crawlers read these tags, so a store
 * without them has one fewer place to agree with its own price.
 *
 * Use this on a store that prints no product:price:* tags today. If your SEO
 * plugin already prints them, leave this deactivated — two sets of the same
 * tags is worse than none.
 */

namespace Imagewize\WooOgProductTags;

defined('ABSPATH') || exit;

add_action('wp_head', __NAMESPACE__.'\\print_tags', 20);

/**
 * Print the tags for the product being viewed.
 *
 * The price is the one a shopper is shown (it follows the store's tax-display
 * setting), with a dot decimal and an ISO currency code. A variable product
 * prints its lowest price, matching the low end of an AggregateOffer.
 * Availability is WooCommerce's own stock status: instock, outofstock or
 * onbackorder.
 */
function print_tags(): void
{
    if (! function_exists('wc_get_product') || ! is_singular('product')) {
        return;
    }

    $product = wc_get_product(get_the_ID());
    if (! $product) {
        return;
    }

    /**
     * Filter the tags before they print.
     *
     * @param  array<string, string>  $tags  Property => content.
     * @param  \WC_Product  $product
     */
    $tags = apply_filters('woo_og_product_tags', [
        'product:price:amount' => wc_format_decimal(wc_get_price_to_display($product), wc_get_price_decimals()),
        'product:price:currency' => get_woocommerce_currency(),
        'product:availability' => $product->get_stock_status(),
    ], $product);

    foreach ($tags as $property => $content) {
        if ($content === '' || $content === null) {
            continue;
        }
        printf('<meta property="%s" content="%s" />'."\n", esc_attr($property), esc_attr((string) $content));
    }
}
