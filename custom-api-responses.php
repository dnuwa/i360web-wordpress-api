<?php

/**
 * Plugin Name:       Custom Response
 * Plugin URI:        https://infinitum360.co
 * Description:       Customise wp responses.
 * Version:           1.0
 * Author:            daniel
 * Author URI:        https://infinitum360.co
 */

function custom_rest_cors() {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', function( $value ) {
		header( 'Access-Control-Allow-Origin: *' );
		header( 'Access-Control-Allow-Methods: GET' );
		header( 'Access-Control-Allow-Credentials: true' );
		header( 'Access-Control-Expose-Headers: Link', false );

		return $value;
	} );
}

function all_solutions(){

    $args = [
        'numberposts' => 9999,
        'post_type'  => 'solutions'
    ];

    $solutions = get_posts($args);

    $data = [];
    $i = 0;

    foreach($solutions as $solution){
        $data[$i]['id'] = $solution->ID;
        $data[$i]['title'] = $solution->post_title;
        $data[$i]['content'] = $solution->post_content;
        $data[$i]['slug'] = $solution->post_name;
        $data[$i]['sub_titles'] = explode (",", get_field('sub-titles', $solution->ID));
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($solution->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($solution->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($solution->ID, 'large');
        $i++;
    }

    return $data;
}

function all_testimonials(WP_REST_Request $request){
    
    $page_number = $request->get_param("pg_no");

    $args = [
        'posts_per_page' => 1,
        'post_type' => 'testimonials',
        'paged' => intval($page_number),
    ];

    $testimonials = new WP_Query($args);
    
    $data = [];
    $i = 0;

    foreach($testimonials->posts as $testimonial){
        $data[$i]['id'] = $testimonial->ID;
        $data[$i]['title'] = $testimonial->post_title;
        $data[$i]['content'] = $testimonial->post_content;
        $data[$i]['slug'] = $testimonial->post_name;
        $data[$i]['author_name'] = get_field('name', $testimonial->ID);
        $data[$i]['job_title'] = get_field('job-title', $testimonial->ID);
        $data[$i]['company'] = get_field('company', $testimonial->ID);
        $data[$i]['project'] = get_field('project', $testimonial->ID);
        $data[$i]['client'] = get_field('client', $testimonial->ID);
        $data[$i]['case_sturdy'] = get_field('case-study', $testimonial->ID);
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($testimonial->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($testimonial->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($testimonial->ID, 'large');
        $i++;
    }

    $data['total_pages'] = $testimonials->max_num_pages;
    $data['total_posts'] = $testimonials->found_posts;
    $data['page_number'] = intval($page_number);

    return $data;
}

//unpaginated
function all_testimonials_unpaginated( $request ){

    $search_param = $request->get_param("search");

    $args = [
        'numberposts' => 9999,
        'post_type' => 'testimonials',
        's' => $search_param,
    ];

    $testimonials = get_posts($args);

    $data = [];
    $i = 0;

    foreach($testimonials as $testimonial){

        $data[$i]['id'] = $testimonial->ID;
        $data[$i]['title'] = $testimonial->post_title;
        $data[$i]['content'] = $testimonial->post_content;
        $data[$i]['slug'] = $testimonial->post_name;
        $data[$i]['author_name'] = get_field('name', $testimonial->ID);
        $data[$i]['job_title'] = get_field('job-title', $testimonial->ID);
        $data[$i]['company'] = get_field('company', $testimonial->ID);
        $data[$i]['project'] = get_field('project', $testimonial->ID);
        $data[$i]['client'] = get_field('client', $testimonial->ID);
        $data[$i]['case_sturdy'] = get_field('case-study', $testimonial->ID);
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($testimonial->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($testimonial->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($testimonial->ID, 'large');
        $i++;
    }

    return $data;
}

function slug_show_author_meta($author_id) {

    $user_data = get_userdata($author_id);
    $array_data = (array)($user_data->data);

    // prevent user enumeration.
    unset($array_data['user_login']);
    unset($array_data['user_pass']);
    unset($array_data['user_activation_key']);
    unset($array_data['user_url']);
    unset($array_data['ID']);

    return array_filter($array_data);
 }

 //paginated blogs
function all_blogs(WP_REST_Request $request){

    $page_number = $request->get_param("pg_no");

    $args = [
        'posts_per_page' => 2,
        'post_type' => 'blogs',
        'paged' => intval($page_number),
    ];

    $blogs = new WP_Query($args);

    $data = [];
    $i = 0;

    foreach($blogs->posts as $blog){
        $data[$i]['id'] = $blog->ID;
        $data[$i]['title'] = $blog->post_title;
        $data[$i]['content'] = $blog->post_content;
        $data[$i]['author'] = slug_show_author_meta($blog->post_author);
        $data[$i]['author_avatar'] = get_avatar_url($blog->post_author);
        $data[$i]['slug'] = $blog->post_name;
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($blog->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($blog->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($blog->ID, 'large');
        $i++;
    }

    $data['total_pages'] = $blogs->max_num_pages;
    $data['total_posts'] = $blogs->found_posts;
    $data['page_number'] = intval($page_number);

    return $data;
}

 //blogs by tag
 function all_blogs_by_tag(WP_REST_Request $request){

    $tag = $request->get_param("tag");

    $args = [
        'post_type' => 'blogs',
        'tag' => $tag,
        'orderby' => 'id',
        'order' => 'ASC'
    ];

    $blogs = new WP_Query($args);

    $data = [];
    $i = 0;

    foreach($blogs->posts as $blog){
        $data[$i]['id'] = $blog->ID;
        $data[$i]['title'] = $blog->post_title;
        $data[$i]['content'] = $blog->post_content;
        $data[$i]['author'] = slug_show_author_meta($blog->post_author);
        $data[$i]['author_avatar'] = get_avatar_url($blog->post_author);
        $data[$i]['slug'] = $blog->post_name;
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($blog->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($blog->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($blog->ID, 'large');
        $i++;
    }

    return $data;
}

// get single blog
function get_blog($slug){

    $args = [
        'name' => $slug['slug'],
        'post_type' => 'blogs',
    ];

    $blog = get_posts($args);

    $source   = ( $gmt ) ? 'gmt' : 'local';
    $datetime = get_post_datetime( $blog[0], 'modified', $source );

    $data['id'] = $blog[0]->ID;
    $data['title'] = $blog[0]->post_title;
    $data['content'] = $blog[0]->post_content;
    $data['author'] = slug_show_author_meta($blog[0]->post_author);
    $data['author_avatar'] = get_avatar_url($blog[0]->post_author);
    $data['slug'] = $blog[0]->post_name;
    $data['modified'] = $datetime;
    $data['caption'] = get_the_post_thumbnail_caption($blog[0]);
    $data['tags'] = get_the_terms($blog[0]->ID, 'post_tag');
    $data['category'] = get_the_terms( $blog[0]->ID, 'category' );
    $data['read_time'] = get_post_custom_values('read_time', $blog[0]->ID);
    $data['featured_image']['thumbnail'] = get_the_post_thumbnail_url($blog[0]->ID, 'thumbnail');
    $data['featured_image']['medium'] = get_the_post_thumbnail_url($blog[0]->ID, 'medium');
    $data['featured_image']['large'] = get_the_post_thumbnail_url($blog[0]->ID, 'large');

    return $data;
}



// returns all or filtered
function all_blogs_unpaged( $request ){

    $search_param = $request->get_param("search");

    $args = [
        'numberposts' => 9999,
        'post_type' => 'blogs',
        's' => $search_param,
    ];

    $blogs = get_posts($args);

    $data = [];
    $i = 0;

    foreach($blogs as $blog){

        $data[$i]['id'] = $blog->ID;
        $data[$i]['title'] = $blog->post_title;
        $data[$i]['content'] = $blog->post_content;
        $data[$i]['author'] = slug_show_author_meta($blog->post_author);
        $data[$i]['author_avatar'] = get_avatar_url($blog->post_author);
        $data[$i]['slug'] = $blog->post_name;
        $data[$i]['modified'] = get_post_datetime( $blog, 'modified', ( $gmt ) ? 'gmt' : 'local' );
        $data[$i]['caption'] = get_the_post_thumbnail_caption($blog);
        $data[$i]['tags'] = get_the_terms($blog->ID, 'post_tag');
        $data[$i]['category'] = get_the_terms( $blog->ID, 'category' );
        $data[$i]['read_time'] = get_post_custom_values('read_time', $blog->ID);
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($blog->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($blog->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($blog->ID, 'large');
        $i++;
    }

    return $data;
}

function all_products(){

    $args = [
        'posts_per_page' => 2,
        'post_type' => 'products',
        'taxonomy' => 3,
        'paged' => 1,
    ];

    $products = new WP_Query($args);
    
    $data = [];
    $i = 0;

    $datax = [];
    $j = 0;

    foreach($products->posts as $product){
        $data[$i]['id'] = $product->ID;
        $data[$i]['description'] = $product->post_content;
        $data[$i]['slug'] = $product->post_name;
        $data[$i]['title'] = $product->post_title;
        // $data[$i]['sub_products'] = get_terms('sub_products');
        $sub_products = get_the_terms( $product->ID, 'sub_products');
        // $data[$i]['sub_products'] = $sub_product;

        $terms_string = join(', ', wp_list_pluck($sub_products , 'term_id'));
        // $data[$i]['sub_product_image'] = intval($terms_string);

        // $data[$i]['term_id'] = wp_list_pluck($sub_products , 'term_id');
        
        
        $data[$i]['featured_image']['thumbnail'] = get_the_post_thumbnail_url($product->ID, 'thumbnail');
        $data[$i]['featured_image']['medium'] = get_the_post_thumbnail_url($product->ID, 'medium');
        $data[$i]['featured_image']['large'] = get_the_post_thumbnail_url($product->ID, 'large');

        if($sub_products){
            foreach($sub_products as $product){
                // $data[$i]['xxxxxx-xxx'] = get_term_meta( $product->term_id, 'sub_product_featured_image', true );
                $datax[$j]['term_is'] = $product->term_id;
                $datax[$j]['name'] = $product->name;
                $datax[$j]['description'] = $product->description;
                $datax[$j]['image'] = get_field('sub_product_featured_image', $product->term_id);
                $j++;
            }
        }

        $data[$i]['sub_products'] = $datax;
        
        $i++;
    }

    // $data['total_pages'] = $products->max_num_pages;
    // $data['total_posts'] = $products->found_posts;

    return $data;
}

//get tags
function all_tags(){
    $tags = get_tags();
    return $tags;
}



// Enable the option show in rest
add_filter( 'acf/rest_api/field_settings/show_in_rest', '__return_true' );

// Enable the option edit in rest
add_filter( 'acf/rest_api/field_settings/edit_in_rest', '__return_true' );

add_action( 'rest_api_init', 'custom_rest_cors', 15 );
add_action('rest_api_init', function(){
    
    register_rest_route('web-api/v1', 'solutions', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_solutions',
    ]);
    
    //paginated testimonials
    register_rest_route('web-api/v1', 'testimonials/(?P<pg_no>\d+)', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_testimonials',
    ]);
    
    //unpaginated testimonials
    register_rest_route('web-api/v1', 'testimonials', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_testimonials_unpaginated',
    ]);

    register_rest_route('web-api/v1', 'blogs/(?P<pg_no>\d+)', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_blogs',
    ]);
    
    //blogs by slug
    register_rest_route('web-api/v1', 'blog/(?P<slug>[a-zA-Z0-9-]+)', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'get_blog',
    ]);

    //blogs by tag
    register_rest_route('web-api/v1', 'blogs/tag/(?P<tag>[a-zA-Z0-9-]+)', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_blogs_by_tag',
    ]);

    //all blogs unpaged
    register_rest_route('web-api/v1', 'blogs', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_blogs_unpaged',
    ]);

    register_rest_route('web-api/v1', 'products', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_products',
    ]);

    register_rest_route('web-api/v1', 'tags', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'all_tags',
    ]);
 });
