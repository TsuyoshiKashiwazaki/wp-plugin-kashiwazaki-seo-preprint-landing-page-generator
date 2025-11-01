<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 正しいPDF URLを取得する（/paper/パス形式を維持）
 */
function plpm_get_correct_pdf_url( $post_id ) {
    $pdf_url = get_post_meta( $post_id, '_plpm_pdf_url', true );

    // 既に /paper/ パス形式の場合はそのまま使用
    if ( !empty($pdf_url) && strpos($pdf_url, '/paper/') !== false ) {
        return $pdf_url;
    }

    // WordPressメディアライブラリURLの場合は /paper/ 形式に変換
    $pdf_attachment_id = get_post_meta( $post_id, '_plpm_pdf_attachment_id', true );
    if ( $pdf_attachment_id ) {
        $wp_pdf_url = wp_get_attachment_url( $pdf_attachment_id );
        if ( $wp_pdf_url ) {
            // /paper/{投稿ID}.pdf 形式のURLを生成
            $paper_pdf_url = home_url('/paper/' . $post_id . '.pdf');

            return $paper_pdf_url;
        }
    }

    return $pdf_url;
}

/**
 * 実際のPDF ファイルURLを取得する（リダイレクト処理用）
 */
function plpm_get_actual_pdf_url( $post_id ) {
    $pdf_attachment_id = get_post_meta( $post_id, '_plpm_pdf_attachment_id', true );
    if ( $pdf_attachment_id ) {
        $wp_pdf_url = wp_get_attachment_url( $pdf_attachment_id );
        if ( $wp_pdf_url ) {
            return $wp_pdf_url;
        }
    }

    // フォールバック: 古いURLをそのまま使用
    return get_post_meta( $post_id, '_plpm_pdf_url', true );
}
function plpm_gather_preprint_data($post_id) {
    $post = get_post($post_id);
    if (!$post) {
        return [];
    }
    $data = [
        'post_id' => $post_id,
        'title' => $post->post_title,
        'is_preprint' => get_post_meta( $post->ID, '_plpm_is_preprint', true ),
        'author' => get_post_meta( $post->ID, '_plpm_author', true ) ?: '',
        'email' => get_post_meta( $post->ID, '_plpm_email', true ),
        'affiliation_name' => get_post_meta( $post->ID, '_plpm_affiliation_name', true ) ?: '',
        'affiliation_url' => get_post_meta( $post->ID, '_plpm_affiliation_url', true ) ?: '#',
        'author_url' => get_post_meta( $post->ID, '_plpm_author_url', true ) ?: '#',
        'google_scholar_url' => get_post_meta( $post->ID, '_plpm_google_scholar_url', true ),
        'doi' => get_post_meta( $post->ID, '_plpm_doi', true ),
        'pub_date_raw' => get_post_meta( $post->ID, '_plpm_pub_date', true ),
        'modified_date_raw' => get_post_meta( $post->ID, '_plpm_modified_date', true ),
        'publisher' => get_post_meta( $post->ID, '_plpm_publisher', true ) ?: '',
        'language' => get_post_meta( $post->ID, '_plpm_language', true ) ?: 'en',
        'keywords' => get_post_meta( $post->ID, '_plpm_keywords', true ),
        'abstract' => get_post_meta( $post->ID, '_plpm_abstract', true ),
        'pdf_url' => plpm_get_correct_pdf_url( $post->ID ),
        'html_filename' => (string) $post->ID,
        'bibtex_key' => get_post_meta( $post->ID, '_plpm_bibtex_key', true ),
        'references' => get_post_meta( $post->ID, '_plpm_references', true ),
        'version' => get_post_meta( $post->ID, '_plpm_version', true ) ?: '1.0',
        'license_name' => get_post_meta( $post->ID, '_plpm_license', true ) ?: 'CC BY 4.0',
        'license_url' => get_post_meta( $post->ID, '_plpm_license_url', true ) ?: 'https://creativecommons.org/licenses/by/4.0/',
        'schema_type' => get_post_meta( $post->ID, '_plpm_schema_type', true ) ?: 'ScholarlyArticle',
        'site_logo_url' => get_post_meta( $post->ID, '_plpm_site_logo_url', true ),
        'og_image_url' => get_post_meta( $post->ID, '_plpm_og_image_url', true ),
        'twitter_site' => get_post_meta( $post->ID, '_plpm_twitter_site', true ),
        'twitter_creator' => get_post_meta( $post->ID, '_plpm_twitter_creator', true ),
        'output_file_url' => home_url('/paper/' . $post->ID . '/'),
        'site_url' => home_url('/'),
        'site_title' => get_bloginfo('name'),
        'list_page_url' => home_url('/paper/'),
    ];
    $data['pub_date_scholar'] = $data['pub_date_raw'] ? date('Y/m/d', strtotime($data['pub_date_raw'])) : '';
    $data['pub_year'] = $data['pub_date_raw'] ? date('Y', strtotime($data['pub_date_raw'])) : '';
    $data['pub_month'] = $data['pub_date_raw'] ? date('m', strtotime($data['pub_date_raw'])) : '';
    $data['pub_date_ymd'] = $data['pub_date_raw'] ? date('Y-m-d', strtotime($data['pub_date_raw'])) : '';
    $data['pub_date_formatted'] = $data['pub_date_raw'] ? date('F j, Y', strtotime($data['pub_date_raw'])) : '';
    try {
        $timezone = new DateTimeZone('Asia/Tokyo');
        $date_obj = $data['pub_date_raw'] ? new DateTime($data['pub_date_raw'] . ' 00:00:00', $timezone) : null;
        $data['pub_date_iso8601'] = $date_obj ? $date_obj->format('c') : ($data['pub_date_raw'] ? date('Y-m-d', strtotime($data['pub_date_raw'])) : '');
    } catch (Exception $e) {
                    // エラーログ削除済み
        $data['pub_date_iso8601'] = $data['pub_date_raw'] ? date('Y-m-d', strtotime($data['pub_date_raw'])) : '';
    }
    $data['modified_date_ymd'] = '';
    $data['modified_date_iso8601'] = '';
    $data['modified_date_formatted'] = '';
    if (!empty($data['modified_date_raw'])) {
        $data['modified_date_ymd'] = date('Y-m-d', strtotime($data['modified_date_raw']));
        $data['modified_date_formatted'] = date('F j, Y', strtotime($data['modified_date_raw']));
        try {
            $timezone = new DateTimeZone('Asia/Tokyo');
            $mod_date_obj = new DateTime($data['modified_date_raw'] . ' 00:00:00', $timezone);
            $data['modified_date_iso8601'] = $mod_date_obj->format('c');
        } catch (Exception $e) {
            // エラーログ削除済み
            $data['modified_date_iso8601'] = date('Y-m-d', strtotime($data['modified_date_raw']));
        }
    }
    $data['dc_type'] = $data['is_preprint'] ? 'Preprint' : 'Text';
    return $data;
}
