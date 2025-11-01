<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_activate() {
    plpm_register_preprint_post_type();

    // ===================================================================
    // 🚨 DIRECTORY CREATION LOGIC REMOVED! 🚨
    // This plugin now operates in FULLY DYNAMIC MODE
    // NO physical /paper/ directory creation needed
    // All URLs handled by WordPress rewrite rules
    // ===================================================================

        // デフォルトオプションを設定
    add_option('plpm_enable_sitemap', 1);
    add_option('plpm_show_author_info', 1);
    add_option('plpm_list_page_title', '投稿論文一覧');
    add_option('plpm_citation_formats', array('bibtex', 'ris', 'redif', 'html_basic', 'html_abstract', 'plain_basic', 'json'));

    // デザインテーマのデフォルト値（デフォルトテーマ）
    add_option('plpm_design_theme', 'default');
    add_option('plpm_primary_color', '#007cba');
    add_option('plpm_secondary_color', '#f8f9fa');
    add_option('plpm_border_color', '#ddd');
    add_option('plpm_bg_color', '#ffffff');
    add_option('plpm_text_color', '#333333');
    add_option('plpm_accent_color', '#0073aa');

    // 既存の間違った値を修正
    $current_title = get_option('plpm_list_page_title');
    if ($current_title === '投稿論文一覧') {
        update_option('plpm_list_page_title', '投稿論文一覧');
    }

    flush_rewrite_rules();

    // ===================================================================
    // 🚨 STATIC FILE GENERATION CALLS REMOVED! 🚨
    // The following legacy functions have been disabled:
    // - plpm_generate_list_page() [created static index.html]
    // - plpm_generate_sitemap() [created static sitemap.xml]
    //
    // Dynamic system handles all content generation automatically.
    // ===================================================================
}
function plpm_deactivate() {
    plpm_delete_sitemap();
    flush_rewrite_rules();
}

function plpm_uninstall() {
    // オプションを削除
    delete_option('plpm_enable_sitemap');
    delete_option('plpm_show_author_info');
    delete_option('plpm_list_page_title');
    delete_option('plpm_citation_formats');

    // デザインテーマ関連オプションを削除
    delete_option('plpm_design_theme');
    delete_option('plpm_primary_color');
    delete_option('plpm_secondary_color');
    delete_option('plpm_border_color');
    delete_option('plpm_bg_color');
    delete_option('plpm_text_color');
    delete_option('plpm_accent_color');
}

