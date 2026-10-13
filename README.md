# WooCommerce Open Graph Product Tags

Prints the Open Graph product tags on single WooCommerce product pages:

```html
<meta property="product:price:amount" content="145.00" />
<meta property="product:price:currency" content="USD" />
<meta property="product:availability" content="instock" />
```

WordPress and WooCommerce core print none of these (WooCommerce prints JSON-LD only), and
some SEO plugins set `og:type=product` without them — The SEO Framework, for one. Google
Merchant Center and shopping crawlers read these tags, so a store that lacks them has one
fewer place to agree with its own price.

## When to use it

On a store that prints no `product:price:*` tags today. **If your SEO plugin already prints
them, leave this deactivated** — two sets of the same tags is worse than none. View the page
source of a product page and search for `product:price:amount` to check.

## What it prints

| Tag | Value |
|-----|-------|
| `product:price:amount` | The price a shopper is shown, so it follows the store's tax-display setting. Dot decimal. A variable product prints its lowest price. |
| `product:price:currency` | The store currency, as an ISO code. |
| `product:availability` | WooCommerce's own stock status: `instock`, `outofstock` or `onbackorder`. |

## Filter

```php
add_filter('woo_og_product_tags', function (array $tags, WC_Product $product): array {
    $tags['product:retailer_item_id'] = $product->get_sku();

    return $tags;
}, 10, 2);
```

An empty value is skipped, so unsetting a tag by returning `''` for it works too.

## Requirements

WordPress 6.5+, PHP 8.1+, WooCommerce (declared with `Requires Plugins`).

## Development

```bash
composer install     # WordPress + WooCommerce stubs for the IDE, and Laravel Pint
vendor/bin/pint      # format
```

The stubs only quiet the editor (`Undefined function 'wc_format_decimal'`); nothing from
`vendor/` is loaded at runtime. `.distignore` keeps it out of release archives.

Test it on a local Bedrock site with [wp-ops](https://github.com/imagewize/wp-ops):

```bash
SITE_ROOT=~/code/<site>/web/app wp-ops rsync-package-to-site plugin woo-og-product-tags ~/code/woo-og-product-tags
```

## License

MIT
