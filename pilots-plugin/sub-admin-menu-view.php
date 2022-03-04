<?php
function sub_view_siteprobooking_options_page() {
    //phpinfo();
    //if (strpos($_GET['page'], "viewsiteprobooking") !== false) echo strpos($_GET['page'], "viewsiteprobooking");
    global $wpdb;
    $table_name = $wpdb->prefix . "siteprobooking";
    ?>
<!-- TABLE RESULT -->
<script>
jQuery(document).ready(function($) {
  $("#myInput").on("click", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
  
  
  $(".edit").on("click", function() {
      var idrow = $(this).attr("data-id");
    $("#edit-id").val(idrow);
    $( ".bcount").prop( "disabled", true );
    $( ".btime").prop( "disabled", true );
    $( "#bcount" + idrow).prop( "disabled", false );
    $( "#btime" + idrow).prop( "disabled", false );

    $( ".edit").show();
    $( ".edit-sitepro-booking" ).hide();
    $( "#edit" + idrow).hide();
    $( "#edit-sitepro-booking" + idrow).show();
  });
  
  $(".edit-order").on("click", function() {
      var orderid = $(this).attr("data-id-order");
      var orderrow = $(this).attr("data-id-row");
    $("#edit-order-id").val(orderid);
    $("#edit-order-row").val(orderrow);
    
    $( ".edit-order-popup").hide();
    $( "#edit-order-popup" + orderid + "-" + orderrow).css('display', 'flex');
  });
  

  $("#inputSearchOrder").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });

  
});
</script>
<?php

$curr_lang = ICL_LANGUAGE_CODE;
 
if ($curr_lang != 'he' && is_product()) {
    $product_id = icl_object_id(get_the_ID(), 'product', false, 'he');
}
else {
    if (isset($_GET['product'])) $product_id = $_GET['product'];
        elseif (is_product()) {
            global $post;
            $product_id = $post->ID;
        }
        else $product_id = 0;
}


global $wpdb;
$table_name = $wpdb->prefix . "siteprobooking";
       
       
/* EDIT ROW TO TABLE (TIME AND COUNT) */
if (isset($_POST['edit-id'])) {
    if (isset($_POST['edit-sitepro-booking'])){
        //echo $_POST['bdate'.$_POST['edit-id']] . " - " .  $_POST['btimeold'.$_POST['edit-id']] . "<br>";
       if ($_POST['btime'.$_POST['edit-id']] != $_POST['btimeold'.$_POST['edit-id']]) {
           $result_search = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE bdate = '" . $_POST['bdate'.$_POST['edit-id']] . "' and btime = '" . $_POST['btime'.$_POST['edit-id']] . "' and bproduct = '" . $product_id . "'");
           if ($result_search == false) {
                $wpdb->query("UPDATE {$table_name} SET bcount = {$_POST['bcount'.$_POST['edit-id']]}, btime = '{$_POST['btime'.$_POST['edit-id']]}'  WHERE id = ".$_POST['edit-id']." and bdate = '" . $_POST['bdate'.$_POST['edit-id']] . "' and btime = '" . $_POST['btimeold'.$_POST['edit-id']] . "' and bproduct = '" . $product_id . "'");
               
                $orders_array = explode("|", $_POST['borders'.$_POST['edit-id']]);   
                foreach ($orders_array as $order_item) {
                    if (!empty($order_item)){
                        $order = wc_get_order( (int)$order_item );
                        
                        $order_items = $order->get_items();
                        
                        
                        foreach( $order_items as $item_id => $item ) {
                            $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true );
                             if ($item->get_product_id() == $product_id && $custom_field == $_POST['bdate'.$_POST['edit-id']] . " / " . $_POST['btimeold'.$_POST['edit-id']]) {
                                wc_update_order_item_meta( $item_id, 'extra_product_field2', $_POST['bdate'.$_POST['edit-id']] . " / " . $_POST['btime'.$_POST['edit-id']]);
                            }
                        }
                    }
                }
           }
           else echo "<p class='error-message'>Time exists</p>";
       }
       else {
           $wpdb->query("UPDATE {$table_name} SET bcount = {$_POST['bcount'.$_POST['edit-id']]}  WHERE id = ".$_POST['edit-id']." and bdate = '" . $_POST['bdate'.$_POST['edit-id']] . "' and btime = '" . $_POST['btimeold'.$_POST['edit-id']] . "' and bproduct = '" . $product_id . "'");
       }
       
       
       eventGoogleCalendar ($product_id, $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token'], 0, $_POST['bdate'.$_POST['edit-id']], $_POST['btime'.$_POST['edit-id']]);


    }
}
/* END EDIT ROW TO TABLE (TIME AND COUNT) */


/* EDIT ORDER */
if (isset($_POST['edit-order-id'])) {
    if (isset($_POST['button-edit-order'])){
        
        $result_old_date_time = $wpdb->get_results("SELECT * FROM {$table_name}  WHERE bdate = '" . $_POST['bdateold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and btime = '" . $_POST['btimeold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and bproduct = '" . $product_id . "'");
       
         if (!empty($result_old_date_time)) {
        
        $result_check_order = $wpdb->get_results("SELECT * FROM {$table_name}  WHERE bdate = '" . $_POST['bdate'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and btime = '" . $_POST['btime'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and bproduct = '" . $product_id . "'");
        if (!empty($result_check_order)) {
            //var_dump($result_check_order);
        if (strpos($result_check_order[0]->borders, $_POST['edit-order-id']) === false)  {

        $order_str = "|" . $_POST['edit-order-id'];
        
        $result_replace_date_time = $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET borders = CONCAT(borders, %s) WHERE bdate = '" . $_POST['bdate'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and btime = '" . $_POST['btime'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and bproduct = '" . $product_id . "'", $order_str));
        
        if ($result_replace_date_time == 1) {
            
            $wpdb->query($wpdb->prepare("UPDATE {$table_name} SET borders = REPLACE(borders, %s, '') WHERE bdate = '" . $_POST['bdateold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and btime = '" . $_POST['btimeold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . "' and bproduct = '" . $product_id . "'", $order_str));
            
            $order = wc_get_order( $_POST['edit-order-id'] );
            $order_items = $order->get_items();
            $count = 0;
            foreach( $order_items as $item_id => $item ) {
                 if ($item->get_product_id() == $product_id){ 
                    $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true );
                    if ($custom_field == $_POST['bdateold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . " / " . $_POST['btimeold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']]) {

                        wc_update_order_item_meta( $item_id, 'extra_product_field2', $_POST['bdate'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']] . " / " . $_POST['btime'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']]);
                        
                        $count += $item->get_quantity();

                    }
                }
            }
            
            
            eventGoogleCalendar ($product_id, $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token'], 0, $_POST['bdateold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']], $_POST['btimeold'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']]);
            eventGoogleCalendar ($product_id, $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token'], 0, $_POST['bdate'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']], $_POST['btime'.$_POST['edit-order-id'].'-'.$_POST['edit-order-row']]);
            
            $count_left = getLeftCardsForDate($result_check_order[0]->borders, $result_check_order[0]->bdate, $result_check_order[0]->btime, $product_id, $result_check_order[0]->bcount) - $count;
            
            if ($count_left < 0) 
                echo "<p class='error-message'>NOTICE: Count Product ( ". $count_left ." left )</p>";
        
            
        }
        else echo "<p class='error-message'>Error date or time</p>";
     }
    
     else echo "<p class='error-message'>Error: Order exists</p>";
    }
     else echo "<p class='error-message'>Error date or time</p>";       
     
    
    }
    
    }
}
/* END EDIT ORDER */


if (isset($_GET['product']) || isset($_GET['page']) || get_post_meta( $product_id, 'woocommerce_custom_fields' )[0] == "yes") {
    
    if (isset($_GET['product']) || isset($_GET['page'])){
?>

    
<section class="list-tab-products">
<?php 
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'meta_key' => 'woocommerce_custom_fields',
	        'meta_value' => 'yes'
        );
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post();
            global $product;
            if (isset($_GET['product']) && $_GET['product'] == get_the_ID()) $class_active="class='active'";
                else $class_active="";
            echo "<a href='/wp-admin/admin.php?page=viewsiteprobooking&product=" . get_the_ID() . "' " . $class_active . ">" . get_the_title() . "</a>";
           endwhile;
        wp_reset_query();
    
        
?>
</section>
<?php 
} 


?>
<section class="result-block">
<?php
    if (isset($_GET['product']) || get_post_meta( $product_id, 'woocommerce_custom_fields' )[0] == "yes") {
?>      
<div class="result-table">
<input id="myInput" type="text" placeholder="Search..">


<?php if ( current_user_can('administrator') ) {  ?>
<input type="text" id="inputSearchOrder" onkeyup="searchOrder()" placeholder="חיפוש">
<?php } ?>

<form action="" method="post">    

<?php 
$result_table = $wpdb->get_results( "SELECT *  FROM {$table_name} WHERE bproduct = " . $product_id . " and bdate >= '" . date("Y-m-d") . "'  ORDER BY bdate, btime");
$arr = "[";
foreach ($result_table as $qproduct) {
    
    $orders_array = explode("|", $qproduct->borders);
    $orders_str = "";
    $count_order_item = 0;
    foreach ($orders_array as $order_item) {
        $order = wc_get_order( (int)$order_item );
        $order_data = "";
        if ($order) {
          if (strcasecmp($order->get_status(),"cancelled") != 0) {
           $items = $order->get_items();
            foreach ( $items as $item_id => $item  ) {
                $product_id_i = $item->get_product_id();
                 $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true ); 
                if ($product_id_i == $product_id && $custom_field == $qproduct->bdate . " / " . $qproduct->btime) {
                    $product_quantity = $item->get_quantity(); 
                   }
            }
            
            $count_order_item += $product_quantity;
        }
      }
    }
    
    $count = $qproduct->bcount - $count_order_item;
    if ($curr_lang == 'he')  {
        if ($count == 1) $count_text = "נותר";
            else $count_text = "נותרו";
    }
        else $count_text = "left";
    if ($count > 0) {
        $arr .= "{name: 'offer', date: '" . $qproduct->bdate . "'}, ";
    }
  
}
$arr .= "]";

if ( current_user_can('administrator') ) { 
?>

<style>
.result-table {
    min-width: 850px;
}
.single-product .table-result {
    min-width: 850px;
}
.table-result input[type=date], .table-result input[type=email], .table-result input[type=number], .table-result input[type=password], .table-result input[type=search], .table-result input[type=tel], .table-result input[type=text], .table-result input[type=url], .table-result select, .table-result textarea {
    font-size: 14px;
}
.single-product .table-result tr.active, .single-product .table-result tr.active:hover {
    background: rgba(22,75,128, 0.5) !important;
}

@media only screen and (max-width: 600px) {
  .single-product .result-table {
      width: 100%;
      overflow-x: scroll;
  }

}
</style>

<script>
     jQuery(document).ready(function($) {
     
        function onSelectHandler(date, context) {
            /**
             * @date is an array which be included dates(clicked date at first index)
             * @context is an object which stored calendar interal data.
             * @context.calendar is a root element reference.
             * @context.calendar is a calendar element reference.
             * @context.storage.activeDates is all toggled data, If you use toggle type calendar.
             * @context.storage.events is all events associated to this date
             */

            var $element = context.element;
            var $calendar = context.calendar;
            //var $box = $element.siblings('.box').show();
            var text = '';

            if (date[0] !== null) {
                text += date[0].format('YYYY-MM-DD');
            }

            if (date[0] !== null && date[1] !== null) {
                text += ' ~ ';
            }
            else if (date[0] === null && date[1] == null) {
                var urlP = window.location.pathname;
                //if (urlP.search( 'wp-admin' ) != -1) 
                    text += '';
                  //else text += '432#@$';
            }

            if (date[1] !== null) {
                text += date[1].format('YYYY-MM-DD');
            }

            //$box.text(text);
            
            //$(".single-product .table-result").show;
            

            $('#myInput').val(text);
             $('#myInput').trigger('click');
             
            
        }
         var arrayFromPHP = <?php echo $arr; ?>;
        // Default Calendar
        $('html[lang="he-IL"] .calendar').pignoseCalendar({
            scheduleOptions: {
        		colors: {
        		    offer: '#ccc',
        			ad: '#5c6270'
        		}
        	},
        	schedules: arrayFromPHP,
            select: onSelectHandler,
            lang: 'he'
        });
        $('html[lang="en-US"] .calendar').pignoseCalendar({
            scheduleOptions: {
        		colors: {
        		    offer: '#2fabb7',
        			ad: '#5c6270'
        		}
        	},
        	schedules: arrayFromPHP,
            select: onSelectHandler,
            lang: 'en'
        });
        
     });
</script>

    <input type='hidden' name='edit-id' id='edit-id' value=''>
    
    <input type='hidden' name='edit-order-id' id='edit-order-id' value=''>
    <input type='hidden' name='edit-order-row' id='edit-order-row' value=''>



<table class="table-result">
  <thead>
  <tr>
    <th>תאריך</th>
    <th>שעה</th>
    <th>כמות כרטיסים</th>
    <th>כרטיסים הוזמנו</th>
    <th>כרטיסים נותרו</th>
    <th>הזמנות</th>
    <th>עריכה</th>
  </tr>
  </thead>
  <tbody id="myTable">
<?php
foreach ($result_table as $qproduct) {
    $orders_array = explode("|", $qproduct->borders);
    $orders_str = "";
    $count_order_item = 0;
    //array_unique($orders_array)
    foreach ($orders_array as $order_item) {
      $order = wc_get_order( (int)$order_item );

        $order_data = "";
        if ($order) {
          if (strcasecmp($order->get_status(),"cancelled") != 0) {
            $first_name = $order->get_billing_first_name();
            $last_name  = $order->get_billing_last_name();
            $order_data = $first_name . " " . $last_name . " #" . $order_item;
            
            
            $items = $order->get_items();
            $product_quantity = 0;
              foreach ( $items as $item_id => $item  ) {
                $product_id_i = $item->get_product_id();
                 $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true ); 
                if ($product_id_i == $product_id && $custom_field == $qproduct->bdate . " / " . $qproduct->btime) {
                    $product_quantity = $item->get_quantity(); 
                   }
            }
            
            $count_order_item += $product_quantity;
            
            $orders_str .= "
                <p>
                    <a href='/wp-admin/post.php?post=" . $order_item . "&action=edit' target='_blank'>" . $order_data . "</a>
                    - ".$product_quantity."
                <span class='edit-order' id='editorder" . $order_item . "' data-id-order='" . $order_item . "' data-id-row='" . $qproduct->id . "'><i class='fa fa-pencil-square' aria-hidden='true'></i></span></p>
                <div class='edit-order-popup' id='edit-order-popup".$order_item . "-" . $qproduct->id."'>
                    <select name='btime" . $order_item . "-" . $qproduct->id . "' >";
                    foreach ($GLOBALS['array_times'] as $time_item){
                        if (strcasecmp($time_item,$qproduct->btime)==0) {$selected = " selected";}
                            else $selected = "";
                        $orders_str .= '<option value="' . $time_item . '" ' . $selected . '>' . $time_item .'</option>';
                    }
                    $orders_str .= "</select>  
                    <input type='hidden' name='bidold" . $order_item . "-" . $qproduct->id . "' value='" . $qproduct->id . "'>
                    <input type='hidden' name='btimeold" . $order_item . "-" . $qproduct->id . "' value='" . $qproduct->btime . "'> 
                    <input type='date' name='bdate" . $order_item . "-" . $qproduct->id . "' value='" . $qproduct->bdate . "'> 
                    <input type='hidden' name='bdateold" . $order_item . "-" . $qproduct->id . "' value='" . $qproduct->bdate . "'> 
                    <input type='hidden' name='bid" . $order_item . "-" . $qproduct->id . "' value='" . $qproduct->id . "'>
                    <input type='hidden' name='bidorder" . $order_item . "-" . $qproduct->id . "' value='" . $order_item . "'>
                    <input type='hidden' name='bcount" . $order_item . "-" . $qproduct->id . "' value='" . $product_quantity . "'>
                    
                    <input type='submit' name='button-edit-order' id='button-edit-order' class='button-edit-order' value='עדכן'>
                </div>
            ";
        }
      }
    }
    $cards_left = $qproduct->bcount - $count_order_item;
    if ($cards_left < 0) $cards_left_minus = "cards_left_minus";
        else $cards_left_minus = "";
    echo "
    <tr id='tr" . $qproduct->id . "'>
        <td>
            <input type='hidden' name='bdate" . $qproduct->id . "' value='" . $qproduct->bdate . "'>
            <input type='hidden' name='btimeold" . $qproduct->id . "' value='" . $qproduct->btime . "'> 
            <p>" . $qproduct->bdate . "</p>
        </td>
        <td style='display: none;'><p>" . $qproduct->btime . "</p></td>
        <td>
            <input type='hidden' name='btimeold" . $qproduct->id . "' value='" . $qproduct->btime . "'>
            <select disabled name='btime" . $qproduct->id . "' id='btime" . $qproduct->id . "' class='btime' >";
            foreach ($GLOBALS['array_times'] as $time_item){
                if (strcasecmp($time_item,$qproduct->btime)==0) {$selected = " selected";}
                    else $selected = "";
                echo '<option value="' . $time_item . '" ' . $selected . '>' . $time_item .'</option>';
            }
            echo "</select>
        </td>
        <td>
            <input disabled style='width: 67px;' type='number' name='bcount" . $qproduct->id . "' id='bcount" . $qproduct->id . "' value='" . $qproduct->bcount . "' class='bcount' >
        </td>
        <td>" . $count_order_item . "</td><td class='ticket-left " . $cards_left_minus . "'>" . $cards_left . "</td><td><input type='hidden' name='borders" . $qproduct->id . "' value='" . $qproduct->borders . "'>" . $orders_str . "</td>
        
        <td>
            <p class='edit' id='edit" . $qproduct->id . "' data-id='" . $qproduct->id . "'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></p>
            <p><input name='edit-sitepro-booking' id='edit-sitepro-booking" . $qproduct->id . "'  class='edit-sitepro-booking' type='submit' value='עדכן'></p>
        </td>
    </tr>";
}
?>
  </tbody>
</table>
<?php 


} 
else {

?>
<style>
.single-product .result-table {
    max-height: 2000px;
    overflow-y: unset;
}
#myTable {
    display: flex;
    flex-wrap: wrap;
}
.single-product .table-result tr td:first-child {
    display: none;
}
.single-product .table-result tr td {
    cursor: pointer;
}
.single-product .table-result tr.active td {
    background: #164b80;
    color: #fff;
}
.table-result tr:nth-child(even) {
    background-color: #fff;
}
.table-result td p:nth-child(2) {
    font-size: 12px;
}

.text-result {
    order: 3;
}

</style>

<table class="table-result">
  <tbody id="myTable">
<?php
foreach ($result_table as $qproduct) {
    
    $orders_array = explode("|", $qproduct->borders);
    $orders_str = "";
    $count_order_item = 0;
    foreach ($orders_array as $order_item) {
        $order = wc_get_order( (int)$order_item );
        $order_data = "";
        if ($order) {
          if (strcasecmp($order->get_status(),"cancelled") != 0) {
           $items = $order->get_items();

            /*foreach ( $items as $item ) {
                $product_quantity = 0;
                $product_id = $item->get_product_id();
                if ($product_id == $product_id) {
                    $product_quantity = $item->get_quantity(); 
                }
                echo $item->get_quantity() . "<br>";
            }*/
            foreach ( $items as $item_id => $item  ) {
                $product_id_i = $item->get_product_id();
                 $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true ); 
                if ($product_id_i == $product_id && $custom_field == $qproduct->bdate . " / " . $qproduct->btime) {
                    $product_quantity = $item->get_quantity(); 
                   }
            }
            
            $count_order_item += $product_quantity;
        }
      }
    }
    
    $count = $qproduct->bcount - $count_order_item;
    if ($curr_lang == 'he') {
        if ($count == 1) $count_text = "נותר";
            else $count_text = "נותרו";
    }
        else $count_text = "left";
    if ($count > 0) {
        echo "<tr style='display: none;' id='tr" . $qproduct->id . "'><td><input type='hidden' name='bid" . $qproduct->id . "' value='" . $qproduct->id . "'><p>" . $qproduct->bdate . "</p></td><td><p>" . $qproduct->btime . "</p><p class='ticket-left'>" . $count . " " . $count_text . "</p></td></tr>";
       
    }
  
}

//var_dump($arr);
?>
  </tbody>
</table>

<script>
     jQuery(document).ready(function($) {
     
        function onSelectHandler(date, context) {
            /**
             * @date is an array which be included dates(clicked date at first index)
             * @context is an object which stored calendar interal data.
             * @context.calendar is a root element reference.
             * @context.calendar is a calendar element reference.
             * @context.storage.activeDates is all toggled data, If you use toggle type calendar.
             * @context.storage.events is all events associated to this date
             */

            var $element = context.element;
            var $calendar = context.calendar;
            //var $box = $element.siblings('.box').show();
            var text = '';

            if (date[0] !== null) {
                text += date[0].format('YYYY-MM-DD');
            }

            if (date[0] !== null && date[1] !== null) {
                text += ' ~ ';
            }
            else if (date[0] === null && date[1] == null) {
                 text += '432#@$';
            }

            if (date[1] !== null) {
                text += date[1].format('YYYY-MM-DD');
            }

            //$box.text(text);
            
            //$(".single-product .table-result").show;
            

            $('#myInput').val(text);
             $('#myInput').trigger('click');
             
            
        }
 

       var arrayFromPHP = <?php echo $arr; ?>;
        //console.log(arrayFromPHP);
            // Default Calendar
        $('html[lang="he-IL"] .calendar').pignoseCalendar({ 
            scheduleOptions: {
        		colors: {
        		    offer: '#2fabb7',
        			ad: '#5c6270'
        		}
        	},
        	schedules: arrayFromPHP,
            select: onSelectHandler,
            lang: 'he'
        });
        $('html[lang="en-US"] .calendar').pignoseCalendar({
            scheduleOptions: {
        		colors: {
        		    offer: '#2fabb7',
        			ad: '#5c6270'
        		}
        	},
        	schedules: arrayFromPHP,
            select: onSelectHandler,
            lang: 'en'
        });
        
     });
</script>

<script>
jQuery(document).ready(function($) {
    var currentdate = new Date(); 
    var currentday = "";
    if (currentdate.getDate() < 10) currentday = "0" + currentdate.getDate();
        else currentday = currentdate.getDate();
    var currentdateText = currentdate.getFullYear() + "-" + (currentdate.getMonth()+1) + "-" + currentday;
    $('#myInput').val(currentdateText);
    $('#myInput').trigger('click');

});
</script>
<?php
}
?>
<script>
jQuery(document).ready(function($) {
    $("#myTable tr").on("click", function() {
        var textResult = $(this).find("td:nth-child(1) p").html() + ' / ' + $(this).find("td:nth-child(2) p:nth-child(1)").html();
       $('input[name="extra_product_field2"]').val(textResult);
       
       $('.text-result').text(textResult);
       
       
       
       $("#myTable tr").removeClass("active");
       $(this).addClass("active");
       
       
       //var xdf = $('#myTable tr.active td:nth-child(2) p:nth-child(2)').text();
       var xdf = $('#myTable tr.active .ticket-left').text();
       xdf = xdf.replace("נותרו", "");
       xdf = xdf.replace("נותר", "");
       xdf = xdf.replace("left", "");
       xdf = xdf.replace(" ", "");

        console.log(xdf);
        $('.qty').attr({ "max" :  xdf }); 
      });

});
</script>
</form>

<?php
if ( current_user_can('administrator') ) {
    if (is_product()) {
        echo "<div class='buttons-view-booking'><a target='_blank' href='/wp-admin/admin.php?page=siteprobooking'>יצירה חדשה של שעה וכמות כרטיסים ביומן</a><a target='_blank' href='/wp-admin/post-new.php?post_type=shop_order'>ביצוע הזמנת טיסה ללקוח</a></div>";
    }
}
?>
</div>


<div id="basic" class="article">
    <div class="calendar"></div>
</div>

<?php 
}
else {
?>
<p class="message-choose-product">נא לבחור מוצר</p>
<?php
}
?>
</section>


<!-- END TABLE RESULT -->

<?php
}}

add_shortcode('calendar_to_product_page', 'sub_view_siteprobooking_options_page' );