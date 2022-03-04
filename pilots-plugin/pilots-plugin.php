<?php
/* 
* Plugin name: Site-Pro Bookings 
* Plugin URI:  https://site-pro.co.il
* Description: Site-Pro Bookings
* Version: 1.1.1
* Author: Site-Pro
* Author URI: https://site-pro.co.il
*/

/* register_activation_hook */
global $jal_db_version;
$jal_db_version = "1.0";


/* GOOGLE CALENDAR */
require_once('google-calendar-api.php');

$file_settings = WP_PLUGIN_DIR . "/pilots-plugin/" . 'google_calendar_settings.txt';
$file_token = WP_PLUGIN_DIR . "/pilots-plugin/" . 'google_calendar_token.txt';

$file_token_content = file_get_contents($file_token);
if (!empty($file_token_content)) {
        $refresh_token = $file_token_content;
}

$file_settings_content = file_get_contents($file_settings);
    if (!empty($file_settings_content)) {
        $array_file_settings_content = explode('$$$',$file_settings_content);
        
        $client_id = $array_file_settings_content[0];

        /* Google App Client Secret */
        $client_secret = $array_file_settings_content[1];
        
        /* Google App Redirect Url */
        $client_redirect_url = 'https://pilots.co.il/wp-admin/admin.php?page=settingsgooglecalendar';
        //echo $client_id . " - " . $client_secret . " - " . $client_redirect_url;
    }
/* END GOOGLE CALENDAR */



add_filter( 'woocommerce_billing_fields', 'true_add_custom_billing_field', 25 );
 
function true_add_custom_billing_field( $fields ) {
 
	// массив нового поля
	$new_field = array(
		'billing_contactmethod' => array(
			'type'          => 'select', // text, textarea, select, radio, checkbox, password
			'required'	=> true, // по сути только добавляет значок "*" и всё
			'class'         => array( 'true-field', 'form-row-wide' ), // массив классов поля
			'label'         => 'Предпочитаемый метод связи',
			'label_class'   => 'true-label', // класс лейбла
			'options'	=> array( // options for  or
				''		=> 'Выберите', // пустое значение
				'По телефону'	=> 'По телефону', // 'значение'=>'заголовок'
				'По email'	=> 'По email'
			)
		)
	);
 
	// объединяем поля
	$fields = array_slice( $fields, 0, 2, true ) + $new_field + array_slice( $fields, 2, NULL, true );
 
	return $fields;
 
}


function jal_install () {
    /* CREATE TABLE DB */
   global $wpdb;
   global $jal_db_version;

   $table_name = $wpdb->prefix . "siteprobooking";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  bdate date NOT NULL,
	  btime text NOT NULL,
	  bcount mediumint(9) NOT NULL,
	  borders text,
	  bproduct mediumint(9) NOT NULL,
	  bevents text,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
      
      
      $rows_affected = $wpdb->insert( $table_name, array( 'bdate' => '0', 'btime' => '00:00', 'bcount' => 0, 'borders' => '0', 'bproduct' => 0 ) );
 
      add_option("jal_db_version", $jal_db_version);
   /* END CREATE TABLE DB */
   
   
    
   }
}
register_activation_hook(__FILE__,'jal_install');
/* END register_activation_hook */


include_once 'add-custom-fields.php';


/* ADD CSS AND SCRIPTS */
function add_plugin_scripts() {
  wp_enqueue_style( 'maincss', plugins_url() . '/pilots-plugin/main.css', array(), '1.1', 'all');
  
  wp_enqueue_style( 'fontawesome4', plugins_url() . '/pilots-plugin/font-awesome/css/font-awesome.min.css', array(), '1.1', 'all');
  
  
  wp_enqueue_style( 'select2css', plugins_url() . '/pilots-plugin/select2/css/select2.min.css', array(), '1.1', 'all');
  wp_enqueue_script( 'select2js', plugins_url() . '/pilots-plugin/select2/js/select2.min.js');
  
  
  wp_enqueue_style( 'pq-calendar-css1', plugins_url() . '/pilots-plugin/pq-calendar/css/pignose.calendar.min.css', array(), '1.1', 'all');
  wp_enqueue_style( 'pq-calendar-css2', plugins_url() . '/pilots-plugin/pq-calendar/css/style.css', array(), '1.1', 'all');
  wp_enqueue_style( 'pq-calendar-css3', plugins_url() . '/pilots-plugin/pq-calendar/css/ui.css', array(), '1.1', 'all');
  
  wp_enqueue_script( 'pq-calendar-js1', plugins_url() . '/pilots-plugin/pq-calendar/js/pignose.calendar.full.min.js', array ( 'jquery' ), 1.1, true);
  wp_enqueue_script( 'pq-calendar-js', plugins_url() . '/pilots-plugin/pq-calendar/js/calendar.js', array ( 'jquery' ), 1.1, true);
  
}
add_action( 'wp_enqueue_scripts', 'add_plugin_scripts' );
add_action( 'admin_enqueue_scripts', 'add_plugin_scripts' );
/* END ADD CSS AND SCRIPTS */


/* ARRAY TIMES */
$array_times = array(
	"08:00",
    "08:15",
    "08:30",
    "08:45",
    "09:00",
    "09:15",
    "09:30",
    "09:45",
    "10:00",
    "10:15",
    "10:30",
    "10:45",
    "11:00",
    "11:15",
    "11:30",
    "11:45",
    "12:00",
    "12:15",
    "12:30",
    "12:45",
    "13:00",
    "13:15",
    "13:30",
    "13:45",
    "14:00",
    "14:15",
    "14:30",
    "14:45",
    "15:00",
    "15:15",
    "15:30",
    "15:45",
    "16:00",
    "16:15",
    "16:30",
    "16:45",
    "17:00",
    "17:15",
    "17:30",
    "17:45",
    "18:00",
    "18:15",
    "18:30",
    "18:45",
    "19:00",
    "19:15",
    "19:30",
    "19:45",
    "20:00",
    "20:15",
    "20:30",
    "20:45",
    "21:00",
    "21:15",
    "21:30",
    "21:45",
    "22:00"
);
/* END ARRAY TIMES */


function filter_woocommerce_add_to_cart() { 
    foreach( WC()->cart->get_cart() as $cart_item ){
        // compatibility with WC +3
        if( version_compare( WC_VERSION, '3.0', '<' ) ){
            $product_id = $cart_item['data']->id; // Before version 3.0
        } else {
            $product_id = $cart_item['data']->get_id(); // For version 3 or more
        }
        if ( $product_id == 11 ) 
            WC()->cart->remove_cart_item( WC()->cart->generate_cart_id( $product_id ) );

    }
    
    
     global $woocommerce;

    $product_id = $_POST['add-to-cart'];

    $found = false;

    //check if product already in cart
    if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
        foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
            $_product = $values['data'];
            if ( $_product->id == $product_id )
                $found = true;
                WC()->cart->remove_cart_item( WC()->cart->generate_cart_id( $_product->id ) );
        }
        // if product not found, add it
        //if ( ! $found ) WC()->cart->add_to_cart( $product_id );
    } else {
        // if no products in cart, add it
       // WC()->cart->add_to_cart( $product_id );
    }
}; 
         
//add_filter( 'woocommerce_add_to_cart', 'filter_woocommerce_add_to_cart', 10, 3 ); 




function action_woocommerce_process_shop_order_meta() { 
    echo "action_woocommerce_process_shop_order_meta";
}; 
         
// add the action 
add_action( 'woocommerce_process_shop_order_meta', 'action_woocommerce_process_shop_order_meta', 10, 3 ); 


//add_filter( 'woocommerce_add_cart_item', 'my_add_cart_item', 10, 1 );
function my_add_cart_item($cart_item) {
    $new_price = wc_get_order_item_meta( $cart_item['data']->get_id(), 'new_price', true );
    $cart_item['data']->set_price($new_price);
}


/* ADD NEW ORDER */
//add_action( 'woocommerce_new_order', 'create_invoice_for_wc_order',  1, 1  );
function create_invoice_for_wc_order( $order_id, $order ) {
    $items = $order->get_items();
    console_log(var_dump($items));
}

//add_action('woocommerce_order_status_completed', 'add_new_order', 10, 1);
add_action('woocommerce_checkout_order_processed', 'add_new_order', 10, 1);
//add_action('woocommerce_thankyou', 'add_new_order', 10, 1);
function add_new_order( $order_id ) {
    if ( ! $order_id )
        return;

        $order = wc_get_order( $order_id );
        
        global $wpdb;
        $table_name = $wpdb->prefix . "siteprobooking";
        
        $check_cart_products = array();
        foreach ( $order->get_items() as $item_id => $item ) {
            $product = $item->get_product();
            $product_id = icl_object_id($product->get_id(), 'product', false, 'he');
            if (get_post_meta( $product_id, 'woocommerce_custom_fields' )[0] == "yes"){
                array_push($check_cart_products, $product_id . " - " . $item['extra_product_field2']);
            }
        }
        
        
        if (array_unique($check_cart_products) == $check_cart_products) {
        
        foreach ( $order->get_items() as $item_id => $item ) {
            $product = $item->get_product();
            $product_id = icl_object_id($product->get_id(), 'product', false, 'he');
          if (get_post_meta( $product_id, 'woocommerce_custom_fields' )[0] == "yes"){
            $item_date = explode( ' / ', $item['extra_product_field2'] );
            
            $result_search = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE bdate = '" . $item_date[0] . "' and btime = '" . $item_date[1] . "' and bproduct = '" . $product_id . "'");

            if (empty($result_search)) {
                throw new Exception('Error : Product not found');
            }
            else
            {
                if (getLeftCardsForDate($result_search[0]->borders, $result_search[0]->bdate, $result_search[0]->btime, $product_id, $result_search[0]->bcount) < $item->get_quantity()) 
                    throw new Exception('Error : Count Product (' . getLeftCardsForDate($result_search[0]->borders, $result_search[0]->bdate, $result_search[0]->btime, $product_id, $result_search[0]->bcount) . ' left)');
            }
            
            /*if ($item->get_quantity() > $result_search[0]->bcount) {
                throw new Exception('Error : Count Product (' . $item->get_quantity() . '/ ' . $result_search[0]->bcount . ')');
            }*/
          }
            
        }
        }
        else {
            throw new Exception('Error : Duplicate values'); 
        }
        
        foreach ( $order->get_items() as $item_id => $item ) {

            $product = $item->get_product();
            //$product_id = $product->get_id();
            $product_id = icl_object_id($product->get_id(), 'product', false, 'he');
            $product_name = $item->get_name();
            
            if (get_post_meta( $product->id, 'woocommerce_custom_fields' )[0] == "yes") {
                $item_date = explode( ' / ', $item['extra_product_field2'] );
                
                $order_str = "|" . $order_id;
                //echo $order_str;
                //echo $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET bcount = bcount-1 , borders = CONCAT(borders, %s) WHERE bdate = '" . $item_date[0] . "' and btime = '" . $item_date[1] . "' and bproduct = '" . $product_id . "'", $order_str));
                
				$result_search_borders = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE bdate = '" . $item_date[0] . "' and btime = '" . $item_date[1] . "' and bproduct = '" . $product_id . "'");
				
                $strpos = strpos($result_search_borders[0]->borders, (string)$order_id);
				
                if ($strpos === false) $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET borders = CONCAT(borders, %s) WHERE bdate = '" . $item_date[0] . "' and btime = '" . $item_date[1] . "' and bproduct = '" . $product_id . "'", $order_str));
                
                
                eventGoogleCalendar ($product_id, $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token'], 0, $item_date[0], $item_date[1]);

            }
        }
 

}
/* END ADD NEW ORDER */



add_action( 'woocommerce_order_status_cancelled', 'change_status_to_refund', 10, 1 );

function change_status_to_refund( $order_id ){
	 $order = wc_get_order( $order_id );
    foreach ( $order->get_items() as $item_id => $item ) {
            $product = $item->get_product();
            $product_id = icl_object_id($product->get_id(), 'product', false, 'he');
            if (get_post_meta( $product_id, 'woocommerce_custom_fields' )[0] == "yes"){
                $item_date = explode( ' / ', $item['extra_product_field2'] );
				eventGoogleCalendar ($product_id, $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token'], 0, $item_date[0], $item_date[1]);
            }
    }
}





/* ADD EVENT TO GOOGLE CALENDAR */
function eventGoogleCalendar ($product_id, $client_id, $client_redirect_url, $client_secret, $refresh_token, $all_day, $event_date, $event_time) {
    
    $event_date_time =  $event_date . "T" . $event_time . ":00";
    $date_start = $event_time . ":00";

    
    switch ($product_id) {
        case 21840:
        case 12111:
        case 3767:
        case 1328:
        case 1324:
            $date_end = date('H:i',strtotime('+60 minutes',strtotime($date_start)));
            break;
        case 15107:
            $date_end = date('H:i',strtotime('+120 minutes',strtotime($date_start)));
            break;
        case 11:
        case 3750:
        case 3747:
            $date_end = date('H:i',strtotime('+90 minutes',strtotime($date_start)));
            break;
        case 222:
        case 249:
        case 12117:
        case 11564:
            $date_end = date('H:i',strtotime('+2 hour +30 minutes',strtotime($date_start)));
            break;
        case 259:
            $date_end = date('H:i',strtotime('+3 hour',strtotime($date_start)));
            break;
        default:
            $date_end = date('H:i',strtotime('+90 minutes',strtotime($date_start)));
    }
    
    
    $event_date_time_end =  $event_date . "T" . $date_end . ":00";
    
    
    $array_date_time = array("start_time"=>$event_date_time,"end_time"=>$event_date_time_end,"event_date"=>null);
    
    $capi = new GoogleCalendarApi();
    $token = $capi->GetAccessToken($client_id, $client_redirect_url, $client_secret, $refresh_token);
    $user_timezone = $capi->GetUserCalendarTimezone($token['access_token']);
    
    
    global $wpdb;
    $table_name = $wpdb->prefix . "siteprobooking";
    $result_search = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE bdate = '" . $event_date . "' and btime = '" . $event_time . "' and bproduct = '" . $product_id . "'");
    
    $product = wc_get_product( $product_id );

    
    if (!empty($result_search)) {
        
        $orders_count = 0;
        $description = "";
            $orders_array = explode("|", $result_search[0]->borders);   
            foreach ($orders_array as $order_item) {
                if (!empty($order_item)){
                    $order = wc_get_order( (int)$order_item );
                    if (strcasecmp($order->get_status(),"cancelled") != 0) {
                        $first_name = $order->get_billing_first_name();
                        $last_name  = $order->get_billing_last_name();
                        $order_note  = $order->get_customer_note();
                        $description .= $first_name . " " . $last_name . " <a href='https://pilots.co.il/wp-admin/post.php?post=" . $order_item . "&action=edit' target='_blank'>#" . $order_item . "</a>";
                        if (!empty($order_note)) $description .= " (" . $order_note .")";
                        $description .= "<br>";
                        $items = $order->get_items();
    
                        foreach ( $items as $item_id => $item  ) {
                            $product_id_i = icl_object_id($item->get_product_id(), 'product', false, 'he');
                             $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true ); 
                            if ($product_id_i == $product_id && $custom_field == $event_date . " / " . $event_time) {
                                $product_quantity = $item->get_quantity(); 
                               }
                        }
                        $orders_count += $product_quantity;
                    }
                }
            }
            $description .= $orders_count . " / " . $result_search[0]->bcount;
            
            $title_event = $product->get_title() . " (" . $orders_count . "/" . $result_search[0]->bcount . ") ";
        
        if (empty($result_search[0]->bevents)) {
            //if (!empty($result_search[0]->borders)){
               $event = $capi->CreateCalendarEvent('primary', $title_event, $all_day, $array_date_time, $user_timezone, $token['access_token'], $description); 
               if (!empty($event)) $wpdb->query("UPDATE {$table_name} SET bevents = '" . $event . "' WHERE bdate = '" . $event_date . "' and btime = '" . $event_time . "' and bproduct = '" . $product_id . "'");
               return $event;
            //}
        }
        else {
            //if ($orders_count != 0) {
            $event = $capi->UpdateCalendarEvent($result_search[0]->bevents, 'primary', $title_event, $all_day, $array_date_time, $user_timezone, $token['access_token'], $description);
            return $event;
            //}
            /*else {
   
                    $event = $capi->DeleteCalendarEvent($result_search[0]->bevents, 'primary', $token['access_token']);
                    //if (!empty($event)) 
                    $wpdb->query("UPDATE {$table_name} SET bevents = '' WHERE bdate = '" . $event_date . "' and btime = '" . $event_time . "' and bproduct = '" . $product_id . "'");
                    //return $event;
                
            }*/
        }
    }
    else $event = $capi->DeleteCalendarEvent($result_search[0]->bevents, 'primary', $token['access_token']);
    
}



function eventGoogleCalendarAddNewRow ($product_id, $client_id, $client_redirect_url, $client_secret, $refresh_token, $all_day, $event_date, $event_time, $count, $capi, $token, $user_timezone) {
    
    $event_date_time =  $event_date . "T" . $event_time . ":00";
    $date_start = $event_time . ":00";

    
    switch ($product_id) {
        case 21840:
        case 12111:
        case 3767:
        case 1328:
        case 1324:
            $date_end = date('H:i',strtotime('+60 minutes',strtotime($date_start)));
            break;
        case 15107:
            $date_end = date('H:i',strtotime('+120 minutes',strtotime($date_start)));
            break;
        case 11:
        case 3750:
        case 3747:
            $date_end = date('H:i',strtotime('+90 minutes',strtotime($date_start)));
            break;
        case 222:
        case 249:
        case 12117:
        case 11564:
            $date_end = date('H:i',strtotime('+2 hour +30 minutes',strtotime($date_start)));
            break;
        case 259:
            $date_end = date('H:i',strtotime('+3 hour',strtotime($date_start)));
            break;
        default:
            $date_end = date('H:i',strtotime('+90 minutes',strtotime($date_start)));
    }
    
    
    $event_date_time_end =  $event_date . "T" . $date_end . ":00";
    
    
    $array_date_time = array("start_time"=>$event_date_time,"end_time"=>$event_date_time_end,"event_date"=>null);
    
    //$capi = new GoogleCalendarApi();
    //$token = $capi->GetAccessToken($client_id, $client_redirect_url, $client_secret, $refresh_token);
    //$user_timezone = $capi->GetUserCalendarTimezone($token['access_token']);
    
    
    $product = wc_get_product( $product_id );

    $description = "  0 / " . $count;
    $title_event = $product->get_title() . " (0/" . $count . ") ";
            
    $event = $capi->CreateCalendarEvent('primary', $title_event, $all_day, $array_date_time, $user_timezone, $token['access_token'], $description);
    
    return $event;
    
}

/* END ADD EVENT TO GOOGLE CALENDAR */





function getDatesFromRange($start, $end, $format = 'Y-m-d') {
    $array = array();
    $interval = new DateInterval('P1D');

    $realEnd = new DateTime($end);
    $realEnd->add($interval);

    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

    foreach($period as $date) { 
        $array[] = $date->format($format); 
    }
    return $array;
}




function getLeftCardsForDate($list_orders, $date, $time, $product_id, $count){
    $orders_array = explode("|", $list_orders);
    $count_order_item = 0;
    foreach ($orders_array as $order_item) {
      $order = wc_get_order( (int)$order_item );
        if ($order) {
          if (strcasecmp($order->get_status(),"cancelled") != 0) {
            $items = $order->get_items();
            $product_quantity = 0;
              foreach ( $items as $item_id => $item  ) {
                $product_id_i = $item->get_product_id();
                 $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true ); 
                if ($product_id_i == $product_id && $custom_field == $date . " / " . $time) {
                    $product_quantity = $item->get_quantity(); 
                   }
            }
            
            $count_order_item += $product_quantity;
        }
      }
    }
    return $count - $count_order_item;
}


/****** PLUGIN ADMIN MENU PAGE ******/

/* ADD ROWS TO TABLE */
include_once 'main-admin-page.php';

/* VIEW TABLE BY PRODUCTS */
include_once 'sub-admin-menu-view.php';

/* EDIT TABLE BY PRODUCTS */
include_once 'sub-admin-menu-edit.php';

/* Google Calendar Settings */
include_once 'google_calendar_settings.php';


add_action( 'admin_menu', 'siteprobooking_options_page' );
function siteprobooking_options_page() {
    add_menu_page(
        'Site-Pro Booking',
        'Site-Pro Booking',
        'manage_options',
        'siteprobooking',
        'siteprobooking_options_page_html',
        'dashicons-calendar-alt',
        20
    );
     add_submenu_page(
        'siteprobooking',
        'View',
        'View',
        'manage_options',
        'viewsiteprobooking',
        'sub_view_siteprobooking_options_page'
    );
     add_submenu_page(
        'siteprobooking',
        'Edit',
        'Edit',
        'manage_options',
        'editsiteprobooking',
        'sub_edit_siteprobooking_options_page'
    );
    /*add_submenu_page(
        'siteprobooking',
        'Google Calendar Settings',
        'Google Calendar Settings',
        'manage_options',
        'settingsgooglecalendar',
        'google_calendar_settings_page'
    );*/
}
/* END PLUGIN ADMIN MENU PAGE */