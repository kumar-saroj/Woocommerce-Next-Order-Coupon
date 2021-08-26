<?php

add_action( 'woocommerce_order_status_completed','create_coupon', 10, 1 );

function create_coupon ($order_id) {

    $order = wc_get_order( $order_id );
    $customer_id = (int)$order->get_user_id();
    $customer_info = get_userdata($customer_id);

    if ($customer_id):

        $coupon_code = 'WELCOME'.$customer_id.$order->id;
        $amount = 200; // Amount
        $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product

        $coupon = array(
            'post_title' => $coupon_code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type'		=> 'shop_coupon'
        );

        $new_coupon_id = wp_insert_post( $coupon );

        // Add meta
        update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
        update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
        update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
        update_post_meta( $new_coupon_id, 'product_ids', '' );
        update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
        update_post_meta( $new_coupon_id, 'usage_limit', '1' );
        update_post_meta( $new_coupon_id, 'expiry_date', '' );
        update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
        update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
        update_post_meta( $new_coupon_id, 'customer_email', $customer_info->user_email);

    endif;

}

add_action( 'woocommerce_email_before_order_table', 'add_order_email_instructions', 10, 2 );

function add_order_email_instructions( $order, $sent_to_admin ) {
    $order_id = wc_get_order( $order );
    $coupon_amount = 200;
    $coupon_code = 'WELCOME'.$order->get_user_id().$order_id->id;

    if ( ! $sent_to_admin && $order->get_user_id() && 'processing' == $order->get_status()) {

     echo '<h2>Get Flat '.$coupon_amount.' off</h2><p id="noc_thanks">Thanks for your purchase! Come back and use the code "<strong>'.$coupon_code.'</strong>" to receive flat '.$coupon_amount.' off on your next purchase!</p>'; 

    }
}
