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
    print_r($user->roles);
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


