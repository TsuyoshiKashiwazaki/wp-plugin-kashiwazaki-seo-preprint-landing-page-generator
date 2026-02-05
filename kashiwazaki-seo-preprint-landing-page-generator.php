<?php
/**
 * Plugin Name: Kashiwazaki SEO Preprint Landing Page Generator
 * Plugin URI: https://www.tsuyoshikashiwazaki.jp
 * Description: Generates SEO-optimized dynamic landing pages for academic preprints via WordPress rewrite rules (NO static files)
 * Version: 1.0.1
 * Author: 柏崎剛 (Tsuyoshi Kashiwazaki)
 * Author URI: https://www.tsuyoshikashiwazaki.jp/profile/
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kashiwazaki-preprint-landing-generator
 * Domain Path: /languages
 *
 * ===================================================================
 * 🚨 CRITICAL NOTICE FOR DEVELOPERS AND AI ASSISTANTS 🚨
 * ===================================================================
 *
 * ❌ NEVER CREATE PHYSICAL DIRECTORIES OR FILES ❌
 *
 * This plugin operates in FULLY DYNAMIC MODE:
 * - NO physical /paper/ directory creation (wp_mkdir_p forbidden)
 * - NO static HTML file generation (file_put_contents forbidden)
 * - NO static sitemap.xml file creation (use WordPress sitemap API)
 * - NO file system writes for /paper/ content
 *
 * ✅ ONLY USE WordPress Dynamic Systems:
 * - WordPress rewrite rules for URL routing (/paper/ URLs)
 * - WordPress template_redirect for content rendering
 * - WordPress standard sitemap integration (wp-sitemap.xml)
 * - Database-driven content management (Custom Post Type)
 *
 * If you see ANY code that creates directories with wp_mkdir_p()
 * or writes files with file_put_contents() for /paper/ paths,
 * REMOVE IT IMMEDIATELY - it will cause server permission errors.
 *
 * This plugin handles all /paper/ URLs dynamically through WordPress
 * template system ONLY. No physical files or directories needed.
 * ===================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PLPM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLPM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLPM_PAPER_DIR', ABSPATH . 'paper/' );
define( 'PLPM_TEXT_DOMAIN', 'kashiwazaki-preprint-landing-generator' );
define( 'PLPM_SITEMAP_FILENAME', 'paper-sitemap.xml' );

require_once PLPM_PLUGIN_DIR . 'includes/cpt-preprint.php';
require_once PLPM_PLUGIN_DIR . 'includes/metabox-preprint.php';
require_once PLPM_PLUGIN_DIR . 'includes/html-template.php';
require_once PLPM_PLUGIN_DIR . 'includes/html-list-template.php';
require_once PLPM_PLUGIN_DIR . 'includes/html-data-collector.php';
require_once PLPM_PLUGIN_DIR . 'includes/html-parts-generator.php';
require_once PLPM_PLUGIN_DIR . 'includes/citation-generator.php';
require_once PLPM_PLUGIN_DIR . 'includes/json-ld-generator.php';
require_once PLPM_PLUGIN_DIR . 'includes/html-generator.php';
require_once PLPM_PLUGIN_DIR . 'includes/html-list-generator.php';
require_once PLPM_PLUGIN_DIR . 'includes/file-deletion.php';
require_once PLPM_PLUGIN_DIR . 'includes/admin-columns.php';
require_once PLPM_PLUGIN_DIR . 'includes/activation.php';
require_once PLPM_PLUGIN_DIR . 'includes/sitemap-generator.php';
require_once PLPM_PLUGIN_DIR . 'includes/dynamic-template-handler.php';
require_once PLPM_PLUGIN_DIR . 'includes/admin-settings.php';

function plpm_load_textdomain_init() {
    load_plugin_textdomain( PLPM_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'plpm_load_textdomain_init' );

/**
 * プラグインオプションを取得する関数
 */
function plpm_get_option($option_name, $default = null) {
    $option_value = get_option('plpm_' . $option_name, $default);
    return $option_value;
}

register_activation_hook( __FILE__, 'plpm_activate' );
register_deactivation_hook( __FILE__, 'plpm_deactivate' );
register_uninstall_hook( __FILE__, 'plpm_uninstall' );

add_action( 'init', 'plpm_register_preprint_post_type' );
add_action( 'init', 'plpm_add_dynamic_rewrite_rules' );
add_action( 'init', 'plpm_force_flush_rewrite_rules' ); // 再有効化
add_action( 'query_vars', 'plpm_add_query_vars' );
// PDFと.htmlリダイレクトを最優先で処理（parse_request アクション - wpより前）
add_action( 'parse_request', 'plpm_handle_parse_request_redirects', -99999 );
// 他プラグイン（例: Google XML Sitemaps）よりも早くハンドリングするため最優先で実行
// PDFリダイレクトのため、さらに優先度を上げる
add_action( 'template_redirect', 'plpm_template_redirect', -99999 );

/**
 * PDFと.htmlリダイレクトを parse_request で最優先処理
 */
function plpm_handle_parse_request_redirects( $wp ) {
    if ( isset($wp->query_vars['plpm_page_type']) ) {
        $page_type = $wp->query_vars['plpm_page_type'];

        if ( $page_type === 'pdf_redirect' && isset($wp->query_vars['plpm_post_id']) ) {
            $post_id = $wp->query_vars['plpm_post_id'];
            $post = get_post( $post_id );

            if ( $post && $post->post_type === 'preprint_page' && $post->post_status === 'publish' ) {
                require_once PLPM_PLUGIN_DIR . 'includes/html-data-collector.php';
                $pdf_url = plpm_get_actual_pdf_url( $post_id );

                // URLバリデーション（日本語文字を含むURLにも対応）
                if ( !empty($pdf_url) && (filter_var($pdf_url, FILTER_VALIDATE_URL) || strpos($pdf_url, 'http') === 0) ) {
                    // 直接ヘッダー送信
                    header( 'Location: ' . $pdf_url, true, 301 );
                    exit;
                }
            }
            // PDF URLが無効な場合は個別ページにリダイレクト
            header( 'Location: ' . home_url('/paper/' . $post_id . '/'), true, 301 );
            exit;

        } elseif ( $page_type === 'legacy_redirect' && isset($wp->query_vars['plpm_post_id']) ) {
            $post_id = $wp->query_vars['plpm_post_id'];
            // 直接ヘッダー送信
            header( 'Location: ' . home_url('/paper/' . $post_id . '/'), true, 301 );
            header( 'X-Redirect-By: PLPM-ParseRequest' );
            exit;
        }
    }
}

// PDFと.htmlリダイレクトの正規化を無効化
add_filter( 'redirect_canonical', 'plpm_disable_canonical_redirect', 1, 2 );
function plpm_disable_canonical_redirect( $redirect_url, $requested_url ) {
    $page_type = get_query_var('plpm_page_type');
    // pdf_redirect または legacy_redirect の場合は正規化リダイレクトを無効化
    if ( $page_type === 'pdf_redirect' || $page_type === 'legacy_redirect' ) {
        return false;
    }
    return $redirect_url;
}

// サイトマップは /paper/paper-sitemap.xml で動的生成

add_action( 'add_meta_boxes_preprint_page', 'plpm_add_meta_boxes' );
add_action( 'save_post_preprint_page', 'plpm_save_meta_box_data', 20 );
add_action( 'before_delete_post', 'plpm_delete_associated_files' );

// preprint_page の投稿URLを /paper/{ID}/ 形式に変換
add_filter( 'post_type_link', 'plpm_preprint_post_type_link', 10, 4 );
function plpm_preprint_post_type_link( $post_link, $post, $leavename, $sample ) {
    if ( $post->post_type === 'preprint_page' && $post->post_status === 'publish' ) {
        return home_url( '/paper/' . $post->ID . '/' );
    }
    return $post_link;
}

add_filter( 'manage_preprint_page_posts_columns', 'plpm_add_admin_columns' );
add_action( 'manage_preprint_page_posts_custom_column', 'plpm_custom_column_content', 10, 2 );

// パンくずリストプラグイン対応：/paper/ の title を提供
add_filter('document_title_parts', 'plpm_set_archive_title', 20);
function plpm_set_archive_title($title_parts) {
    if (get_query_var('plpm_page_type') === 'list') {
        $title_parts['title'] = plpm_get_option('list_page_title', '投稿論文一覧');
    }
    return $title_parts;
}

// 抜粋列は表示されるようになりました（Abstractが自動的に抜粋にコピーされます）

// 投稿更新メッセージにプレプリントページURLを追加
add_filter( 'post_updated_messages', 'plpm_custom_post_updated_messages' );

function plpm_admin_scripts($hook_suffix) {
    $screen = get_current_screen();
    if ( $screen && $screen->post_type === 'preprint_page' && ( $hook_suffix === 'post.php' || $hook_suffix === 'post-new.php' ) ) {
        wp_enqueue_media();
        wp_enqueue_script(
            'plpm-media-uploader',
            PLPM_PLUGIN_URL . 'js/media-uploader.js',
            ['jquery'],
            filemtime(PLPM_PLUGIN_DIR . 'js/media-uploader.js'),
            true
        );
        wp_localize_script(
            'plpm-media-uploader',
            'plpm_media_vars',
            [
                'modalTitle'  => __( 'Select PDF File', PLPM_TEXT_DOMAIN ),
                'modalButton' => __( 'Use this PDF', PLPM_TEXT_DOMAIN ),
                'clearButton' => __( 'Clear PDF', PLPM_TEXT_DOMAIN ),
            ]
        );

        // 公開ボタン強制表示のJavaScriptを削除（メモリ不足の原因）
    }

    // 抜粋列のスタイリング（表示するようになったため、スタイルを調整）
    if ( $screen && $screen->post_type === 'preprint_page' && $hook_suffix === 'edit.php' ) {
        wp_add_inline_style( 'wp-admin', '
            .wp-list-table .column-excerpt {
                width: 20%;
            }
            .wp-list-table td.excerpt.column-excerpt {
                word-wrap: break-word;
            }
        ' );
    }
}
add_action('admin_enqueue_scripts', 'plpm_admin_scripts');

// 管理画面のheadで抜粋列のスタイルを調整
function plpm_excerpt_column_styles() {
    $screen = get_current_screen();
    if ( $screen && $screen->post_type === 'preprint_page' && $screen->base === 'edit' ) {
        ?>
        <style type="text/css">
            /* 抜粋列のスタイル調整 */
            .wp-list-table .column-excerpt {
                width: 20%;
            }
            .wp-list-table td.excerpt.column-excerpt {
                word-wrap: break-word;
                max-width: 300px;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'plpm_excerpt_column_styles');

/**
 * 一時的なリライトルールフラッシュ（デバッグ用）
 */
function plpm_force_flush_rewrite_rules() {
    // サイトマップアクセス確認用の一時的な処理
    static $flushed = false;
    if (!$flushed && is_admin()) {
        flush_rewrite_rules();
        $flushed = true;
        // デバッグログ削除済み
    }
}

// カスタム投稿タイプの権限変更処理を削除（メモリ不足の原因）

// 権限強制変更処理を削除（メモリ不足の原因）

// 投稿ボックス強制追加処理を削除（メモリ不足の原因）

// プラグイン設定を強制リセット（デバッグ用） - メモリ不足のため削除
// function plpm_debug_force_reset() は一時的に無効化

/**
 * プレプリントページの投稿更新メッセージをカスタマイズ
 */
function plpm_custom_post_updated_messages( $messages ) {
    global $post;

    if ( ! $post || $post->post_type !== 'preprint_page' ) {
        return $messages;
    }

    $post_id = $post->ID;
    $preprint_url = home_url( '/paper/' . $post_id . '/' );
    $list_url = home_url( '/paper/' );

    $messages['preprint_page'] = array(
        0  => '', // 未使用
        1  => sprintf(
            __( '投稿を更新しました。<a href="%s" target="_blank">プレプリントページを表示</a>', PLPM_TEXT_DOMAIN ),
            esc_url( $preprint_url )
        ),
        2  => __( 'カスタムフィールドを更新しました。', PLPM_TEXT_DOMAIN ),
        3  => __( 'カスタムフィールドを削除しました。', PLPM_TEXT_DOMAIN ),
        4  => sprintf(
            __( '投稿を更新しました。<br><strong>📄 プレプリントページ:</strong> <a href="%s" target="_blank">%s</a><br><strong>📋 一覧ページ:</strong> <a href="%s" target="_blank">%s</a>', PLPM_TEXT_DOMAIN ),
            esc_url( $preprint_url ),
            esc_html( $preprint_url ),
            esc_url( $list_url ),
            esc_html( $list_url )
        ),
        5  => isset( $_GET['revision'] )
            ? sprintf(
                __( '投稿をリビジョンから復元しました。', PLPM_TEXT_DOMAIN ),
                wp_post_revision_title( (int) $_GET['revision'], false )
            )
            : false,
        6  => sprintf(
            __( '✅ 投稿を公開しました！<br><strong>📄 プレプリントページ:</strong> <a href="%s" target="_blank">%s</a><br><strong>📋 一覧ページ:</strong> <a href="%s" target="_blank">%s</a>', PLPM_TEXT_DOMAIN ),
            esc_url( $preprint_url ),
            esc_html( $preprint_url ),
            esc_url( $list_url ),
            esc_html( $list_url )
        ),
        7  => sprintf(
            __( '投稿を保存しました。<br><strong>📄 プレプリントページ:</strong> <a href="%s" target="_blank">%s</a><br><strong>📋 一覧ページ:</strong> <a href="%s" target="_blank">%s</a>', PLPM_TEXT_DOMAIN ),
            esc_url( $preprint_url ),
            esc_html( $preprint_url ),
            esc_url( $list_url ),
            esc_html( $list_url )
        ),
        8  => sprintf(
            __( '投稿を送信しました。<a href="%s" target="_blank">プレプリントページをプレビュー</a>', PLPM_TEXT_DOMAIN ),
            esc_url( add_query_arg( 'preview', 'true', $preprint_url ) )
        ),
        9  => sprintf(
            __( '投稿を予約しました。<a href="%s" target="_blank">プレプリントページをプレビュー</a>', PLPM_TEXT_DOMAIN ),
            esc_url( $preprint_url )
        ),
        10 => sprintf(
            __( '下書きを更新しました。<a href="%s" target="_blank">プレプリントページをプレビュー</a>', PLPM_TEXT_DOMAIN ),
            esc_url( add_query_arg( 'preview', 'true', $preprint_url ) )
        ),
    );

    return $messages;
}

// 静的ディレクトリチェック機能は削除済み - 動的テンプレート処理を使用
