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


function wceg_cap_byline_migrate() {
    // Setup an empty dumb array. We'll use this later on to store how many posts dont have data.
    $results = array();

    // Setup a query to get posts that don't have a byline array meta field. Limit to 10.
    $args = array(
        'post_type' => array('post','work','press','news'),
        'posts_per_page' => 1000,
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
                print_r($new_data);
                echo '<strong>'.get_the_id().'</strong>';
                echo '<br>'.get_the_title().'  -- Ran '. $i . '<br><br>';
            } else {
                echo '<span style="color:red">'.get_the_title().' Had No Persons -- Ran' . $i . '</span><br><br>';
            }
            $i++;
        }

    }
    // Restore original post data to start through the loop again.
    wp_reset_postdata();

    // // Provide a count of how many posts after running this query do not have the byline_array field.
    // $results["count"] = get_missing_byline_array_count();
    // // Write json header
    // header("Content-type: application/json");
    //
    // $return = json_encode($results);
    // echo $return;
    echo $results["count"];

    die();
}
add_action('wp_ajax_wceg_cap_byline_migrate', 'wceg_cap_byline_migrate');
add_action('wp_ajax_nopriv_wceg_cap_byline_migrate', 'wceg_cap_byline_migrate');
// To run hit - http://domain.com/wp-admin/admin-ajax.php?action=wceg_cap_byline_migrate

function hit_cap_byline_migrate() {
    // Setup an empty dumb array. We'll use this later on to store how many posts dont have data.
    $results = array();

    // Setup a query to get posts that don't have a byline array meta field. Limit to 10.
    $args = array(
        'post_type' => array('post','stories'),
        'posts_per_page' => 1000,
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
                print_r($new_data);
                echo '<strong>'.get_the_id().'</strong>';
                echo '<br>'.get_the_title().'  -- Ran '. $i . '<br><br>';
            } else {
                echo '<span style="color:red">'.get_the_title().' Had No Persons -- Ran' . $i . '</span><br><br>';
            }
            $i++;
        }

    }
    // Restore original post data to start through the loop again.
    wp_reset_postdata();

    // Provide a count of how many posts after running this query do not have the byline_array field.
    $results["count"] = get_missing_byline_array_count();
    // Write json header
    // header("Content-type: application/json");
    //
    // $return = json_encode($results);
    // echo $return;
    echo $results["count"];

    die();
}
add_action('wp_ajax_hit_cap_byline_migrate', 'hit_cap_byline_migrate');
add_action('wp_ajax_nopriv_hit_cap_byline_migrate', 'hit_cap_byline_migrate');
// To run hit - http://domain.com/wp-admin/admin-ajax.php?action=hit_cap_byline_migrate

function c3_cap_byline_migrate() {
	// Set up an empty array. We'll use this to store bios without data.
	$missed_bios = array();

	// Setup a query to get posts of type bio
	$args = array(
		'post_type'      => array( 'bio' ),
		'posts_per_page' => 1000,
		'fields'         => 'ids',
		'cache_results'  => false
	);
	$the_query = new WP_Query( $args );

	// Loop through this query, get the person term that already exists, copy email address and
	// twitter handle from the bio to the person
	if ( $the_query->have_posts() ) {
		$i = 1;
		while ( $the_query->have_posts() ) {
			$the_query->the_post();

			// get the email address and twitter handle
			$email_address  = get_post_meta( get_the_ID(), '_bio_email', true );
			$twitter_handle = get_post_meta( get_the_id(), '_bio_twitter', true );

			$persons = get_the_terms( get_the_ID(), 'person' );

			// We don't want to throw errors if there are any posts with no data.
			if ( ! empty( $persons ) ) {
				$person = array_shift( $persons );
				if ( ! empty( $email_address ) ) {
					update_field( 'field_539f06f598929', $email_address, $person );
				}
				if ( ! empty( $twitter_handle ) ) {
					update_field( 'field_539efea738186', $twitter_handle, $person );
				}

				echo '<strong>' . get_the_id() . '</strong>';
				echo '<br>' . get_the_title() . '  -- Ran ' . $i . '<br><br>';
			} else {
				echo '<span style="color:red">' . get_the_title() . ' Had No Persons -- Ran' . $i . '</span><br><br>';
				$missed_bios[] = get_the_ID();
			}
			$i ++;
		}
	}
	// Restore original post data to start through the loop again.
	wp_reset_postdata();

	print_r($missed_bios);

	die();
}

add_action( 'wp_ajax_c3_cap_byline_migrate', 'c3_cap_byline_migrate' );
add_action( 'wp_ajax_nopriv_c3_cap_byline_migrate', 'c3_cap_byline_migrate' );
// To run hit - http://domain.com/wp-admin/admin-ajax.php?action=c3_cap_byline_migrate

function gp_cap_byline_migrate() {

    // Setup a query to get posts of type bio
    $args = array(
        'post_type'      => array( 'bio' ),
        'posts_per_page' => 1000,
        'cache_results'  => false
    );
    $the_query = new WP_Query( $args );

    // Loop through this query, set person_is_linked to true for the person
    // corresponding to each bio
    if ( $the_query->have_posts() ) {
        $posts = $the_query->get_posts();
        foreach ($posts as $post) {
            echo "for Bio post_id: $post->ID<br/>\n";

            // get person
            $person = get_term_by('slug', $post->post_name, 'person');
            if ($person) {
                // update person_is_linked
                update_field('field_53a2ff7d56f11', true, $person->term_id);
                echo "update person (term_id, name): ($person->term_id, $person->name)";
            }
            echo "<br/><br/>\n";
        }
    }

    die();
}

add_action( 'wp_ajax_gp_cap_byline_migrate', 'gp_cap_byline_migrate' );
add_action( 'wp_ajax_nopriv_gp_cap_byline_migrate', 'gp_cap_byline_migrate' );
// To run hit - http://domain.com/wp-admin/admin-ajax.php?action=gp_cap_byline_migrate
