# Kashiwazaki SEO Preprint Landing Page Generator

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.1-blue.svg)](https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-preprint-landing-page-generator/releases)

学術論文プレプリント（査読前論文）用のSEO最適化ランディングページを**完全動的に生成**するWordPressプラグインです。

**特徴**: Google Scholar完全対応 | 12種類の引用フォーマット | 完全動的システム
## 目次

- [概要](#概要)
- [主な特徴](#主な特徴)
- [Google Scholar 完全対応](#google-scholar-完全対応)
- [技術的特徴：完全動的システム](#技術的特徴完全動的システム)
- [URL構造](#url構造)
- [引用フォーマット](#引用フォーマット)
- [動作環境](#動作環境)
- [インストール](#インストール)
- [初期設定](#初期設定)
- [使用方法](#使用方法)
- [設定オプション](#設定オプション)
- [開発者向けガイド](#開発者向けガイド)
- [トラブルシューティング](#トラブルシューティング)
- [FAQ](#faq)
- [ライセンス](#ライセンス)
- [著者](#著者)

## 概要

このプラグインは、学術論文のプレプリント（査読前に公開する研究論文）や正式な学術論文のための、SEO最適化された専用ランディングページを生成します。

### なぜこのプラグインが必要か

1. **Google Scholar への登録**: 学術論文を Google Scholar に確実に登録させるため、推奨されるすべての citation メタタグを自動生成
2. **引用の容易性**: 12種類の引用フォーマット（BibTeX, RIS, APA, MLA等）を即座にエクスポート
3. **SEO最適化**: Schema.org 構造化データ、Dublin Core、Open Graph 等の完全対応
4. **管理の効率化**: WordPressの管理画面から簡単に論文情報を管理

### 従来の方法との違い

| 項目 | 従来の静的HTML | このプラグイン |
|------|---------------|---------------|
| ファイル生成 | 必要（HTMLファイルを手動作成） | 不要（完全動的） |
| 更新作業 | 手動でHTMLを編集 | WordPress管理画面で編集 |
| メタタグ管理 | 手動で記述 | 自動生成 |
| サイトマップ | 手動更新 | 自動生成 |
| テーマ統合 | 困難 | 自動統合 |

## 主な特徴

### 1. 完全動的ランディングページ生成

- `/paper/123/` のような美しいURLで論文ページを表示
- 物理的なHTMLファイルは一切生成しない（サーバー容量節約）
- WordPressの記事と同じ感覚で論文情報を管理

### 2. Google Scholar 完全対応

Google Scholar のクローラー（Googlebot-Scholar）が推奨するすべてのメタタグに対応。論文を確実にGoogle Scholarに登録できます。

### 3. 12種類の引用フォーマットエクスポート

研究者が必要とするあらゆる引用形式に対応：
- **文献管理ツール用**: BibTeX, RIS, ReDIF, JSON
- **スタイルガイド用**: APA, MLA, Chicago, IEEE
- **Webコピペ用**: HTML, Plain Text（抄録付き/なし）

### 4. 自動XMLサイトマップ生成

`/paper/paper-sitemap.xml` で検索エンジン用サイトマップを自動生成。論文を追加するたびに自動更新されます。

### 5. PDFビューア統合

論文ページ内でPDFをプレビュー表示。複数のビューワーに対応し、自動フォールバック機能付き。

### 6. WordPressテーマ完全統合

サイトのヘッダー、フッター、ナビゲーションメニューを自動継承。論文ページもサイトデザインと統一されます。

### 7. パンくずリスト対応

3階層のパンくずナビゲーション（トップ > 投稿論文一覧 > 論文タイトル）を自動生成。Schema.org構造化データにも対応。

### 8. レスポンシブUI

デスクトップでは2カラム、モバイルでは1カラムに自動調整される著者情報レイアウト。

### 9. カラーテーマカスタマイズ

サイトのデザインに合わせて6色をカスタマイズ可能。CSS変数でテーマ全体の色を一括変更。

### 10. セキュリティ対策

入力のサニタイズ、出力のエスケープ、Nonce検証、権限チェックなど、WordPress推奨のセキュリティ対策を完全実装。

## Google Scholar 完全対応

### 対応メタタグ一覧（全17種類）

このプラグインは、Google Scholarが推奨するすべての citation メタタグを自動生成します。

#### 必須メタタグ（4個）

| メタタグ | 説明 | 例 |
|---------|------|-----|
| `citation_title` | 論文タイトル | LSIキーワードを用いたSEO対策の効果検証研究 |
| `citation_author` | 著者名 | 柏崎 剛 |
| `citation_publication_date` | 公開日 | 2025-04-09 |
| `citation_pdf_url` | PDF URL | https://example.com/paper/123.pdf |

#### 推奨メタタグ（13個）

| メタタグ | 説明 | 実装状況 |
|---------|------|---------|
| `citation_author_institution` | 著者所属機関 | ✅ 実装済み |
| `citation_publisher` | 出版者 | ✅ 実装済み |
| `citation_language` | 言語コード | ✅ 実装済み |
| `citation_keywords` | キーワード | ✅ 実装済み |
| `citation_abstract_html_url` | 抄録URL | ✅ 実装済み |
| `citation_fulltext_html_url` | フルテキストURL | ✅ 実装済み |
| `citation_year` | 公開年 | ✅ 実装済み |
| `citation_doi` | DOI | ✅ 実装済み |
| `citation_reference` | 参考文献 | ✅ 実装済み |

### 対応する識別子

- **DOI** (Digital Object Identifier): 10.xxxx/yyyy 形式
- **その他**: arXiv ID, PubMed ID 等も将来対応可能

### Google Scholar推奨事項への準拠

1. **メタタグの配置**: すべて `<head>` 内に配置
2. **文字エンコーディング**: UTF-8
3. **PDF アクセス**: ロボットがアクセス可能なURL
4. **フルテキストHTML**: 論文本文のHTMLバージョン提供
5. **抄録**: 独立したセクション（#abstract アンカー）
6. **参考文献**: 個別の `citation_reference` メタタグ

### その他のメタデータ標準対応

#### Dublin Core（全15種類）

| メタタグ | 内容 |
|---------|------|
| `DC.title` | タイトル |
| `DC.creator` | 著者 |
| `DC.date` | 公開日 |
| `DC.publisher` | 出版者 |
| `DC.language` | 言語 |
| `DC.description` | 説明 |
| `DC.rights` | 権利情報 |
| `DC.type` | 文書タイプ |
| `DC.format` | フォーマット |
| `DC.identifier` | 識別子 |
| `DC.subject` | 主題 |
| その他 | 更新日等 |

#### Schema.org 構造化データ（JSON-LD）

**対応タイプ**:
- `ScholarlyArticle`（デフォルト）- 学術論文
- `TechArticle` - 技術記事
- `Report` - レポート
- `Article` - 一般記事
- `WebPage` - Webページ

**含まれるプロパティ**（25個以上）:
- 基本情報: headline, name, description, abstract
- メタデータ: keywords, inLanguage, datePublished, dateModified
- 著者情報: author (Person) - name, email, url, affiliation
- 出版者情報: publisher (Organization) - name, url, logo
- ライセンス: license (URL)
- バージョン: version
- 関連メディア: associatedMedia (PDF)
- 識別子: identifier (DOI)
- 画像: image (ImageObject)
- メインエンティティ: mainEntityOfPage

## 技術的特徴：完全動的システム

### なぜ「完全動的」なのか

従来のWordPressプラグインは、HTMLファイルやPDFファイルを物理的にサーバーに保存する「静的ファイル生成方式」が一般的でした。

このプラグインは、**一切の静的ファイルを生成せず、すべてをWordPressのリライトルールとテンプレートシステムで動的に処理**します。

### 完全動的システムの利点

1. **サーバー容量の節約**: HTMLファイルを生成しないため、ディスク使用量が激減
2. **即座の反映**: 投稿を保存すると即座にページに反映（ファイル生成の待ち時間なし）
3. **権限エラーの回避**: ディレクトリ作成やファイル書き込み権限の問題が発生しない
4. **管理の簡素化**: WordPressの管理画面のみで完結
5. **バックアップの容易性**: データベースのバックアップのみでOK

### 禁止事項（重要）

以下の処理は**絶対に行いません**（開発者向け注意事項）：

```php
// ❌ これらは絶対に使用しない
wp_mkdir_p('/path/to/paper/');          // ディレクトリ作成禁止
file_put_contents('xxx.html', $html);   // HTMLファイル書き込み禁止
copy($src, $dst);                        // ファイルコピー禁止
```

すべて **WordPress Rewrite Rules** と **template_redirect** フックで処理します。

### リライトルールの仕組み

```php
// 例: /paper/123/ にアクセスすると...
add_rewrite_rule(
    '^paper/([0-9]+)/?$',
    'index.php?plpm_page_type=single&plpm_post_id=$matches[1]',
    'top'
);

// WordPressが内部的に以下のように変換
// /paper/123/ → index.php?plpm_page_type=single&plpm_post_id=123

// template_redirectフックで処理
function plpm_template_redirect() {
    if (get_query_var('plpm_page_type') === 'single') {
        $post_id = get_query_var('plpm_post_id');
        // 動的にHTMLを生成して出力
        plpm_display_single_page($post_id);
        exit;
    }
}
```

## URL構造

### URLパターン一覧

| URL | 処理内容 | HTTPステータス |
|-----|---------|--------------|
| `/paper/` | 投稿論文一覧ページ（動的生成） | 200 |
| `/paper/123/` | 論文ID 123の詳細ページ（動的生成） | 200 |
| `/paper/123.html` | 旧URL形式 → `/paper/123/` へリダイレクト | 301 |
| `/paper/123.pdf` | 実際のPDFファイルへリダイレクト | 301 |
| `/paper/paper-sitemap.xml` | XMLサイトマップ（動的生成） | 200 |

### レガシーURL対応

旧バージョンで `.html` 形式を使用していた場合でも、301リダイレクトで新形式に自動転送されるため、SEOへの影響を最小化できます。

### PDFリダイレクト

`/paper/123.pdf` にアクセスすると、WordPressメディアライブラリの実際のPDFファイル（例: `/wp-content/uploads/2025/09/論文.pdf`）に301リダイレクトされます。

これにより、短くて覚えやすいURLで論文PDFを共有できます。

## 引用フォーマット

### 対応フォーマット（12種類）

#### 1. 文献管理ツール用（4種類）

| フォーマット | 拡張子 | 対応ツール | 説明 |
|------------|--------|-----------|------|
| BibTeX | .bib | LaTeX, Overleaf | LaTeX文書作成の標準形式 |
| RIS | .ris | EndNote, Mendeley, Zotero | 最も広く使われる交換形式 |
| ReDIF | .redif | RePEc | 経済学論文専用フォーマット |
| JSON | .json | カスタムツール | プログラムでの処理に最適 |

#### 2. スタイルガイド用（4種類）

| フォーマット | 分野 | 説明 |
|------------|------|------|
| APA Style | 心理学・教育学・社会科学 | American Psychological Association |
| MLA Style | 人文科学・文学 | Modern Language Association |
| Chicago Style | 歴史学・人文科学 | The Chicago Manual of Style |
| IEEE Style | 工学・情報科学 | Institute of Electrical and Electronics Engineers |

#### 3. Webコピペ用（4種類）

| フォーマット | 説明 |
|------------|------|
| HTML | HTML形式の引用（基本） |
| HTML + Abstract | 抄録付きHTML引用 |
| Plain Text | プレーンテキスト引用 |
| Plain Text + Abstract | 抄録付きプレーンテキスト |

### ダウンロード方式

すべての引用フォーマットは、`data:` URIスキームを使用してブラウザ内で動的に生成されます。

- サーバー側でファイルを生成しない（サーバー負荷軽減）
- `download` 属性で自動的にファイル名を設定
- BibTeX Keyに基づいたファイル名（例: `Kashiwazaki2025LSI.bib`）

### 管理画面での設定

管理画面の「基本設定」で、12種類の引用フォーマットを個別にON/OFFできます。不要な形式を非表示にすることで、ユーザーの混乱を防げます。

## 動作環境

### 必須要件

- **WordPress**: 5.0 以上
- **PHP**: 7.4 以上
- **メモリ**: 128MB以上推奨
- **パーマリンク設定**: デフォルト以外（カスタム構造推奨）

### 推奨環境

- **WordPress**: 6.0 以上
- **PHP**: 8.0 以上
- **Webサーバー**: Nginx または Apache（mod_rewrite有効）
- **HTTPS**: 有効化推奨

### 互換性

#### 対応テーマ
- WordPress標準テーマ（Twenty Twenty-Five等）
- `get_header()` / `get_footer()` をサポートする全テーマ

#### 対応プラグイン
- Kashiwazaki SEO Perfect Breadcrumbs（パンくずリスト）
- Kashiwazaki SEO Schema Content Type Builder（構造化データ）
- 一般的なSEOプラグイン（Yoast SEO, Rank Math等は自動無効化）

## インストール

### 方法1: 手動インストール

1. このリポジトリをダウンロードまたはクローン
```bash
git clone https://github.com/TsuyoshiKashiwazaki/wp-plugin-kashiwazaki-seo-preprint-landing-page-generator.git
```

2. プラグインディレクトリにアップロード
```
/wp-content/plugins/kashiwazaki-seo-preprint-landing-page-generator/
```

3. WordPress管理画面で有効化
   - 「プラグイン」→ 「インストール済みプラグイン」
   - 「Kashiwazaki SEO Preprint Landing Page Generator」を探す
   - 「有効化」をクリック

4. **重要**: パーマリンク設定を更新
   - 「設定」→ 「パーマリンク設定」
   - 何も変更せずに「変更を保存」をクリック
   - これでリライトルールがデータベースに保存されます

### 方法2: WordPress.org からインストール（将来対応予定）

現在は手動インストールのみです。WordPress.org への申請は今後予定しています。

## 初期設定

### 1. 基本設定画面へアクセス

WordPress管理画面で：
- 「Kashiwazaki SEO Preprint Landing Page Generator」メニューをクリック
- または「すべての投稿論文」→「基本設定」

### 2. 一般設定

#### リストページタイトル
`/paper/` ページに表示されるタイトルです。パンくずリストにも使用されます。

**推奨値**:
- 日本語論文の場合: 「投稿論文一覧」「論文アーカイブ」
- 英語論文の場合: 「Publications」「Research Papers」

#### 著者情報の表示
チェックを入れると、一覧ページと詳細ページで著者情報が表示されます。

### 3. Citation Export 設定

12種類の引用フォーマットから、サイトで提供したいものを選択してください。

**推奨設定**（すべてON）:
- 研究者は様々な文献管理ツールを使用するため、できるだけ多くの形式を提供することを推奨

### 4. デザインテーマ設定

サイトのデザインに合わせてカラーテーマを選択または作成してください。

**テーマ選択肢**:
- **デフォルトテーマ**: WordPress標準カラー
- **ダークテーマ**: 暗めの配色
- **カスタムテーマ**: 6色を個別に設定

**カスタマイズ可能な色**:
- プライマリカラー（リンク、ボタン等）
- セカンダリカラー（背景等）
- ボーダー色
- 背景色
- テキスト色
- アクセント色

## 使用方法

### 論文ページの作成手順

#### ステップ1: 新規投稿を作成

1. WordPress管理画面で「すべての投稿論文」→「新規追加」
2. 論文タイトルを入力

#### ステップ2: 必須フィールドを入力

**必須項目**（6個）:
1. **Author Name**: 著者名（例: 柏崎 剛）
2. **Publication Date**: 公開日（YYYY-MM-DD形式、例: 2025-04-09）
3. **Publisher**: 出版者（例: SEO対策研究室）
4. **Keywords**: キーワード（カンマ区切り、例: SEO,論文,研究）
5. **Abstract**: 抄録（論文の要約）
6. **PDF File URL**: PDFファイル
   - 「PDFファイルを選択」ボタンからメディアライブラリで選択
   - または直接URLを入力

**BibTeX Key**: 引用キー（例: Kashiwazaki2025LSI）
   - 著者名の最初の文字 + 年 + タイトルキーワード
   - 英数字のみ（ハイフン、コロン、アンダースコア可）

#### ステップ3: オプションフィールドを入力（推奨）

**Google Scholar対応のために推奨**:
- **Contact Email**: 連絡先メールアドレス
- **Author Affiliation Name**: 所属機関名（例: 株式会社コンテンシャル）
- **Affiliation URL**: 所属機関URL
- **Google Scholar URL**: Google Scholar プロフィールURL
- **DOI**: Digital Object Identifier（例: 10.1000/xyz123）

**その他**:
- **Modified Date**: 更新日（内容を修正した場合）
- **Language Code**: 言語コード（ja, en等、デフォルト: en）
- **References**: 参考文献（1行1文献）
- **Version**: バージョン番号（例: 1.0.0）
- **License Name**: ライセンス名（例: CC BY 4.0）
- **License URL**: ライセンスURL

#### ステップ4: 公開

「公開」ボタンをクリック。投稿は自動的に公開状態になります。

#### ステップ5: 確認

公開後、以下のURLで論文ページを確認できます：
- 詳細ページ: `https://yoursite.com/paper/123/`
- PDF: `https://yoursite.com/paper/123.pdf`
- 一覧ページ: `https://yoursite.com/paper/`

管理画面の投稿一覧に「プレプリントページURL」列が表示され、ワンクリックでアクセスできます。

## 設定オプション

### 一般設定

#### リストページタイトル
`/paper/` 一覧ページに表示されるタイトルです。

**用途**:
- ページのH1タグ
- パンくずリストの表示名
- ページタイトル（`<title>` タグ）

**例**:
- 日本語: 「投稿論文一覧」「論文アーカイブ」「研究成果」
- 英語: 「Publications」「Research Papers」「Preprints」

#### 著者情報の表示
一覧ページと詳細ページで著者情報を表示するかどうかを設定します。

**表示される情報**（詳細ページ）:
- Type（論文種別）
- Author（著者名）
- Contact（連絡先）
- Affiliation（所属機関）
- DOI
- Published Date（公開日）
- Modified Date（更新日）
- Publisher（出版者）
- License（ライセンス）
- Version（バージョン）

#### サイトマップ生成
`/paper/paper-sitemap.xml` でXMLサイトマップを生成するかを設定します。

**推奨**: ON（検索エンジンに論文を確実にインデックスさせるため）

### Citation Export 設定

提供する引用フォーマットを選択します。

**各フォーマットの選択基準**:
- **BibTeX**: LaTeX利用者向け（必須）
- **RIS**: 一般的な文献管理ツール利用者向け（必須）
- **APA, MLA, Chicago, IEEE**: 学生・研究者向け（推奨）
- **HTML, Plain Text**: 一般読者向け（推奨）
- **JSON**: 開発者向け（オプション）
- **ReDIF**: 経済学論文の場合のみON

### デザインテーマ設定

#### テーマ選択

**デフォルトテーマ**:
- プライマリ: #0073aa（青）
- セカンダリ: #005177（濃い青）
- 背景: #ffffff（白）
- テキスト: #333333（ダークグレー）

**ダークテーマ**:
- プライマリ: #4a9eff（明るい青）
- 背景: #1a1a1a（ダークグレー）
- テキスト: #e0e0e0（ライトグレー）

**カスタムテーマ**:
- 6色を個別にカラーピッカーで選択

#### CSS変数

選択したカラーテーマは、以下のCSS変数として適用されます：

```css
:root {
  --plpm-primary: #0073aa;     /* リンク、ボタン */
  --plpm-secondary: #f9f9f9;   /* ボックス背景 */
  --plpm-border: #dddddd;      /* ボーダー */
  --plpm-bg: #ffffff;          /* ページ背景 */
  --plpm-text: #333333;        /* テキスト */
  --plpm-accent: #005177;      /* ホバー時の色 */
}
```

## 開発者向けガイド

### ファイル構成

```
kashiwazaki-seo-preprint-landing-page-generator/
├── kashiwazaki-seo-preprint-landing-page-generator.php  # メインファイル
├── readme.txt                                            # WordPress.org用
├── README.md                                             # GitHub用（本ファイル）
├── CHANGELOG.md                                          # 変更履歴
├── LICENSE                                               # ライセンス
├── DEVELOPER_WARNINGS.md                                 # 開発者警告
├── includes/
│   ├── activation.php                # プラグイン有効化処理
│   ├── admin-columns.php             # 管理画面カラム表示
│   ├── admin-settings.php            # 管理画面設定ページ
│   ├── citation-generator.php        # 引用フォーマット生成
│   ├── cpt-preprint.php              # カスタム投稿タイプ登録
│   ├── dynamic-template-handler.php  # リライトルール・テンプレート処理（最重要）
│   ├── file-deletion.php             # ファイル削除（レガシー機能）
│   ├── html-data-collector.php       # データ収集
│   ├── html-generator.php            # HTML生成（レガシー）
│   ├── html-list-generator.php       # 一覧ページ生成
│   ├── html-list-template.php        # 一覧テンプレート（レガシー）
│   ├── html-parts-generator.php      # UI部品生成（著者情報、PDFビューア等）
│   ├── html-template.php             # HTMLテンプレート（レガシー）
│   ├── json-ld-generator.php         # JSON-LD構造化データ生成
│   ├── metabox-preprint.php          # メタボックスUI
│   └── sitemap-generator.php         # サイトマップ生成
└── js/
    └── media-uploader.js             # PDFメディアライブラリ選択UI
```

### 主要関数リファレンス

#### リライトルール関連

| 関数名 | ファイル | 説明 |
|--------|---------|------|
| `plpm_add_dynamic_rewrite_rules()` | dynamic-template-handler.php | リライトルールを登録 |
| `plpm_add_query_vars()` | dynamic-template-handler.php | カスタムクエリ変数を追加 |
| `plpm_template_redirect()` | dynamic-template-handler.php | URLに応じた処理を振り分け |

#### ページ生成関連

| 関数名 | ファイル | 説明 |
|--------|---------|------|
| `plpm_display_list_page()` | dynamic-template-handler.php | 一覧ページを表示 |
| `plpm_display_single_page()` | dynamic-template-handler.php | 個別ページを表示 |
| `plpm_render_single_page_template()` | dynamic-template-handler.php | 個別ページテンプレート生成 |
| `plpm_display_sitemap()` | dynamic-template-handler.php | サイトマップを出力 |

#### データ収集関連

| 関数名 | ファイル | 説明 |
|--------|---------|------|
| `plpm_gather_preprint_data()` | html-data-collector.php | 論文データを収集 |
| `plpm_get_correct_pdf_url()` | html-data-collector.php | PDFのURLを取得 |
| `plpm_get_actual_pdf_url()` | html-data-collector.php | 実際のPDFファイルURLを取得 |

#### 引用・構造化データ関連

| 関数名 | ファイル | 説明 |
|--------|---------|------|
| `plpm_generate_all_citation_formats()` | citation-generator.php | 全引用フォーマット生成 |
| `plpm_generate_json_ld_for_single_page()` | json-ld-generator.php | JSON-LD生成 |
| `plpm_generate_citation_download_links_html()` | citation-generator.php | ダウンロードリンク生成 |

#### UI部品生成関連

| 関数名 | ファイル | 説明 |
|--------|---------|------|
| `plpm_generate_author_info_html_for_single_page()` | html-parts-generator.php | 著者情報HTML生成 |
| `plpm_generate_pdf_viewer_html_for_single_page()` | html-parts-generator.php | PDFビューアHTML生成 |

### データ構造（メタデータキー）

#### 必須メタデータ

| メタキー | 型 | 説明 |
|---------|---|------|
| `_plpm_author` | string | 著者名 |
| `_plpm_pub_date` | string | 公開日（YYYY-MM-DD） |
| `_plpm_publisher` | string | 出版者 |
| `_plpm_keywords` | string | キーワード（カンマ区切り） |
| `_plpm_abstract` | text | 抄録 |
| `_plpm_pdf_url` | string | PDFのURL |
| `_plpm_pdf_attachment_id` | int | PDFのメディアID |
| `_plpm_bibtex_key` | string | BibTeXキー |

#### オプションメタデータ

| メタキー | 型 | 説明 |
|---------|---|------|
| `_plpm_is_preprint` | boolean | プレプリントか |
| `_plpm_email` | string | 連絡先メール |
| `_plpm_affiliation_name` | string | 所属機関名 |
| `_plpm_affiliation_url` | string | 所属機関URL |
| `_plpm_author_url` | string | 著者URL |
| `_plpm_google_scholar_url` | string | Google Scholar URL |
| `_plpm_doi` | string | DOI |
| `_plpm_modified_date` | string | 更新日 |
| `_plpm_language` | string | 言語コード |
| `_plpm_references` | text | 参考文献 |
| `_plpm_version` | string | バージョン |
| `_plpm_license_name` | string | ライセンス名 |
| `_plpm_license_url` | string | ライセンスURL |
| `_plpm_schema_type` | string | Schema.orgタイプ |

### フック・フィルター一覧

#### アクションフック

| フック名 | 優先度 | 説明 |
|---------|-------|------|
| `init` | 10 | カスタム投稿タイプ登録、リライトルール追加 |
| `parse_request` | -99999 | PDF/HTMLリダイレクト処理 |
| `template_redirect` | -99999 | ページ生成処理 |
| `wp_head` | 10 | メタタグ・JSON-LD出力 |
| `add_meta_boxes_preprint_page` | 10 | メタボックス追加 |
| `save_post_preprint_page` | 20 | メタデータ保存 |

#### フィルターフック

| フック名 | 優先度 | 説明 |
|---------|-------|------|
| `redirect_canonical` | 1 | 正規化リダイレクト制御 |
| `document_title_parts` | 1-20 | ページタイトル設定 |
| `wp_title` | 999 | タイトル設定 |

### カスタマイズ例

#### 例1: カスタムCSS追加

テーマの `functions.php` に追加：

```php
add_action('wp_head', function() {
    if (get_query_var('plpm_page_type')) {
        echo '<style>
            .plpm-single-page-container {
                max-width: 1000px; /* 幅を変更 */
            }
        </style>';
    }
}, 1000);
```

#### 例2: メタタグのカスタマイズ

```php
add_filter('plpm_citation_meta_tags', function($meta_tags, $data) {
    // カスタムメタタグを追加
    $meta_tags['custom_field'] = '<meta name="custom" content="' . esc_attr($data['custom']) . '">';
    return $meta_tags;
}, 10, 2);
```

## トラブルシューティング

### 問題1: `/paper/123/` にアクセスすると404エラー

**原因**: リライトルールがデータベースに保存されていない

**解決方法**:
1. WordPress管理画面で「設定」→「パーマリンク設定」
2. 何も変更せずに「変更を保存」をクリック
3. リライトルールがフラッシュされ、404エラーが解消します

### 問題2: PDFが表示されず、ダウンロードされる

**原因**: サーバーのPDFファイルに `Content-Disposition: attachment` ヘッダーが設定されている

**解決方法**:
- このプラグインは Mozilla PDF.js を優先使用しているため、通常は問題ありません
- それでもダウンロードされる場合、セキュリティプラグインやサーバー設定を確認してください

### 問題3: サイトマップ（/paper/paper-sitemap.xml）が表示されない

**原因1**: サイトマップ生成が無効化されている
- 「基本設定」→「サイトマップ生成」がOFFになっていないか確認

**原因2**: 他のサイトマッププラグインと競合
- Google XML Sitemaps等、他のサイトマッププラグインが優先されている可能性

**解決方法**:
- このプラグインは優先度 -99999 で処理するため、通常は問題ありません
- それでも表示されない場合、他のプラグインを一時的に無効化して確認

### 問題4: パンくずリストに「HOME」のみ表示される

**原因**: パンくずリストプラグインとの連携が正しくない

**解決方法**:
- 「設定」→「パーマリンク設定」で「変更を保存」
- パンくずリストプラグインのキャッシュをクリア

### 問題5: 他プラグインのスキーマが混入する

**原因**: 他のSEOプラグインやスキーマプラグインが同時に動作している

**確認方法**:
```bash
# JSON-LDを確認
curl -s https://yoursite.com/paper/123/ | grep -A 10 '"@type"'
```

**解決方法**:
- このプラグインは `global $post` を適切に設定しています
- 他のSEOプラグインを一時的に無効化して確認してください

### 問題6: 管理画面が重い

**原因**: メモリ不足

**解決方法**:
- PHPメモリ制限を増やす（wp-config.phpに `define('WP_MEMORY_LIMIT', '256M');`）

## FAQ

### Q1: プレプリントとは何ですか？

A: プレプリント（Preprint）は、正式な査読（peer review）を受ける前に公開される研究論文のことです。研究成果の早期共有や、他の研究者からのフィードバックを得ることを目的としています。

### Q2: Google Scholarに確実に登録されますか？

A: このプラグインは Google Scholar が推奨するすべてのメタタグを実装していますが、登録は Google Scholar の判断によります。ただし、技術的には完全に準拠しています。

**登録のための条件**:
- 公開URLであること（localhost不可）
- PDFファイルがアクセス可能であること
- 抄録（Abstract）が含まれていること
- 適切なメタタグが設定されていること（このプラグインが自動生成）

### Q3: 既存の論文ページがある場合、移行できますか？

A: はい。既存のHTMLページのメタデータをコピーして、このプラグインの投稿として登録できます。

**移行手順**:
1. 既存HTMLから情報を抽出（タイトル、著者、抄録等）
2. 新規投稿として登録
3. 既存HTMLから新URLに301リダイレクト設定

### Q4: 複数の著者に対応していますか？

A: 単著者のみ対応です。複数著者対応は今後のバージョンで実装予定です。

### Q5: WordPress.org で公開されていますか？

A: 現在はGitHubでのみ公開しています。WordPress.org への申請は今後予定しています。

### Q6: 商用利用できますか？

A: はい。GPL-2.0-or-laterライセンスのため、商用・非商用を問わず自由に利用できます。

### Q7: 論文以外（技術記事、レポート等）にも使えますか？

A: はい。Schema.org タイプを変更することで、TechArticle（技術記事）、Report（レポート）等にも対応できます。

### Q8: PDFファイルは必須ですか？

A: はい。Google Scholar への登録にはPDFファイルが必要です。PDFがない場合、`citation_pdf_url` メタタグが生成されず、Google Scholar に登録されない可能性が高くなります。

### Q9: 日本語の論文に対応していますか？

A: はい。日本語・英語の両方に対応しています。言語コード（`citation_language`）を適切に設定してください。

### Q10: サイトマップはどこに登録すればいいですか？

A: Google Search Console に `/paper/paper-sitemap.xml` を登録してください。

## セキュリティ

### 実装済みセキュリティ対策

1. **入力のサニタイズ**
   - `sanitize_text_field()` - テキストフィールド
   - `sanitize_email()` - メールアドレス
   - `esc_url_raw()` - URL
   - `wp_kses_post()` - HTML（抄録等）

2. **出力のエスケープ**
   - `esc_html()` - HTMLテキスト
   - `esc_attr()` - HTML属性
   - `esc_url()` - URL
   - `esc_js()` - JavaScript

3. **認証と権限**
   - Nonce検証（`wp_verify_nonce()`）
   - 権限チェック（`current_user_can('manage_options')`）
   - 直接アクセス防止（`if (!defined('ABSPATH')) exit;`）

4. **その他**
   - SQLインジェクション対策（WordPress API使用）
   - XSS対策（全出力をエスケープ）
   - CSRF対策（Nonceトークン）
   - スパムボット対策（`antispambot()`でメール難読化）

## パフォーマンス

### 最適化項目

1. **静的ファイル不要**: HTMLファイルを生成しないため、ストレージとI/O負荷を削減
2. **キャッシュバスター**: PDF更新時にブラウザキャッシュを自動無効化
3. **条件付きスクリプト**: 管理画面でのみJavaScript読み込み
4. **最小限のクエリ**: 必要なデータのみをデータベースから取得
5. **FastCGI バッファ最適化**: 大規模テーマでも正常動作

### 推奨サーバー設定

- **PHPメモリ**: 128MB以上（256MB推奨）
- **最大実行時間**: 30秒以上
- **FastCGI バッファ**: 32k以上

## 貢献

Issue報告やPull Requestを歓迎します。

### Issue報告

バグ報告や機能リクエストは、GitHubのIssueページで受け付けています。

### Pull Request

コード貢献の際は、WordPress Coding Standardsに準拠してください。

## ライセンス

GPL-2.0-or-later

このプラグインはフリーソフトウェアです。Free Software Foundationが公開するGNU General Public License（バージョン2、またはそれ以降のバージョン）の条件の下で再配布および変更することができます。

## 著者

**柏崎剛（Tsuyoshi Kashiwazaki）**

- ウェブサイト: https://www.tsuyoshikashiwazaki.jp/
- プロフィール: https://www.tsuyoshikashiwazaki.jp/profile/
- GitHub: https://github.com/TsuyoshiKashiwazaki

## 関連リンク

- [Google Scholar インデックスガイド](https://scholar.google.com/intl/ja/scholar/inclusion.html)
- [Schema.org - ScholarlyArticle](https://schema.org/ScholarlyArticle)
- [Dublin Core Metadata Initiative](https://www.dublincore.org/)
- [BibTeX フォーマット仕様](http://www.bibtex.org/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)

---

<div align="center">

**Keywords**: WordPress, Plugin, Preprint, Academic, Google Scholar, SEO, Landing Page, Citation, BibTeX, Schema.org

Made by [Tsuyoshi Kashiwazaki](https://github.com/TsuyoshiKashiwazaki)

</div>
