<?php
add_action('init', 'hc_portfolio_init');
function hc_portfolio_init() 
{
  $labels = array(
    'name' => _x('Portfolio Media', 'post type general name'),
    'singular_name' => _x('Portfolio Item', 'post type singular name'),
    'add_new' => _x('Add New', 'Portfolio Item'),
    'add_new_item' => __('Add New Portfolio Item'),
    'edit_item' => __('Edit Portfolio Item'),
    'new_item' => __('New Portfolio Item'),
    'view_item' => __('View Portfolio Item'),
    'search_items' => __('Search Portfolio Items'),
    'not_found' =>  __('No Portfolios Items found'),
    'not_found_in_trash' => __('No Portfolio Items found in Trash'), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => true,
    'menu_position' => null,
    'supports' => array('title','thumbnail','page-attributes')
  ); 
  register_post_type('hc_portfolio',$args);
}

//add filter to insure the text Portfolio, or portfolio, is displayed when user updates a portfolio 
add_filter('post_updated_messages', 'portfolio_updated_messages');
function portfolio_updated_messages( $messages ) {

  $messages['portfolio'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Portfolio Item updated. <a href="%s">View Portfolio</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Portfolio Item updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Portfolio Item restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Portfolio Item published. <a href="%s">View Portfolio Item</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Portfolio saved.'),
    8 => sprintf( __('Portfolio Item submitted. <a target="_blank" href="%s">Preview Portfolio Item</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Portfolio Item scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Portfolio Item</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Portfolio Item draft updated. <a target="_blank" href="%s">Preview Portfolio Item</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}
