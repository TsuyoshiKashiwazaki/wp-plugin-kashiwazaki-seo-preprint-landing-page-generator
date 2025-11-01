<?php
/**
 * ===================================================================
 * 🚨 STATIC HTML GENERATION DISABLED! 🚨
 * ===================================================================
 *
 * This file contains LEGACY static HTML generation code that is
 * COMPLETELY DISABLED in the current dynamic system.
 *
 * DO NOT re-enable this code - it will create physical files and
 * cause server permission errors.
 *
 * All HTML is now generated dynamically via template_redirect.
 * ===================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * LEGACY FUNCTION - DISABLED FOR DYNAMIC SYSTEM
 * This function is kept for reference but should NEVER be called
 */
function plpm_generate_html_file( $post_id ) {
    // ===================================================================
    // 🚨 FUNCTION DISABLED - DYNAMIC SYSTEM ACTIVE 🚨
    // This function creates static files and is now disabled.
    // All HTML is generated dynamically via WordPress template system.
    // ===================================================================
    return true; // Always return success for compatibility
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'preprint_page' || $post->post_status !== 'publish') {
        return false;
    }
    $preprint_data = plpm_gather_preprint_data($post_id);
    if ( empty($preprint_data['title']) || empty($preprint_data['author']) || empty($preprint_data['pub_date_raw']) ||
         empty($preprint_data['publisher']) || empty($preprint_data['keywords']) || empty($preprint_data['abstract']) ||
         empty($preprint_data['pdf_url']) || empty($preprint_data['html_filename']) || empty($preprint_data['bibtex_key']) ) {
        $error_message = 'Error: Missing required fields';
        if (empty($preprint_data['pdf_url'])) $error_message .= ' (PDF URL is missing)';
        update_post_meta($post_id, '_plpm_generation_status', $error_message);
        // エラーログ削除済み
        return false;
    }
    $output_dir_path = PLPM_PAPER_DIR;
    $output_file_path = $output_dir_path . $preprint_data['html_filename'] . '.html';
    if ( ! file_exists( $output_dir_path ) ) {
        if ( ! wp_mkdir_p( $output_dir_path ) ) {
            update_post_meta($post_id, '_plpm_generation_status', 'Error: Could not create directory ' . $output_dir_path);
            // エラーログ削除済み
            return false;
        }
    }
    if ( ! wp_is_writable( $output_dir_path ) ) {
        update_post_meta($post_id, '_plpm_generation_status', 'Error: Directory not writable ' . $output_dir_path);
        // エラーログ削除済み
        return false;
    }
    $html_template = plpm_get_html_template_content();
    $breadcrumb_html = plpm_generate_breadcrumb_html_for_single_page($preprint_data);
    $author_info_html = plpm_generate_author_info_html_for_single_page($preprint_data);
    $pdf_viewer_html = plpm_generate_pdf_viewer_html_for_single_page($preprint_data);
    $back_button_html = plpm_generate_back_button_html($preprint_data['list_page_url']);
    $json_ld_html = plpm_generate_json_ld_for_single_page($preprint_data);
    $citations = plpm_generate_all_citation_formats($preprint_data);
    $download_links_html = plpm_generate_citation_download_links_html($preprint_data['bibtex_key'], $citations);
    $replacements = [
        '[ページタイトル]' => esc_html($preprint_data['title']),
        '[メタ説明]' => esc_attr(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 120) . '...'),
        '[メタキーワード]' => esc_attr($preprint_data['keywords'] . ', ' . $preprint_data['author'] . ', ' . $preprint_data['publisher'] . ($preprint_data['is_preprint'] ? ', Preprint' : '')),
        '[著者名と発行者]' => esc_attr($preprint_data['author'] . ', ' . $preprint_data['publisher']),
        '[カノニカルURL]' => esc_url($preprint_data['output_file_url']),
        '[論文タイトル]' => esc_html($preprint_data['title']),
        '[著者名]' => esc_html($preprint_data['author']),
        '[公開日YYYY/MM/DD]' => esc_html($preprint_data['pub_date_scholar']),
        '[公開日YYYY-MM-DD]' => esc_html($preprint_data['pub_date_ymd']),
        '[更新日YYYY-MM-DD]' => !empty($preprint_data['modified_date_ymd']) ? esc_html($preprint_data['modified_date_ymd']) : '',
        '[発行者]' => esc_html($preprint_data['publisher']),
        '[言語]' => esc_attr($preprint_data['language']),
        '[キーワード]' => esc_html($preprint_data['keywords']),
        '[HTMLファイルのURL]' => esc_url($preprint_data['output_file_url']),
        '[PDFファイルのURL]' => esc_url($preprint_data['pdf_url']),
        '[公開年YYYY]' => esc_html($preprint_data['pub_year']),
        '[公開日時ISO8601]' => esc_attr($preprint_data['pub_date_iso8601']),
        '[更新日時ISO8601]' => !empty($preprint_data['modified_date_iso8601']) ? esc_attr($preprint_data['modified_date_iso8601']) : '',
        '[要旨簡易]' => esc_html(mb_substr(wp_strip_all_tags($preprint_data['abstract']), 0, 100) . '...'),
        '[要旨全文]' => esc_html($preprint_data['abstract']),
        '[ライセンス名]' => esc_html($preprint_data['license_name']),
        '[ライセンスURL]' => esc_url($preprint_data['license_url']),
        '[DCタイプ]' => esc_attr($preprint_data['dc_type']),
        '[Schemaタイプ]' => esc_attr($preprint_data['schema_type']),
        '[所属組織名]' => esc_html($preprint_data['affiliation_name']),
        '[所属組織URL]' => esc_url($preprint_data['affiliation_url']),
        '[著者個人URL]' => esc_url($preprint_data['author_url']),
        '[版本]' => esc_html($preprint_data['version']),
        '[バージョン]' => esc_html($preprint_data['version']),
        '[OGタイプ]' => 'article',
        '[サイト名]' => esc_attr($preprint_data['publisher']),
        '[ロケール]' => ($preprint_data['language'] === 'ja' ? 'ja_JP' : 'en_US'),
        '[Twitterカードタイプ]' => 'summary',
        '[コピーライト年]' => date('Y'),
        '[要旨全文HTML]' => wpautop(esc_html($preprint_data['abstract'])),

        '[BIBTEX_KEY]' => esc_html($preprint_data['bibtex_key']),
        '[公開月MM]' => esc_html($preprint_data['pub_month']),
        '[DOI]' => esc_attr($preprint_data['doi']),
        '<!-- BREADCRUMB_PLACEHOLDER -->' => $breadcrumb_html,
        '<!-- AUTHOR_INFO_PLACEHOLDER -->' => $author_info_html,
        '<!-- PDF_VIEWER_PLACEHOLDER -->' => $pdf_viewer_html,
        '<!-- BACK_BUTTON_PLACEHOLDER -->' => $back_button_html,
        '<!-- JSON_LD_PLACEHOLDER -->' => $json_ld_html,
        '<!-- DOWNLOAD_LINKS_PLACEHOLDER -->' => $download_links_html,
        '<!-- CITATION_DOI_PLACEHOLDER -->' => plpm_generate_doi_meta_tag($preprint_data),
        '<!-- DC_MODIFIED_DATE_PLACEHOLDER -->' => plpm_generate_dc_modified_date_meta_tag($preprint_data),
        '<!-- CITATION_AUTHOR_INSTITUTION_PLACEHOLDER -->' => plpm_generate_citation_author_institution_meta_tag($preprint_data),
        '<!-- CITATION_REFERENCES_PLACEHOLDER -->' => plpm_generate_citation_references_meta_tags($preprint_data),
        '<!-- CITATION_SCRIPT_PLACEHOLDER -->' => '',
    ];
    $html_content = str_replace(array_keys($replacements), array_values($replacements), $html_template);
    if ( file_put_contents( $output_file_path, $html_content ) === false ) {
        update_post_meta($post_id, '_plpm_generation_status', 'Error: Could not write file ' . $output_file_path);
        do_action('plpm_generation_failed', $post_id, 'Could not write file');
        // エラーログ削除済み
        return false;
    } else {
        update_post_meta($post_id, '_plpm_generation_status', 'Success: ' . current_time('mysql'));
        update_post_meta($post_id, '_plpm_generated_url', $preprint_data['output_file_url']);
        do_action('plpm_generation_success', $post_id, $preprint_data['output_file_url']);

        return true;
    }
}
