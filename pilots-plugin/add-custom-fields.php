<?php

/* ADD CUSTOM FIELD TO PRODUCT PAGE */ 
 // Display custom field on single product page
    function d_extra_product_field(){
        global $product;
        //echo $product->id;
        if ($product->id == 23664) {
            $value = isset( $_POST['extra_product_field'] ) ? sanitize_text_field( $_POST['extra_product_field'] ) : '';
            printf( '<label></label><input name="extra_product_field" placeholder="%s" value="%s" />', __( 'שם מלא בעברית\אנגלית' ), esc_attr( $value ) );
        }
        if (get_post_meta( $product->id, 'woocommerce_custom_fields' )[0] == "yes") {
            $value = isset( $_POST['extra_product_field2'] ) ? sanitize_text_field( $_POST['extra_product_field2'] ) : '';
            printf( '<label></label><input type="hidden" name="extra_product_field2" placeholder="%s" value="%s" required /><p class="text-result"></p>', __( '' ), esc_attr( $value ) );
            
           /* if ( current_user_can('administrator') ) { 
                $value_new_price = isset( $_POST['new_price'] ) ? sanitize_text_field( $_POST['new_price'] ) : $product->get_price();
                printf( '<div class="new-admin-price"><label>מחיר חדש: </label><input type="number" name="new_price" placeholder="%s" value="%s" /></div>', __( '' ), esc_attr( $value_new_price ) );
            }*/
        }
        if (current_user_can('administrator')) {
           $value_new_price = isset( $_POST['new_price'] ) ? sanitize_text_field( $_POST['new_price'] ) : $product->get_price();
                printf( '<div class="new-admin-price"><label>מחיר חדש: </label><input type="number" name="new_price" placeholder="%s" value="%s" /></div>', __( '' ), esc_attr( $value_new_price ) ); 
        }
    }
    add_action( 'woocommerce_before_add_to_cart_button', 'd_extra_product_field', 9 );

    // validate when add to cart
    function d_extra_field_validation($passed, $product_id, $qty){

        if( isset( $_POST['extra_product_field'] ) && sanitize_text_field( $_POST['extra_product_field'] ) == '' ){
            $product = wc_get_product( $product_id );
            wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some text.' ), $product->get_title() ), 'error' );
            return false;
        }
        
        if( isset( $_POST['extra_product_field2'] ) && sanitize_text_field( $_POST['extra_product_field2'] ) == '' ){
            $product = wc_get_product( $product_id );
            wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some text.' ), $product->get_title() ), 'error' );
            return false;
        }
        

        return $passed;

    }
    add_filter( 'woocommerce_add_to_cart_validation', 'd_extra_field_validation', 10, 3 );

     // add custom field data in to cart
    function d_add_cart_item_data( $cart_item, $product_id ){

        if( isset( $_POST['extra_product_field'] ) ) {
            $cart_item['extra_product_field'] = sanitize_text_field( $_POST['extra_product_field'] );
        }
        if( isset( $_POST['extra_product_field2'] ) ) {
            $cart_item['extra_product_field2'] = sanitize_text_field( $_POST['extra_product_field2'] );
        }
        if( isset( $_POST['new_price'] )) {
            $cart_item['new_price'] = sanitize_text_field( $_POST['new_price'] );
        }

        return $cart_item;

    }
    add_filter( 'woocommerce_add_cart_item_data', 'd_add_cart_item_data', 10, 2 );

    // load data from session
    function d_get_cart_data_f_session( $cart_item, $values ) {

        if ( isset( $values['extra_product_field'] ) ){
            $cart_item['extra_product_field'] = $values['extra_product_field'];
        }
        if ( isset( $values['extra_product_field2'] ) ){
            $cart_item['extra_product_field2'] = $values['extra_product_field2'];
        }
        if ( isset( $values['new_price'] ) ){
            $cart_item['new_price'] = $values['new_price'];
            $cart_item['data']->set_price($cart_item['new_price']);
        }
        
        return $cart_item;

    }
    add_filter( 'woocommerce_get_cart_item_from_session', 'd_get_cart_data_f_session', 20, 2 );


    //add meta to order
    function d_add_order_meta( $item_id, $values ) {

        if ( ! empty( $values['extra_product_field'] ) ) {
            woocommerce_add_order_item_meta( $item_id, 'extra_product_field', $values['extra_product_field'] );           
        }
        if ( ! empty( $values['extra_product_field2'] ) ) {
            woocommerce_add_order_item_meta( $item_id, 'extra_product_field2', $values['extra_product_field2'] );           
        }
        /*if ( ! empty( $values['new_price'] ) ) {
            woocommerce_add_order_item_meta( $item_id, 'new_price', $values['new_price'] );           
        }*/
    }
    add_action( 'woocommerce_add_order_item_meta', 'd_add_order_meta', 10, 2 );

    // display data in cart
    function d_get_itemdata( $other_data, $cart_item ) {

        if ( isset( $cart_item['extra_product_field'] ) ){

            $other_data[] = array(
                'name' => __( 'שם מלא בעברית\אנגלית' ),
                'value' => sanitize_text_field( $cart_item['extra_product_field'] )
            );

        }
        if ( isset( $cart_item['extra_product_field2'] ) ){

            $other_data[] = array(
                'name' => __( 'תאריך / שעה' ),
                'value' => sanitize_text_field( $cart_item['extra_product_field2'] )
            );

        }
        /*if ( isset( $cart_item['new_price'] ) ){

            $other_data[] = array(
                'name' => __( 'מחיר חדש' ),
                'value' => sanitize_text_field( $cart_item['new_price'] )
            );

        }*/

        return $other_data;

    }
    add_filter( 'woocommerce_get_item_data', 'd_get_itemdata', 10, 2 );


    // display custom field data in order view
    function d_dis_metadata_order( $cart_item, $order_item ){

        if( isset( $order_item['extra_product_field'] ) ){
            $cart_item_meta['extra_product_field'] = $order_item['extra_product_field'];
        }
        if( isset( $order_item['extra_product_field2'] ) ){
            $cart_item_meta['extra_product_field2'] = $order_item['extra_product_field2'];
        }
        
        /*if( isset( $order_item['new_price'] ) ){
            $cart_item_meta['new_price'] = $order_item['new_price'];
        }*/

        return $cart_item;

    }
    add_filter( 'woocommerce_order_item_product', 'd_dis_metadata_order', 10, 2 );


    // add field data in email
    function d_order_email_data( $fields ) { 
        $fields['extra_product_field'] = __( 'שם מלא בעברית\אנגלית' ); 
        $fields['extra_product_field2'] = __( 'תאריך / שעה' ); 
        //$fields['new_price'] = __( 'מחיר חדש' );
        $fields['new_price'] = array(
            'label' => __( ' ' ),
            'value' => "",
        );
        return $fields; 
    } 
    add_filter('woocommerce_email_order_meta_fields', 'd_order_email_data');

    // again order
    function d_order_again_meta_data( $cart_item, $order_item, $order ){

        if( isset( $order_item['extra_product_field'] ) ){
            $cart_item_meta['extra_product_field'] = $order_item['extra_product_field'];
        }
        if( isset( $order_item['extra_product_field2'] ) ){
            $cart_item_meta['extra_product_field2'] = $order_item['extra_product_field2'];
        }
        /*if( isset( $order_item['new_price'] ) ){
            $cart_item_meta['new_price'] = $order_item['new_price'];
        }*/

        return $cart_item;

    }
    add_filter( 'woocommerce_order_again_cart_item_data', 'd_order_again_meta_data', 10, 3 );
/* END ADD CUSTOM FIELD TO PRODUCT PAGE */   
   


/* ADD CUSTOM FIELD TO ADMIN PRODUCT PAGE */
function woocommerce_product_custom_fields()
{
  $args = array(
      'id' => 'woocommerce_custom_fields',
      'label' => __('Site-Pro Booking', 'cwoa'),
  );
  woocommerce_wp_checkbox($args);
}
 
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');

function save_woocommerce_product_custom_fields($post_id)
{
    $product = wc_get_product($post_id);
    $custom_fields_woocommerce_title = isset($_POST['woocommerce_custom_fields']) ? $_POST['woocommerce_custom_fields'] : '';
    $product->update_meta_data('woocommerce_custom_fields', sanitize_text_field($custom_fields_woocommerce_title));
    $product->save();
}
add_action('woocommerce_process_product_meta', 'save_woocommerce_product_custom_fields');



/* END ADD CUSTOM FIELD TO ADMIN PRODUCT PAGE */