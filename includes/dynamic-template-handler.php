<?php
/**
 * ===================================================================
 * 🚨 WARNING: FULLY DYNAMIC SYSTEM - NO STATIC FILES! 🚨
 * ===================================================================
 *
 * This file handles ALL /paper/ URLs dynamically through WordPress:
 * - NO physical directory creation (/paper/ is virtual)
 * - NO static HTML file generation (template_redirect renders)
 * - NO file system writes (database-driven content)
 *
 * DO NOT add any wp_mkdir_p() or file_put_contents() functions!
 * All content is generated on-the-fly via WordPress template system.
 * ===================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 動的テンプレート処理のためのリライトルール設定
 */
function plpm_add_dynamic_rewrite_rules() {
    // サイトマップ: /paper/paper-sitemap.xml (最優先)
    add_rewrite_rule(
        '^paper/paper-sitemap\.xml$',
        'index.php?plpm_page_type=sitemap',
        'top'
    );

    // PDFリダイレクト: /paper/123.pdf (拡張子付きを先にマッチ)
    add_rewrite_rule(
        'paper/([0-9]+)\.pdf/?$',
        'index.php?plpm_page_type=pdf_redirect&plpm_post_id=$matches[1]',
        'top'
    );

    // 旧URL互換性: /paper/123.html → /paper/123/ にリダイレクト
    add_rewrite_rule(
        'paper/([0-9]+)\.html/?$',
        'index.php?plpm_page_type=legacy_redirect&plpm_post_id=$matches[1]',
        'top'
    );

    // 個別ページ: /paper/123 または /paper/123/ (新形式、拡張子なし)
    add_rewrite_rule(
        '^paper/([0-9]+)/?$',
        'index.php?plpm_page_type=single&plpm_post_id=$matches[1]',
        'top'
    );

    // 一覧ページ: /paper/ (最後)
    add_rewrite_rule(
        '^paper/?$',
        'index.php?plpm_page_type=list',
        'top'
    );
}

/**
 * カスタムクエリ変数を追加
 */
function plpm_add_query_vars( $vars ) {
    $vars[] = 'plpm_page_type';
    $vars[] = 'plpm_post_id';
    return $vars;
}

/**
 * テンプレートリダイレクト処理
 */
function plpm_template_redirect() {
    $page_type = get_query_var('plpm_page_type');

    // PDFと.htmlリダイレクトの場合、WordPressの正規化リダイレクトを無効化
    if ( $page_type === 'pdf_redirect' || $page_type === 'legacy_redirect' ) {
        remove_action( 'template_redirect', 'redirect_canonical' );
    }

    // クエリ形式アクセスのリダイレクト
    // 例: ?post_type=preprint_page&p=19836 → /paper/19836/ に301
    if (!is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === 'preprint_page') {
        $post_id = isset($_GET['p']) ? absint($_GET['p']) : 0;
        if ($post_id > 0) {
            $post = get_post($post_id);
            if ($post && $post->post_type === 'preprint_page' && $post->post_status === 'publish') {
                wp_redirect( home_url('/paper/' . $post_id . '/'), 301 );
                exit;
            } else {
                wp_redirect( home_url('/paper/'), 301 );
                exit;
            }
        } else {
            // ID不明の場合は一覧へ
            wp_redirect( home_url('/paper/'), 301 );
            exit;
        }
    }

    // 一時的なデバッグ - /paper/アクセス時のみ
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/paper/') !== false) {
        // デバッグログ削除済み

        // サイトマップアクセス時の詳細ログ
        if (strpos($_SERVER['REQUEST_URI'] ?? '', 'paper-sitemap.xml') !== false) {
            // デバッグログ削除済み
        }
    }



    if ( $page_type === 'legacy_redirect' ) {
        // 旧URL形式 (.html) を新形式 (/) にリダイレクト
        $post_id = get_query_var('plpm_post_id');
        if ( $post_id ) {
            // 直接ヘッダー送信（他プラグインのフィルターを完全回避）
            header( 'Location: ' . home_url('/paper/' . $post_id . '/'), true, 301 );
            header( 'X-Redirect-By: PLPM' );
            exit;
        }
    } elseif ( $page_type === 'pdf_redirect' ) {
        $post_id = get_query_var('plpm_post_id');
        if ( $post_id ) {
            // KSTBプラグインのリダイレクトフィルターを一時的に削除
            remove_all_filters('wp_redirect', 1);
            plpm_handle_pdf_redirect( $post_id );
            exit;
        }
    } elseif ( $page_type === 'list' ) {
        plpm_display_list_page();
        exit;
    } elseif ( $page_type === 'single' ) {
        $post_id = get_query_var('plpm_post_id');
        if ( $post_id ) {
            plpm_display_single_page( $post_id );
            exit;
        }
    } elseif ( $page_type === 'sitemap' ) {
        // デバッグログ削除済み
        plpm_display_sitemap();
        exit;
    }

    // サイトマップアクセスのフォールバック検出
    if (strpos($_SERVER['REQUEST_URI'] ?? '', 'paper-sitemap.xml') !== false) {
        // デバッグログ削除済み
        // 強制的にサイトマップを表示
        plpm_display_sitemap();
        exit;
    }
}

/**
 * PDF リダイレクト処理
 */
function plpm_handle_pdf_redirect( $post_id ) {
    // 投稿が存在するかチェック
    $post = get_post( $post_id );
    if ( !$post || $post->post_type !== 'preprint_page' || $post->post_status !== 'publish' ) {
        // 直接ヘッダー送信（wp_redirect フィルターを回避）
        header( 'Location: ' . home_url('/paper/'), true, 301 );
        exit;
    }

    // 実際のPDF ファイルURLを取得
    $pdf_url = plpm_get_actual_pdf_url( $post_id );

    if ( !empty($pdf_url) && filter_var($pdf_url, FILTER_VALIDATE_URL) ) {
        // 直接ヘッダー送信（他プラグインのフィルターを完全回避）
        header( 'Location: ' . $pdf_url, true, 301 );
        header( 'X-Redirect-By: PLPM' );
        exit;
    } else {
        // PDF URLが無効な場合は個別ページにリダイレクト
        header( 'Location: ' . home_url('/paper/' . $post_id . '/'), true, 301 );
        exit;
    }
}

/**
 * 一覧ページを動的表示
 */
function plpm_display_list_page() {
    global $wp_query;

    // アーカイブページとして明示的に設定
    $wp_query->is_post_type_archive = 'preprint_page';
    $wp_query->is_archive = true;
    $wp_query->is_home = false;
    $wp_query->is_singular = false;
    $wp_query->query_vars['post_type'] = 'preprint_page';

    $post_type_obj = get_post_type_object('preprint_page');

    // パンくずリスト用に labels->name をカスタム設定値で上書き
    $custom_title = plpm_get_option('list_page_title', '投稿論文一覧');
    $post_type_obj->labels->name = $custom_title;

    $wp_query->queried_object = $post_type_obj;
    $wp_query->queried_object_id = null;

    // wp_head フックで再度強制設定（パンくずリストプラグインより先に実行）
    add_action('wp_head', function() use ($custom_title) {
        global $wp_query;
        $wp_query->is_post_type_archive = 'preprint_page';
        $wp_query->is_archive = true;
        $wp_query->is_home = false;
        $wp_query->is_singular = false;
        $post_type_obj = get_post_type_object('preprint_page');
        $post_type_obj->labels->name = $custom_title;
        $wp_query->queried_object = $post_type_obj;
        $wp_query->queried_object_id = null;
    }, -999); // 最優先で実行

    // wp_title フィルターを追加してタイトルを設定（パンくずリスト用）
    add_filter('wp_title', function($title) {
        return plpm_get_option('list_page_title', '投稿論文一覧');
    }, 999);

    // HTMLテンプレートのコンテンツを取得（get_header/get_footerを含む修正版）
    plpm_render_list_page_template();
}

/**
 * 個別ページを動的表示
 */
function plpm_display_single_page( $post_id ) {
    global $wp_query, $post;

    $post = get_post( $post_id );

    if ( !$post || $post->post_type !== 'preprint_page' || $post->post_status !== 'publish' ) {
        wp_redirect( home_url('/paper/') );
        exit;
    }

    // グローバルクエリを個別ページとして設定（パンくずリスト対応）
    $wp_query->is_singular = true;
    $wp_query->is_single = true;
    $wp_query->is_home = false;
    $wp_query->is_archive = false;
    $wp_query->queried_object = $post;
    $wp_query->queried_object_id = $post->ID;
    $wp_query->post = $post;

    // setup_postdata() を実行（他プラグインとの互換性向上）
    setup_postdata( $post );

    // 早期タイトル設定（最優先）
    $preprint_data = plpm_gather_preprint_data($post_id);
    add_filter( 'pre_get_document_title', function() use ( $preprint_data ) {
        return esc_html($preprint_data['title']);
    }, -999);

    add_filter( 'wp_title', function() use ( $preprint_data ) {
        return esc_html($preprint_data['title']);
    }, -999);

    // HTMLテンプレートのコンテンツを取得（get_header/get_footerを含む修正版）
    plpm_render_single_page_template( $post_id );
}

/**
 * 一覧ページテンプレートをレンダリング（WordPressテーマ統合版）
 */
function plpm_render_list_page_template() {
    // データ収集（既存の関数を活用）
    $output_file_url = home_url('/paper/');
    $site_url = home_url('/');
    $site_name = get_bloginfo('name');

    $args = array(
        'post_type'      => 'preprint_page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'meta_query'     => array(
             'relation' => 'AND',
             array( 'key' => '_plpm_author', 'compare' => 'EXISTS' ),
             array( 'key' => '_plpm_pub_date', 'compare' => 'EXISTS' ),
             array( 'key' => '_plpm_publisher', 'compare' => 'EXISTS' ),
             array( 'key' => '_plpm_keywords', 'compare' => 'EXISTS' ),
             array( 'key' => '_plpm_abstract', 'compare' => 'EXISTS' ),
             array( 'key' => '_plpm_pdf_url', 'compare' => 'EXISTS' ),
             array( 'key' => '_plpm_bibtex_key', 'compare' => 'EXISTS' ),
        )
    );

    $preprint_query = new WP_Query( $args );
    $list_items_html = '';
    $json_ld_list_elements = [];

    if ( $preprint_query->have_posts() ) {
        $position = 1;
        while ( $preprint_query->have_posts() ) {
            $preprint_query->the_post();
            $post_id = get_the_ID();
            $title = get_the_title();
            $author = get_post_meta( $post_id, '_plpm_author', true );
            $email = get_post_meta( $post_id, '_plpm_email', true );
            $affiliation_name = get_post_meta( $post_id, '_plpm_affiliation_name', true );
            $affiliation_url = get_post_meta( $post_id, '_plpm_affiliation_url', true ) ?: '#';
            $author_url = get_post_meta( $post_id, '_plpm_author_url', true ) ?: '#';
            $pub_date = get_post_meta( $post_id, '_plpm_pub_date', true );
            $modified_date = get_post_meta( $post_id, '_plpm_modified_date', true );
            $publisher = get_post_meta( $post_id, '_plpm_publisher', true );
            $language = get_post_meta( $post_id, '_plpm_language', true ) ?: 'en';
            $keywords = get_post_meta( $post_id, '_plpm_keywords', true );
            $abstract = get_post_meta( $post_id, '_plpm_abstract', true );
            $pdf_url = plpm_get_correct_pdf_url( $post_id );
            $version = get_post_meta( $post_id, '_plpm_version', true ) ?: '1.0';
            $license_url = get_post_meta( $post_id, '_plpm_license_url', true ) ?: '#';
            $schema_type = get_post_meta( $post_id, '_plpm_schema_type', true ) ?: 'ScholarlyArticle';
            $og_image_url = get_post_meta( $post_id, '_plpm_og_image_url', true );
            $doi = get_post_meta( $post_id, '_plpm_doi', true );
            $single_page_url = home_url('/paper/' . $post_id . '/');

            if ( empty($title) || empty($author) || empty($pub_date) || empty($publisher) || empty($pdf_url) ) {
                 continue;
            }

            try { $timezone = new DateTimeZone('Asia/Tokyo'); $date_obj = new DateTime($pub_date . ' 00:00:00', $timezone); $pub_date_iso8601 = $date_obj->format('c'); }
            catch (Exception $e) { $pub_date_iso8601 = date('Y-m-d', strtotime($pub_date)); }
            $modified_date_iso8601 = '';
            if (!empty($modified_date)) {
                try { $timezone = new DateTimeZone('Asia/Tokyo'); $mod_date_obj = new DateTime($modified_date . ' 00:00:00', $timezone); $modified_date_iso8601 = $mod_date_obj->format('c'); }
                catch (Exception $e) { $modified_date_iso8601 = date('Y-m-d', strtotime($modified_date)); }
            }

            $list_items_html .= '<div class="preprint-item">';
            $list_items_html .= '<h3><a href="' . esc_url($single_page_url) . '">' . esc_html($title) . '</a></h3>';

            // 設定値に基づいて著者情報を表示
            if (plpm_get_option('show_author_info', 1)) {
                $list_items_html .= '<p class="author-info"><strong>' . __('Author:', PLPM_TEXT_DOMAIN) . '</strong> ' . esc_html($author);
                if (!empty($doi)) {
                    $list_items_html .= ' | <strong>' . esc_html__('DOI:', PLPM_TEXT_DOMAIN) . '</strong> <a href="https://doi.org/' . esc_attr($doi) . '" target="_blank" rel="noopener noreferrer">' . esc_html($doi) . '</a>';
                }
                $list_items_html .= '</p>';
            }
            $list_items_html .= '<p class="pub-info"><strong>' . __('Published Date:', PLPM_TEXT_DOMAIN) . '</strong> ' . esc_html($pub_date);
            if (!empty($modified_date)) {
                $list_items_html .= ' | <strong>' . __('Modified Date:', PLPM_TEXT_DOMAIN) . '</strong> ' . esc_html($modified_date);
            }
            $list_items_html .= ' | <strong>' . __('Publisher:', PLPM_TEXT_DOMAIN) . '</strong> ' . esc_html($publisher) . ' | <strong>' . __('Version:', PLPM_TEXT_DOMAIN) . '</strong> ' . esc_html($version) . '</p>';
             if (!empty($abstract)) {
                $excerpt = wp_strip_all_tags($abstract);
                $excerpt = mb_substr($excerpt, 0, 200);
                 if (mb_strlen(wp_strip_all_tags($abstract)) > 200) {
                     $excerpt .= '...';
                 }
                 $list_items_html .= '<p class="abstract-excerpt">' . esc_html($excerpt) . '</p>';
             }
            $list_items_html .= '<p class="links">';
            $list_items_html .= '<a href="' . esc_url($single_page_url) . '">' . __('View Landing Page', PLPM_TEXT_DOMAIN) . '</a>';
            $download_filename = $post_id . '-' . $title . '.pdf';
            $list_items_html .= ' | <a href="' . esc_url($pdf_url) . '" download="' . esc_attr($download_filename) . '" target="_blank" rel="noopener noreferrer">' . __('Download PDF', PLPM_TEXT_DOMAIN) . '</a>';
            $list_items_html .= '</p>';
            $list_items_html .= '</div>';

            $item_ld = [
                "@context" => "https://schema.org",
                "@type" => $schema_type,
                "headline" => $title,
                "name" => $title,
                "description" => mb_substr(wp_strip_all_tags($abstract), 0, 120) . '...',
                "abstract" => wp_strip_all_tags($abstract),
                "keywords" => $keywords,
                "inLanguage" => $language,
                "datePublished" => $pub_date_iso8601,
                "author" => [
                    "@type" => "Person",
                    "name" => $author,
                ],
                 "publisher" => [
                    "@type" => "Organization",
                    "name" => $publisher,
                    "url" => esc_url($affiliation_url),
                 ],
                "license" => esc_url($license_url),
                "mainEntityOfPage" => [ "@type" => "WebPage", "@id" => esc_url($single_page_url) ],
                "version" => $version,
                "associatedMedia" => [
                    "@type" => "MediaObject",
                    "contentUrl" => esc_url($pdf_url),
                    "encodingFormat" => "application/pdf",
                    "name" => $title . " (PDF)"
                ],
            ];
            if (!empty($modified_date_iso8601)) {
                $item_ld['dateModified'] = $modified_date_iso8601;
            }
            if (!empty($affiliation_name)) {
                 if (!isset($item_ld['author']['affiliation'])) { $item_ld['author']['affiliation'] = []; }
                 $item_ld['author']['affiliation'] = array_merge($item_ld['author']['affiliation'], [
                    "@type" => "Organization",
                    "name" => $affiliation_name,
                 ]);
            }
             if (!empty($author_url) && $author_url !== '#') {
                 $item_ld['author']['url'] = esc_url($author_url);
            }
             if (!empty($email)) {
                 $item_ld['author']['email'] = sanitize_email($email);
             }
            if (!empty($og_image_url)) {
                 $item_ld['image'] = [
                    "@type" => "ImageObject",
                    "url" => esc_url($og_image_url),
                 ];
            }
             if (!empty($doi)) {
                 $item_ld['identifier'] = esc_attr($doi);
             }
            $json_ld_list_elements[] = [
                "@type" => "ListItem",
                "position" => $position,
                "item" => $item_ld
            ];
            $position++;
        }
        wp_reset_postdata();
    } else {
        $list_items_html = '<p>' . __('投稿論文が見つかりませんでした。', PLPM_TEXT_DOMAIN) . '</p>';
    }

    $json_ld_item_list = [
        "@context" => "https://schema.org",
        "@type" => "CollectionPage",
        "mainEntity" => [
             "@type" => "ItemList",
             "itemListElement" => $json_ld_list_elements,
             "numberOfItems" => count($json_ld_list_elements),
        ],
        "name" => esc_html__('投稿論文一覧', PLPM_TEXT_DOMAIN) . ' - ' . $site_name,
        "description" => esc_attr__('投稿論文一覧 - 公開された論文とプレプリント by', PLPM_TEXT_DOMAIN) . ' ' . $site_name . '.',
        "url" => esc_url($output_file_url),
        "publisher" => [
             "@type" => "Organization",
             "name" => $site_name,
             "url" => $site_url,
        ],
    ];

    // SEO設定（一覧ページ用、低優先度）
    add_filter( 'document_title_parts', function( $title ) use ( $site_name ) {
        return [
            'title' => esc_html__('投稿論文一覧', PLPM_TEXT_DOMAIN),
            'site' => $site_name
        ];
    }, 15);

    // SEOプラグインの無効化（JSON-LDの重複を防ぐ）
    add_filter('wpseo_head', '__return_false', 999);
    add_filter('rank_math/head', '__return_false', 999);
    add_filter('aioseo_disable', '__return_true', 999);
    add_filter('jetpack_enable_open_graph', '__return_false', 999);

    // wp_headフックでメタデータとJSON-LDを出力
    add_action( 'wp_head', function() use ( $output_file_url, $site_name, $json_ld_item_list ) {
        echo '<meta name="description" content="' . esc_attr(__('投稿論文一覧 - 公開された論文とプレプリント by', PLPM_TEXT_DOMAIN) . ' ' . $site_name . '.') . '">' . "\n";
        echo '<link rel="canonical" href="' . esc_url($output_file_url) . '">' . "\n";
        echo '<meta name="robots" content="index, follow">' . "\n";

        // JSON-LD構造化データ
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($json_ld_item_list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT);
        echo "\n" . '</script>' . "\n";

        // カスタムCSS
        echo '<style>' . "\n";
        echo plpm_get_dynamic_css() . "\n";
        echo plpm_get_list_page_custom_css() . "\n";
        echo '</style>' . "\n";
    }, 1 );

    // WordPressテーマのヘッダーを使用
    get_header();
    ?>

    <div class="plpm-list-page-container container">
        <header class="page-header">
            <?php
            // パンくずリストプラグインが有効な場合は表示
            if (function_exists('kspb_display_breadcrumbs')) {
                kspb_display_breadcrumbs();
            }
            ?>
            <h1><?php echo esc_html(plpm_get_option('list_page_title', '投稿論文一覧')); ?></h1>
        </header>

        <main>
          <div class="paper-list">
            <?php echo $list_items_html; ?>
          </div>
        </main>
    </div>

    <?php
    // WordPressテーマのフッターを使用
    get_footer();
}

/**
 * 個別ページテンプレートをレンダリング
 */
function plpm_render_single_page_template( $post_id ) {
    $preprint_data = plpm_gather_preprint_data($post_id);

    // デバッグ：必須フィールドのチェック
    // デバッグログ削除済み
    $required_fields = ['title', 'author', 'pub_date_raw', 'publisher', 'keywords', 'abstract', 'pdf_url', 'bibtex_key'];
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($preprint_data[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        // デバッグログ削除済み
        wp_redirect( home_url('/paper/') );
        exit;
    }

    // SEO設定を最初に設定（個別ページ用、最高優先度）
    add_filter( 'pre_get_document_title', function( $title ) use ( $preprint_data ) {
        return esc_html($preprint_data['title']);
    }, 1);

    add_filter( 'document_title_parts', function( $title ) use ( $preprint_data ) {
        return [
            'title' => esc_html($preprint_data['title'])
        ];
    }, 1);

    // デバッグログ削除済み



    $breadcrumb_html = plpm_generate_breadcrumb_html_for_single_page($preprint_data);
    $author_info_html = plpm_generate_author_info_html_for_single_page($preprint_data);
    $pdf_viewer_html = plpm_generate_pdf_viewer_html_for_single_page($preprint_data);



    $back_button_html = plpm_generate_back_button_html($preprint_data['list_page_url']);
    $json_ld_html = plpm_generate_json_ld_for_single_page($preprint_data);
    $citations = plpm_generate_all_citation_formats($preprint_data);
    $download_links_html = plpm_generate_citation_download_links_html($preprint_data['bibtex_key'], $citations);

    // paper/ページのみでテーマの重複メタデータを選択的に無効化
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // タイトルタグの重複を防ぐ（シンプルアプローチ）
    remove_action('wp_head', '_wp_render_title_tag', 1);
    remove_theme_support('title-tag');
    add_filter('wp_title', function() use ( $preprint_data ) {
        return esc_html($preprint_data['title']);
    }, 999);

    // SEOプラグインの無効化
    add_filter('wpseo_head', '__return_false', 999);
    add_filter('rank_math/head', '__return_false', 999);
    add_filter('aioseo_disable', '__return_true', 999);
    add_filter('jetpack_enable_open_graph', '__return_false', 999);

    add_action( 'wp_head', function() use ( $preprint_data, $json_ld_html ) {
        // 論文専用のタイトルタグを直接出力
        echo '<title>' . esc_html($preprint_data['title']) . '</title>' . "\n";
        echo '<meta name="description" content="' . esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 120) . '...') . '">' . "\n";
        echo '<meta name="keywords" content="' . esc_attr($preprint_data['keywords'] . ', ' . $preprint_data['author'] . ', ' . $preprint_data['publisher'] . ($preprint_data['is_preprint'] ? ', Preprint' : '')) . '">' . "\n";
        echo '<meta name="author" content="' . esc_attr($preprint_data['author'] . ', ' . $preprint_data['publisher']) . '">' . "\n";
        echo '<link rel="canonical" href="' . esc_url($preprint_data['output_file_url']) . '">' . "\n";
        echo '<meta name="robots" content="index, follow">' . "\n";

        // Citation meta tags
        echo '<meta name="citation_title" content="' . esc_attr($preprint_data['title']) . '">' . "\n";
        echo '<meta name="citation_author" content="' . esc_attr($preprint_data['author']) . '">' . "\n";
        echo plpm_generate_citation_author_institution_meta_tag($preprint_data) . "\n";
        echo '<meta name="citation_publication_date" content="' . esc_attr($preprint_data['pub_date_ymd']) . '">' . "\n";
        echo '<meta name="citation_publisher" content="' . esc_attr($preprint_data['publisher']) . '">' . "\n";
        echo '<meta name="citation_language" content="' . esc_attr($preprint_data['language']) . '">' . "\n";
        echo '<meta name="citation_keywords" content="' . esc_attr($preprint_data['keywords']) . '">' . "\n";
        echo '<meta name="citation_abstract_html_url" content="' . esc_url($preprint_data['output_file_url'] . '#abstract') . '">' . "\n";
        echo '<meta name="citation_fulltext_html_url" content="' . esc_url($preprint_data['output_file_url']) . '">' . "\n";
        echo '<meta name="citation_pdf_url" content="' . esc_url($preprint_data['pdf_url']) . '">' . "\n";
        echo '<meta name="citation_year" content="' . esc_attr($preprint_data['pub_year']) . '">' . "\n";
        echo plpm_generate_citation_references_meta_tags($preprint_data) . "\n";
        echo plpm_generate_doi_meta_tag($preprint_data) . "\n";

        // Dublin Core meta tags
        echo '<meta name="DC.title" content="' . esc_attr($preprint_data['title']) . '">' . "\n";
        echo '<meta name="DC.creator" content="' . esc_attr($preprint_data['author']) . '">' . "\n";
        echo '<meta name="DC.date" content="' . esc_attr($preprint_data['pub_date_ymd']) . '">' . "\n";
        echo plpm_generate_dc_modified_date_meta_tag($preprint_data) . "\n";
        echo '<meta name="DC.publisher" content="' . esc_attr($preprint_data['publisher']) . '">' . "\n";
        echo '<meta name="DC.language" content="' . esc_attr($preprint_data['language']) . '">' . "\n";
        echo '<meta name="DC.description" content="' . esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 100) . '...') . '">' . "\n";
        echo '<meta name="DC.rights" content="' . esc_attr($preprint_data['license_name']) . '">' . "\n";
        echo '<meta name="DC.type" content="' . esc_attr($preprint_data['dc_type']) . '">' . "\n";
        echo '<meta name="DC.format" content="text/html">' . "\n";
        echo '<meta name="DC.identifier" content="' . esc_url($preprint_data['output_file_url']) . '">' . "\n";
        if (!empty($preprint_data['doi'])) {
            echo '<meta name="DC.identifier" scheme="DOI" content="' . esc_attr($preprint_data['doi']) . '">' . "\n";
        }
        echo '<meta name="DC.subject" content="' . esc_attr($preprint_data['keywords']) . '">' . "\n";

        // Open Graph meta tags
        echo '<meta property="og:title" content="' . esc_attr($preprint_data['title']) . '">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($preprint_data['output_file_url']) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 100) . '...') . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr($preprint_data['publisher']) . '">' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr($preprint_data['language'] === 'ja' ? 'ja_JP' : 'en_US') . '">' . "\n";
        echo '<meta property="article:published_time" content="' . esc_attr($preprint_data['pub_date_iso8601']) . '">' . "\n";
        if (!empty($preprint_data['modified_date_iso8601'])) {
            echo '<meta property="article:modified_time" content="' . esc_attr($preprint_data['modified_date_iso8601']) . '">' . "\n";
        }

        // Twitter meta tags
        echo '<meta name="twitter:card" content="summary">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($preprint_data['title']) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 100) . '...') . '">' . "\n";

        // JSON-LD（論文専用の構造化データ）
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $json_ld_html;
        echo "\n";
    });

    // カスタムCSSを wp_head に追加
    add_action( 'wp_head', function() use ( $preprint_data ) {
        echo '<style>' . "\n";
        echo plpm_get_dynamic_css() . "\n";
        echo plpm_get_single_page_custom_css() . "\n";
        echo '</style>' . "\n";
    }, 999);

    // WordPressテーマのヘッダーを使用
    get_header();

    // 出力をフラッシュ（FastCGIバッファ対策）
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();

    // パンくずリストプラグインが有効な場合は表示
    if (function_exists('kspb_display_breadcrumbs')) {
        kspb_display_breadcrumbs();
    }

    // コンテンツを出力
    echo '<div class="plpm-single-page-container container">' . "\n";
    echo '<header class="page-header">' . "\n";
    echo '<h1>' . esc_html($preprint_data['title']) . '</h1>' . "\n";
    echo $author_info_html . "\n";
    echo '</header>' . "\n";

    echo '<main>' . "\n";
    echo $pdf_viewer_html . "\n";

    echo '<section id="abstract">' . "\n";
    echo '<h2>Abstract</h2>' . "\n";
    echo wpautop(esc_html($preprint_data['abstract'])) . "\n";
    echo '</section>' . "\n";

    echo '<section class="keywords">' . "\n";
    echo '<h2>Keywords</h2>' . "\n";
    echo '<p>' . esc_html($preprint_data['keywords']) . '</p>' . "\n";
    echo '</section>' . "\n";

    echo '<section class="citation">' . "\n";
    echo '<h2>Citation Export</h2>' . "\n";
    echo '<div class="citation-downloads">' . "\n";
    echo '<h3>Download in other formats:</h3>' . "\n";
    echo $download_links_html . "\n";
    echo '</div>' . "\n";
    echo '</section>' . "\n";

    echo $back_button_html . "\n";
    echo '</main>' . "\n";
    echo '</div>' . "\n";

    // WordPressテーマのフッターを使用
    get_footer();
}

/**
 * リストページ用の独自ヘッダーをレンダリング
 */
function plpm_render_list_page_header($output_file_url, $site_name, $json_ld_item_list) {
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- リストページ専用タイトル -->
        <title>投稿論文一覧 | <?php echo esc_html($site_name); ?></title>

        <!-- 基本メタデータ -->
        <meta name="description" content="<?php echo esc_attr(__('投稿論文一覧 - 公開された論文とプレプリント by', PLPM_TEXT_DOMAIN) . ' ' . $site_name . '.'); ?>">
        <link rel="canonical" href="<?php echo esc_url($output_file_url); ?>">
        <meta name="robots" content="index, follow">

        <!-- JSON-LD構造化データ -->
        <script type="application/ld+json">
        <?php echo json_encode($json_ld_item_list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT); ?>
        </script>

        <!-- パンくずリスト構造化データ -->
        <?php
        $breadcrumb_data = [
            'site_title' => get_bloginfo('name'),
            'site_url' => home_url('/'),
            'list_page_url' => home_url('/paper/')
        ];
        $breadcrumb_ld = plpm_generate_breadcrumb_jsonld_for_list_page($breadcrumb_data);
        ?>
        <script type="application/ld+json">
        <?php echo json_encode($breadcrumb_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT); ?>
        </script>

        <!-- 動的CSS（カラーテーマ対応） -->
        <style>
        <?php echo plpm_get_dynamic_css(); ?>

                /* リストページ専用スタイル */
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: var(--plpm-bg);
            color: var(--plpm-text);
        }

        <?php echo plpm_get_site_header_css(); ?>
        <?php echo plpm_get_page_header_css(); ?>

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
                        /* パンくずリスト（リストページ） */
        .breadcrumb {
            margin: 0.5em 0 2em 0;
            padding: 0;
            list-style: none;
            font-size: 0.9em;
            color: var(--plpm-text);
            opacity: 0.8;
        }
        .breadcrumb li {
            display: inline;
        }
        .breadcrumb li a {
            color: var(--plpm-primary);
            text-decoration: none;
        }
        .breadcrumb li a:hover {
            text-decoration: underline;
        }
        .breadcrumb li:after {
            content: ' / ';
            padding: 0 0.5em;
            color: var(--plpm-border);
        }
        .breadcrumb li:last-child::after {
            content: '';
        }
        .breadcrumb li:last-child {
            color: var(--plpm-text);
            font-weight: 500;
        }

        h1 {
            font-size: 1.8em;
            margin: 0 0 1em 0;
            color: var(--plpm-text);
            cursor: default;
            transition: color 0.2s ease;
        }
        h1:hover {
            color: var(--plpm-primary);
            text-decoration: underline;
        }
                .paper-list {
            list-style: none;
            padding: 0;
        }
        .preprint-item {
            background: var(--plpm-secondary);
            padding: 1.5em;
            border-radius: 5px;
            margin: 1.5em 0;
            border: 1px solid var(--plpm-border);
            color: var(--plpm-text);
        }
        .preprint-item h3 {
            margin-top: 0;
            margin-bottom: 0.5em;
            font-size: 1.3em;
            color: var(--plpm-text);
        }
        .preprint-item h3 a {
            color: var(--plpm-primary);
            text-decoration: underline;
            transition: all 0.2s ease;
        }
        .preprint-item h3 a:hover {
            color: var(--plpm-accent);
            text-shadow: 0 0 1px rgba(0, 86, 179, 0.3);
        }
        .preprint-item .author-info, .preprint-item .pub-info {
            color: var(--plpm-text);
            opacity: 0.8;
            font-size: 0.9em;
            margin-bottom: 0.8em;
        }
        .preprint-item .author-info a, .preprint-item .pub-info a {
            color: var(--plpm-primary);
            text-decoration: none;
        }
        .preprint-item .author-info a:hover, .preprint-item .pub-info a:hover {
            text-decoration: underline;
        }
        .preprint-item .abstract-excerpt {
            font-size: 0.95em;
            margin: 1em 0;
            text-align: justify;
        }
        .preprint-item .links a {
            display: inline-block;
            margin-right: 1em;
            color: var(--plpm-primary);
            text-decoration: none;
        }
                .preprint-item .links a:hover {
            text-decoration: underline;
        }

        /* フッター */
        .site-footer {
            margin-top: 3em;
            padding: 2em 0 1em 0;
            border-top: 2px solid var(--plpm-border);
            background-color: var(--plpm-secondary);
            color: var(--plpm-text);
            text-align: center;
        }
        .footer-main {
            margin-bottom: 2em;
        }
        .footer-main h3 {
            margin: 0 0 0.5em 0;
            font-size: 1.5em;
        }
        .footer-main h3 a {
            color: var(--plpm-text);
            text-decoration: none;
            font-weight: bold;
        }
        .footer-main h3 a:hover {
            text-decoration: underline;
        }
        .site-description {
            font-style: italic;
            opacity: 0.8;
            margin: 0.5em 0 0 0;
            font-size: 1em;
        }
        .footer-bottom {
            border-top: 1px solid var(--plpm-border);
            padding-top: 1em;
            text-align: center;
            font-size: 0.9em;
            opacity: 0.8;
        }
        .footer-bottom p {
            margin: 0.5em 0;
        }
        .footer-bottom a {
            color: var(--plpm-primary);
            text-decoration: none;
        }
        .footer-bottom a:hover {
            text-decoration: underline;
        }

        </style>
    </head>
    <body>
    <?php
}

/**
 * 個別ページ用の独自ヘッダーをレンダリング（wp_headを使用しない）
 */
function plpm_render_single_page_header($preprint_data) {
    ?>
    <!DOCTYPE html>
    <html lang="<?php echo esc_attr($preprint_data['language']); ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- 論文専用タイトル -->
        <title><?php echo esc_html($preprint_data['title']); ?> | <?php echo esc_html(get_bloginfo('name')); ?></title>

        <!-- 基本メタデータ -->
        <meta name="description" content="<?php echo esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 120) . '...'); ?>">
        <meta name="keywords" content="<?php echo esc_attr($preprint_data['keywords'] . ', ' . $preprint_data['author'] . ', ' . $preprint_data['publisher'] . ($preprint_data['is_preprint'] ? ', Preprint' : '')); ?>">
        <meta name="author" content="<?php echo esc_attr($preprint_data['author'] . ', ' . $preprint_data['publisher']); ?>">
        <link rel="canonical" href="<?php echo esc_url($preprint_data['output_file_url']); ?>">
        <meta name="robots" content="index, follow">

        <!-- Citation meta tags -->
        <meta name="citation_title" content="<?php echo esc_attr($preprint_data['title']); ?>">
        <meta name="citation_author" content="<?php echo esc_attr($preprint_data['author']); ?>">
        <?php echo plpm_generate_citation_author_institution_meta_tag($preprint_data); ?>
        <meta name="citation_publication_date" content="<?php echo esc_attr($preprint_data['pub_date_ymd']); ?>">
        <meta name="citation_publisher" content="<?php echo esc_attr($preprint_data['publisher']); ?>">
        <meta name="citation_language" content="<?php echo esc_attr($preprint_data['language']); ?>">
        <meta name="citation_keywords" content="<?php echo esc_attr($preprint_data['keywords']); ?>">
        <meta name="citation_abstract_html_url" content="<?php echo esc_url($preprint_data['output_file_url'] . '#abstract'); ?>">
        <meta name="citation_fulltext_html_url" content="<?php echo esc_url($preprint_data['output_file_url']); ?>">
        <meta name="citation_pdf_url" content="<?php echo esc_url($preprint_data['pdf_url']); ?>">
        <meta name="citation_year" content="<?php echo esc_attr($preprint_data['pub_year']); ?>">

        <?php echo plpm_generate_doi_meta_tag($preprint_data); ?>

        <!-- Dublin Core meta tags -->
        <meta name="DC.title" content="<?php echo esc_attr($preprint_data['title']); ?>">
        <meta name="DC.creator" content="<?php echo esc_attr($preprint_data['author']); ?>">
        <meta name="DC.date" content="<?php echo esc_attr($preprint_data['pub_date_ymd']); ?>">
        <?php echo plpm_generate_dc_modified_date_meta_tag($preprint_data); ?>
        <meta name="DC.publisher" content="<?php echo esc_attr($preprint_data['publisher']); ?>">
        <meta name="DC.language" content="<?php echo esc_attr($preprint_data['language']); ?>">
        <meta name="DC.description" content="<?php echo esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 100) . '...'); ?>">
        <meta name="DC.rights" content="<?php echo esc_attr($preprint_data['license_name']); ?>">
        <meta name="DC.type" content="<?php echo esc_attr($preprint_data['dc_type']); ?>">
        <meta name="DC.format" content="text/html">
        <meta name="DC.identifier" content="<?php echo esc_url($preprint_data['output_file_url']); ?>">
        <?php if (!empty($preprint_data['doi'])): ?>
        <meta name="DC.identifier" scheme="DOI" content="<?php echo esc_attr($preprint_data['doi']); ?>">
        <?php endif; ?>
        <meta name="DC.subject" content="<?php echo esc_attr($preprint_data['keywords']); ?>">

        <!-- Open Graph meta tags -->
        <meta property="og:title" content="<?php echo esc_attr($preprint_data['title']); ?>">
        <meta property="og:type" content="article">
        <meta property="og:url" content="<?php echo esc_url($preprint_data['output_file_url']); ?>">
        <meta property="og:description" content="<?php echo esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 100) . '...'); ?>">
        <meta property="og:site_name" content="<?php echo esc_attr($preprint_data['publisher']); ?>">
        <meta property="og:locale" content="<?php echo esc_attr($preprint_data['language'] === 'ja' ? 'ja_JP' : 'en_US'); ?>">
        <meta property="article:published_time" content="<?php echo esc_attr($preprint_data['pub_date_iso8601']); ?>">
        <?php if (!empty($preprint_data['modified_date_iso8601'])): ?>
        <meta property="article:modified_time" content="<?php echo esc_attr($preprint_data['modified_date_iso8601']); ?>">
        <?php endif; ?>

        <!-- Twitter meta tags -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="<?php echo esc_attr($preprint_data['title']); ?>">
        <meta name="twitter:description" content="<?php echo esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 100) . '...'); ?>">

        <?php echo plpm_generate_citation_references_meta_tags($preprint_data); ?>

        <!-- JSON-LD構造化データ -->
        <?php echo plpm_generate_json_ld_for_single_page($preprint_data); ?>

        <!-- パンくずリスト構造化データ -->
        <?php
        $breadcrumb_ld = plpm_generate_breadcrumb_jsonld_for_single_page($preprint_data);
        ?>
        <script type="application/ld+json">
        <?php echo json_encode($breadcrumb_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT); ?>
        </script>

        <!-- 動的CSS（カラーテーマ対応） -->
        <style>
        <?php echo plpm_get_dynamic_css(); ?>

                /* 個別ページ専用スタイル */
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: var(--plpm-bg);
            color: var(--plpm-text);
        }

        <?php echo plpm_get_site_header_css(); ?>
        <?php echo plpm_get_page_header_css(); ?>

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--plpm-bg);
            color: var(--plpm-text);
        }
        h1 {
            font-size: 1.8em;
            margin: 0 0 1em 0;
            color: var(--plpm-text);
            cursor: default;
            transition: color 0.2s ease;
        }
        h1:hover {
            color: var(--plpm-primary);
            text-decoration: underline;
        }
        h2 {
            font-size: 1.4em;
            border-bottom: 1px solid var(--plpm-border);
            padding-bottom: 0.2em;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            color: var(--plpm-text);
        }
        .author-info {
            margin-bottom: 1.5em;
        }
        .author-info p {
            margin: 0.5em 0;
        }
        .author-info strong {
            min-width: 120px;
            display: inline-block;
        }
                .author-info a {
            color: var(--plpm-primary);
            text-decoration: none;
        }
        .author-info a:hover {
            text-decoration: underline;
        }
        .author-info .plpm-author-link {
            text-decoration: underline;
            color: var(--plpm-primary);
        }
        .author-info .plpm-author-link:hover {
            color: var(--plpm-accent);
        }
        .author-info a:not(.plpm-author-link) {
            text-decoration: none;
            color: var(--plpm-text);
        }
        .author-info a:not(.plpm-author-link):hover {
            text-decoration: underline;
        }
                        /* パンくずリスト（個別ページ） */
        .breadcrumb {
            margin: 0.5em 0 2em 0;
            padding: 0;
            list-style: none;
            font-size: 0.9em;
            color: var(--plpm-text);
            opacity: 0.8;
        }
        .breadcrumb li {
            display: inline;
        }
        .breadcrumb li a {
            color: var(--plpm-primary);
            text-decoration: none;
        }
        .breadcrumb li a:hover {
            text-decoration: underline;
        }
        .breadcrumb li:after {
            content: ' / ';
            padding: 0 0.5em;
            color: var(--plpm-border);
        }
        .breadcrumb li:last-child::after {
            content: '';
        }
        .breadcrumb li:last-child {
            color: var(--plpm-text);
            font-weight: 500;
        }
                .plpm-pdf-viewer-container {
            border: 1px solid var(--plpm-border);
            border-radius: 8px;
            margin-bottom: 1.5em;
            background-color: var(--plpm-bg);
        }
                .plpm-pdf-viewer-container iframe {
            width: 100%;
            height: 800px;
            border: none;
            border-radius: 0 0 8px 8px;
        }

        /* PDFコントロールボタン */
        .plpm-pdf-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }
        .plpm-pdf-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            min-width: 140px;
            justify-content: center;
        }
        .plpm-pdf-btn-primary {
            background: linear-gradient(135deg, var(--plpm-primary), var(--plpm-accent));
            color: var(--plpm-bg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .plpm-pdf-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            filter: brightness(1.1);
        }
        .plpm-pdf-btn-secondary {
            background: var(--plpm-secondary);
            color: var(--plpm-text);
            border: 2px solid var(--plpm-border);
        }
        .plpm-pdf-btn-secondary:hover {
            background: var(--plpm-border);
            border-color: var(--plpm-primary);
            color: var(--plpm-primary);
            transform: translateY(-1px);
        }
        .plpm-pdf-btn:active {
            transform: translateY(0);
        }
        .plpm-pdf-btn:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }
        .plpm-pdf-btn.loading {
            pointer-events: none;
        }
        .plpm-pdf-btn.loading .reload-icon {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* レスポンシブ対応 */
        @media (max-width: 480px) {
            .plpm-pdf-controls {
                flex-direction: column;
                gap: 0.75rem;
            }
            .plpm-pdf-btn {
                width: 100%;
                max-width: 280px;
            }
            .plpm-pdf-btn .btn-text {
                font-size: 0.9rem;
            }
        }
        /* レガシー対応 */
        .google-pdf-viewer-container {
            border: 1px solid var(--plpm-border);
            padding: 1em;
            margin-bottom: 1.5em;
            background-color: var(--plpm-secondary);
        }
        .google-pdf-viewer-container iframe {
            width: 100%;
            height: 600px;
            border: none;
        }
        section {
            margin-bottom: 1.5em;
        }
        .abstract p {
            text-align: justify;
        }
        .citation-downloads {
            margin-top: 1em;
        }
        .citation-downloads h3 {
            margin-top: 0;
            margin-bottom: 0.8em;
            font-size: 1.1em;
            color: var(--plpm-text);
        }
        .citation-downloads a {
            display: inline-block;
            background-color: var(--plpm-secondary);
            color: var(--plpm-text);
            padding: 8px 15px;
            text-decoration: none;
            border: 1px solid var(--plpm-border);
            border-radius: 4px;
            margin-right: 0.5em;
            margin-bottom: 0.5em;
            font-size: 0.9em;
        }
        .citation-downloads a:hover {
            background-color: var(--plpm-border);
            text-decoration: none;
        }
        .back-button {
            display: inline-block;
            margin-top: 1.5em;
            padding: 8px 15px;
            background-color: var(--plpm-secondary);
            color: var(--plpm-text);
            text-decoration: none;
            border: 1px solid var(--plpm-border);
            border-radius: 4px;
            font-size: 0.9em;
        }
        .back-button:hover {
            background-color: var(--plpm-border);
            text-decoration: none;
        }
        a {
            color: var(--plpm-primary);
        }
                a:hover {
            text-decoration: underline;
        }

        /* フッター */
        .site-footer {
            margin-top: 3em;
            padding: 2em 0 1em 0;
            border-top: 2px solid var(--plpm-border);
            background-color: var(--plpm-secondary);
            color: var(--plpm-text);
            text-align: center;
        }
        .footer-main {
            margin-bottom: 2em;
        }
        .footer-main h3 {
            margin: 0 0 0.5em 0;
            font-size: 1.5em;
        }
        .footer-main h3 a {
            color: var(--plpm-text);
            text-decoration: none;
            font-weight: bold;
        }
        .footer-main h3 a:hover {
            text-decoration: underline;
        }
        .site-description {
            font-style: italic;
            opacity: 0.8;
            margin: 0.5em 0 0 0;
            font-size: 1em;
        }
        .footer-bottom {
            border-top: 1px solid var(--plpm-border);
            padding-top: 1em;
            text-align: center;
            font-size: 0.9em;
            opacity: 0.8;
        }
        .footer-bottom p {
            margin: 0.5em 0;
        }
        .footer-bottom a {
            color: var(--plpm-primary);
            text-decoration: none;
        }
        .footer-bottom a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .footer-main h3 {
                font-size: 1.3em;
            }
            .site-description {
                font-size: 0.9em;
            }
        }
        </style>
    </head>
    <body>
    <?php
}

/**
 * サイト情報を含むフッターHTMLを生成
 */
function plpm_generate_site_footer() {
    $site_name = get_bloginfo('name');
    $site_description = get_bloginfo('description');
    $site_url = home_url('/');
    $current_year = date('Y');
    $copyright_text = plpm_get_option('copyright_text', '');

    $html = '<footer class="site-footer">';

    // サイト情報（中央配置、シンプル）
    $html .= '<div class="footer-main">';
    $html .= '<h3><a href="' . esc_url($site_url) . '">' . esc_html($site_name) . '</a></h3>';
    if (!empty($site_description)) {
        $html .= '<p class="site-description">' . esc_html($site_description) . '</p>';
    }
    $html .= '</div>';

    // コピーライト
    $html .= '<div class="footer-bottom">';
    if (!empty($copyright_text)) {
        $html .= '<p>' . esc_html($copyright_text) . '</p>';
    } else {
        $html .= '<p>&copy; ' . esc_html($current_year) . ' <a href="' . esc_url($site_url) . '">' . esc_html($site_name) . '</a>. ' . __('All rights reserved.', PLPM_TEXT_DOMAIN) . '</p>';
    }
    $html .= '</div>';

    $html .= '</footer>';

    return $html;
}

/**
 * 共通ヘッダーCSS（サイトヘッダー用）
 */
function plpm_get_site_header_css() {
    return '
        /* サイトヘッダー */
        .site-header {
            background-color: var(--plpm-secondary);
            padding: 1em 0;
            margin-bottom: 2em;
        }
        .header-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .site-title {
            margin: 0;
            font-size: 1.5em;
        }
        .site-title a {
            color: var(--plpm-text);
            text-decoration: none;
            font-weight: bold;
        }
        .site-title a:hover {
            color: var(--plpm-primary);
            text-decoration: underline;
        }
        .main-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 1.5em;
        }
        .main-nav a {
            color: var(--plpm-text);
            text-decoration: none;
            padding: 0.5em 0;
        }
        .main-nav a:hover, .main-nav a.current {
            color: var(--plpm-primary);
            text-decoration: underline;
        }

        /* レスポンシブ */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1em;
                text-align: center;
            }
            .main-nav ul {
                gap: 1em;
            }
        }
    ';
}

/**
 * 共通ページヘッダーCSS（ページタイトル部分）
 */
function plpm_get_page_header_css() {
    return '
        .page-header {
            margin-bottom: 2em;
        }
    ';
}

/**
 * 動的CSS変数を生成
 */
function plpm_get_dynamic_css() {
    // カラーテーマ設定を取得
    $primary_color = plpm_get_option('primary_color', '#0073aa');
    $secondary_color = plpm_get_option('secondary_color', '#005177');
    $border_color = plpm_get_option('border_color', '#dddddd');
    $bg_color = plpm_get_option('bg_color', '#ffffff');
    $text_color = plpm_get_option('text_color', '#333333');
    $accent_color = plpm_get_option('accent_color', '#f0f0f0');

    $css = ':root {';
    $css .= '--plpm-primary: ' . $primary_color . ';';
    $css .= '--plpm-secondary: ' . $secondary_color . ';';
    $css .= '--plpm-border: ' . $border_color . ';';
    $css .= '--plpm-bg: ' . $bg_color . ';';
    $css .= '--plpm-text: ' . $text_color . ';';
    $css .= '--plpm-accent: ' . $accent_color . ';';
    $css .= '}';
    return $css;
}

/**
 * リストページ用のカスタムCSS
 */
function plpm_get_list_page_custom_css() {
    return '
        /* リストページ専用スタイル */
        .plpm-list-page-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .plpm-list-page-container .page-header {
            margin-bottom: 2em;
        }

        .plpm-list-page-container h1 {
            font-size: 1.8em;
            margin: 0 0 1em 0;
            color: var(--plpm-text, #333);
        }

        .plpm-list-page-container .paper-list {
            list-style: none;
            padding: 0;
        }
        .plpm-list-page-container .preprint-item {
            background: var(--plpm-secondary, #f9f9f9);
            padding: 1.5em;
            border-radius: 5px;
            margin: 1.5em 0;
            border: 1px solid var(--plpm-border, #ddd);
            color: var(--plpm-text, #333);
        }
        .plpm-list-page-container .preprint-item h3 {
            margin-top: 0;
            margin-bottom: 0.5em;
            font-size: 1.3em;
            color: var(--plpm-text, #333);
        }
        .plpm-list-page-container .preprint-item h3 a {
            color: var(--plpm-primary, #0073aa);
            text-decoration: underline;
            transition: all 0.2s ease;
        }
        .plpm-list-page-container .preprint-item h3 a:hover {
            color: var(--plpm-accent, #005177);
            text-shadow: 0 0 1px rgba(0, 86, 179, 0.3);
        }
        .plpm-list-page-container .preprint-item .author-info,
        .plpm-list-page-container .preprint-item .pub-info {
            color: var(--plpm-text, #333);
            opacity: 0.8;
            font-size: 0.9em;
            margin-bottom: 0.8em;
        }
        .plpm-list-page-container .preprint-item .author-info a,
        .plpm-list-page-container .preprint-item .pub-info a {
            color: var(--plpm-primary, #0073aa);
            text-decoration: none;
        }
        .plpm-list-page-container .preprint-item .author-info a:hover,
        .plpm-list-page-container .preprint-item .pub-info a:hover {
            text-decoration: underline;
        }
        .plpm-list-page-container .preprint-item .abstract-excerpt {
            font-size: 0.95em;
            margin: 1em 0;
            text-align: justify;
        }
        .plpm-list-page-container .preprint-item .links a {
            display: inline-block;
            margin-right: 1em;
            color: var(--plpm-primary, #0073aa);
            text-decoration: none;
        }
        .plpm-list-page-container .preprint-item .links a:hover {
            text-decoration: underline;
        }
    ';
}

/**
 * 個別ページ用のカスタムCSS
 */
function plpm_get_single_page_custom_css() {
    return '
        /* 個別ページ専用スタイル */
        .plpm-single-page-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .plpm-single-page-container .page-header {
            margin-bottom: 2em;
        }

        .plpm-single-page-container h1 {
            font-size: 1.8em;
            margin: 0 0 1em 0;
            color: var(--plpm-text, #333);
        }

        .plpm-single-page-container h2 {
            font-size: 1.4em;
            border-bottom: 1px solid var(--plpm-border, #ddd);
            padding-bottom: 0.2em;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
            color: var(--plpm-text, #333);
        }

        .plpm-single-page-container section {
            margin-bottom: 1.5em;
        }

        .plpm-single-page-container .citation-downloads a {
            display: inline-block;
            background-color: var(--plpm-secondary, #f9f9f9);
            color: var(--plpm-text, #333);
            padding: 8px 15px;
            text-decoration: none;
            border: 1px solid var(--plpm-border, #ddd);
            border-radius: 4px;
            margin-right: 0.5em;
            margin-bottom: 0.5em;
            font-size: 0.9em;
        }

        .plpm-single-page-container .citation-downloads a:hover {
            background-color: var(--plpm-border, #e0e0e0);
            text-decoration: none;
        }

        .plpm-single-page-container .back-button {
            display: inline-block;
            margin-top: 1.5em;
            padding: 8px 15px;
            background-color: var(--plpm-secondary, #f9f9f9);
            color: var(--plpm-text, #333);
            text-decoration: none;
            border: 1px solid var(--plpm-border, #ddd);
            border-radius: 4px;
            font-size: 0.9em;
        }

        .plpm-single-page-container .back-button:hover {
            background-color: var(--plpm-border, #e0e0e0);
            text-decoration: none;
        }

        /* PDFボタン */
        .plpm-pdf-controls {
            margin: 1em 0;
            text-align: center;
        }

        .plpm-pdf-btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .plpm-reload-btn {
            background: var(--plpm-secondary, #f0f0f0);
            color: var(--plpm-text, #333);
            border: 1px solid var(--plpm-border, #ccc);
        }

        .plpm-reload-btn:hover {
            background: var(--plpm-border, #e0e0e0);
        }

        .plpm-download-btn {
            background: var(--plpm-primary, #0073aa);
            color: white;
            border: none;
        }

        .plpm-download-btn:hover {
            background: var(--plpm-accent, #005177);
        }

        @media (max-width: 600px) {
            .plpm-pdf-btn {
                display: block;
                margin: 5px auto;
                width: 80%;
                max-width: 300px;
            }
        }

        /* スピナーアニメーション（大） */
        .plpm-spinner-circle {
            width: 60px;
            height: 60px;
            border: 4px solid var(--plpm-border, #ddd);
            border-top-color: var(--plpm-primary, #0073aa);
            border-radius: 50%;
            animation: plpm-spin 0.8s linear infinite;
        }

        /* スピナーアニメーション（小 - ボタン用） */
        .plpm-spinner-small {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid var(--plpm-border, #ddd);
            border-top-color: white;
            border-radius: 50%;
            animation: plpm-spin 0.6s linear infinite;
            vertical-align: middle;
            margin-right: 5px;
        }

        @keyframes plpm-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* 著者情報ボックス */
        .plpm-author-info-box {
            background: var(--plpm-secondary, #f9f9f9);
            border: 1px solid var(--plpm-border, #ddd);
            border-radius: 8px;
            padding: 1.5em;
            margin: 1.5em 0;
        }

        .plpm-author-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1em;
        }

        .plpm-info-column {
            display: flex;
            flex-direction: column;
            gap: 0.8em;
        }

        .plpm-info-item {
            display: flex;
            align-items: baseline;
            line-height: 1.6;
        }

        .plpm-info-label {
            font-weight: 600;
            color: var(--plpm-text, #333);
            min-width: 100px;
            margin-right: 0.5em;
            flex-shrink: 0;
        }

        .plpm-info-value {
            color: var(--plpm-text, #333);
        }

        .plpm-info-value a {
            color: var(--plpm-primary, #0073aa);
            text-decoration: none;
        }

        .plpm-info-value a:hover {
            text-decoration: underline;
        }

        .plpm-author-link {
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .plpm-author-info-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Tooltip */
        .plpm-tooltip {
            display: inline-block;
            width: 16px;
            height: 16px;
            line-height: 16px;
            text-align: center;
            border-radius: 50%;
            background: var(--plpm-primary, #0073aa);
            color: white;
            font-size: 12px;
            font-weight: bold;
            cursor: help;
            position: relative;
            margin-left: 4px;
        }

        .plpm-tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--plpm-text, #333);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            white-space: normal;
            width: 250px;
            font-size: 13px;
            font-weight: normal;
            line-height: 1.4;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .plpm-tooltip:hover::before {
            content: "";
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--plpm-text, #333);
            z-index: 1000;
        }

        @media (max-width: 600px) {
            .plpm-tooltip:hover::after {
                width: 200px;
                font-size: 12px;
            }
        }
    ';
}

/**
 * サイトマップを動的に表示
 */
function plpm_display_sitemap() {
    // デバッグログ削除済み

    if (!plpm_get_option('enable_sitemap', 1)) {
        // デバッグログ削除済み
        status_header(404);
        exit;
    }

    // バッファリングをクリアして直接出力
    if (ob_get_level()) {
        ob_end_clean();
    }

    // XMLヘッダーを送信
    status_header(200);
    header('Content-Type: application/xml; charset=utf-8');
    header('X-Robots-Tag: noindex');

    // サイトマップのXMLを生成
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    // リストページを追加
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . esc_url(home_url('/paper/')) . '</loc>' . "\n";
    $xml .= '    <lastmod>' . date('c') . '</lastmod>' . "\n";
    $xml .= '    <changefreq>daily</changefreq>' . "\n";
    $xml .= '    <priority>0.9</priority>' . "\n";
    $xml .= '  </url>' . "\n";

    // 個別論文ページを追加（PDFメタの有無に関わらず公開済みをすべて含める）
    $args = array(
        'post_type' => 'preprint_page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
    );

    $preprint_query = new WP_Query($args);
    if ($preprint_query->have_posts()) {
        while ($preprint_query->have_posts()) {
            $preprint_query->the_post();
            $post = get_post();

            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc_url(home_url('/paper/' . $post->ID . '/')) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . get_post_modified_time('c', true, $post->ID) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.8</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        wp_reset_postdata();
    }

    $xml .= '</urlset>';

    echo $xml;
}
