<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_generate_breadcrumb_html_for_single_page(array $data) {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    $html .= '<li><a href="' . esc_url($data['site_url']) . '">' . esc_html($data['site_title']) . '</a></li>';
    $html .= '<li><a href="' . esc_url($data['list_page_url']) . '">' . esc_html__('投稿論文一覧', PLPM_TEXT_DOMAIN) . '</a></li>';
    $html .= '<li>' . esc_html($data['title']) . '</li>';
    $html .= '</ol></nav>';
    return $html;
}

function plpm_generate_breadcrumb_html_for_list_page(array $data) {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    $html .= '<li><a href="' . esc_url($data['site_url']) . '">' . esc_html($data['site_title']) . '</a></li>';
    $html .= '<li>' . esc_html__('投稿論文一覧', PLPM_TEXT_DOMAIN) . '</li>';
    $html .= '</ol></nav>';
    return $html;
}

function plpm_generate_breadcrumb_jsonld_for_single_page(array $data) {
    $breadcrumb_ld = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            [
                "@type" => "ListItem",
                "position" => 1,
                "name" => $data['site_title'],
                "item" => $data['site_url']
            ],
            [
                "@type" => "ListItem",
                "position" => 2,
                "name" => __('投稿論文一覧', PLPM_TEXT_DOMAIN),
                "item" => $data['list_page_url']
            ],
            [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $data['title'],
                "item" => $data['output_file_url']
            ]
        ]
    ];
    return $breadcrumb_ld;
}

function plpm_generate_breadcrumb_jsonld_for_list_page(array $data) {
    $breadcrumb_ld = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => [
            [
                "@type" => "ListItem",
                "position" => 1,
                "name" => $data['site_title'],
                "item" => $data['site_url']
            ],
            [
                "@type" => "ListItem",
                "position" => 2,
                "name" => __('投稿論文一覧', PLPM_TEXT_DOMAIN),
                "item" => $data['list_page_url']
            ]
        ]
    ];
    return $breadcrumb_ld;
}
function plpm_generate_author_info_html_for_single_page(array $data) {
    $html = '<div class="plpm-author-info-box">';

    // 2カラムグリッドレイアウト
    $html .= '<div class="plpm-author-info-grid">';

    // 左カラム
    $html .= '<div class="plpm-info-column">';

    if ($data['is_preprint']) {
        $html .= '<div class="plpm-info-item">';
        $html .= '<span class="plpm-info-label">';
        $html .= esc_html__('Type:', PLPM_TEXT_DOMAIN);
        $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('プレプリントは、正式な査読前に公開された研究論文です。研究の早期共有を目的としています。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
        $html .= '</span>';
        $html .= '<span class="plpm-info-value">Preprint</span>';
        $html .= '</div>';
    }

    $author_display = esc_html($data['author']);
    if (!empty($data['author']) && !empty($data['google_scholar_url'])) {
        $author_display = '<a href="' . esc_url($data['google_scholar_url']) . '" class="plpm-author-link" target="_blank" rel="noopener noreferrer" title="' . esc_attr(sprintf(__('Link to %s\'s Google Scholar profile', PLPM_TEXT_DOMAIN), $data['author'])) . '">' . esc_html($data['author']) . '</a>';
    }
    $html .= '<div class="plpm-info-item">';
    $html .= '<span class="plpm-info-label">' . esc_html__('Author:', PLPM_TEXT_DOMAIN) . '</span>';
    $html .= '<span class="plpm-info-value">' . $author_display . '</span>';
    $html .= '</div>';

    if (!empty($data['email'])) {
        $html .= '<div class="plpm-info-item">';
        $html .= '<span class="plpm-info-label">' . esc_html__('Contact:', PLPM_TEXT_DOMAIN) . '</span>';
        $html .= '<span class="plpm-info-value">' . antispambot($data['email']) . '</span>';
        $html .= '</div>';
    }

    $affiliation_display = esc_html($data['affiliation_name']);
    if (!empty($data['affiliation_name']) && !empty($data['affiliation_url']) && $data['affiliation_url'] !== '#') {
        $affiliation_display = '<a href="' . esc_url($data['affiliation_url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($data['affiliation_name']) . '</a>';
    }
    $html .= '<div class="plpm-info-item">';
    $html .= '<span class="plpm-info-label">';
    $html .= esc_html__('Affiliation:', PLPM_TEXT_DOMAIN);
    $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('著者の所属機関です。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
    $html .= '</span>';
    $html .= '<span class="plpm-info-value">' . $affiliation_display . '</span>';
    $html .= '</div>';

    if (!empty($data['doi'])) {
        $doi_display = '<a href="https://doi.org/' . esc_attr($data['doi']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($data['doi']) . '</a>';
        $html .= '<div class="plpm-info-item">';
        $html .= '<span class="plpm-info-label">';
        $html .= esc_html__('DOI:', PLPM_TEXT_DOMAIN);
        $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('Digital Object Identifier - 論文の永続的な識別子です。このリンクから論文の公式ページにアクセスできます。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
        $html .= '</span>';
        $html .= '<span class="plpm-info-value">' . $doi_display . '</span>';
        $html .= '</div>';
    }

    $html .= '</div>'; // 左カラム終了

    // 右カラム
    $html .= '<div class="plpm-info-column">';

    $html .= '<div class="plpm-info-item">';
    $html .= '<span class="plpm-info-label">';
    $html .= esc_html__('Published:', PLPM_TEXT_DOMAIN);
    $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('論文の初回公開日です。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
    $html .= '</span>';
    $html .= '<span class="plpm-info-value">' . esc_html($data['pub_date_raw']) . '</span>';
    $html .= '</div>';

    if (!empty($data['modified_date_raw'])) {
        $html .= '<div class="plpm-info-item">';
        $html .= '<span class="plpm-info-label">';
        $html .= esc_html__('Modified:', PLPM_TEXT_DOMAIN);
        $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('論文の最終更新日です。内容の修正や追加があった場合に更新されます。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
        $html .= '</span>';
        $html .= '<span class="plpm-info-value">' . esc_html($data['modified_date_raw']) . '</span>';
        $html .= '</div>';
    }

    $html .= '<div class="plpm-info-item">';
    $html .= '<span class="plpm-info-label">';
    $html .= esc_html__('Publisher:', PLPM_TEXT_DOMAIN);
    $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('論文を公開している組織または個人です。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
    $html .= '</span>';
    $html .= '<span class="plpm-info-value">' . esc_html($data['publisher']) . '</span>';
    $html .= '</div>';

    $license_display = esc_html($data['license_name']);
    if (!empty($data['license_name']) && !empty($data['license_url']) && $data['license_url'] !== '#') {
        $license_display = '<a href="' . esc_url($data['license_url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($data['license_name']) . '</a>';
    }
    $html .= '<div class="plpm-info-item">';
    $html .= '<span class="plpm-info-label">';
    $html .= esc_html__('License:', PLPM_TEXT_DOMAIN);
    $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('論文の利用ライセンスです。Creative Commonsライセンスにより、条件付きで自由に利用できます。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
    $html .= '</span>';
    $html .= '<span class="plpm-info-value">' . $license_display . '</span>';
    $html .= '</div>';

    $html .= '<div class="plpm-info-item">';
    $html .= '<span class="plpm-info-label">';
    $html .= esc_html__('Version:', PLPM_TEXT_DOMAIN);
    $html .= ' <span class="plpm-tooltip" data-tooltip="' . esc_attr__('論文のバージョン番号です。改訂があるたびに更新されます。', PLPM_TEXT_DOMAIN) . '">ⓘ</span>';
    $html .= '</span>';
    $html .= '<span class="plpm-info-value">' . esc_html($data['version']) . '</span>';
    $html .= '</div>';

    $html .= '</div>'; // 右カラム終了

    $html .= '</div>'; // グリッド終了
    $html .= '</div>'; // ボックス終了

    return $html;
}
function plpm_generate_pdf_viewer_html_for_single_page(array $data) {
    if (empty($data['pdf_url'])) {
        // エラーログ削除済み
        return '<p>' . __('PDF URL is not available. Please upload or select a PDF file in the admin panel.', PLPM_TEXT_DOMAIN) . '</p>';
    }

    // Google Viewer用には実際のWordPressメディアライブラリURLを使用
    $actual_pdf_url = plpm_get_actual_pdf_url($data['post_id']);
    if (empty($actual_pdf_url)) {
        $actual_pdf_url = $data['pdf_url'];
    }

    $modified_timestamp = get_post_modified_time('U', true, $data['post_id']);
    $pdf_url_with_cachebuster = add_query_arg('_', $modified_timestamp, $actual_pdf_url);
    // 複数のPDFビューアーオプションを準備
    $viewers = array(
        'google' => 'https://docs.google.com/gview?url=' . urlencode(esc_url_raw($pdf_url_with_cachebuster)) . '&embedded=true',
        'mozilla' => 'https://mozilla.github.io/pdf.js/web/viewer.html?file=' . urlencode(esc_url_raw($pdf_url_with_cachebuster)),
        'office' => 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode(esc_url_raw($pdf_url_with_cachebuster)),
        'direct' => $pdf_url_with_cachebuster . '#toolbar=0&navpanes=0&scrollbar=0'
    );

    // デフォルトビューワーを mozilla (PDF.js) に変更（Content-Disposition対策）
    $primary_viewer_url = $viewers['mozilla'];

    $download_filename = $data['post_id'] . '-' . $data['title'] . '.pdf';

            // 視覚的に明確なPDFビューアー（自動リトライ付き）
    $html = '<div class="plpm-pdf-viewer-container">';

    // PDFフレームのヘッダー
    $html .= '<div style="background: var(--plpm-bg, #f8f9fa); border: 2px solid var(--plpm-border, #dee2e6); border-bottom: 1px solid var(--plpm-border, #dee2e6); padding: 10px; border-radius: 8px 8px 0 0; font-weight: bold; color: var(--plpm-text, #333);">';
    $html .= '📄 ' . __('PDF文書', PLPM_TEXT_DOMAIN) . ' - ' . esc_html($data['title']);
    $html .= '</div>';

    // PDFローディング表示（シンプルなスピナー）
    $html .= '<div id="plpm-pdf-loading-' . $data['post_id'] . '" style="background: var(--plpm-bg, white); border: 2px solid var(--plpm-border, #dee2e6); border-top: none; border-radius: 0 0 8px 8px; height: 800px; display: flex; align-items: center; justify-content: center; flex-direction: column;">';
    $html .= '<div class="plpm-spinner-circle"></div>';
    $html .= '<div style="margin-top: 20px; font-size: 16px; color: var(--plpm-text, #666);">PDF読み込み中...</div>';
    $html .= '</div>';

    // メインのiframe（JavaScriptで後から読み込み）
    $html .= '<iframe id="plpm-pdf-iframe-' . $data['post_id'] . '" src="" style="width:100%; height:800px; border: 2px solid var(--plpm-border, #dee2e6); border-top: none; border-radius: 0 0 8px 8px; display: none;" title="' . esc_attr__('PDF Viewer for', PLPM_TEXT_DOMAIN) . ' ' . esc_attr($data['title']) . '">';

    // iframe内のフォールバック（簡潔に）
    $html .= '<div style="padding: 40px; text-align: center; background: #f9f9f9;">';
    $html .= '<p><strong>' . __('PDFを読み込めませんでした。', PLPM_TEXT_DOMAIN) . '</strong></p>';
    $html .= '<p><small>' . __('下のダウンロードボタンをご利用ください。', PLPM_TEXT_DOMAIN) . '</small></p>';
    $html .= '</div>';

    $html .= '</iframe>';

    // エラー表示（最初は非表示）
    $html .= '<div id="plpm-pdf-error-' . $data['post_id'] . '" style="background: var(--plpm-bg, white); border: 2px solid #dc3545; border-top: none; border-radius: 0 0 8px 8px; height: 800px; display: none; align-items: center; justify-content: center; flex-direction: column;">';
    $html .= '<div style="text-align: center; color: #dc3545;">';
    $html .= '<div style="font-size: 48px; margin-bottom: 20px;">⚠️</div>';
    $html .= '<div style="font-size: 18px; margin-bottom: 10px; font-weight: bold;">PDFを読み込めませんでした</div>';
    $html .= '<div style="font-size: 14px; color: #6c757d;">下のボタンでダウンロードしてください</div>';
    $html .= '</div>';
    $html .= '</div>';

    // カラーテーマ対応のシンプルなボタン配置
    $html .= '<div class="plpm-pdf-controls">';
    $html .= '<button id="plpm-reload-btn-' . $data['post_id'] . '" onclick="reloadPdfWithLoading(' . $data['post_id'] . ', \'' . esc_js($primary_viewer_url) . '\')" class="plpm-pdf-btn plpm-reload-btn">';
    $html .= '🔄 ' . __('PDFを再読み込み', PLPM_TEXT_DOMAIN);
    $html .= '</button>';
    $html .= sprintf(
        '<a href="%s" download="%s" target="_blank" rel="noopener noreferrer" class="plpm-pdf-btn plpm-download-btn">📄 %s</a>',
        esc_url($data['pdf_url']),
        esc_attr($download_filename),
        __('PDFをダウンロード', PLPM_TEXT_DOMAIN)
    );
    $html .= '</div>';

    // ローディングアニメーションのCSS
    $html .= '<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes loading-bar {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .loading-spin {
        display: inline-block;
        animation: spin 1s linear infinite;
    }
    .plpm-pdf-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    </style>';

    $html .= '</div>';

        // 自動リトライ付きPDF読み込みJavaScript（複数ビューアー対応）
    $post_id = $data['post_id'];
    $js_pdf_url = esc_js($primary_viewer_url);
    $js_viewers = json_encode($viewers);

    $html .= <<<EOJ
<script type="text/javascript">
// 利用可能なPDFビューアー（優先度順）
var pdfViewers = {$js_viewers};
// mozilla (PDF.js) を最優先に変更（Content-Disposition: attachment 対策）
var viewerOrder = ["mozilla", "google", "office", "direct"];

// DOMContentLoaded後にPDFを読み込み
document.addEventListener("DOMContentLoaded", function() {
    loadPdfWithAutoRetry({$post_id}, "{$js_pdf_url}", 0, 0);
});

function loadPdfWithAutoRetry(postId, pdfUrl, retryCount, viewerIndex) {
    retryCount = retryCount || 0;
    viewerIndex = viewerIndex || 0;

    const maxRetries = 3;
    const iframe = document.getElementById("plpm-pdf-iframe-" + postId);
    const loading = document.getElementById("plpm-pdf-loading-" + postId);
    const error = document.getElementById("plpm-pdf-error-" + postId);

    if (!iframe || !loading) return;

    // ローディング表示
    loading.style.display = "flex";
    iframe.style.display = "none";
    if (error) error.style.display = "none";

    // 現在のビューアーを取得
    const currentViewer = viewerOrder[viewerIndex];
    const currentUrl = pdfViewers[currentViewer];

    // 遅延後にPDF読み込み
    setTimeout(function() {
        const finalUrl = currentUrl + (currentUrl.includes("?") ? "&" : "?") + "retry=" + retryCount + "&t=" + Date.now();
        iframe.src = finalUrl;

        let loadTimeout;
        let loaded = false;

        function onLoadSuccess() {
            if (loaded) return;
            loaded = true;
            clearTimeout(loadTimeout);
            loading.style.display = "none";
            iframe.style.display = "block";
            if (error) error.style.display = "none";
        }

        function onLoadError() {
            if (loaded) return;
            loaded = true;
            clearTimeout(loadTimeout);

            if (retryCount < maxRetries) {
                // 同じビューアーでリトライ
                setTimeout(function() {
                    loadPdfWithAutoRetry(postId, pdfUrl, retryCount + 1, viewerIndex);
                }, 2000 + (retryCount * 1000));
            } else if (viewerIndex < viewerOrder.length - 1) {
                // 次のビューアーを試す
                setTimeout(function() {
                    loadPdfWithAutoRetry(postId, pdfUrl, 0, viewerIndex + 1);
                }, 1000);
            } else {
                // 全てのビューアーで失敗
                loading.style.display = "none";
                if (error) error.style.display = "flex";
                iframe.style.display = "none";
            }
        }

        iframe.onload = onLoadSuccess;
        iframe.onerror = onLoadError;

        // 10秒でタイムアウト（短縮）
        loadTimeout = setTimeout(onLoadError, 10000);

    }, retryCount * 500);
}

function reloadPdfWithLoading(postId, pdfUrl) {
    var button = document.getElementById("plpm-reload-btn-" + postId);
    var iframe = document.getElementById("plpm-pdf-iframe-" + postId);
    var loading = document.getElementById("plpm-pdf-loading-" + postId);
    var error = document.getElementById("plpm-pdf-error-" + postId);

    if (button) {
        // ボタンをローディング状態に
        button.disabled = true;
        button.innerHTML = '<span class="plpm-spinner-small"></span> 読み込み中...';
        button.style.opacity = '0.7';

        // ローディング表示を表示、iframeとエラーを非表示
        if (loading) loading.style.display = "flex";
        if (iframe) iframe.style.display = "none";
        if (error) error.style.display = "none";

        // キャッシュバスター付きで再読み込み
        var timestamp = Date.now();
        var newUrl = pdfUrl.split('?')[0] + '?t=' + timestamp;

        // PDF再読み込み（最初のビューアーから再開）
        loadPdfWithAutoRetry(postId, newUrl, 0, 0);

        // 3秒後にボタン復活
        setTimeout(function() {
            button.disabled = false;
            button.innerHTML = '🔄 PDFを再読み込み';
            button.style.opacity = '1';
        }, 3000);
    }
}

// direct モード用のシンプルなリロード（iframe を直接リロード）
function reloadPdfDirect(postId) {
    var iframe = document.getElementById("plpm-pdf-iframe-" + postId);
    var button = document.getElementById("plpm-reload-btn-" + postId);
    var icon = button ? button.querySelector(".reload-icon") : null;
    var btnText = button ? button.querySelector(".btn-text") : null;

    if (iframe) {
        // ボタンのローディング表示
        if (button && icon) {
            button.disabled = true;
            icon.textContent = "⏳";
            if (btnText) btnText.textContent = "読み込み中...";
        }

        // iframeを再読み込み（キャッシュバスター付き）
        var currentSrc = iframe.src;
        var timestamp = Date.now();

        // URLにタイムスタンプを追加してキャッシュを回避
        if (currentSrc.indexOf('?') !== -1) {
            iframe.src = currentSrc.split('?')[0] + '?t=' + timestamp + '#toolbar=0&navpanes=0&scrollbar=0';
        } else {
            iframe.src = currentSrc.split('#')[0] + '?t=' + timestamp + '#toolbar=0&navpanes=0&scrollbar=0';
        }

        // 2秒後にボタン復活
        setTimeout(function() {
            if (button && icon) {
                button.disabled = false;
                icon.textContent = "🔄";
                if (btnText) btnText.textContent = "PDFを再読み込み";
            }
        }, 2000);
    }
}

// 後方互換性のため
function reloadPdf(postId, pdfUrl) {
    reloadPdfWithLoading(postId, pdfUrl);
}
</script>
EOJ;

    return $html;
}
function plpm_generate_back_button_html(string $list_page_url) {
    $list_page_title = plpm_get_option('list_page_title', '投稿論文一覧');
    return '<p><a href="' . esc_url($list_page_url) . '" class="back-button">' . sprintf(esc_html__('← %sに戻る', PLPM_TEXT_DOMAIN), esc_html($list_page_title)) . '</a></p>';
}
function plpm_generate_doi_meta_tag(array $data) {
    if (!empty($data['doi'])) {
        return '<meta name="citation_doi" content="' . esc_attr($data['doi']) . '">' . "\n";
    }
    return '';
}
function plpm_generate_dc_modified_date_meta_tag(array $data) {
    if (!empty($data['modified_date_ymd'])) {
        return '<meta name="DC.date.modified" content="' . esc_attr($data['modified_date_ymd']) . '">' . "\n";
    }
    return '';
}
function plpm_generate_citation_author_institution_meta_tag(array $data) {
    if (!empty($data['affiliation_name'])) {
        return '<meta name="citation_author_institution" content="' . esc_attr($data['affiliation_name']) . '">' . "\n";
    }
    return '';
}
function plpm_generate_citation_references_meta_tags(array $data) {
    $html = '';
    if (!empty($data['references'])) {
        $reference_lines = preg_split('/\r\n|\r|\n/', $data['references']);
        foreach ($reference_lines as $line) {
            $trimmed_line = trim($line);
            if (!empty($trimmed_line)) {
                $html .= '    <meta name="citation_reference" content="' . esc_attr($trimmed_line) . '">' . "\n";
            }
        }
    }
    return rtrim($html);
}
