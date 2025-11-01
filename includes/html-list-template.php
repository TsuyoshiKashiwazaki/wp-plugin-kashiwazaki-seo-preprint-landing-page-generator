<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_get_html_list_template_content() {
    $template = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>[ページタイトル] - [サイトタイトル]</title>
<meta name="description" content="投稿論文一覧 - 公開された論文とプレプリント by [サイトタイトル].">
<link rel="canonical" href="[一覧ページのURL]">
<meta name="robots" content="index, follow">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<meta name="theme-color" content="#ffffff">
<script type="application/ld+json">
<!-- JSON_LD_ITEM_LIST_PLACEHOLDER -->
</script>
<style>
body { font-family: sans-serif; line-height: 1.6; margin: 2em; max-width: 800px; margin-left: auto; margin-right: auto; padding: 1em; }
header { border-bottom: 2px solid #eee; padding-bottom: 1em; margin-bottom: 2em; }
header h1 { font-size: 2em; margin: 0; }
main h2 { font-size: 1.6em; border-bottom: 1px solid #eee; padding-bottom: 0.3em; margin-bottom: 1.5em; }
.preprint-item { border: 1px solid #ddd; padding: 1.5em; margin-bottom: 2em; border-radius: 5px; background-color: #f9f9f9; }
.preprint-item h3 { font-size: 1.3em; margin-top: 0; margin-bottom: 0.5em; }
.preprint-item h3 a {
    text-decoration: underline;
    color: #0073aa;
    transition: all 0.2s ease;
}
.preprint-item h3 a:hover {
    text-decoration: underline;
    color: #0056b3;
    text-shadow: 0 0 1px rgba(0, 86, 179, 0.3);
}
.preprint-item .author-info, .preprint-item .pub-info { font-size: 0.9em; color: #555; margin-bottom: 0.8em; }
.preprint-item .author-info a { text-decoration: none; color: #0066cc; }
.preprint-item .author-info a:hover { text-decoration: underline; }
.preprint-item .abstract-excerpt { font-size: 0.95em; margin-top: 0; margin-bottom: 1em; text-align: justify; }
.preprint-item .links a { display: inline-block; margin-right: 1em; color: #0066cc; text-decoration: none; }
.preprint-item .links a:hover { text-decoration: underline; }
footer { margin-top: 3em; text-align: center; font-size: 0.8em; color: #555; }
footer p { margin: 0.5em 0; }
</style>
</head>
<body>
<header>
  <h1><a href="[サイトURL]">[サイトタイトル]</a></h1>
</header>
<main>
  <h2>[ページタイトル]</h2>
  <div class="preprint-list">
    <!-- PREPRINT_LIST_PLACEHOLDER -->
  </div>
</main>
<footer>
  <hr>
  <p>Copyright © [コピーライト年] [サイトタイトル]. All Rights Reserved.</p>
  <p><a href="[一覧ページのURL]">[一覧ページのURL]</a></p>
</footer>
</body>
</html>
HTML;
    return $template;
}
