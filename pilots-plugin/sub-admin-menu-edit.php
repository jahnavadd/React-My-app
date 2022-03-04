<?php
function sub_edit_siteprobooking_options_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . "siteprobooking";
    
    $date_now = date("Y-m-d");
    $date_half_year = date('Y-m-d', strtotime('+3 months'));
    
    if (isset($_GET['product'])) $product_id = $_GET['product'];



    ?>
    <div class="wrap">
       
   
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      
       <?php
       
               if (isset($_POST['edit-booking']) && isset($_POST['times']) && isset($_POST['timesnew']) && isset($_POST['date-start']) && isset($_POST['date-end']) && !empty($_POST['week'])) {
            echo "<section class='result-message'>";
            $dateTimestamp1 = strtotime($_POST['date-start']);
            $dateTimestamp2 = strtotime($_POST['date-end']);
            
            $product = wc_get_product( $_GET['product'] );
            
            if ($dateTimestamp1 <= $dateTimestamp2) {
            
            foreach (getDatesFromRange($_POST['date-start'], $_POST['date-end']) as $dateOption) {
                    if (in_array ( date("w", strtotime($dateOption)), $_POST['week'])){
                            $result_search_old = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE bdate = '" . $dateOption . "' and btime = '" . $_POST['times'] . "' and bproduct = '" . $_GET['product'] . "'");
                            $result_search = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE bdate = '" . $dateOption . "' and btime = '" . $_POST['timesnew'] . "' and bproduct = '" . $_GET['product'] . "'");
                            if (empty($result_search) && !empty($result_search_old)) {
                                
                                $result_search_time = $wpdb->get_results( "SELECT borders FROM {$table_name} WHERE bdate = '" . $dateOption . "' and btime = '" . $_POST['times'] . "' and bproduct = '" . $_GET['product'] . "'");
                                foreach ($result_search_time as $item_result_search_time) {
                                    
                                    
                                    $orders_array = explode("|", $item_result_search_time->borders);   
                                        foreach ($orders_array as $order_item) {
                                            if (!empty($order_item)){
                                                $order = wc_get_order( (int)$order_item );
                                                
                                                $order_items = $order->get_items();
                                                foreach( $order_items as $item_id => $item ) {
                                                    $custom_field = wc_get_order_item_meta( $item_id, 'extra_product_field2', true );
                                                    //var_dump($custom_field);
                                                   
                                                    if ($item->get_product_id() == $product_id && $custom_field == $dateOption . " / " . $_POST['times']) {
                                                        wc_update_order_item_meta( $item_id, 'extra_product_field2', $dateOption . " / " . $_POST['timesnew']);
                                                    }
                                                }
                                            }
                                        }
                                }
                                
                                    $wpdb->query("UPDATE {$table_name} SET btime = '{$_POST['timesnew']}'  WHERE bdate = '" . $dateOption . "' and btime = '" . $_POST['times'] . "' and bproduct = '" . $_GET['product'] . "'");
                                    
                                    eventGoogleCalendar ($_GET['product'], $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token'], 0, $dateOption, $_POST['timesnew']);

                                    
                                    echo "<p class='result-line'>" . $product->get_title() . " - " . $dateOption . " - " . $_POST['times'] . " - " .  $_POST['timesnew'] . "</p>";
                                    
                                
                              }
                              else echo "<p class='result-line empty-result-line'>" . $product->get_title() . " - " . $dateOption . " - " . $_POST['times'] . " - " .  $_POST['timesnew'] . "</p>";

                       }
                }
                //$result_search = $wpdb->get_results( "SELECT btime FROM {$table_name} WHERE bdate >= '" . $_POST['date-start'] . "' and bdate <= '" . $_POST['date-end'] . "' and btime = '" . $_POST['times'] . "' and bproduct = '" . $_GET['product'] . "'");
                //$result_search = $wpdb->get_results( "SELECT btime FROM {$table_name} WHERE bdate >= '" . $_POST['date-start'] . "' and bdate <= '" . $_POST['date-end'] . "' and bproduct = '" . $_GET['product'] . "'");
                //$array_times_product = [];
                //foreach($result_search as $result_row) {
                 //  array_push($array_times_product, $result_row->btime);
                //}
                //print_r(array_unique($array_times_product));
                            
                //echo $wpdb->query("UPDATE {$table_name} SET btime = '{$_POST['timesnew']}'  WHERE bdate >= '" . $_POST['date-start'] . "' and bdate <= '" . $_POST['date-end'] . "' and btime = '" . $_POST['times'] . "' and bproduct = '" . $_GET['product'] . "'");
              
            }
            else echo "Error date";
            echo "</section>";
      }
  
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
                    echo "<a href='/wp-admin/admin.php?page=editsiteprobooking&product=" . get_the_ID() . "' " . $class_active . ">" . get_the_title() . "</a>";
                   endwhile;
                wp_reset_query();
            
                
        ?>
        </section>

       <section class="result-block"> 
       
    <?php 
        
        

        
        
        if (isset($_GET['product'])) {


        $result_search_times_product = $wpdb->get_results( "SELECT btime FROM {$table_name} WHERE bproduct = '" . $_GET['product'] . "'");
        $array_times_product = [];
        foreach($result_search_times_product as $result_row) {
            array_push($array_times_product, $result_row->btime);
        }
        //print_r(array_unique($array_times_product));
        
        $array_times_new = [];
        foreach ($GLOBALS['array_times'] as $time_item){
            $count = 0;
            foreach (array_unique($array_times_product) as $item_time_product){
                if ($time_item == $item_time_product) $count++;
            }
            if ($count == 0) array_push($array_times_new, $time_item);
        }
        //print_r(array_unique($array_times_new));
        
        $unique_array_times_product = array_unique($array_times_product);
        
        sort($unique_array_times_product, SORT_NATURAL | SORT_FLAG_CASE);

      ?>   

       
       
      <form action="" method="post" class="form_add_data_to_db">

        <p><label for="date-start">מ-</label> <input type="date" id="date-start" name="date-start" required min="<?=$date_now?>" max="<?=$date_half_year?>">
        <label style="margin-right: 20px; display: inline-block;" for="date-end">עד-</label> <input type="date" id="date-end" name="date-end" required min="<?=$date_now?>" max="<?=$date_half_year?>"></p>
        <p class="week-line">
            <label>מופע חוזר</label>
            <input type="checkbox" value="0" name="week[]" id="week1"><label for="week1"> א</label>
            <input type="checkbox" value="1" name="week[]"><label for="week1"> ב</label>
            <input type="checkbox" value="2" name="week[]"><label for="week1"> ג</label>
            <input type="checkbox" value="3" name="week[]"><label for="week1"> ד</label>
            <input type="checkbox" value="4" name="week[]"><label for="week1"> ה</label>
            <input type="checkbox" value="5" name="week[]"><label for="week1"> ו</label>
            <input type="checkbox" value="6" name="week[]"><label for="week1"> ש</label>
        </p>
        <p><label class="label-custom-width">בחירת שעה קיימת </label><select name="times"  required>
            <?php
                
                foreach ($unique_array_times_product as $time_item){
                    echo '<option value="' . $time_item . '">' . $time_item . '</option>';
                }
            ?>
        </select></p>
        <p><label class="label-custom-width">בחירת שעה חדשה </label><select name="timesnew"  required>
            <?php
                foreach ($GLOBALS['array_times'] as $time_item){
                    echo '<option value="' . $time_item . '">' . $time_item . '</option>';
                }
            ?>
        </select></p>
        
        <p><input type="submit" name="edit-booking" id="edit-booking" value="עדכן"></p>
      </form>
      
      <?php
        }
            else echo '<p class="message-choose-product">נא לבחור מוצר</p>';
      ?>
      </section>
</div>
<?php  
}