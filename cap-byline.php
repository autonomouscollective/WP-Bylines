<?php
/**
 * Plugin Name: CAP Bylines
 * Description: Provides a CAP standardized method for choosing authors for posts
 * Version: 1.0
 * Author: Seth Rubenstein for Center for American Progress
 * Author URI: http://sethrubenstein.info
 * License: GPL2
 */
function cap_byline_activate() {
	$form = Array (
		'labelPlacement' => 'top_label',
		'useCurrentUserAsAuthor' => 1,
		'title' => 'Contact Author',
		'description' => 'Fill out the form below to contact this author',
		'descriptionPlacement' => 'below',
		'button' => Array ( 'type' => 'text', 'text' => 'Submit' ),
		'fields' => Array (
				'0' => Array (
					'id' => '1',
					'isRequired' => '1',
					'size' => 'medium',
					'type' => 'name',
					'label' => 'Name',
					'inputs' =>Array (
							'0' => Array ( 'id' => '1.3', 'label' => 'First' ),
							'1' => Array ( 'id' => '1.6', 'label' => 'Last' )
							),
					'formId' => '2',
					'pageNumber' => '1',
					'descriptionPlacement' => 'below',
				),
				'1' => Array (
						'id' => '2',
						'isRequired' => '1',
						'size' => 'medium',
						'type' => 'email',
						'label' => 'Email',
						'formId' => '2',
						'pageNumber' => '1',
						'descriptionPlacement' => 'below',
				),
				'2' => Array (
					'id' => '3',
					'isRequired' => '1',
					'size' => 'medium',
					'type' => 'textarea',
					'label' => 'Message',
					'formId' => '2',
					'pageNumber' => '1',
					'descriptionPlacement' => 'below',
				),
				'3' => Array (
					'allowsPrepopulate' => 1,
					'id' => 4,
					'size' => 'medium',
					'type' => 'hidden',
					'inputName' => 'author_contact_to',
					'label' => 'To',
					'formId' => 2,
					'pageNumber' => 1,
					'descriptionPlacement' => 'below'
				)
			),
		'enableHoneypot' => '1',
		'enableAnimation' => '1',
		'id' => '2',
		'notifications' => Array (
			'53a057ebea107' => Array (
				'id' => '53a057ebea107',
				'to' => '{admin_email}',
				'name' => 'Admin Notification',
				'event' => 'form_submission',
				'toType' => 'email',
				'subject' => 'You have received a message from '.get_bloginfo('name').'',
				'message' => '{all_fields}'
			),
		),
		'confirmations' => Array (
			'53a057ebeadd6' => Array (
				'id' => '53a057ebeadd6',
				'isDefault' => '1',
				'type' => 'message',
				'name' => 'Default Confirmation',
				'message' => 'Thank you for contacting me.',
				'disableAutoformat' => null,
				'pageId' => null,
				'url' => null,
				'queryString' => null,
				'conditionalLogic' => Array ( ),
			)
		),
		'is_active' => '1',
		'date_created' => '2014-06-17 15:17:18',
		'is_trash' => '0',
	);
	$form_id = GFAPI::add_form($form);
}
register_activation_hook( __FILE__, 'cap_byline_activate' );

if( function_exists("register_field_group") ) {
	register_field_group(array (
		'id' => 'acf_person-settings',
		'title' => 'Person Settings',
		'fields' => array (
			array (
				'key' => 'field_539efe9038185',
				'label' => 'Bio Pic',
				'name' => 'person_photo',
				'type' => 'image',
				'save_format' => 'id',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_539f068a98928',
				'label' => 'Title',
				'name' => 'person_title',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
			array (
				'key' => 'field_539f06f598929',
				'label' => 'Contact Email',
				'name' => 'person_email',
				'type' => 'email',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array (
				'key' => 'field_539efea738186',
				'label' => 'Twitter Handle',
				'name' => 'person_twitter_handle',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '@',
				'append' => '',
				'formatting' => 'none',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'ef_taxonomy',
					'operator' => '==',
					'value' => 'person',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'no_box',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_cap-byline-settings',
		'title' => 'CAP Byline Settings',
		'fields' => array (
			array (
				'key' => 'field_53a069c4d2202',
				'label' => 'Author Contact Form ID',
				'name' => 'author_contact_form_id',
				'type' => 'text',
				'instructions' => 'Enter the ID of the form titled "Contact Author" for the contact functionality to work on author bio pages.',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_53a2ff7d56f69',
				'label' => 'Disable Auto Author Select',
				'name' => 'disable_auto_author_select',
				'type' => 'true_false',
				'instructions' => 'Checking this field will disable the auto selection of the author when writing a post.',
				'message' => '',
				'default_value' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'acf-options',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}


function person_tax_create() {
	register_taxonomy(
		'person',
		array('post'),
		array(
			'label' => __( 'Person' ),
			'rewrite' => array( 'slug' => 'person', 'with_front' => false ),
			'hierarchical' => false,
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'person_tax_create' );

function cap_person_autoselect_author( $post_id ){
	global $post;
	$author_slug = get_the_author_meta( 'user_login', $post->post_author );
	// if the author has a person record then set that automatically
	if ( term_exists( $author_slug, 'person' ) && 0 == get_field( 'disable_auto_author_select','options' ) ) {
		wp_set_post_terms($post_id, $author_slug, 'person', true);
	}
}
add_action('save_post', 'cap_person_autoselect_author');

function cap_list_of_authors($disablelink) {
	global $post;
	// lets get all the people associated with this post
	$people = get_the_terms( $post->ID, 'person' );
	// lets setup an array to organize these people based on some conditions below
	$byline_array = array();
	$primary_author_slug = '';

	if ( 0 == get_field( 'disable_auto_author_select','options' ) ) {
		// get the actual author of this post
		$primary_author_slug .= get_the_author_meta( 'user_login', $post->post_author );
		// add the author to the start of the authors list
		$byline_array[] = $primary_author_slug;
	}

	// add people who have a description
	// (this is our condition for recognizing this person as having a bio page)
	// and then check to make sure it's not the original author. Proceed to add to array.
	foreach ( $people as $person ) {
		if ( !empty( $person->description ) ) {
			if ( $primary_author_slug != $person->slug ) {
				$byline_array[] = $person->slug;
			}
		}
	}
	// add people who do not have a description ("guest authors, contributors")
	foreach ( $people as $person ) {
		if ( empty( $person->description ) ) {
			$byline_array[] = $person->slug;
		}
	}

	$i = 1;
	$total_num_people = count($byline_array);
	$output = '';
	foreach ( $byline_array as $author ) {
		$data = get_term_by( 'slug', $author, 'person', 'ARRAY_A');
		//print_r($data);
		$name = $data['name'];
		$slug = $data['slug'];
		if (!empty( $data['description'] ) ) {
			// Simple check to see if true is passed into the cap_list_of_authors function.
			// If it is then lets output the list regardless if they have bio or not with no links to profiles.
			if ( true == $disablelink ) {
				$output .= $name;
			} else {
				$output .= '<a href="'.get_bloginfo('url').'/?person='.$slug.'">'.$name.'</a>';
			}
		} else {
			$output .= $name;
		}

		if ( $total_num_people > 1 ) {
			if ( $i != $total_num_people ) {
				$output .= ', ';
			}
		}
		$i++;
	}
	return $output;
}

// Check for existence of cap_byline function as a theme may override this functionality.
if ( ! function_exists( 'get_cap_byline' ) ) {

	function get_cap_byline($type) {
		global $post;
		$time_string = '<time class="entry-date published" datetime="'.esc_attr( get_the_date( 'c' ) ).'">'.esc_html( get_the_date() ).'</time>';

		$markup = '';
		if ( 'dateonly' == $type ) {
			 $markup .= '<span class="posted-on">'.$time_string.'</span>';
		} elseif ( 'bylineonly' == $type ) {
			$markup .= ' by '.cap_list_of_authors(null);
		} else {
			$markup .= '<span class="byline"> by '.cap_list_of_authors(null).'. </span>';
			$markup .= '<span class="posted-on">Posted on '.$time_string.'</span>';
		}
		return $markup;
	}

}

function cap_byline($type) {
	echo get_cap_byline($type);
}

if ( ! function_exists( 'cap_person_bio' ) ) {
	function cap_person_bio($style) {
		/**
		 * Note from Seth:
		 * I'm not happy with the css trickery and markup I've produced here. Eventually cleanup. This
		 * was a quick and dirty implentation that would work across sites.
		 */
		global $wp_query;
    	$person = $wp_query->get_queried_object();
		$person_photo = get_field( 'person_photo', 'person_'.$person->term_id );
		$person_title = get_field( 'person_title', 'person_'.$person->term_id );
		$person_email = get_field( 'person_email', 'person_'.$person->term_id );
		$person_twitter_handle = get_field( 'person_twitter_handle', 'person_'.$person->term_id );

		$markup = '<div class="person '.$style.'">';
		// if the "full" style is being displayed then add the person name and title atop the bio
		if ( 'full' == $style ) {
			$markup .= '<div class="person-title"><h1>'.$person->name.'<br><small>'.$person_title.'</small></h1></div>';
		}
		// begin the actual bio area
		$markup .= '<div class="person-bio">';
		if (!empty($person_photo)) {
			$markup .= '<div class="bio-pic">'.wp_get_attachment_image( $person_photo, 'medium' ).'</div>';
		}
		// get the bio and add it to $markup
		$markup .= '<div class="bio">'.$person->description;
		// if either an email addy or twitter handle are present lets add a hard line break for spacing
		if ( !empty($person_email) || !empty($person_twitter_handle) ) {
			$markup .= '<div id="contact-button-seperator"></div>';
		}

		// if the bio has an email associated add a contact modal form to $markup also check the form ID is present
		if ( !empty($person_email) && get_field('author_contact_form_id', 'options') ) {
			$markup .= '<a id="contact-modal-link" class="cap-contact-modal-link" href="javascript:void(0);"><img src="'.plugin_dir_url('cap-byline.php').'/cap-byline/mail.png" width="18px"> Contact '.$person->name.'</a>';
			$markup .= '
			<script>
			jQuery(document).ready(function(){
				jQuery("#contact-modal-link").click(function(){
						jQuery("#contact-modal").addClass("active");
				});
				jQuery("#contact-modal .close-modal").click(function(){
						jQuery("#contact-modal").removeClass("active");
				});
			});
			</script>
			';
			$markup .= '<div id="contact-modal" class="modal"><div class="modal-wrapper"><div class="close-modal"><img src="'.plugin_dir_url('cap-byline.php').'/cap-byline/close_circle.png"></div><div class="modal-window">';
			$markup .= do_shortcode("[gravityform id=".get_field('author_contact_form_id', 'options')." field_values='author_contact_to=".$person_email."']");
			add_filter("gform_notification_email", "change_notification_email", 10, 2);
			$markup .= '</div></div></div>';
			$markup .= '
			<style>
			div.modal {
			    display: none;
			    position: fixed;
			    top: 0;
			    left: 0;
			    height: 100%;
			    width: 100%;
			    background-color: rgba(0,0,0,0.6);
				z-index: 999;
			}
			div.modal.active {
			    display: table;
			}
			div.modal-wrapper {
			    display: table-cell;
			    vertical-align: middle;
			    z-index: 9999;
			    text-align: center;
			}
			div.modal-window {
			    text-align: left;
			    display: inline-block;
			    min-width: 50%;
			    background-color: #fff;
			    padding: 2em;
			}
			div.close-modal {
				position: relative;
				bottom: -20px;
				text-align: right;
				width: 52%;
				margin: 0 auto;
			}
			#contact-modal-link {
				color: #000;
				font-weight: bold;
				position: relative;
				top: -4px;
			}
			#contact-modal-link img {
				position: relative;
				top: 3px;
			}
			span#twitter-follow {
				margin-left: 5px;
			}
			#contact-button-seperator {
				height: 15px;
			}
			</style>
			';
		}
		// if the bio has a twitter handle associated add the follow button to $markup
		if ( !empty($person_twitter_handle) ) {
			$markup .= '<span id="twitter-follow"><a href="https://twitter.com/'.$person_twitter_handle.'" class="twitter-follow-button" data-show-count="false" data-lang="en">Follow @'.$person_twitter_handle.'</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></span>';
		}

		$markup .= '</div>'; // close out .bio
		$markup .= '</div>'; // close out .person-bio
		$markup .= '</div>'; // close out .person .$style

		echo $markup;
	}
}

function route_notification($notification, $form , $entry) {
    global $post;
	if ($form["id"] == get_field('author_contact_form_id', 'options') ) {
		$email_to = gform_get_meta($entry["id"], 'author_contact_to');
		if ($email_to){
			$notification['to'] = $email_to;
		}
	}
    return $notification ;
}
add_filter( 'gform_notification', 'route_notification', 10, 3 );
