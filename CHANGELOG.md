# 変更履歴

このプロジェクトのすべての重要な変更はこのファイルに記録されます。

フォーマットは [Keep a Changelog](https://keepachangelog.com/ja/1.0.0/) に基づいており、このプロジェクトは[セマンティックバージョニング](https://semver.org/lang/ja/)に準拠しています。

## [1.0.0] - 2025-11-01

### 追加

#### コア機能
- 学術論文プレプリント用の完全動的ランディングページ生成システム
  - WordPress リライトルールによる完全動的処理（物理ファイル生成なし）
  - `/paper/{ID}/` 形式の美しいURL構造
  - 一覧ページ `/paper/` の自動生成
  - XMLサイトマップ `/paper/paper-sitemap.xml` の自動生成

#### Google Scholar 完全対応
- citation メタタグ 全17種類の自動生成
  - 必須: citation_title, citation_author, citation_publication_date, citation_pdf_url
  - 推奨: citation_author_institution, citation_publisher, citation_language, citation_keywords, citation_abstract_html_url, citation_fulltext_html_url, citation_year, citation_doi, citation_reference 等
- DOI (Digital Object Identifier) 対応
- 参考文献の個別メタタグ出力
- Google Scholarクローラー（Googlebot-Scholar）の推奨事項への完全準拠

#### 引用フォーマットエクスポート（12種類）
- **文献管理ツール用**:
  - BibTeX (.bib) - LaTeX, Overleaf対応
  - RIS (.ris) - EndNote, Mendeley, Zotero対応
  - ReDIF (.redif) - RePEc (経済学論文) 対応
  - JSON (.json) - カスタムツール対応
- **スタイルガイド用**:
  - APA Style (.txt) - 心理学・教育学・社会科学
  - MLA Style (.txt) - 人文科学・文学
  - Chicago Style (.txt) - 歴史学・人文科学
  - IEEE Style (.txt) - 工学・情報科学
- **Webコピペ用**:
  - HTML (.html) - 基本引用
  - HTML + Abstract (.html) - 抄録付き引用
  - Plain Text (.txt) - プレーンテキスト引用
  - Plain Text + Abstract (.txt) - 抄録付きプレーンテキスト引用
- data: URI スキームによるブラウザ内動的生成（サーバー負荷なし）
- 管理画面での個別ON/OFF設定

#### PDFビューア機能
- Mozilla PDF.js 統合（デフォルトビューア）
- 複数ビューアー自動フォールバック機能
  - Mozilla PDF.js → Google Docs Viewer → Microsoft Office Viewer → Direct Embed
- 自動リトライ機能（最大3回）
- Content-Disposition: attachment ヘッダー対策
- PDFリロードボタン（キャッシュバスター付き）
- ローディングスピナー表示（大小2種類）
- エラーハンドリングとフォールバック表示

#### WordPress テーマ統合
- get_header() / get_footer() によるテーマヘッダー/フッター自動継承
- テーマのCSS/JavaScriptを自動読み込み
- カスタムCSS変数システム（6色カスタマイズ可能）
- FastCGIバッファ最適化（大規模テーマ対応）

#### パンくずリスト対応
- 3階層パンくずナビゲーション（トップ > 一覧 > 個別）
- Kashiwazaki SEO Perfect Breadcrumbs プラグイン連携
- Schema.org BreadcrumbList JSON-LD 自動生成
- カスタム投稿タイプアーカイブの自動検出

#### UI/UX
- レスポンシブ2カラム著者情報レイアウト
  - デスクトップ: 2カラムグリッド
  - モバイル（768px以下）: 1カラム自動切替
- インタラクティブツールチップ（7項目）
  - Type, DOI, Affiliation, Published, Modified, Publisher, License, Version
  - CSS `::after` 擬似要素で実装
  - カラーテーマ対応
- カラーテーマカスタマイズ
  - デフォルト/ダーク/カスタムテーマ
  - 6色のカラーピッカー
  - CSS変数による一括変更

#### 構造化データ
- Schema.org JSON-LD（ScholarlyArticle他5種類対応）
  - ScholarlyArticle (学術論文)
  - TechArticle (技術記事)
  - Report (レポート)
  - Article (一般記事)
  - WebPage (Webページ)
- 25個以上のプロパティを含む完全な構造化データ
- Dublin Core メタタグ 全15種類
- Open Graph Protocol メタタグ
- Twitter Card メタタグ

#### カスタム投稿タイプ
- 投稿タイプ名: `preprint_page`
- 29個のカスタムフィールド（必須6個、オプション23個）
- メディアライブラリPDFアップロード統合
- 管理画面カラム表示カスタマイズ
- 投稿一覧に「プレプリントページURL」列を追加

#### URL処理
- レガシーURL対応: `/paper/123.html` → `/paper/123/` (301リダイレクト)
- PDFリダイレクト: `/paper/123.pdf` → 実際のPDFファイル (301リダイレクト)
- クエリ形式URL自動リダイレクト: `?post_type=preprint_page&p=123` → `/paper/123/`
- リライトルール優先度最適化（-99999で最優先処理）

#### 管理画面
- 基本設定ページ
  - 一般設定（ページタイトル、著者情報表示、サイトマップ生成）
  - Citation Export 設定（12種類の個別ON/OFF）
  - デザインテーマ設定（カラーピッカー）
- メタボックスUI（29フィールド）
- PDFメディアライブラリ選択UI（JavaScript）
- 投稿更新メッセージカスタマイズ


### セキュリティ

#### 入力検証
- 全入力フィールドにサニタイゼーション実装
  - `sanitize_text_field()` - テキストフィールド
  - `sanitize_email()` - メールアドレス
  - `esc_url_raw()` - URL
  - `wp_kses_post()` - HTML（抄録等）
  - `absint()` - 整数値

#### 出力エスケープ
- 全出力にエスケープ処理実装
  - `esc_html()` - HTMLテキスト
  - `esc_attr()` - HTML属性
  - `esc_url()` - URL
  - `esc_js()` - JavaScript文字列

#### 認証・認可
- Nonce検証（管理画面フォーム）
  - `wp_verify_nonce()` による CSRF 対策
- 権限チェック
  - `current_user_can('manage_options')` による権限確認
- 直接アクセス防止
  - `if (!defined('ABSPATH')) exit;` による直接実行防止

#### その他
- SQLインジェクション対策（WordPress API のみ使用、生SQLクエリなし）
- XSS対策（全出力をエスケープ）
- スパムボット対策（`antispambot()` でメール難読化）
- ファイルアップロード検証

[1.0.0]: https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-preprint-landing-page-generator/releases/tag/v1.0.0
