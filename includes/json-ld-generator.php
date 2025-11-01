<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_generate_json_ld_for_single_page(array $data) {
    $json_ld = [
        "@context" => "https://schema.org",
        "@type" => $data['schema_type'],
        "headline" => $data['title'],
        "name" => $data['title'],
        "description" => mb_substr(wp_strip_all_tags($data['abstract']), 0, 120) . '...',
        "abstract" => wp_strip_all_tags($data['abstract']),
        "keywords" => $data['keywords'],
        "inLanguage" => $data['language'],
        "datePublished" => $data['pub_date_iso8601'],
        "author" => [
            "@type" => "Person",
            "name" => $data['author'],
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => $data['publisher'],
            "url" => esc_url($data['affiliation_url']),
        ],
        "license" => esc_url($data['license_url']),
        "mainEntityOfPage" => ["@type" => "WebPage", "@id" => esc_url($data['output_file_url'])],
        "version" => $data['version'],
        "associatedMedia" => [
            "@type" => "MediaObject",
            "contentUrl" => esc_url($data['pdf_url']),
            "encodingFormat" => "application/pdf",
            "name" => $data['title'] . " (PDF)"
        ]
    ];
    if (!empty($data['modified_date_iso8601'])) {
        $json_ld['dateModified'] = $data['modified_date_iso8601'];
    }
    if (!empty($data['affiliation_name']) || (!empty($data['affiliation_url']) && $data['affiliation_url'] !== '#')) {
        $json_ld['author']['affiliation'] = ["@type" => "Organization"];
        if (!empty($data['affiliation_name'])) $json_ld['author']['affiliation']['name'] = $data['affiliation_name'];
        if (!empty($data['affiliation_url']) && $data['affiliation_url'] !== '#') $json_ld['author']['affiliation']['url'] = esc_url($data['affiliation_url']);
    }
    if (!empty($data['author_url']) && $data['author_url'] !== '#') {
        $json_ld['author']['url'] = esc_url($data['author_url']);
    }
    if (!empty($data['email'])) {
        $json_ld['author']['email'] = sanitize_email($data['email']);
    }
    if (!empty($data['google_scholar_url'])) {
        $json_ld['author']['sameAs'] = [esc_url($data['google_scholar_url'])];
    }
    if (!empty($data['doi'])) {
        $json_ld['identifier'] = esc_attr($data['doi']);
    }
    if (!empty($data['og_image_url'])) {
        $json_ld['image'] = ["@type" => "ImageObject", "url" => esc_url($data['og_image_url'])];
    }
    if (!empty($data['site_logo_url'])) {
        if (!isset($json_ld['publisher']) || !is_array($json_ld['publisher'])) {
            $json_ld['publisher'] = ["@type" => "Organization"];
        }
        if (empty($json_ld['publisher']['name'])) $json_ld['publisher']['name'] = get_bloginfo('name');
        if (empty($json_ld['publisher']['url']) || $json_ld['publisher']['url'] === '#') $json_ld['publisher']['url'] = home_url('/');
        $json_ld['publisher']['logo'] = ["@type" => "ImageObject", "url" => esc_url($data['site_logo_url'])];
    }
    return '<script type="application/ld+json">' .
           json_encode($json_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT) .
           '</script>';
}