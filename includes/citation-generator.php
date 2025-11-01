<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_generate_all_citation_formats(array $data) {
    $citations = [];
    $citations['html_basic'] = sprintf(
        '<p><strong>%s</strong>. “%s.” %s, %s. <a href="%s">%s</a>%s</p>',
        esc_html($data['author']), esc_html($data['title']), esc_html($data['publisher']), esc_html($data['pub_year']),
        esc_url($data['output_file_url']), esc_html($data['output_file_url']),
        !empty($data['doi']) ? sprintf(' DOI: <a href="https://doi.org/%s" target="_blank" rel="noopener noreferrer">%s</a>', esc_attr($data['doi']), esc_html($data['doi'])) : ''
    );
    $citations['html_abstract'] = $citations['html_basic'] . '<p>' . esc_html(wp_strip_all_tags($data['abstract'])) . '</p>';
    $citations['plain_basic'] = sprintf(
        '%s. "%s." %s, %s. %s%s',
        $data['author'], $data['title'], $data['publisher'], $data['pub_year'],
        $data['output_file_url'],
        !empty($data['doi']) ? ' DOI: ' . $data['doi'] : ''
    );
    $citations['plain_abstract'] = $citations['plain_basic'] . "\n\n" . wp_strip_all_tags($data['abstract']);
    $citations['bibtex'] = sprintf(
        "@misc{%s,\n  author    = \"{%s}\",\n  title     = \"{%s}\",\n  year      = \"{%s}\",\n  month     = \"{%s}\",\n  publisher = \"{%s}\",\n  howpublished = {%s},\n  url       = \"%s\",\n  note      = \"Version: %s%s%s\"\n}",
        esc_attr($data['bibtex_key']), esc_attr($data['author']), esc_attr($data['title']),
        esc_attr($data['pub_year']), esc_attr($data['pub_month']), esc_attr($data['publisher']),
        esc_attr($data['dc_type']), esc_url($data['output_file_url']), esc_attr($data['version']),
        !empty($data['doi']) ? ', DOI: ' . esc_attr($data['doi']) : '',
        !empty($data['modified_date_ymd']) ? ', Modified: ' . esc_attr($data['modified_date_ymd']) : ''
    );
    $citations['ris'] = sprintf(
        "TY  - UNPB\nTI  - %s\nAU  - %s\nPY  - %s///%s\n%sPB  - %s\nKW  - %s\nN2  - %s\nUR  - %s\nDO  - %s\nER  -",
        $data['title'], $data['author'], $data['pub_year'], $data['pub_month'],
        !empty($data['modified_date_ymd']) ? sprintf("DA  - %s///%s\n", date('Y', strtotime($data['modified_date_ymd'])), date('m', strtotime($data['modified_date_ymd']))) : '',
        $data['publisher'], $data['keywords'],
        str_replace(["\r\n", "\r", "\n"], " ", wp_strip_all_tags($data['abstract'])),
        $data['output_file_url'], !empty($data['doi']) ? $data['doi'] : ''
    );
    $citations['redif'] = sprintf(
        "Template-Type: ReDIF-Paper 1.0\nTitle: %s\nAuthor-Name: %s\nYear: %s\nMonth: %s\nProvider-Name: %s\nFile-URL: %s\nFile-Format: application/pdf\nAbstract: %s\nHandle: RePEc:%s%s%s",
        $data['title'], $data['author'], $data['pub_year'], $data['pub_month'], $data['publisher'],
        $data['pdf_url'], str_replace(["\r\n", "\r", "\n"], " ", wp_strip_all_tags($data['abstract'])),
        $data['bibtex_key'],
        !empty($data['doi']) ? "\nDOI: " . $data['doi'] : '',
        !empty($data['modified_date_ymd']) ? "\nRevision-Date: " . $data['modified_date_ymd'] : ''
    );
    $json_data_for_citation = [
        'bibtex_key' => $data['bibtex_key'], 'author' => $data['author'], 'title' => $data['title'],
        'year' => $data['pub_year'], 'month' => $data['pub_month'], 'publisher' => $data['publisher'],
        'dc_type' => $data['dc_type'], 'html_url' => $data['output_file_url'], 'pdf_url' => $data['pdf_url'],
        'version' => $data['version'], 'abstract' => wp_strip_all_tags($data['abstract']),
        'keywords' => $data['keywords'], 'language' => $data['language'], 'doi' => $data['doi'],
        'pub_date' => $data['pub_date_ymd'], 'modified_date' => $data['modified_date_ymd'],
    ];
    $citations['json'] = json_encode($json_data_for_citation, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PRETTY_PRINT);

        // APA形式 (American Psychological Association)
    $citations['apa'] = sprintf(
        '%s (%s). %s. %s. Retrieved from %s%s',
        $data['author'],
        $data['pub_year'],
        $data['title'],
        $data['publisher'],
        $data['output_file_url'],
        !empty($data['doi']) ? ' https://doi.org/' . $data['doi'] : ''
    );

    // MLA形式 (Modern Language Association)
    $citations['mla'] = sprintf(
        '%s. "%s." %s, %s. Web. %s.',
        $data['author'],
        $data['title'],
        $data['publisher'],
        $data['pub_date_formatted'] ?: $data['pub_year'],
        date('j M Y')
    );

    // Chicago形式 (Chicago Manual of Style)
    $citations['chicago'] = sprintf(
        '%s. "%s." %s. Last modified %s. %s%s.',
        $data['author'],
        $data['title'],
        $data['publisher'],
        $data['modified_date_formatted'] ?: $data['pub_date_formatted'] ?: $data['pub_year'],
        $data['output_file_url'],
        !empty($data['doi']) ? ' https://doi.org/' . $data['doi'] : ''
    );

    // IEEE形式 (Institute of Electrical and Electronics Engineers)
    $citations['ieee'] = sprintf(
        '%s, "%s," %s, %s, [Online]. Available: %s. [Accessed: %s]%s',
        $data['author'],
        $data['title'],
        $data['publisher'],
        $data['pub_year'],
        $data['output_file_url'],
        date('M. j, Y'),
        !empty($data['doi']) ? '. DOI: ' . $data['doi'] : ''
    );

    return $citations;
}
function plpm_generate_citation_download_links_html(string $bibtex_key, array $citations) {
    // 設定値から有効なフォーマットを取得（デフォルトは全形式）
    $enabled_formats = plpm_get_option('citation_formats', array('html_basic', 'html_abstract', 'plain_basic', 'plain_abstract', 'bibtex', 'ris', 'endnote', 'json', 'apa', 'mla', 'chicago', 'ieee'));

    $format_mapping = array(
        'bibtex' => array(
            'label' => __('BibTeX', PLPM_TEXT_DOMAIN),
            'key' => 'bibtex',
            'mime' => 'application/x-bibtex',
            'ext' => '.bib'
        ),
        'ris' => array(
            'label' => __('RIS', PLPM_TEXT_DOMAIN),
            'key' => 'ris',
            'mime' => 'application/x-research-info-systems',
            'ext' => '.ris'
        ),
        'endnote' => array(
            'label' => __('ReDIF', PLPM_TEXT_DOMAIN),
            'key' => 'redif',
            'mime' => 'text/plain',
            'ext' => '.redif'
        ),
        'html_basic' => array(
            'label' => __('HTML', PLPM_TEXT_DOMAIN),
            'key' => 'html_basic',
            'mime' => 'text/html',
            'ext' => '.html'
        ),
        'html_abstract' => array(
            'label' => __('HTML + Abstract', PLPM_TEXT_DOMAIN),
            'key' => 'html_abstract',
            'mime' => 'text/html',
            'ext' => '-abs.html'
        ),
        'plain_basic' => array(
            'label' => __('Plain Text', PLPM_TEXT_DOMAIN),
            'key' => 'plain_basic',
            'mime' => 'text/plain',
            'ext' => '.txt'
        ),
        'plain_abstract' => array(
            'label' => __('Plain Text + Abstract', PLPM_TEXT_DOMAIN),
            'key' => 'plain_abstract',
            'mime' => 'text/plain',
            'ext' => '-abs.txt'
        ),
        'json' => array(
            'label' => __('JSON', PLPM_TEXT_DOMAIN),
            'key' => 'json',
            'mime' => 'application/json',
            'ext' => '.json'
        ),
        'apa' => array(
            'label' => __('APA Style', PLPM_TEXT_DOMAIN),
            'key' => 'apa',
            'mime' => 'text/plain',
            'ext' => '-apa.txt'
        ),
        'mla' => array(
            'label' => __('MLA Style', PLPM_TEXT_DOMAIN),
            'key' => 'mla',
            'mime' => 'text/plain',
            'ext' => '-mla.txt'
        ),
        'chicago' => array(
            'label' => __('Chicago Style', PLPM_TEXT_DOMAIN),
            'key' => 'chicago',
            'mime' => 'text/plain',
            'ext' => '-chicago.txt'
        ),
        'ieee' => array(
            'label' => __('IEEE Style', PLPM_TEXT_DOMAIN),
            'key' => 'ieee',
            'mime' => 'text/plain',
            'ext' => '-ieee.txt'
        )
    );

    $html_links = array();

    foreach ($enabled_formats as $format) {
        if (isset($format_mapping[$format]) && isset($citations[$format_mapping[$format]['key']])) {
            $map = $format_mapping[$format];
            $html_links[] = '<a href="data:' . $map['mime'] . ';charset=utf-8,' . rawurlencode($citations[$map['key']]) . '" download="' . esc_attr($bibtex_key) . $map['ext'] . '">' . $map['label'] . '</a>';
        }
    }

    return implode("\n", $html_links);
}
