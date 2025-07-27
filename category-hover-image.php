<?php
/*
Plugin Name: Category Hover Image
Description: Display WooCommerce categories with hover image change using shortcode [category_image_list].
Version: 1.0
Author: Hamza Saleem
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Register Shortcode
function chi_render_category_list() {
    if ( ! function_exists('get_woocommerce_term_meta') ) {
        return '<p>WooCommerce is not active.</p>';
    }

    // Fetch WooCommerce product categories
    $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0 // Only top-level categories
    ) );

    if ( empty($categories) ) {
        return '<p>No categories found.</p>';
    }

    // Prepare category images and list
    $output = '<div class="chi-container" style="display:flex;gap:20px;">';

    // Left Column - Default image is first category
    $first_cat_id = $categories[0]->term_id;
    $first_image = wp_get_attachment_url( get_term_meta( $first_cat_id, 'thumbnail_id', true ) );

    if ( ! $first_image ) {
        $first_image = wc_placeholder_img_src(); // Fallback
    }

    $output .= '<div class="chi-left-col" style="flex:1;">
                    <img id="chi-main-image" src="'.esc_url($first_image).'" style="width:100%;max-width:400px;" />
                </div>';

    // Right Column - Category list
    $output .= '<div class="chi-right-col" style="flex:1;"><ul style="list-style:none;padding:0;">';
    foreach ( $categories as $category ) {
        $image_url = wp_get_attachment_url( get_term_meta( $category->term_id, 'thumbnail_id', true ) );
        if ( ! $image_url ) {
            $image_url = wc_placeholder_img_src();
        }
        $output .= '<li class="chi-cat-item" data-image="'.esc_url($image_url).'" style="margin:10px 0;cursor:pointer;">'
                    .esc_html($category->name).'</li>';
    }
    $output .= '</ul></div>';

    $output .= '</div>';

    // Add inline JS for hover functionality
    $output .= '<script>
        document.addEventListener("DOMContentLoaded", function(){
            const mainImage = document.getElementById("chi-main-image");
            const catItems = document.querySelectorAll(".chi-cat-item");
            catItems.forEach(item => {
                item.addEventListener("mouseenter", function(){
                    mainImage.src = this.dataset.image;
                });
            });
        });
    </script>';

    return $output;
}
add_shortcode( 'category_image_list', 'chi_render_category_list' );
