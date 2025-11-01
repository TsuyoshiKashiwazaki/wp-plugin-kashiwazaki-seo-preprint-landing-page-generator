<?php
/**
 * ===================================================================
 * 🚨 STATIC LIST PAGE GENERATION DISABLED! 🚨
 * ===================================================================
 *
 * This file contains LEGACY static index.html generation code that is
 * COMPLETELY DISABLED in the current dynamic system.
 *
 * DO NOT re-enable this code - it will create physical files and
 * cause server permission errors.
 *
 * All list pages are now generated dynamically via template_redirect.
 * ===================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * LEGACY FUNCTION - DISABLED FOR DYNAMIC SYSTEM
 * This function is kept for reference but should NEVER be called
 */
function plpm_generate_list_page() {
    // ===================================================================
    // 🚨 FUNCTION DISABLED - DYNAMIC SYSTEM ACTIVE 🚨
    // This function creates static index.html and is now disabled.
    // All list pages are generated dynamically via WordPress template system.
    // ===================================================================
    return true; // Always return success for compatibility
    $output_dir_path = PLPM_PAPER_DIR;
    $output_file_path = $output_dir_path . 'index.html';
    $output_file_url = home_url('/paper/');
    $site_url = home_url('/');
    $site_name = get_bloginfo('name');
    if ( ! file_exists( $output_dir_path ) ) {
        if ( ! wp_mkdir_p( $output_dir_path ) ) {
            // エラーログ削除済み
            return false;
        }
    }
    if ( ! wp_is_writable( $output_dir_path ) ) {
        // エラーログ削除済み
        return false;
    }
    $args = array(
        'post_type'      => 'preprint_page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value',
        'order'          => 'DESC',
        'meta_key'       => '_plpm_pub_date',
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
                 // エラーログ削除済み
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
            $list_items_html .= '<p class="author-info"><strong>' . __('Author:', PLPM_TEXT_DOMAIN) . '</strong> ' . esc_html($author);
             if (!empty($doi)) {
                 $list_items_html .= ' | <strong>' . esc_html__('DOI:', PLPM_TEXT_DOMAIN) . '</strong> <a href="https://doi.org/' . esc_attr($doi) . '" target="_blank" rel="noopener noreferrer">' . esc_html($doi) . '</a>';
             }
            $list_items_html .= '</p>';
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
    $html_template = plpm_get_html_list_template_content();
    $replacements = [
        '[ページタイトル]' => esc_html(plpm_get_option('list_page_title', '投稿論文一覧')),
        '[サイトタイトル]' => esc_html($site_name),
        '[サイトURL]' => esc_url($site_url),
        '[一覧ページのURL]' => esc_url($output_file_url),
        '<!-- PREPRINT_LIST_PLACEHOLDER -->' => $list_items_html,
        '[コピーライト年]' => date('Y'),
         '[現在の年]' => date('Y'),
        '<!-- JSON_LD_ITEM_LIST_PLACEHOLDER -->' => json_encode($json_ld_item_list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT),
    ];
    $final_html_content = str_replace(array_keys($replacements), array_values($replacements), $html_template);
    if ( file_put_contents( $output_file_path, $final_html_content ) === false ) {
        // エラーログ削除済み
        do_action('plpm_list_generation_failed', 'Could not write file');
        return false;
    } else {
        do_action('plpm_list_generation_success', $output_file_url);
        return true;
    }
}
