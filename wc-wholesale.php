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
// function custom_woocommerce_dropdown_variation_attribute_options_html( $html, $args )
// {
//     $product = $args[ 'product' ];
//     $attribute = $args[ 'attribute' ];
//     $terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
//     $options = $args[ 'options' ];
//     if ( empty( $options ) && !empty( $product ) && !empty( $attribute ) ) {
//         $attributes = $product->get_variation_attributes();
//         $options = $attributes[ $attribute ];
//     }

//     foreach ( $terms as $term ) {
//         if ( in_array( $term->slug, $options ) && ***SOME CONDITION***) {
//             $html = str_replace( '<option value="' . esc_attr( $term->slug ) . '" ', '<option hidden value="' . esc_attr( $term->slug ) . '" ', $html );
//         }
//     }
//     return $html;
// }
// add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'custom_woocommerce_dropdown_variation_attribute_options_html', 10, 2 );


// display on specific product page
// add_action( 'woocommerce_before_main_content', 'display_on_product_page' );
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'filter_dropdown_variation_args', 10, 1 );

function filter_dropdown_variation_args( $args ) {

    $user = wp_get_current_user();
    $options = $args['options'];
    $hidden = ['2-Pack','3-Pack'];
    
    if(! in_array( 'administrator', (array) $user->roles )){
        echo 'user';
        print_r($args['options']);
        // Dont show "Choose an option"
        $args['show_option_none'] = false;

        // Remove the option values
        foreach( $args['options'] as $key => $option ){
            // if( $option === "2-Pack" ) 
            // if(in_array($option, $hidden))
            if(strpos($option, 'Pack') !== false || strpos($option, 'pack') !== false){
                unset($args['options'][$key]);
            }
        }
        return $args;
    }else{
        echo'admin';
        // $args['options'] = $options;
        // print_r($args['options']);
        return $args;
    }
}


function display_on_product_page(){
    // global $woocommerce, $product, $post;
    $user = wp_get_current_user();

    $product_id = 38;
    $product = wc_get_product($product_id);
    $variations = $product->get_available_variations();
   

    if(is_single('38') && in_array( 'administrator', (array) $user->roles )){
        echo 'Something......';
    }
}