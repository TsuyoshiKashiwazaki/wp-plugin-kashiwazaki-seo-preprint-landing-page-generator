<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_add_meta_boxes() {
    add_meta_box(
        'plpm_preprint_details',
        __( 'Preprint Landing Page Metadata', PLPM_TEXT_DOMAIN ),
        'plpm_details_meta_box_html',
        'preprint_page',
        'normal',
        'high'
    );
}
function plpm_details_meta_box_html( $post ) {
    wp_nonce_field( 'plpm_save_meta_box_data', 'plpm_meta_box_nonce' );
    $is_preprint       = get_post_meta( $post->ID, '_plpm_is_preprint', true );
    $author            = get_post_meta( $post->ID, '_plpm_author', true ) ?: '';
    $email             = get_post_meta( $post->ID, '_plpm_email', true );
    $affiliation_name  = get_post_meta( $post->ID, '_plpm_affiliation_name', true ) ?: '';
    $affiliation_url   = get_post_meta( $post->ID, '_plpm_affiliation_url', true ) ?: '';
    $author_url        = get_post_meta( $post->ID, '_plpm_author_url', true ) ?: '';
    $google_scholar_url = get_post_meta( $post->ID, '_plpm_google_scholar_url', true );
    $doi               = get_post_meta( $post->ID, '_plpm_doi', true );
    $pub_date          = get_post_meta( $post->ID, '_plpm_pub_date', true );
    $modified_date     = get_post_meta( $post->ID, '_plpm_modified_date', true );
    $publisher         = get_post_meta( $post->ID, '_plpm_publisher', true ) ?: '';
    $language          = get_post_meta( $post->ID, '_plpm_language', true ) ?: 'en';
    $keywords          = get_post_meta( $post->ID, '_plpm_keywords', true );
    $abstract          = get_post_meta( $post->ID, '_plpm_abstract', true );
    $pdf_url           = get_post_meta( $post->ID, '_plpm_pdf_url', true );
    $pdf_attachment_id = get_post_meta( $post->ID, '_plpm_pdf_attachment_id', true );
    $bibtex_key        = get_post_meta( $post->ID, '_plpm_bibtex_key', true );
    $references        = get_post_meta( $post->ID, '_plpm_references', true );
    $version           = get_post_meta( $post->ID, '_plpm_version', true ) ?: '1.0';
    $license_name      = get_post_meta( $post->ID, '_plpm_license', true ) ?: 'CC BY 4.0';
    $license_url       = get_post_meta( $post->ID, '_plpm_license_url', true ) ?: 'https://creativecommons.org/licenses/by/4.0/';
    $schema_type       = get_post_meta( $post->ID, '_plpm_schema_type', true ) ?: 'ScholarlyArticle';
    $site_logo_url     = get_post_meta( $post->ID, '_plpm_site_logo_url', true );
    $og_image_url      = get_post_meta( $post->ID, '_plpm_og_image_url', true );
    $twitter_site      = get_post_meta( $post->ID, '_plpm_twitter_site', true );
    $twitter_creator   = get_post_meta( $post->ID, '_plpm_twitter_creator', true );
    $generated_url     = get_post_meta( $post->ID, '_plpm_generated_url', true );
    $generation_status = get_post_meta( $post->ID, '_plpm_generation_status', true );
    ?>
<p><strong><?php _e('Instructions:', PLPM_TEXT_DOMAIN); ?></strong> <?php _e('Fill in the metadata for your Preprint Landing Page. The HTML landing page will be automatically generated or updated in the <code>/paper/</code> directory when you save or update this entry. The HTML filename is the Post ID.', PLPM_TEXT_DOMAIN); ?></p>
<?php if ($generated_url && strpos($generation_status, 'Success') !== false): ?>
    <p style="background: #e7f7e7; border-left: 4px solid #4caf50; padding: 10px;"><strong><?php _e('Generated Page URL:', PLPM_TEXT_DOMAIN); ?></strong> <a href="<?php echo esc_url($generated_url); ?>" target="_blank"><?php echo esc_html($generated_url); ?></a><br><small><?php printf(__('Last generated: %s', PLPM_TEXT_DOMAIN), esc_html(str_replace('Success: ', '', $generation_status))); ?></small></p>
<?php elseif (strpos($generation_status, 'Error') !== false): ?>
     <p style="background: #fff1f1; border-left: 4px solid #d9534f; padding: 10px;"><strong><?php _e('Generation Error:', PLPM_TEXT_DOMAIN); ?></strong> <?php echo esc_html($generation_status); ?></p>
<?php endif; ?>
<table class="form-table">
    <tr><th><label for="plpm_is_preprint"><?php _e('Is this a Preprint Landing Page?', PLPM_TEXT_DOMAIN); ?></label></th><td><input name="plpm_is_preprint" type="checkbox" id="plpm_is_preprint" value="1" <?php checked($is_preprint, '1'); ?>></td></tr>
    <tr><th><label for="plpm_author"><?php _e('Author Name', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th><td><input type="text" id="plpm_author" name="plpm_author" value="<?php echo esc_attr($author); ?>" class="regular-text" /></td></tr>
    <tr><th><label for="plpm_email"><?php _e('Contact Email (Optional)', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="email" id="plpm_email" name="plpm_email" value="<?php echo esc_attr($email); ?>" class="regular-text" /></td></tr>
    <tr><th><label for="plpm_affiliation_name"><?php _e('Author Affiliation Name', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="text" id="plpm_affiliation_name" name="plpm_affiliation_name" value="<?php echo esc_attr($affiliation_name); ?>" class="regular-text" /><p class="description"><?php _e('Used for citation_author_institution meta tag and Schema.org publisher name.', PLPM_TEXT_DOMAIN); ?></p></td></tr>
    <tr><th><label for="plpm_affiliation_url"><?php _e('Affiliation URL', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="url" id="plpm_affiliation_url" name="plpm_affiliation_url" value="<?php echo esc_url($affiliation_url); ?>" class="regular-text" /><p class="description"><?php _e('Used for Schema.org publisher URL and author affiliation URL.', PLPM_TEXT_DOMAIN); ?></p></td></tr>
    <tr><th><label for="plpm_author_url"><?php _e('Author URL (Optional)', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="url" id="plpm_author_url" name="plpm_author_url" value="<?php echo esc_url($author_url); ?>" class="regular-text" /><p class="description"><?php _e('Used for Schema.org author URL.', PLPM_TEXT_DOMAIN); ?></p></td></tr>
    <tr>
        <th><label for="plpm_google_scholar_url"><?php _e('Google Scholar URL (Optional)', PLPM_TEXT_DOMAIN); ?></label></th>
        <td>
            <input type="url" id="plpm_google_scholar_url" name="plpm_google_scholar_url" value="<?php echo esc_url($google_scholar_url); ?>" class="regular-text" />
            <p class="description"><?php _e('Enter the URL to the author\'s Google Scholar profile to link it from the author name on the landing page.', PLPM_TEXT_DOMAIN); ?></p>
        </td>
    </tr>
    <tr>
        <th><label for="plpm_doi"><?php _e('DOI (Digital Object Identifier) (Optional)', PLPM_TEXT_DOMAIN); ?></label></th>
        <td>
            <input type="text" id="plpm_doi" name="plpm_doi" value="<?php echo esc_attr($doi); ?>" class="regular-text" pattern="^10\.\d{4,9}\/[-._;()/:A-Z0-9]+$" title="<?php esc_attr_e('Enter a valid DOI (e.g., 10.xxxx/yyyy). Case insensitive.', PLPM_TEXT_DOMAIN); ?>" />
            <p class="description"><?php _e('Enter the DOI for this paper (e.g., 10.1234/abc.xyz).', PLPM_TEXT_DOMAIN); ?></p>
        </td>
    </tr>
    <tr><th><label for="plpm_pub_date"><?php _e('Publication Date', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th><td><input type="date" id="plpm_pub_date" name="plpm_pub_date" value="<?php echo esc_attr($pub_date); ?>" /></td></tr>
    <tr><th><label for="plpm_modified_date"><?php _e('Modified Date', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="date" id="plpm_modified_date" name="plpm_modified_date" value="<?php echo esc_attr($modified_date); ?>" /><p class="description"><?php _e('Optional. If this paper is revised, enter the date of modification.', PLPM_TEXT_DOMAIN); ?></p></td></tr>
    <tr><th><label for="plpm_publisher"><?php _e('Publisher', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th><td><input type="text" id="plpm_publisher" name="plpm_publisher" value="<?php echo esc_attr($publisher); ?>" class="regular-text" /><p class="description"><?php _e('Used for citation_publisher meta tag and OGP site_name.', PLPM_TEXT_DOMAIN); ?></p></td></tr>
    <tr><th><label for="plpm_language"><?php _e('Language Code (e.g., en, ja)', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th><td><input type="text" id="plpm_language" name="plpm_language" value="<?php echo esc_attr($language); ?>" size="5" /></td></tr>
    <tr><th><label for="plpm_keywords"><?php _e('Keywords (comma-separated)', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th><td><input type="text" id="plpm_keywords" name="plpm_keywords" value="<?php echo esc_attr($keywords); ?>" class="large-text" /></td></tr>
    <tr><th><label for="plpm_abstract"><?php _e('Abstract', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th><td><textarea id="plpm_abstract" name="plpm_abstract" rows="10" class="large-text"><?php echo esc_textarea($abstract); ?></textarea></td></tr>
    <tr>
        <th><label for="plpm_pdf_url"><?php _e('PDF File URL', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th>
        <td>
            <input type="url" id="plpm_pdf_url" name="plpm_pdf_url" value="<?php echo esc_url($pdf_url); ?>" class="regular-text plpm_pdf_url_field" />
            <input type="hidden" id="plpm_pdf_attachment_id" name="plpm_pdf_attachment_id" value="<?php echo esc_attr($pdf_attachment_id); ?>" class="plpm_pdf_attachment_id_field" />
            <button type="button" class="button plpm_upload_pdf_button"><?php _e('Select PDF from Media', PLPM_TEXT_DOMAIN); ?></button>
            <p class="description"><?php _e('Select or enter the URL of the PDF file. The PDF file will be copied to the /paper/ directory to match the HTML location for Google Scholar.', PLPM_TEXT_DOMAIN); ?></p>
        </td>
    </tr>
    <tr>
        <th><label for="plpm_bibtex_key"><?php _e('BibTeX Key', PLPM_TEXT_DOMAIN); ?></label> <span class="description">(recommended)</span></th>
        <td>
            <input type="text" id="plpm_bibtex_key" name="plpm_bibtex_key" value="<?php echo esc_attr($bibtex_key); ?>" class="regular-text" pattern="[a-zA-Z0-9\-:_\/]+" title="<?php esc_attr_e('Only alphanumeric characters, hyphen, colon, underscore, slash allowed.', PLPM_TEXT_DOMAIN); ?>" />
            <p class="description"><?php _e('Enter a unique key for BibTeX citation (e.g., Kashiwazaki2025SEO). Avoid spaces and special characters like %. Used as ReDIF Handle as well.', PLPM_TEXT_DOMAIN); ?></p>
        </td>
    </tr>
    <tr><th><label for="plpm_references"><?php _e('References (One per line)', PLPM_TEXT_DOMAIN); ?></label></th><td><textarea id="plpm_references" name="plpm_references" rows="10" class="large-text"><?php echo esc_textarea($references); ?></textarea><p class="description"><?php _e('Enter each reference on a new line. These will be added as citation_reference meta tags.', PLPM_TEXT_DOMAIN); ?></p></td></tr>
    <tr><th><label for="plpm_version"><?php _e('Version', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="text" id="plpm_version" name="plpm_version" value="<?php echo esc_attr($version); ?>" size="5" /></td></tr>
    <tr><th><label for="plpm_license"><?php _e('License Name', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="text" id="plpm_license" name="plpm_license" value="<?php echo esc_attr($license_name); ?>" class="regular-text" /></td></tr>
    <tr><th><label for="plpm_license_url"><?php _e('License URL', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="url" id="plpm_license_url" name="plpm_license_url" value="<?php echo esc_url($license_url); ?>" class="regular-text" /></td></tr>
    <tr><th><label for="plpm_schema_type"><?php _e('Schema.org Base Type', PLPM_TEXT_DOMAIN); ?></label></th><td><select name="plpm_schema_type" id="plpm_schema_type"><option value="ScholarlyArticle" <?php selected($schema_type, 'ScholarlyArticle'); ?>>ScholarlyArticle</option><option value="TechArticle" <?php selected($schema_type, 'TechArticle'); ?>>TechArticle</option><option value="Report" <?php selected($schema_type, 'Report'); ?>>Report</option><option value="Article" <?php selected($schema_type, 'Article'); ?>>Article</option><option value="WebPage" <?php selected($schema_type, 'WebPage'); ?>>WebPage</option></select></td></tr>
    <tr><th><label for="plpm_site_logo_url"><?php _e('Site Logo URL (Optional)', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="url" id="plpm_site_logo_url" name="plpm_site_logo_url" value="<?php echo esc_url($site_logo_url); ?>" class="regular-text" /></td></tr>
    <tr><th><label for="plpm_og_image_url"><?php _e('OG/Twitter Image URL (Optional)', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="url" id="plpm_og_image_url" name="plpm_og_image_url" value="<?php echo esc_url($og_image_url); ?>" class="regular-text" /></td></tr>
    <tr><th><label for="plpm_twitter_site"><?php _e('Twitter Site Handle (Optional, include @)', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="text" id="plpm_twitter_site" name="plpm_twitter_site" value="<?php echo esc_attr($twitter_site); ?>" class="regular-text" /></td></tr>
    <tr><th><label for="plpm_twitter_creator"><?php _e('Twitter Creator Handle (Optional, include @)', PLPM_TEXT_DOMAIN); ?></label></th><td><input type="text" id="plpm_twitter_creator" name="plpm_twitter_creator" value="<?php echo esc_attr($twitter_creator); ?>" class="regular-text" /></td></tr>
</table>
<?php
}
function plpm_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['plpm_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['plpm_meta_box_nonce'], 'plpm_save_meta_box_data' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) && ! current_user_can( 'manage_options' ) ) return;
    if ( 'preprint_page' !== get_post_type($post_id) ) return;

    // デバッグログ削除済み

    // 無限ループ防止フラグ（固定IDベース）
    static $processed_posts = array();
    if ( isset($processed_posts[$post_id]) ) return;
    $processed_posts[$post_id] = true;
    $fields = [
        'plpm_is_preprint', 'plpm_author', 'plpm_email', 'plpm_affiliation_name', 'plpm_affiliation_url', 'plpm_author_url',
        'plpm_google_scholar_url', 'plpm_doi',
        'plpm_pub_date', 'plpm_modified_date',
        'plpm_publisher', 'plpm_language', 'plpm_keywords', 'plpm_abstract',
        'plpm_pdf_attachment_id',
        'plpm_bibtex_key',
        'plpm_references', 'plpm_version', 'plpm_license', 'plpm_license_url', 'plpm_schema_type', 'plpm_site_logo_url',
        'plpm_og_image_url', 'plpm_twitter_site', 'plpm_twitter_creator'
    ];
    $new_pdf_url = '';
    $pdf_attachment_id = isset($_POST['plpm_pdf_attachment_id']) ? absint($_POST['plpm_pdf_attachment_id']) : 0;
    $original_pdf_url_input = isset($_POST['plpm_pdf_url']) ? esc_url_raw($_POST['plpm_pdf_url']) : '';
    if ($pdf_attachment_id > 0) {
        // 動的テンプレートシステムではWordPressメディアライブラリのURLをそのまま使用
        $new_pdf_url = wp_get_attachment_url($pdf_attachment_id);
        if ($new_pdf_url) {
            update_post_meta($post_id, '_plpm_pdf_attachment_id', $pdf_attachment_id);

        } else {
            $new_pdf_url = get_post_meta($post_id, '_plpm_pdf_url', true);
            update_post_meta($post_id, '_plpm_generation_status', 'Error: Failed to get PDF URL from media library.');
            // エラーログ削除済み
        }
    } elseif (!empty($original_pdf_url_input) && filter_var($original_pdf_url_input, FILTER_VALIDATE_URL)) {
         // 動的システムでは /paper/ URLは無効なので、一般的な外部URLのみ受け付ける
         $new_pdf_url = $original_pdf_url_input;
         update_post_meta($post_id, '_plpm_pdf_attachment_id', '');
          if (strpos(get_post_meta($post_id, '_plpm_generation_status', true), 'Error: Failed to copy PDF file') !== false) {
              update_post_meta($post_id, '_plpm_generation_status', '');
          }

    } else {
        $new_pdf_url = '';
        update_post_meta($post_id, '_plpm_pdf_attachment_id', '');
         if (strpos(get_post_meta($post_id, '_plpm_generation_status', true), 'Error: Failed to copy PDF file') !== false) {
              update_post_meta($post_id, '_plpm_generation_status', '');
          }
    }
    update_post_meta($post_id, '_plpm_pdf_url', $new_pdf_url);
    // Abstract保存用の変数を初期化
    $abstract_value = '';

    foreach ($fields as $field) {
        $meta_key = '_' . $field;
        if ($field === 'plpm_pdf_url' || $field === 'plpm_pdf_attachment_id') {
             continue;
        }
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            if ($field === 'plpm_email') $sanitized_value = sanitize_email($value);
            elseif ($field === 'plpm_abstract' || $field === 'plpm_references') {
                $sanitized_value = sanitize_textarea_field(stripslashes($value));
                // Abstractの場合は後で抜粋にコピーするために保存
                if ($field === 'plpm_abstract') {
                    $abstract_value = $sanitized_value;
                }
            }
            elseif (in_array($field, ['plpm_google_scholar_url', 'plpm_affiliation_url', 'plpm_author_url', 'plpm_license_url', 'plpm_site_logo_url', 'plpm_og_image_url'])) $sanitized_value = esc_url_raw($value);
            elseif ($field === 'plpm_bibtex_key') $sanitized_value = preg_replace('/[^a-zA-Z0-9\-:_\/]/', '', $value);
            elseif ($field === 'plpm_doi') $sanitized_value = preg_replace('/[^a-zA-Z0-9-*:;()\/. ]/', '', $value);
            elseif ($field === 'plpm_pub_date' || $field === 'plpm_modified_date') {
                $sanitized_value = sanitize_text_field($value);
                if (!empty($sanitized_value) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $sanitized_value)) {
                    $sanitized_value = '';
                }
            }
            else $sanitized_value = sanitize_text_field($value);
            if ( empty($sanitized_value) && $field === 'plpm_modified_date' ) {
                delete_post_meta($post_id, $meta_key);
            } else {
                update_post_meta($post_id, $meta_key, $sanitized_value);
            }
        } else {
            if ($field === 'plpm_is_preprint') {
                delete_post_meta($post_id, $meta_key);
            }
            if ( $field === 'plpm_modified_date' ) {
                 delete_post_meta($post_id, $meta_key);
            }
        }
    }

    // Abstractを投稿の抜粋（Description）にもコピー
    if (isset($_POST['plpm_abstract'])) {
        wp_update_post(array(
            'ID' => $post_id,
            'post_excerpt' => $abstract_value
        ));
    }

    // 投稿の状態を公開に設定（新規投稿または下書きの場合）
    $post = get_post($post_id);
    if ($post && in_array($post->post_status, ['auto-draft', 'draft'])) {
        wp_update_post(array(
            'ID' => $post_id,
            'post_status' => 'publish'
        ));
    }

    // 静的ファイル生成を無効化 - 動的テンプレートシステムを使用
    // $post = get_post($post_id);
    // if ($post && $post->post_status === 'publish' && !empty($new_pdf_url)) {
    //     plpm_generate_html_file($post_id);
    // } else {
    //     plpm_delete_html_file_by_id($post_id);
    //     delete_post_meta($post_id, '_plpm_generated_url');
    //     delete_post_meta($post_id, '_plpm_generation_status');
    // }
    // plpm_generate_list_page();
    // plpm_generate_sitemap();

    // 処理完了でフラグをリセット（投稿ID単位）
    unset($processed_posts[$post_id]);
}
function plpm_regenerate_list_and_sitemap_after_delete( $post_id ) {
    // 静的ファイル生成を無効化 - 動的テンプレートシステムを使用
    // plpm_generate_list_page();
    // plpm_generate_sitemap();
}
