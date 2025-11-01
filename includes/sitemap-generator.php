<?php
/**
 * ===================================================================
 * 🚨 STATIC SITEMAP GENERATION FORBIDDEN! 🚨
 * ===================================================================
 *
 * DO NOT generate static sitemap.xml files in /paper/ directory!
 * This will create physical directories and cause permission errors.
 *
 * USE ONLY: WordPress standard sitemap integration (wp-sitemap.xml)
 * All sitemap functions should integrate with wp_sitemaps API.
 *
 * NO wp_mkdir_p() or file_put_contents() for sitemap files!
 * ===================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_generate_sitemap() {
    // 設定値をチェック
    if (!plpm_get_option('enable_sitemap', 1)) {
        return false; // サイトマップ生成が無効の場合は処理しない
    }

    $output_dir_path = PLPM_PAPER_DIR;
    $output_file_path = $output_dir_path . PLPM_SITEMAP_FILENAME;
    $site_url = home_url('/');
    $list_page_url = home_url('/paper/');

    // 動的テンプレートシステムでは静的ファイル生成は不要
    // 代わりにWordPressの標準サイトマップに統合する
    return true; // 動的システムでは常に成功とする
}

/**
 * WordPressの標準サイトマップにプラグインページを追加
 */
function plpm_add_to_wordpress_sitemap($provider, $name) {
    if (!plpm_get_option('enable_sitemap', 1)) {
        return $provider; // サイトマップ生成が無効の場合は何もしない
    }

    if ($name === 'preprints') {
        return new PLPM_Sitemap_Provider();
    }
    return $provider;
}
add_filter('wp_sitemaps_add_provider', 'plpm_add_to_wordpress_sitemap', 10, 2);

/**
 * WordPress標準サイトマップへ独自プロバイダを登録
 */
function plpm_register_sitemap_providers( $providers ) {
    if (!plpm_get_option('enable_sitemap', 1)) {
        return $providers;
    }

    // すでに登録済みでなければ追加
    if ( ! isset( $providers['preprints'] ) ) {
        $providers['preprints'] = new PLPM_Sitemap_Provider();
    }

    return $providers;
}
add_filter( 'wp_sitemaps_register_providers', 'plpm_register_sitemap_providers' );

/**
 * カスタムサイトマッププロバイダー
 */
class PLPM_Sitemap_Provider extends WP_Sitemaps_Provider {

    public function get_name() {
        return 'preprints';
    }

    public function get_url_list($page_num, $object_subtype = '') {
        $url_list = array();

        // 一覧ページを追加
        $url_list[] = array(
            'loc' => home_url('/paper/'),
            'lastmod' => current_time('mysql', true),
            'priority' => 0.9,
            'changefreq' => 'daily'
        );

        // 個別ページを追加
        $args = array(
            'post_type' => 'preprint_page',
            'post_status' => 'publish',
            'posts_per_page' => wp_sitemaps_get_max_urls($this->get_name()),
            'paged' => $page_num,
            'orderby' => 'modified',
            'order' => 'DESC',
            'meta_query' => array(
                'relation' => 'AND',
                array('key' => '_plpm_pdf_url', 'compare' => 'EXISTS'),
            )
        );

        $preprint_query = new WP_Query($args);
        if ($preprint_query->have_posts()) {
            while ($preprint_query->have_posts()) {
                $preprint_query->the_post();
                $post = get_post();
                $url_list[] = array(
                    'loc' => home_url('/paper/' . $post->ID . '/'),
                    'lastmod' => get_post_modified_time('c', true, $post->ID),
                    'priority' => 0.8,
                    'changefreq' => 'weekly'
                );
            }
            wp_reset_postdata();
        }

        return $url_list;
    }

    public function get_max_num_pages($object_subtype = '') {
        $args = array(
            'post_type' => 'preprint_page',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => array(
                'relation' => 'AND',
                array('key' => '_plpm_pdf_url', 'compare' => 'EXISTS'),
            )
        );
        $query = new WP_Query($args);
        return (int) ceil($query->found_posts / wp_sitemaps_get_max_urls($this->get_name()));
    }
}
/**
 * LEGACY FUNCTION - DISABLED FOR DYNAMIC SYSTEM
 * This function attempted to delete static sitemap files
 */
function plpm_delete_sitemap() {
    // ===================================================================
    // 🚨 FUNCTION DISABLED - NO STATIC FILES TO DELETE 🚨
    // This function tried to delete static sitemap.xml files.
    // Dynamic system uses WordPress sitemap API - no files to delete.
    // ===================================================================
    do_action('plpm_sitemap_deletion_success');
    return true; // Always return success - no files to delete
}
