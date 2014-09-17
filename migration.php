<?php
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
        'posts_per_page' => 100,
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
    //echo $results["count"];

    die();
}
add_action('wp_ajax_cap_byline_migrate', 'cap_byline_migrate');
add_action('wp_ajax_nopriv_cap_byline_migrate', 'cap_byline_migrate');
// To run hit - http://domain.com/wp-admin/admin-ajax.php?action=cap_byline_migrate
