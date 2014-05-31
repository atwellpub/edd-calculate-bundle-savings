edd-calculate-bundle-savings
============================

WordPress short code to render savings data about a bundle in Easy Digital Downloads


## How to Use ##

* Install as a WordPress plugin 
* Build your shortcode and place it into your content to return a price number

## How to build the Shortcode ##

[edd-bundle-savings]

Accepts

[edd-bundle-savings
    return = "savings"
    bundle_id = 9457
    bundle_variant_price_id = 0
    download_variant_price_id = 0]
    
###return###

'return' is programmed to accept:

* savings (The true cost - the bundle cost = savings)
* total_price (The true cost)
* bundle_price (The cost of the bundle)

###bundle_id###

'bundle_id' accepts the ID of the bundle product

###bundle_variant_price_id###

If your bundle has multiple price options we need to set the id of the variation.
This value is usually 0 for variation 1, 1 for variation 2, and 2 for variation 3.

###download_variant_price_id###

If the products in the bundle have variant prices themselves then we need to tell the shortcode which price variation to measure against. This assumes that your product price variations all have the same pricing structure. 
