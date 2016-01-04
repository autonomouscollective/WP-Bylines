<?php
/**
 * Plugin Name: CAP Byline
 * Plugin URI: https://github.com/amprog/cap-byline
 * Description: Provides a CAP standardized method for choosing authors for posts
 * Version: 1.2
 * Author: Seth Rubenstein for Center for American Progress
 * Author URI: https://github.com/amprog
 * License: GPL2
 */
$plugin_dir = plugin_dir_path( __FILE__ );

/**
 * Create person taxonomy
 */
function person_tax_create() {
    register_taxonomy(
        'person',
        get_post_types(),
        array(
            'label' => __( 'Person' ),
            'rewrite' => array( 'slug' => 'person', 'with_front' => false ),
            'hierarchical' => false,
            // 'show_admin_column' => true,
            'capabilities' => array(
                'manage_terms' => 'edit_others_posts',
                'edit_terms' => 'edit_others_posts',
                'delete_terms' => 'edit_others_posts'
            )
        )
    );
}
add_action( 'init', 'person_tax_create' );

function persons_column($columns) {
    $columns['persons'] = 'Persons';
    return $columns;
}
add_filter('manage_posts_columns', 'persons_column');

function show_persons_column($name) {
    global $post;
    switch ($name) {
        case 'persons':
        $views = get_cap_authors($post->ID, true, false, false);
        echo $views;
    }
}
add_action('manage_posts_custom_column',  'show_persons_column');

/**
 * Remove the Person metabox from all post types.
 */
function remove_person_meta_box() {
    $post_types = get_post_types( '', 'names' );
    foreach ( $post_types as $post_type ) {
        remove_meta_box( 'tagsdiv-person', ''. $post_type .'', 'side' );
    }
}
add_action( 'admin_menu' , 'remove_person_meta_box' );
/**
 * On CAP Byline activation run these actions
 */
function cap_byline_activate() {
    /**
     * Create the Gravity Form contact form for bios
     */
    if ( function_exists('gform_notification') ) {
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
                        'inputName' => 'author_contact_email',
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
}
register_activation_hook( __FILE__, 'cap_byline_activate' );

/**
 * Register fields
 */
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
                'key' => 'field_55197b1a6eeb8',
                'label' => 'Hi-res Bio Pic',
                'name' => 'person_photo_hi_res',
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
            array(
                'key'           => 'field_560434a4d45fe',
                'label'         => 'Facebook ID',
                'name'          => 'person_facebook_id',
                'type'          => 'text',
                'default_value' => '',
                'placeholder'   => '',
                'prepend'       => '',
                'append'        => '',
                'formatting'    => 'none',
                'maxlength'     => '',
            ),
            array (
                'key' => 'field_53a2ff7d56f11',
                'label' => 'Person Is Linked?',
                'name' => 'person_is_linked',
                'type' => 'true_false',
                'instructions' => 'Checking this field will enable the bio link for a person in the byline.',
                'message' => '',
                'default_value' => 0,
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
            array (
                'key' => 'field_53a2fe7d56009',
                'label' => 'Disable Updated Time',
                'name' => 'global_disable_update_time',
                'type' => 'true_false',
                'instructions' => 'Checking this field will disable the updated time globally.',
                'message' => '',
                'default_value' => 0,
            ),
            array (
                'key' => 'field_53a2fe7d51239',
                'label' => 'Display Post Time',
                'name' => 'global_display_post_time',
                'type' => 'true_false',
                'instructions' => 'Checking this field will display the time a post is published globally.',
                'message' => '',
                'default_value' => 1,
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

    /**
     * CAP Byline Field
     */
    register_field_group(array (
        'key' => 'group_53f38caa7634f',
        'title' => 'Byline',
        'fields' => array (
            array (
                'key' => 'field_53f38cd042a42',
                'label' => 'Byline',
                'name' => 'byline_array',
                'prefix' => '',
                'type' => 'taxonomy',
                'instructions' => 'This field will autocomplete names. Start typing to add existing person(s) to this post.',
                'required' => 0,
                'conditional_logic' => 0,
                'taxonomy' => 'person',
                'field_type' => 'multi_select',
                'allow_null' => 0,
                'load_save_terms' => 0,
                'return_format' => 'id',
                'multiple' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '!=',
                    'value' => 'state-year-report',
                ),
                array (
                    'param' => 'post_type',
                    'operator' => '!=',
                    'value' => 'cd-report',
                ),
                array (
                    'param' => 'post_type',
                    'operator' => '!=',
                    'value' => 'page',
                ),
            ),

        ),
        'menu_order' => 15,
        'position' => 'acf_after_title',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'field',
        'hide_on_screen' => '',
    ));
}

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page();
}

/**
 * Sets the person terms to the post via the ACF Byline Array field.
 */
function cap_byline_array_set_terms( $post_id ) {
    global $post;
    // Dont run this on the state-year-report post type
    if ( !is_singular('state-year-report') ) {
        // Get the ACF Byline Array
        $field_data = get_field('byline_array');
        $persons = array();
        // Check to see if this post has any authors if it does not, proceed with auto selection
        // we do this check becuase we don't want to continue to autoselect if they've removed the autoselect author in one off cases.
        // Also we're presuming to autoselect as a function only if no authors are present.
        if ( empty($field_data) ) {
            // Get the author information
            $author_slug = get_the_author_meta( 'user_login', $post->post_author );
            $author_data = get_term_by( 'slug', $author_slug, 'person' );
            $author_id = $author_data->term_id;
            // Check for an author byline override. Basically this is a intern function.
            $default_byline_override = get_user_meta( $post->post_author, '_default_byline', true );
            $default_byline = get_term_by( 'id', $default_byline_override, 'person' );
            $default_byline_id = $default_byline->term_id;

            // If a override is present use that first
            if ( !empty($default_byline_override) && false === get_field( 'disable_auto_author_select','options' ) ) {
                $persons[] = $default_byline_id;
            // If a person exists with the slug of the author then auto add it.
            } elseif ( term_exists( $author_slug, 'person' ) && false === get_field( 'disable_auto_author_select','options' ) ) {
                $persons[] = $author_id;
            }

        }

        // Go through the persons from the field add them to the persons array.
        foreach ($field_data as $data) {
            $persons[] = $data;
        }
        // Go back and update the field with the new data
        update_field('field_53f38cd042a42', $persons);
        // Set this posts person terms to the persons array
        wp_set_post_terms( $post_id, $persons, 'person', false );
    }
}

// run before ACF saves the $_POST['fields'] data
add_action('acf/save_post', 'cap_byline_array_set_terms', 20);

/**
 * Returns the posts list of authors based on various criteria.
 * @param $post_id is the post id, we should pass this in always.
 * @param $disable_link defaults to false, if set to true the output is only the name
 * @param $as_array defaults to false, if set to true returns as an array of persons either as slug or name
 * @param $return_slugs defaults to true, if $as_array is set to true return person slugs if set to false then return names
 * @param $byline_field defaults to byline_array. This allows you to create identical other fields such as "with" for American Progress and check for that new field.
 */
function get_cap_authors($post_id, $disable_link=false, $as_array=false, $return_slugs=true, $byline_field='byline_array') {
    $people = get_field($byline_field, $post_id);
	$byline_array = array();
	
    if ( !empty($people) ) {
        // let's setup an array to organize these people based on some conditions below
        foreach ( $people as $person ) {
            $get_byline = get_term_by( 'id', $person, 'person' );
            $byline_array[] = $get_byline->slug;
        }
    }

    // Check for the display function, if as_array is set to true then just return the array...
    // if not then proceed with the listing function.
    if ( true == $as_array ) {

        if ( true == $return_slugs ) {
            return $byline_array;
        } else {
            // Because we're setting to return as an array but not to return slugs we'll return the full name of the persons in an array.
            $return_names_array = array();
            foreach ( $byline_array as $author ) {
                $data = get_term_by( 'slug', $author, 'person', 'ARRAY_A');
                $name = $data['name'];
                $return_names_array[] = $name;
            }
            return $return_names_array;
        }

    } else {
        // We're compiling a byline list of the authors of this post
        $i = 1;
        $total_num_people = count($byline_array);
        $output = '';
        if (!empty($byline_array)) {
            foreach ( $byline_array as $author ) {
                $data = get_term_by( 'slug', $author, 'person', 'ARRAY_A');
                $name = $data['name'];
                $slug = $data['slug'];
                $id = $data['term_id'];
                $person_twitter_handle = get_field( 'person_twitter_handle', 'person_'.$id );

                //If disable links is set to true or if this person specifically has no linked bio, display name only.
                if ( true == $disable_link || false == get_field('person_is_linked', 'person_'.$id ) ) {
                    $output .= $name;
                } else {
                    $output .= '<a href="/?person='.$slug.'">'.$name.'</a>';
                    // Checks for single instance of any post type, not just Wordpress defaults
                    if ( !empty($person_twitter_handle) && is_singular( get_post_type() ) ) {
                        $output .= "<a href=\"https://twitter.com/intent/user?screen_name=".$person_twitter_handle."\"><img src=\"" .content_url(). "/plugins/cap-byline/bird_blue_16.png\" class=\"twitter-bird\"></a>";
                    }
                }

                if ( $total_num_people > 1 && $total_num_people <= 2 ) {
                    if ( $i != $total_num_people ) {
                        if (has_filter('cap_byline_and')) {
                            $output .= apply_filters('cap_byline_and', $content);
                        } else {
                            $output .= ' & ';
                        }
                    }
                } elseif ( $total_num_people > 2 ) {
                    if ( $i != $total_num_people ) {
                        $output .= ', ';
                    }
                }
                $i++;
            }
        } else {
            /**
             * @todo Replace with wp_error
             */
            $output = "<!--Found No Data, Check CAP Byline Plugin-->";
        }
        return $output;
    }
}

/**
 * Display the list of authors along with the post time.
 */
function get_cap_byline($type, $post_id) {
    // If is a single post page display the time, otherwise just display only the date.
    if (is_singular() && true===get_field( 'global_display_post_time', 'options')) {
        $time_format = 'F j, Y \a\t g:i a';
    } else {
        $time_format = 'F j, Y';
    }

    $time_string = '<time class="published" datetime="%1$s">%2$s</time>';

    // if the post time is not within one hour of the updated time...
    if ( get_the_modified_time('jnyH') != get_the_time('jnyH') && true == get_post_meta( $post_id, 'cap_enable_updated_time', true ) && false == get_field( 'global_disable_update_time', 'options' ) ) {
        $time_string .= '&nbsp;<time class="updated" datetime="%3$s">Updated: %4$s</time>';
    }

    $time_string = sprintf( $time_string,
        esc_attr( get_the_date($time_format, $post_id) ), //%1$s
        esc_html( get_the_date($time_format, $post_id) ), //%2$s
        esc_attr( get_the_modified_date($time_format, $post_id) ), //%3$s
        esc_html( get_the_modified_date($time_format, $post_id) ) //%4$s
    );

    $markup = '';
    if ( 'dateonly' == $type ) {
         $markup .= '<span class="posted-on">'.$time_string.'</span>';
    } elseif ( 'bylineonly' == $type ) {
        $markup .= ' by '.get_cap_authors($post_id, null, null, null);
    } else {

        if( has_filter('cap_full_byline_open') ) {
            $markup .= apply_filters('cap_full_byline_open', $content);
        }

        if ( has_filter('cap_full_byline_persons') ) {
            $markup .= apply_filters('cap_full_byline_persons', $content, $post_id);
        } else {
            $markup .= '<span class="byline"> by ';
            if ('nolinks' == $type) {
                $markup .= get_cap_authors($post_id, true, null, null);
            } else {
                $markup .= get_cap_authors($post_id, null, null, null);
            }
            $markup .= '</span>';
        }

        if( has_filter('cap_full_byline_time') ) {
            $markup .= apply_filters('cap_full_byline_time', $content, $post_id);
        } else {
            $markup .= ' <span class="posted-on">Posted on '.$time_string.'</span>';
        }

        if( has_filter('cap_full_byline_close') ) {
            $markup .= apply_filters('cap_full_byline_close', $content);
        }
    }
    return $markup;
}

function cap_byline($type) {
    global $post;
    echo get_cap_byline($type, $post->ID);
}

if ( ! function_exists( 'cap_person_bio' ) ) {
    function cap_person_bio($style, $person = null) {
        /**
         * Note from Seth:
         * I'm not happy with the css trickery and markup I've produced here. Eventually cleanup. This
         * was a quick and dirty implentation that would work across sites.
         */
        if ( empty( $person ) ) {
            global $wp_query;
            $person = $wp_query->get_queried_object();
        }

        $person_photo = get_field( 'person_photo', 'person_'.$person->term_id );
        $person_photo_hi_res = get_field( 'person_photo_hi_res', 'person_'.$person->term_id );
        // This field is only being used by ThinkProgress post ACF migration.
        // The field itself is registered only in the TP theme in fields.php
        $person_photo_legacy = get_field( 'person_photo_legacy', 'person_'.$person->term_id );
        $person_title = get_field( 'person_title', 'person_'.$person->term_id );
        $person_email = get_field( 'person_email', 'person_'.$person->term_id );
        $person_twitter_handle = get_field( 'person_twitter_handle', 'person_'.$person->term_id );

        if (!empty($person_photo)) {
            $person_photo_output = wp_get_attachment_image( $person_photo, 'medium' );
            $person_photo_hi_res_output = wp_get_attachment_image_src( $person_photo_hi_res, 'full');
        }

        $markup = '<div class="person '.$style.'">';
        // if the "full" style is being displayed then add the person name and title atop the bio
        if ( 'full' == $style ) {
            $markup .= '<div class="person-title"><h1>'.$person->name.'<br><small>'.$person_title.'</small></h1></div>';
        }

        // begin the actual bio area
        $markup .= '<div class="person-bio">';
        if (!empty($person_photo)) {
            $markup .= '<div class="bio-pic">'.$person_photo_output;

            // optional hi res photo
            if (!empty($person_photo_hi_res_output)) {
                $markup .= '<div class="bio-pic-hi-res"><a href="'.$person_photo_hi_res_output[0].'">Download hi-res</a></div>';
            }

            $markup .= '</div>';
        } elseif (!empty($person_photo_legacy)) {
            $markup .= '<div class="bio-pic"><img src="'.$person_photo_legacy.'"></div>';
        }
        // get the bio and add it to $markup
        $markup .= '<div class="bio">';
        if ( empty($style) ) {
            $markup .= '<strong>'.$person->name.'</strong> ';
        }
        $markup .= $person->description;
        // if either an email addy or twitter handle are present lets add a hard line break for spacing
        if ( !empty($person_email) || !empty($person_twitter_handle) ) {
            $markup .= '<div id="contact-button-seperator"></div>';
        }

        // if the bio has an email associated add a contact modal form to $markup also check the form ID is present
        if ( !empty($person_email) ) {
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
            $markup .= gravity_form( get_field('author_contact_form_id', 'options'), false, false, false, array('author_contact_email' => ''.$person->term_id.''), true, 25, false );
            gravity_form_enqueue_scripts( get_field('author_contact_form_id', 'options'), true );
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

function cap_byline_contact_form_email($entry, $form) {
    if ( $form["id"] == get_field('author_contact_form_id', 'options') ) {
        $email_to = get_field( 'person_email', 'person_'.$entry[4] );
        $email_from_first = $entry['1.3'];
        $email_from_last = $entry['1.6'];
        $email_from = $entry[2];
        $email_message = '<strong>You have a new message from '.$email_from_first.' '.$email_from_last.' at '.$email_from.'</strong><br><br>';
        $email_message .= $entry[3];
        if ( !empty($email_to) ){
            $mail_headers[] = 'From: "' . $email_from_first . ' ' . $email_from_last . ' via AmericanProgress" <no-reply@americanprogress.org>';
            $mail_headers[] = "Reply-To: $email_from";
            wp_mail( $email_to, 'You have a new message from '.$email_from_first.' '.$email_from_last.'', $email_message, $mail_headers );
        }
    }
}
add_action( "gform_after_submission", "cap_byline_contact_form_email", 10, 2 );

function cap_rss_other_author($name){
    global $post;
    if( is_feed() ){
        $authors = get_cap_authors($post->ID, true, true, false);
        if($authors !== NULL){
            $name = "";
            for($i = 0; $i < count($authors); $i++){
                $name .= $authors[$i] . ', ';
            }
            $name = rtrim($name, ', ');
        }
    }
    return $name;
}
add_filter( 'the_author', 'cap_rss_other_author' );
add_filter ( 'get_the_author_display_name', 'cap_rss_other_author' ) ;

/**
 * Returns the list of facebook ids for the authors (person terms) of the current post
 * @return array
 */
function get_the_cap_author_facebook_ids() {
    global $post;
    $facebook_ids = array();

    $authors = get_cap_authors( $post->ID, true, true, true );
    if ( ! empty( $authors ) && is_array( $authors ) ) {
        foreach ( $authors as $author ) {
            $data        = get_term_by( 'slug', $author, 'person', 'ARRAY_A' );
            $id          = $data['term_id'];
            $facebook_id = get_field( 'person_facebook_id', 'person_' . $id );
            if ( ! empty( $facebook_id ) ) {
                $facebook_ids[] = esc_attr( $facebook_id );
            }
        }
    }

    return $facebook_ids;
}

include $plugin_dir.'/migration.php';
