<?php

/** 
 * @package meppswholesale
*/
/*
Plugin Name: Wholesale bulk orders
Plugin URI: https://github.com/meppps/wc-default-shipping
Description: Set wholesale and bulk order rules
Version: 1.0
Author: Mikey Epps
Author URI: http://github.com/meppps
License: GPLv2 or later
*/

// security precaution
if(!defined('ABSPATH')){
    exit;
}


// Hide price range
function wc_varb_price_range( $wcv_price, $product ) {
 
    // $prefix = sprintf('%s: ', __('From', 'wcvp_range'));
    $prefix = '';
 
    $wcv_reg_min_price = $product->get_variation_regular_price( 'min', true );
    $wcv_min_sale_price    = $product->get_variation_sale_price( 'min', true );
    $wcv_max_price = $product->get_variation_price( 'max', true );
    $wcv_min_price = $product->get_variation_price( 'min', true );
 
    $wcv_price = ( $wcv_min_sale_price == $wcv_reg_min_price ) ?
        wc_price( $wcv_reg_min_price ) :
        '<del>' . wc_price( $wcv_reg_min_price ) . '</del>' . '<ins>' . wc_price( $wcv_min_sale_price ) . '</ins>';
 
    return ( $wcv_min_price == $wcv_max_price ) ?
        $wcv_price :
        sprintf('%s%s', $prefix, $wcv_price);
}
 
add_filter( 'woocommerce_variable_sale_price_html', 'wc_varb_price_range', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'wc_varb_price_range', 10, 2 );




// // hide bulk from retail

add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'filter_dropdown_variation_args', 10, 1 );

function filter_dropdown_variation_args( $args ) {

    $user = wp_get_current_user();
    $options = $args['options'];
    $hidden = ['2-Pack','3-Pack'];
    
    if(! in_array( 'wholesale_customer', (array) $user->roles )){
        
        // Dont show "Choose an option"
        $args['show_option_none'] = false;

        // Remove the option values
        foreach( $args['options'] as $key => $option ){

            if(strpos($option, 'Pack') !== false || strpos($option, 'pack') !== false){
                unset($args['options'][$key]);
            }
        }
        return $args;
    }else{
        return $args;
    }
}


// Replace add to cart btn with find distr. btn on product page
function replace_addtocart_btn() {
	
	global $post;
	$user = wp_get_current_user();
	$user_exception = 'wholesale_customer';
		
	// Apply only to distributor only products
	if ( has_term( 'distributor-only', 'product_cat', $post->ID ) ) {

		// If user not distributor, change btn
		if(! in_array( $user_exception, (array) $user->roles )){

			// echo('You are NOT a distributor');

			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			add_action( 'woocommerce_single_product_summary', 'find_distr_button', 15 ); 
	
			function find_distr_button() {
				$locator_url = 'https://stainoutsystem.com/find-a-store/';
				echo '<a href="'.$locator_url.'"><button class="single_add_to_cart_button button">Find a distributor</button></a>';
			};

		}


	
	}
 
}


// Make products unpurchasable for non-distributors
function non_purchasable( $purchasable, $product ){

	global $post;
	$user = wp_get_current_user();
	$user_exception = 'wholesale_customer';

	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	$cat_list = [];

	forEach($terms as $term){
		$cat_name = $term->slug;
		array_push($cat_list, $cat_name);
	}

	// Distrubutor only product AND User is NOT distributor
    if( in_array('distributor-only',$cat_list) && !in_array( $user_exception, (array) $user->roles ) )
        $purchasable = false;
    return $purchasable;
}

add_action('woocommerce_before_single_product','replace_addtocart_btn');
add_filter( 'woocommerce_is_purchasable', 'non_purchasable', 10, 2 );