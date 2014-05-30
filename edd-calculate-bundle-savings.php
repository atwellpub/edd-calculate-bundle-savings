<?php

/*
Plugin Name: Easy Digital Downloads - Calculate Bundle Savings
Plugin URI:
Description: Adds shortcodes that will calculate a bundle's total savings by ID
Version:  1.0.1
Author: Hudson Atwell
Author URI: http://www.hudsonatwell.co
*/

if ( !class_exists('EDD_Caluclate_Bundle_Savings') ) {
	class EDD_Caluclate_Bundle_Savings {
		
		public function __construct() {
			self::add_shortcodes();
		}
		
		public static function add_shortcodes() {		
			add_shortcode('edd-bundle-savings' , array( __CLASS__ , 'calculate_savings' ) );
		}
		
		/* Shortcode to retrieve data related to bundle savings 
		*
		* @accepts return
		*	{savings,bundle_price,total_price}
		* @accepts bundle_id
		*	INT ID of bundle being called
		* @accepts bundle_variant_key
		*	INT array key of bundle price variation
		* @accepts download_variant_key
		*	IN array key of download variants to target (assumed uniform structure across products)
		*/
		public static function calculate_savings( $atts ) {
			$atts = extract(shortcode_atts( array(
				  'return' => 'savings',
				  'bundle_id' => '0',
				  'bundle_variant_key' => 0,
				  'download_variant_key' => 0
			), $atts ));
			
			if ( !$bundle_id ) {
				return 'No bundle ID found in shortcode';
			}
			
			if ( !edd_is_bundled_product( $bundle_id ) ) {
				return 'Not a bundle!';
			}
			
			$bundled_products = get_post_meta( $bundle_id , '_edd_bundled_products' , true);
			
			if ( !$bundled_products ) { 
				return 'bundle '.$bundle_id.' has no downloads!';
			}
			
			$prices = array();
			$total_price = 0;
			
			foreach ($bundled_products as $download_id ) {
			
				if ( edd_has_variable_prices( $download_id ) ) {
					$prices[ $download_id ] = edd_get_variable_prices( $download_id );
				} else {
					$prices[ $download_id ] = edd_get_download_price( $download_id );
				}
			}
			
			foreach ($prices as $download_id => $download_price ) {
			
				if ( !is_array($download_price) )
				{
					$total_price = $total_price + $download_price;
					
				} else {
				
					foreach ($download_price as $variant_key => $total) {
						if ( $download_variant_key && $download_variant_key == $variant_key ) {
							/* Get target variant price */
							$cost = $total['amount'];
							break;
						} else {
							/* Or use last variant price - typically most expensive */
							$cost = $total['amount'];
						}
					}
					
					/* Add product cost to $total_price total */
					$total_price = $total_price + $cost;
					
				}
			}
			
			/* Calculate Total Bundle Cost */			
			if ( edd_has_variable_prices( $bundle_id ) ) {
				$bundle_price = edd_get_variable_prices( $bundle_id );
			} else {
				$bundle_price = edd_get_download_price( $bundle_id );
			}
			
			/* If Bundle has multiple variants discover which one to use */
			if ( is_array($bundle_price) ) {
				foreach ( $bundle_price as $variant_key => $price ) {
					if ( $variant_key == $bundle_variant_key ) {
						
						$bundle_price = $price['amount'];
						break;
					
					}
				}
			} 
			
			/* Get Savings */
			$bundle_price = $bundle_price;
			$total_price = $total_price;
			$savings = $total_price - $bundle_price;
			
			
			switch ($return) {
				case 'total_price':
					return number_format( (double) $total_price, 2, '.', '' );
				case 'bundle_price':
					return number_format( (double) $bundle_price, 2, '.', '' );				
				case 'savings':
					/* this is how edd sanatizes the number format */
					$decimals = apply_filters( 'edd_sanitize_amount_decimals', 2, $savings );
					return number_format( (double) $savings, $decimals, '.', '' );
			}
			
		}
	}

	$GLOBALS['EDD_Caluclate_Bundle_Savings'] = new EDD_Caluclate_Bundle_Savings();
}
