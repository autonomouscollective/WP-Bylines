<?php
function cap_byline_migrate_add_to_menu() {
    // Add a new top-level menu
    add_menu_page(__('Byline Migration Tool'), __('Byline Migration Tool'), 'manage_options', 'byline_migraiton_tool', 'byline_migration_display' );
}
add_action('admin_menu', 'cap_byline_migrate_add_to_menu' );
/**
 * Quick, dirty, but it works migration script. This will get all post types, loop through them and then update the acf cap byline field with the original terms data.
 */

// Let's get the missing posts with missing byline arrays and provide a number as to how many there are.
function get_missing_byline_array_count() {

    global $wpdb;

    $query = "
        SELECT count(*)
        FROM wp_posts
        WHERE post_status = 'publish' AND ID NOT IN (
            SELECT post_id
            FROM wp_postmeta
            WHERE meta_key = 'byline_array' AND meta_value != '' AND meta_value IS NOT NULL
        );
    ";

    $count = $wpdb->get_var($query);

    return $count;
}

function cap_byline_migrate() {
    // Setup an empty dumb array. We'll use this later on to store how many posts dont have data.
    $results = array();

    // Setup a query to get posts that don't have a byline array meta field. Limit to 10.
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => 'byline_array',
                'compare' => 'NOT EXISTS',
            )
        ),
        'cache_results' => false
    );
    $the_query = new WP_Query( $args );

    // Loop through this query, get the person terms that already exist, copy those via
    // update_field (an ACF function) to the new byline_array field.
    if ( $the_query->have_posts() ) {
        $i = 1;
        while ( $the_query->have_posts() ) {
            $the_query->the_post();
            $persons = get_the_terms( get_the_ID(), 'person' );
            $new_data = array();
            // We don't want to throw errors if there are any posts with no data.
            if (!empty($persons)) {
                foreach ($persons as $person ) {
                    $new_data[] = $person->term_id;
                }
                update_field('field_53f38cd042a42', $new_data);
                // print_r($new_data);
                // echo '<strong>'.get_the_id().'</strong>';
                // echo '<br>'.get_the_title().'  -- Ran '. $i . '<br><br>';
            } else {
                // echo '<span style="color:red">'.get_the_title().' Had No Persons -- Ran' . $i . '</span><br><br>';
            }
            $i++;
        }

    }
    // Restore original post data to start through the loop again.
    wp_reset_postdata();

    // Provide a count of how many posts after running this query do not have the byline_array field.
    $results["count"] = get_missing_byline_array_count();
    // Write json header
    header("Content-type: application/json");

    $return = json_encode($results);
    echo $return;
    // echo $results["count"];

    die();
}
add_action('wp_ajax_cap_byline_migrate', 'cap_byline_migrate');
add_action('wp_ajax_nopriv_cap_byline_migrate', 'cap_byline_migrate');
// To run hit - http://domain.com/wp-admin/admin-ajax.php?action=cap_byline_migrate

function byline_migration_display() {
    // # of missing short urls when the page loads
    $max = get_missing_byline_array_count();
?>
<style type="text/css">
.progress-label {
float: left;
margin-left: 33%;
margin-top: 5px;
font-weight: bold;
text-shadow: 1px 1px 0 #fff;
}
#progressbar {
width: 30%;
float: left;
margin-right: 1em;
}
</style>

<script>
jQuery(document).ready(function($) {
// initialize progress bar and label for updating missing shorturls
 var progressbar = jQuery( "#progressbar" ),
 progressLabel = jQuery( ".progress-label" );
 progressbar.progressbar({
     value: 0,
     max: <?php echo $max; ?>,
     change: function() {
         // update the progress bar and label
         var val = progressbar.progressbar("option", "max") - progressbar.progressbar( "option", "value");
         progressLabel.text( "" + val + " Missing Byline Arrays" );

         if (val > 0) {
             // process the next batch
             jQuery("#backfill").click();
         } else {
             // disable the button
             jQuery("#backfill").removeClass("button-primary").addClass("button-primary-inactive").attr("disabled","disabled");
         }
    },
    complete: function() {
        // update the progress label
        progressLabel.text( "Complete!" );
    }
 });

 /**
  * Update the progress bar
  */
 function progress(value) {
     var val = progressbar.progressbar("option", "max") - value;
    progressbar.progressbar( "option", "value", val );
 }

 /**
  * If there are any missing short urls, then enable the update button and set its click handler
  */
 if (<?php echo $max; ?>) {
    jQuery("#backfill").each(function(){
        jQuery(this).removeClass("button-primary-inactive").addClass("button-primary").removeAttr("disabled");

        jQuery(this).click(function(event){
            event.preventDefault();
            console.log('Running migrate');
            jQuery.ajax({
                url: "/wp-admin/admin-ajax.php?action=cap_byline_migrate",
                type: "post",
                success: function(response){
                    console.log('Migration moving along');
                    if (response.error) {
                    console.log('Migration error');    jQuery("#backfillerror").addClass("error").html(response.error);
                    } else {
                        console.log('Migration batch complete');
                        progress(response.count);
                    }
                },
                error: function() {
                console.log('Migration did not get started');    jQuery("#backfillerror").addClass("error").html("error");
                }
            });
        });
    });
 }
});
</script>

<div class="wrap">
<h2>Cap Byline Migration Assistant</h2>

<div id="result"></div>
</div>

<br/>

<div class="wrap">
<div id="progressbar"><div class="progress-label"><?php echo get_missing_byline_array_count(); ?> Missing Byline Arrays</div></div><form><input type="button" name="backfill" id="backfill" class="button-primary-inactive" value="Generate Missing Byline Arrays" disabled="disabled"  /></form>
<div id="backfillerror"></div>
</div>

<?php
}
