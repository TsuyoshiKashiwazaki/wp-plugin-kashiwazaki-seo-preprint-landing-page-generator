<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_add_admin_columns( $columns ) {
    $new_columns = [];
    foreach ($columns as $key => $title) {
        // 抜粋列を除外
        if ($key === 'excerpt') {
            continue;
        }

        $new_columns[$key] = $title;
        if ($key == 'title') {
            $new_columns['pdf_url'] = __( 'PDF URL', PLPM_TEXT_DOMAIN );
            $new_columns['generated_html'] = __( 'Generated HTML', PLPM_TEXT_DOMAIN );
            $new_columns['plpm_keywords'] = __( 'Keywords', PLPM_TEXT_DOMAIN );
        }
    }

    // 抜粋列を確実に削除
    unset($new_columns['excerpt']);

    $date_column = [];
    if(isset($new_columns['date'])){
        $date_column = ['date' => $new_columns['date']];
        unset($new_columns['date']);
    }
    $custom_date_columns = [
        'plpm_pub_date' => __( 'Published', PLPM_TEXT_DOMAIN ),
        'plpm_modified_date' => __( 'Modified', PLPM_TEXT_DOMAIN ),
    ];
    $position_key = 'generated_html';
    if (array_key_exists($position_key, $new_columns)) {
        $offset = array_search($position_key, array_keys($new_columns)) + 1;
        $new_columns = array_slice($new_columns, 0, $offset, true) +
                       $custom_date_columns +
                       array_slice($new_columns, $offset, null, true);
    } else {
        $new_columns = array_merge($new_columns, $custom_date_columns);
    }
    if (!empty($date_column)) {
         $new_columns = array_merge($new_columns, $date_column);
    }
    return $new_columns;
}
function plpm_custom_column_content( $column_name, $post_id ) {
    if ( $column_name == 'pdf_url' ) {
        $pdf_url = get_post_meta( $post_id, '_plpm_pdf_url', true );
        if ( $pdf_url ) {
            echo '<a href="' . esc_url( $pdf_url ) . '" target="_blank">' . esc_html( basename($pdf_url) ) . '</a>';
        } else {
            echo '—';
        }
    }
    if ( $column_name == 'generated_html' ) {
        $post_status = get_post_status($post_id);

        if ($post_status === 'publish') {
            // 動的テンプレートシステム用のURL生成
            $dynamic_url = home_url('/paper/' . $post_id . '/');

            // 必須フィールドの存在確認
            $required_fields_exist = true;
            $required_meta_keys = ['_plpm_author', '_plpm_pub_date', '_plpm_publisher', '_plpm_keywords', '_plpm_abstract', '_plpm_pdf_url', '_plpm_bibtex_key'];

            foreach ($required_meta_keys as $key) {
                if (empty(get_post_meta($post_id, $key, true))) {
                    $required_fields_exist = false;
                    break;
                }
            }

            if ($required_fields_exist) {
                echo '<a href="' . esc_url($dynamic_url) . '" target="_blank">' . esc_html($post_id . '/') . '</a>';
                echo '<br><small style="color:#46b450;">' . __('Dynamic Template', PLPM_TEXT_DOMAIN) . '</small>';
            } else {
                echo '<small style="color:#dc3545;">' . __('Missing required fields', PLPM_TEXT_DOMAIN) . '</small>';
            }
        } else {
            echo '<small style="color:#777;">(' . esc_html(get_post_status_object($post_status)->label) . ')</small>';
        }
    }
    if ( $column_name == 'plpm_pub_date' ) {
        $pub_date = get_post_meta( $post_id, '_plpm_pub_date', true );
        if ( $pub_date ) {
            echo esc_html( $pub_date );
        } else {
            echo '—';
        }
    }
    if ( $column_name == 'plpm_modified_date' ) {
        $modified_date = get_post_meta( $post_id, '_plpm_modified_date', true );
        if ( $modified_date ) {
            echo esc_html( $modified_date );
        } else {
            echo '—';
        }
    }
    if ( $column_name == 'plpm_keywords' ) {
        $keywords = get_post_meta( $post_id, '_plpm_keywords', true );
        if ( $keywords ) {
            $keywords_array = array_map('trim', explode(',', $keywords));
            $display_keywords = array_slice($keywords_array, 0, 3); // 最初の3つまで表示
            echo '<span style="font-size: 12px; color: #666;">' . esc_html( implode(', ', $display_keywords) );
            if (count($keywords_array) > 3) {
                echo ' <em>(+' . (count($keywords_array) - 3) . ' more)</em>';
            }
            echo '</span>';
        } else {
            echo '—';
        }
    }
}
