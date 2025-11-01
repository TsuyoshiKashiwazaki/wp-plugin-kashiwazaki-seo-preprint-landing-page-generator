<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_get_html_template_content() {
    $template = <<<'HTML'
<!DOCTYPE html>
<html lang="[言語]" prefix="og: http://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>[ページタイトル]</title>
<meta name="description" content="[メタ説明]">
<meta name="keywords" content="[メタキーワード]">
<meta name="author" content="[著者名と発行者]">
<link rel="canonical" href="[カノニカルURL]">
<meta name="robots" content="index, follow">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<meta name="theme-color" content="#ffffff">
<meta name="citation_title"              content="[論文タイトル]">
<meta name="citation_author"             content="[著者名]">
<!-- CITATION_AUTHOR_INSTITUTION_PLACEHOLDER -->
<meta name="citation_publication_date"   content="[公開日YYYY-MM-DD]">
<meta name="citation_publisher"          content="[発行者]">
<meta name="citation_language"           content="[言語]">
<meta name="citation_keywords"           content="[キーワード]">
<meta name="citation_abstract_html_url"  content="[HTMLファイルのURL]#abstract">
<meta name="citation_fulltext_html_url"  content="[HTMLファイルのURL]">
<meta name="citation_pdf_url"            content="[PDFファイルのURL]">
<meta name="citation_year"               content="[公開年YYYY]">
<!-- CITATION_REFERENCES_PLACEHOLDER -->
<!-- CITATION_DOI_PLACEHOLDER -->
<meta name="DC.title"       content="[論文タイトル]">
<meta name="DC.creator"     content="[著者名]">
<meta name="DC.date"        content="[公開日YYYY-MM-DD]">
<!-- DC_MODIFIED_DATE_PLACEHOLDER -->
<meta name="DC.publisher"   content="[発行者]">
<meta name="DC.language"    content="[言語]">
<meta name="DC.description" content="[要旨簡易]">
<meta name="DC.rights"      content="[ライセンス名]">
<meta name="DC.type"        content="[DCタイプ]">
<meta name="DC.format"      content="text/html">
<meta name="DC.identifier"  content="[HTMLファイルのURL]">
<meta name="DC.identifier" scheme="DOI" content="[DOI]">
<meta name="DC.subject"     content="[キーワード]">
<meta property="og:title"       content="[論文タイトル]">
<meta property="og:type"        content="[OGタイプ]">
<meta property="og:url"         content="[HTMLファイルのURL]">
<meta property="og:description" content="[要旨簡易]">
<meta property="og:site_name"   content="[サイト名]">
<meta property="og:locale"      content="[ロケール]">
<meta property="article:published_time" content="[公開日時ISO8601]">
<meta property="article:modified_time" content="[更新日時ISO8601]">
<meta name="twitter:card"        content="[Twitterカードタイプ]">
<meta name="twitter:title"       content="[論文タイトル]">
<meta name="twitter:description" content="[要旨簡易]">
<!-- JSON_LD_PLACEHOLDER -->
<style>
body { font-family: sans-serif; line-height: 1.6; margin: 2em; max-width: 800px; margin-left: auto; margin-right: auto; padding: 1em; }
h1, h2 { margin-top: 1.5em; margin-bottom: 0.5em; }
h1 { font-size: 1.8em; border-bottom: 2px solid #eee; padding-bottom: 0.3em; }
h2 { font-size: 1.4em; border-bottom: 1px solid #eee; padding-bottom: 0.2em; }
.author-info { margin-bottom: 1.5em; }
.author-info p, .abstract p, .keywords p { margin: 0.5em 0; }
.author-info strong { min-width: 120px; display: inline-block; }
.author-info p .plpm-author-link { text-decoration: underline; color: #0066cc; }
.author-info p .plpm-author-link:hover { color: #005177; }
.author-info p a:not(.plpm-author-link) { text-decoration: none; color: inherit; }
.author-info p a:not(.plpm-author-link):hover { text-decoration: underline; }
.abstract, .keywords, .pdf-link, .citation, .pdf-viewer { margin-bottom: 1.5em; }
.pdf-link a { display: inline-block; background-color: #0073aa; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; }
.pdf-link a:hover { background-color: #005177; }
.google-pdf-viewer-container { border: 1px solid #ccc; padding: 1em; margin-bottom: 1.5em; }
.google-pdf-viewer-container iframe { width: 100%; height: 800px; border: none; }
a { color: #0066cc; }
a:hover { text-decoration: underline; }
footer { margin-top: 2em; text-align: center; font-size: 0.8em; color: #555; }
code { background: #f0f0f0; padding: .2em .4em; border-radius: 3px; font-family: monospace; }
pre  { background: #f0f0f0; padding: 1em; overflow-x: auto; border-radius: 4px; }
.abstract p { text-align: justify; }
.breadcrumb { padding: 0; margin: 0 0 1em 0; list-style: none; background: none; font-size: 0.9em; color: #555; }
.breadcrumb li { display: inline; }
.breadcrumb li a { text-decoration: none; color: #555; }
.breadcrumb li a:hover { text-decoration: underline; }
.breadcrumb li:after { content: '/'; padding: 0 0.5em; color: #ccc; }
.breadcrumb li:last-child::after { content: ''; }
.breadcrumb li:last-child { color: #333; }
.back-button { display: inline-block; margin-top: 1.5em; margin-bottom: 0; padding: 8px 15px; background-color: #f2f2f2; color: #333; text-decoration: none; border: 1px solid #ccc; border-radius: 4px; font-size: 0.9em; }
.back-button:hover { background-color: #e9e9e9; text-decoration: none; }
.citation-downloads { margin-top: 1em; }
.citation-downloads h3 { margin-top: 0; margin-bottom: 0.8em; font-size: 1.1em; }
.citation-downloads a { display: inline-block; background-color: #f9f9f9; color: #333; padding: 8px 15px; text-decoration: none; border: 1px solid #ccc; border-radius: 4px; margin-right: 0.5em; margin-bottom: 0.5em; font-size: 0.9em; }
.citation-downloads a:hover { background-color: #eee; text-decoration: none; }
</style>
</head>
<body>
<header>
  <h1>[論文タイトル]</h1>
  <!-- AUTHOR_INFO_PLACEHOLDER -->
</header>
<main>
  <!-- BREADCRUMB_PLACEHOLDER -->
  <!-- PDF_VIEWER_PLACEHOLDER -->
  <section id="abstract" class="abstract">
    <h2>Abstract</h2>
    [要旨全文HTML]
  </section>
  <section class="keywords">
    <h2>Keywords</h2>
    <p>[キーワード]</p>
  </section>
  <section class="citation">
    <h2>Citation Export</h2>
    <div class="citation-downloads">
        <h3>Download in other formats:</h3>
        <!-- DOWNLOAD_LINKS_PLACEHOLDER -->
    </div>
  </section>
  <!-- BACK_BUTTON_PLACEHOLDER -->
</main>
<footer>
  <hr>
  <p>Copyright © [コピーライト年] [著者名] / [発行者]. All Rights Reserved.<br>
     Licensed under <a href="[ライセンスURL]" target="_blank" rel="noopener noreferrer">[ライセンス名]</a>.</p>
  <p><a href="[HTMLファイルのURL]">[HTMLファイルのURL]</a></p>
</footer>
<!-- CITATION_SCRIPT_PLACEHOLDER -->
</body>
</html>
HTML;
    return $template;
}
?>
