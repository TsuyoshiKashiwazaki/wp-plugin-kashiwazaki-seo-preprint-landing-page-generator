=== Kashiwazaki SEO Preprint Landing Page Generator ===
Contributors: kashiwazakitsuyoshi
Tags: preprint, landing page, pdf, seo, academic, scholarly, static html, google scholar
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

学術論文のプレプリント（査読前論文）のSEO最適化されたランディングページを自動生成するWordPressプラグイン。

== Description ==

Kashiwazaki SEO Preprint Landing Page Generatorは、学術論文のプレプリントや研究成果物のための静的HTMLランディングページを生成するWordPressプラグインです。Google Scholar対応のメタタグ、Schema.org構造化データ、複数の引用形式エクスポートなど、学術コンテンツのSEOに必要な機能を網羅しています。

= 主な機能 =

* **カスタム投稿タイプ** - 専用の「Preprint Landing Page」投稿タイプで論文情報を管理
* **静的HTMLページ生成** - パフォーマンスを重視した静的HTMLファイルの自動生成
* **包括的なSEO対応** - Google Scholar、Dublin Core、Schema.org対応のメタタグを自動生成
* **PDF管理機能** - WordPressメディアライブラリと連携したPDFファイル管理
* **引用形式エクスポート** - BibTeX、RIS、ReDIF、JSON形式での引用情報エクスポート
* **自動サイトマップ生成** - 検索エンジン向けXMLサイトマップの自動生成
* **多言語対応** - 国際化対応済み（日本語・英語）

= 生成されるファイル =

* 個別ページ: `/paper/[投稿ID].html`
* 一覧ページ: `/paper/index.html`
* サイトマップ: `/paper/paper-sitemap.xml`
* PDFファイル: `/paper/[投稿ID].pdf`

= 対応する学術メタデータ =

* Google Scholar citation_* メタタグ
* Dublin Core メタデータ
* Schema.org JSON-LD（ScholarlyArticle等）
* Open Graph Protocol (OGP)
* Twitter Card

== Installation ==

1. プラグインファイルを `/wp-content/plugins/kashiwazaki-seo-preprint-landing-page-generator/` ディレクトリにアップロード
2. WordPressの「プラグイン」メニューからプラグインを有効化
3. `/paper/` ディレクトリが自動的に作成されることを確認（書き込み権限が必要）
4. 「Kashiwazaki SEO Preprint Landing Page Generator」メニューから新規投稿を作成

== Frequently Asked Questions ==

= PDFファイルはどこに保存されますか？ =

PDFファイルは `/paper/` ディレクトリに投稿IDをファイル名として保存されます（例: `/paper/123.pdf`）。

= 生成されたHTMLファイルを手動で編集できますか？ =

生成されたHTMLファイルは投稿を更新するたびに上書きされるため、手動編集は推奨されません。カスタマイズが必要な場合は、プラグインのテンプレートファイルを編集してください。

= Google Scholarに対応していますか？ =

はい、Google Scholarが推奨するすべてのcitation_*メタタグに対応しています。

= DOIはサポートされていますか？ =

はい、DOI（Digital Object Identifier）の入力と表示に対応しています。

== Changelog ==

= 1.0.0 =
* 初回リリース
* Google Scholar完全対応（citation メタタグ全17種類）
* 12種類の引用フォーマットエクスポート
* 完全動的ランディングページ生成
* XMLサイトマップ自動生成
* PDFビューア統合
* WordPressテーマ統合
* レスポンシブUI

== Requirements ==

* WordPress 5.0以上
* PHP 7.2以上
* `/paper/` ディレクトリへの書き込み権限
* WordPressメディアライブラリ機能

== Support ==

サポートやバグ報告については、作者のウェブサイト（https://www.tsuyoshikashiwazaki.jp/）までお問い合わせください。 