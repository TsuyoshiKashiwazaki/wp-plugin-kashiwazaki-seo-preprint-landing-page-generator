<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 管理画面にプラグイン設定ページを追加
 */
function plpm_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=preprint_page',
        __('基本設定', PLPM_TEXT_DOMAIN),
        __('基本設定', PLPM_TEXT_DOMAIN),
        'manage_options',
        'plpm-basic-settings',
        'plpm_basic_settings_page'
    );
}
add_action('admin_menu', 'plpm_add_admin_menu');

/**
 * 基本設定ページの表示
 */
function plpm_basic_settings_page() {
    // サイトマップ再生成処理
    if (isset($_POST['regenerate_sitemap'])) {
        if (!wp_verify_nonce($_POST['plpm_sitemap_nonce'], 'plpm_sitemap_action')) {
            wp_die(__('Security check failed.', PLPM_TEXT_DOMAIN));
        }

        $result = plpm_generate_sitemap_file();
        if ($result) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('サイトマップを再生成しました！', PLPM_TEXT_DOMAIN) . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('サイトマップの生成に失敗しました。', PLPM_TEXT_DOMAIN) . '</p></div>';
        }
    }

    if (isset($_POST['submit'])) {
        // nonce検証
        if (!wp_verify_nonce($_POST['plpm_settings_nonce'], 'plpm_settings_action')) {
            wp_die(__('Security check failed.', PLPM_TEXT_DOMAIN));
        }

        // 設定を保存
        update_option('plpm_list_page_title', sanitize_text_field($_POST['list_page_title']));
        update_option('plpm_show_author_info', isset($_POST['show_author_info']) ? 1 : 0);
        update_option('plpm_enable_sitemap', isset($_POST['enable_sitemap']) ? 1 : 0);

        // Citation形式の設定を保存
        $citation_formats = array();
        $available_formats = array('html_basic', 'html_abstract', 'plain_basic', 'plain_abstract', 'bibtex', 'ris', 'redif', 'json', 'apa', 'mla', 'chicago', 'ieee');
        foreach ($available_formats as $format) {
            if (isset($_POST['citation_formats']) && in_array($format, $_POST['citation_formats'])) {
                $citation_formats[] = $format;
            }
        }
        update_option('plpm_citation_formats', $citation_formats);

        // デザインテーマ設定
        if (isset($_POST['design_theme'])) {
            $selected_theme = sanitize_text_field($_POST['design_theme']);
            update_option('plpm_design_theme', $selected_theme);

            // 選択されたテーマの色設定を取得して保存
            $theme_colors = plpm_get_theme_colors($selected_theme);
            if ($theme_colors) {
                foreach ($theme_colors as $color_key => $color_value) {
                    update_option('plpm_' . $color_key, $color_value);
                }
            }
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully!', PLPM_TEXT_DOMAIN) . '</p></div>';
    }

    // 現在の設定値を取得
    $list_page_title = plpm_get_option('list_page_title', '投稿論文一覧');
    $show_author_info = plpm_get_option('show_author_info', 1);
    $enable_sitemap = plpm_get_option('enable_sitemap', 1);
    // Citation形式のデフォルト（全12形式）
    $all_citation_formats = array('html_basic', 'html_abstract', 'plain_basic', 'plain_abstract', 'bibtex', 'ris', 'redif', 'json', 'apa', 'mla', 'chicago', 'ieee');
    $citation_formats = plpm_get_option('citation_formats', $all_citation_formats);

    // 設定が空の場合、または古い設定の場合は全形式をデフォルトに設定
    if (empty($citation_formats) || count($citation_formats) < count($all_citation_formats)) {
        $citation_formats = $all_citation_formats;
        plpm_update_option('citation_formats', $citation_formats);
    }

    // カラーテーマのデフォルト値
    $primary_color = plpm_get_option('primary_color', '#0073aa');
    $secondary_color = plpm_get_option('secondary_color', '#005177');
    $border_color = plpm_get_option('border_color', '#dddddd');
    $bg_color = plpm_get_option('bg_color', '#ffffff');
    $text_color = plpm_get_option('text_color', '#333333');
    $accent_color = plpm_get_option('accent_color', '#f0f0f0');
        ?>
    <div class="wrap">
        <h1><?php echo esc_html__('基本設定', PLPM_TEXT_DOMAIN); ?></h1>

        <form method="post" action="">
            <?php wp_nonce_field('plpm_settings_action', 'plpm_settings_nonce'); ?>

            <h2><?php echo esc_html__('一般設定', PLPM_TEXT_DOMAIN); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="list_page_title"><?php echo esc_html__('リストページタイトル', PLPM_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <input type="text" id="list_page_title" name="list_page_title"
                               value="<?php echo esc_attr($list_page_title); ?>" class="regular-text" />
                        <p class="description">
                            <?php echo esc_html__('/paper/ ページのタイトルとパンくずリストに表示されます。', PLPM_TEXT_DOMAIN); ?><br>
                            <?php echo esc_html__('例：「投稿論文一覧」「論文アーカイブ」「Publications」など', PLPM_TEXT_DOMAIN); ?>
                        </p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="show_author_info"><?php echo esc_html__('著者情報の表示', PLPM_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="show_author_info" name="show_author_info"
                                   <?php checked($show_author_info, 1); ?> />
                            <?php echo esc_html__('リストページと詳細ページで著者情報を表示する', PLPM_TEXT_DOMAIN); ?>
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="enable_sitemap"><?php echo esc_html__('サイトマップ生成', PLPM_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="enable_sitemap" name="enable_sitemap"
                                   <?php checked($enable_sitemap, 1); ?> />
                            <?php echo esc_html__('論文ページ用のXMLサイトマップを自動生成する', PLPM_TEXT_DOMAIN); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <h2><?php echo esc_html__('Citation Export設定', PLPM_TEXT_DOMAIN); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php echo esc_html__('表示するCitation形式', PLPM_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php echo esc_html__('Citation形式を選択', PLPM_TEXT_DOMAIN); ?></legend>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="bibtex"
                                       <?php checked(in_array('bibtex', $citation_formats)); ?> />
                                <?php echo esc_html__('BibTeX', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="ris"
                                       <?php checked(in_array('ris', $citation_formats)); ?> />
                                <?php echo esc_html__('RIS', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="redif"
                                       <?php checked(in_array('redif', $citation_formats)); ?> />
                                <?php echo esc_html__('ReDIF', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="html_basic"
                                       <?php checked(in_array('html_basic', $citation_formats)); ?> />
                                <?php echo esc_html__('HTML', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="html_abstract"
                                       <?php checked(in_array('html_abstract', $citation_formats)); ?> />
                                <?php echo esc_html__('HTML + Abstract', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="plain_basic"
                                       <?php checked(in_array('plain_basic', $citation_formats)); ?> />
                                <?php echo esc_html__('Plain Text', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="plain_abstract"
                                       <?php checked(in_array('plain_abstract', $citation_formats)); ?> />
                                <?php echo esc_html__('Plain Text + Abstract', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="json"
                                       <?php checked(in_array('json', $citation_formats)); ?> />
                                <?php echo esc_html__('JSON', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="apa"
                                       <?php checked(in_array('apa', $citation_formats)); ?> />
                                <?php echo esc_html__('APA Style', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="mla"
                                       <?php checked(in_array('mla', $citation_formats)); ?> />
                                <?php echo esc_html__('MLA Style', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="chicago"
                                       <?php checked(in_array('chicago', $citation_formats)); ?> />
                                <?php echo esc_html__('Chicago Style', PLPM_TEXT_DOMAIN); ?>
                            </label><br>

                            <label>
                                <input type="checkbox" name="citation_formats[]" value="ieee"
                                       <?php checked(in_array('ieee', $citation_formats)); ?> />
                                <?php echo esc_html__('IEEE Style', PLPM_TEXT_DOMAIN); ?>
                            </label>

                            <p class="description">
                                <?php echo esc_html__('個別論文ページのCitation Exportセクションに表示する形式を選択してください。', PLPM_TEXT_DOMAIN); ?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <h2><?php echo esc_html__('デザインテーマ選択', PLPM_TEXT_DOMAIN); ?></h2>

            <?php
            // デザインテーマの定義
            $design_themes = array(
                'default' => array(
                    'name' => 'デフォルト（ブルー）',
                    'description' => '清潔感のあるブルー系テーマ',
                    'colors' => array(
                        'primary_color' => '#007cba',
                        'secondary_color' => '#f8f9fa',
                        'border_color' => '#ddd',
                        'bg_color' => '#ffffff',
                        'text_color' => '#333333',
                        'accent_color' => '#0073aa'
                    )
                ),
                'orange' => array(
                    'name' => 'オレンジテーマ',
                    'description' => '暖かみのあるオレンジ系テーマ',
                    'colors' => array(
                        'primary_color' => '#ff6600',
                        'secondary_color' => '#fff7f0',
                        'border_color' => '#ffcc99',
                        'bg_color' => '#ffffff',
                        'text_color' => '#333333',
                        'accent_color' => '#e55a00'
                    )
                ),
                'green' => array(
                    'name' => 'グリーンテーマ',
                    'description' => '自然をイメージしたグリーン系テーマ',
                    'colors' => array(
                        'primary_color' => '#28a745',
                        'secondary_color' => '#f8fff8',
                        'border_color' => '#c3e6cb',
                        'bg_color' => '#ffffff',
                        'text_color' => '#333333',
                        'accent_color' => '#1e7e34'
                    )
                ),
                'purple' => array(
                    'name' => 'パープルテーマ',
                    'description' => '上品で知的なパープル系テーマ',
                    'colors' => array(
                        'primary_color' => '#6f42c1',
                        'secondary_color' => '#f8f6ff',
                        'border_color' => '#d1c4e9',
                        'bg_color' => '#ffffff',
                        'text_color' => '#333333',
                        'accent_color' => '#5a32a3'
                    )
                ),
                'dark' => array(
                    'name' => 'ダークテーマ',
                    'description' => 'モダンなダーク系テーマ（高コントラスト）',
                    'colors' => array(
                        'primary_color' => '#66d9ff',
                        'secondary_color' => '#2d3748',
                        'border_color' => '#4a5568',
                        'bg_color' => '#1a202c',
                        'text_color' => '#ffffff',
                        'accent_color' => '#ffd700'
                    )
                )
            );

            $current_theme = plpm_get_option('design_theme', 'default');
            ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="design_theme"><?php echo esc_html__('デザインテーマ', PLPM_TEXT_DOMAIN); ?></label>
                    </th>
                    <td>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 20px;">
                            <?php foreach ($design_themes as $theme_key => $theme_data): ?>
                                <div class="theme-option" style="border: 2px solid #ddd; border-radius: 8px; padding: 15px; position: relative;">
                                    <label style="display: block; cursor: pointer;">
                                        <input type="radio" name="design_theme" value="<?php echo esc_attr($theme_key); ?>"
                                               <?php checked($current_theme, $theme_key); ?>
                                               style="margin-bottom: 10px;" />
                                        <strong><?php echo esc_html($theme_data['name']); ?></strong>
                                        <br><small style="color: #666;"><?php echo esc_html($theme_data['description']); ?></small>
                                    </label>

                                    <!-- カラーパレット表示 -->
                                    <div style="display: flex; gap: 5px; margin-top: 10px; flex-wrap: wrap;">
                                        <?php foreach ($theme_data['colors'] as $color_key => $color_value): ?>
                                            <div style="width: 25px; height: 25px; background-color: <?php echo esc_attr($color_value); ?>; border: 1px solid #ccc; border-radius: 3px;"
                                                 title="<?php echo esc_attr($color_key . ': ' . $color_value); ?>"></div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="description"><?php echo esc_html__('お好みのデザインテーマを選択してください。選択したテーマの色設定が自動的に適用されます。', PLPM_TEXT_DOMAIN); ?></p>

                        <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            $('input[name="design_theme"]').change(function() {
                                $('.theme-option').css('border-color', '#ddd');
                                $(this).closest('.theme-option').css('border-color', '#007cba');
                            });

                            // 初期状態で選択されているテーマをハイライト
                            $('input[name="design_theme"]:checked').closest('.theme-option').css('border-color', '#007cba');
                        });
                        </script>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>

        <hr>

        <h2><?php echo esc_html__('Current Settings Status', PLPM_TEXT_DOMAIN); ?></h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Setting', PLPM_TEXT_DOMAIN); ?></th>
                    <th><?php echo esc_html__('Current Value', PLPM_TEXT_DOMAIN); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo esc_html__('List Page Title', PLPM_TEXT_DOMAIN); ?></td>
                    <td><code><?php echo esc_html($list_page_title); ?></code></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Show Author Info', PLPM_TEXT_DOMAIN); ?></td>
                    <td><code><?php echo $show_author_info ? 'Enabled' : 'Disabled'; ?></code></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Generate Sitemap', PLPM_TEXT_DOMAIN); ?></td>
                    <td><code><?php echo $enable_sitemap ? 'Enabled' : 'Disabled'; ?></code></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Paper Directory', PLPM_TEXT_DOMAIN); ?></td>
                    <td><code><?php echo esc_html(PLPM_PAPER_DIR); ?></code></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('List Page URL', PLPM_TEXT_DOMAIN); ?></td>
                    <td><code><?php echo esc_url(home_url('/paper/')); ?></code></td>
                </tr>
                                <tr>
                    <td><?php echo esc_html__('Sitemap URL', PLPM_TEXT_DOMAIN); ?></td>
                    <td>
                        <?php if ($enable_sitemap):
                            $sitemap_url = home_url('/paper/paper-sitemap.xml');
                        ?>
                            <code><a href="<?php echo esc_url($sitemap_url); ?>" target="_blank"><?php echo esc_html($sitemap_url); ?></a></code>
                            <br><small>
                                <?php if (plpm_sitemap_exists()): ?>
                                    <span style="color: green;">✓ <?php echo esc_html__('サイトマップが生成されています', PLPM_TEXT_DOMAIN); ?></span>
                                <?php else: ?>
                                    <span style="color: orange;">⚠ <?php echo esc_html__('サイトマップが見つかりません', PLPM_TEXT_DOMAIN); ?></span>
                                <?php endif; ?>
                            </small>
                        <?php else: ?>
                            <code><?php echo esc_html__('無効', PLPM_TEXT_DOMAIN); ?></code>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Citation Formats', PLPM_TEXT_DOMAIN); ?></td>
                    <td>
                        <code>
                            <?php
                            if (!empty($citation_formats)) {
                                $format_names = array(
                                    'html_basic' => 'HTML',
                                    'html_abstract' => 'HTML + Abstract',
                                    'plain_basic' => 'Plain Text',
                                    'plain_abstract' => 'Plain Text + Abstract',
                                    'bibtex' => 'BibTeX',
                                    'ris' => 'RIS',
                                    'redif' => 'ReDIF',
                                    'json' => 'JSON',
                                    'apa' => 'APA Style',
                                    'mla' => 'MLA Style',
                                    'chicago' => 'Chicago Style',
                                    'ieee' => 'IEEE Style'
                                );
                                $active_formats = array();
                                foreach ($citation_formats as $format) {
                                    if (isset($format_names[$format])) {
                                        $active_formats[] = $format_names[$format];
                                    }
                                }
                                echo esc_html(implode(', ', $active_formats));
                            } else {
                                echo esc_html__('なし', PLPM_TEXT_DOMAIN);
                            }
                            ?>
                        </code>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('動的処理状況', PLPM_TEXT_DOMAIN); ?></td>
                    <td>
                        <code><?php echo esc_html__('完全動的モード', PLPM_TEXT_DOMAIN); ?></code>
                        <br><small><?php echo esc_html__('静的ファイルを生成せず、WordPressのリライトルールで処理', PLPM_TEXT_DOMAIN); ?></small>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if ($enable_sitemap): ?>
        <h3><?php echo esc_html__('サイトマップ管理', PLPM_TEXT_DOMAIN); ?></h3>
        <table class="widefat">
            <tbody>
                <tr>
                    <td style="width: 200px;"><strong><?php echo esc_html__('サイトマップURL', PLPM_TEXT_DOMAIN); ?></strong></td>
                    <td>
                        <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank" class="button button-secondary">
                            <?php echo esc_html__('サイトマップを表示', PLPM_TEXT_DOMAIN); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php echo esc_html__('再生成', PLPM_TEXT_DOMAIN); ?></strong></td>
                    <td>
                        <form method="post" action="" style="display: inline;">
                            <?php wp_nonce_field('plpm_sitemap_action', 'plpm_sitemap_nonce'); ?>
                            <input type="hidden" name="regenerate_sitemap" value="1" />
                            <button type="submit" class="button button-primary">
                                <?php echo esc_html__('サイトマップを再生成', PLPM_TEXT_DOMAIN); ?>
                            </button>
                        </form>
                        <p class="description"><?php echo esc_html__('論文が更新された場合にサイトマップを手動で再生成できます。', PLPM_TEXT_DOMAIN); ?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * サイトマップが存在するかチェック
 */
function plpm_sitemap_exists() {
    $sitemap_url = home_url('/paper/paper-sitemap.xml');
    $response = wp_remote_head($sitemap_url, array('sslverify' => false));
    return (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200);
}

/**
 * サイトマップの再生成（動的生成なので実際にはキャッシュクリアのみ）
 */
function plpm_generate_sitemap_file() {
    if (!plpm_get_option('enable_sitemap', 1)) {
        return false;
    }

    // 動的サイトマップなのでキャッシュをクリア
    wp_cache_flush();

    return true; // 動的生成なので常に成功
}

/**
 * テーマの色設定を取得
 */
function plpm_get_theme_colors($theme_key) {
    $design_themes = array(
        'default' => array(
            'primary_color' => '#007cba',
            'secondary_color' => '#f8f9fa',
            'border_color' => '#ddd',
            'bg_color' => '#ffffff',
            'text_color' => '#333333',
            'accent_color' => '#0073aa'
        ),
        'orange' => array(
            'primary_color' => '#ff6600',
            'secondary_color' => '#fff7f0',
            'border_color' => '#ffcc99',
            'bg_color' => '#ffffff',
            'text_color' => '#333333',
            'accent_color' => '#e55a00'
        ),
        'green' => array(
            'primary_color' => '#28a745',
            'secondary_color' => '#f8fff8',
            'border_color' => '#c3e6cb',
            'bg_color' => '#ffffff',
            'text_color' => '#333333',
            'accent_color' => '#1e7e34'
        ),
        'purple' => array(
            'primary_color' => '#6f42c1',
            'secondary_color' => '#f8f6ff',
            'border_color' => '#d1c4e9',
            'bg_color' => '#ffffff',
            'text_color' => '#333333',
            'accent_color' => '#5a32a3'
        ),
        'dark' => array(
            'primary_color' => '#66d9ff',
            'secondary_color' => '#2d3748',
            'border_color' => '#4a5568',
            'bg_color' => '#1a202c',
            'text_color' => '#ffffff',
            'accent_color' => '#ffd700'
        )
    );

    return isset($design_themes[$theme_key]) ? $design_themes[$theme_key] : $design_themes['default'];
}
