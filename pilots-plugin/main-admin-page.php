<?php
/* ADD ROWS TO TABLE */
function siteprobooking_options_page_html() {
    global $wpdb;
    $table_name = $wpdb->prefix . "siteprobooking";
    ?>
    <div class="wrap">
       
   
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      
      <?php
      if (isset($_POST['create-booking']) && isset($_POST['times']) && isset($_POST['date-start']) && isset($_POST['date-end']) && isset($_POST['products']) && !empty($_POST['week'])) {
            echo "<section class='result-message'>";
            $dateTimestamp1 = strtotime($_POST['date-start']);
            $dateTimestamp2 = strtotime($_POST['date-end']);
            if ($dateTimestamp1 <= $dateTimestamp2) {
                
                $capi = new GoogleCalendarApi();
                $token = $capi->GetAccessToken($GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token']);
                $user_timezone = $capi->GetUserCalendarTimezone($token['access_token']);
                
                
              foreach ($_POST['products'] as $productOption) {
                  foreach (getDatesFromRange($_POST['date-start'], $_POST['date-end']) as $dateOption) {
                      if (in_array ( date("w", strtotime($dateOption)), $_POST['week'])){
                        foreach ($_POST['times'] as $timeOption) {
                            $result_search = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE bdate = '" . $dateOption . "' and btime = '" . $timeOption . "' and bproduct = '" . $productOption . "'");
                            if (empty($result_search)) {
                                $event = eventGoogleCalendarAddNewRow ($productOption, $GLOBALS['client_id'], $GLOBALS['client_redirect_url'], $GLOBALS['client_secret'], $GLOBALS['refresh_token'], 0, $dateOption, $timeOption, $_POST['count'], $capi, $token, $user_timezone);
                               $rows_affected = $wpdb->insert( $table_name, array( 'bdate' => $dateOption, 'btime' => $timeOption, 'bcount' => $_POST['count'], 'borders' => '', 'bproduct' => $productOption, 'bevents' =>  $event) );
                               if (!empty($rows_affected)){
                                   
                                  
                                   
                                   
                                   $product = wc_get_product( $productOption );
                                   echo "<p class='result-line'>" . $product->get_title() . " - " . $dateOption . " - " .  $timeOption . "</p>";
                               }
                               
                            }
                            else {
                                   $product = wc_get_product( $productOption );
                                   echo "<p class='result-line empty-result-line'>" . $product->get_title() . " - " . $dateOption . " - " .  $timeOption . "</p>";
                               }
                        }
                       }
                    }
              }
            }
            else echo "Error date";
            echo "</section>";
      }
      ?>
      
      <?php
        $date_now = date("Y-m-d");
        $date_half_year = date('Y-m-d', strtotime('+3 months'));
      ?>
       
         
      <form action="" method="post" class="form_add_data_to_db">
        <p><label class="label-custom-width">סוג פעילות </label><select class="js-example-basic-multiple js-states form-control" name="products[]" id="product" multiple="multiple" required>
            
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
            echo "<option value='" . get_the_ID() . "'>" . get_the_title() . "</option>";
           endwhile;
        wp_reset_query();
        ?>
        
        </select></p>
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
        <p><label class="label-custom-width">בחירת שעת הפעילות </label><select class="js-example-basic-multiple js-states form-control" name="times[]" multiple="multiple" required>
            <?php
                foreach ($GLOBALS['array_times'] as $time_item){
                    echo '<option value="' . $time_item . '">' . $time_item . '</option>';
                }
            ?>
        </select></p>
        <p><label class="label-custom-width">כמות כרטיסים </label><input type="number" value="10" id="count" name="count" required></p>
        
        <p><input type="submit" name="create-booking" id="create-booking" value="הוסף"></p>
      </form>
<script>
jQuery(document).ready(function($) {
    $('.js-example-basic-multiple').select2({
        width: '500px'
    });
});
</script>  
</div>
<?php  
}