<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_register_preprint_post_type() {
    $labels = [
        'name'                  => _x( '投稿論文一覧', 'Post type general name', PLPM_TEXT_DOMAIN ),
        'singular_name'         => _x( 'Preprint Landing Page', 'Post type singular name', PLPM_TEXT_DOMAIN ),
        'menu_name'             => _x( 'Kashiwazaki SEO Preprint Landing Page Generator', 'Admin Menu text', PLPM_TEXT_DOMAIN ), // プラグイン名に統一
        'name_admin_bar'        => _x( 'Preprint Landing Page', 'Add New on Toolbar', PLPM_TEXT_DOMAIN ),
        'add_new'               => __( 'Add New', PLPM_TEXT_DOMAIN ),
        'add_new_item'          => __( 'Add New Preprint Landing Page', PLPM_TEXT_DOMAIN ),
        'new_item'              => __( 'New Preprint Landing Page', PLPM_TEXT_DOMAIN ),
        'edit_item'             => __( 'Edit Preprint Landing Page', PLPM_TEXT_DOMAIN ),
        'view_item'             => __( 'View Generated HTML', PLPM_TEXT_DOMAIN ),
        'all_items'             => __( 'すべての投稿論文', PLPM_TEXT_DOMAIN ),
        'search_items'          => __( '投稿論文を検索', PLPM_TEXT_DOMAIN ),
        'parent_item_colon'     => __( 'Parent Preprint Landing Page:', PLPM_TEXT_DOMAIN ),
        'not_found'             => __( '投稿論文が見つかりませんでした。', PLPM_TEXT_DOMAIN ),
        'not_found_in_trash'    => __( 'ゴミ箱に投稿論文がありません。', PLPM_TEXT_DOMAIN ),
        'featured_image'        => _x( 'Preprint Landing Page Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', PLPM_TEXT_DOMAIN ),
        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', PLPM_TEXT_DOMAIN ),
        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', PLPM_TEXT_DOMAIN ),
        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', PLPM_TEXT_DOMAIN ),
        'archives'              => _x( 'Preprint Landing Page Archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', PLPM_TEXT_DOMAIN ),
        'insert_into_item'      => _x( 'Insert into preprint page', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', PLPM_TEXT_DOMAIN ),
        'uploaded_to_this_item' => _x( 'Uploaded to this preprint page', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', PLPM_TEXT_DOMAIN ),
        'filter_items_list'     => _x( 'Filter preprint pages list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', PLPM_TEXT_DOMAIN ),
        'items_list_navigation' => _x( 'Preprint pages list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', PLPM_TEXT_DOMAIN ),
        'items_list'            => _x( 'Preprint pages list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', PLPM_TEXT_DOMAIN ),
    ];
    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => false,
        'rewrite'            => ['slug' => 'paper', 'with_front' => false],
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'capabilities'       => array(
            'publish_posts'       => 'publish_posts',
            'edit_posts'          => 'edit_posts',
            'edit_others_posts'   => 'edit_others_posts',
            'delete_posts'        => 'delete_posts',
            'delete_others_posts' => 'delete_others_posts',
            'read_private_posts'  => 'read_private_posts',
            'edit_post'           => 'edit_post',
            'delete_post'         => 'delete_post',
            'read_post'           => 'read_post',
        ),
        'has_archive'        => false, // 動的テンプレート処理を使用するため無効化
        'hierarchical'       => false,
        'menu_position'      => 81,
        'supports'           => [ 'title', 'excerpt' ],
        'menu_icon'          => 'dashicons-media-document',
        'show_in_rest'       => false,
        'can_export'         => true,
        'delete_with_user'   => false,
    ];
    register_post_type( 'preprint_page', $args );
}
