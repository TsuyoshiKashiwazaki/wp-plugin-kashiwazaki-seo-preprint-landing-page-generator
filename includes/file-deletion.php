<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function plpm_delete_html_file_by_id( $post_id ) {
    if ( empty($post_id) || !is_numeric($post_id) ) return false;
    $output_dir_path = PLPM_PAPER_DIR;
    $output_file_path = $output_dir_path . $post_id . '.html';
    if ( file_exists( $output_file_path ) ) {
        if ( is_writable( $output_file_path ) ) {
            if( unlink( $output_file_path ) ){
                 do_action('plpm_deletion_success', $post_id, 'html');

                 return true;
            } else {
                 do_action('plpm_deletion_failed', $post_id, 'html', 'unlink failed');
                 // エラーログ削除済み
                 return false;
            }
        } else {
             do_action('plpm_deletion_failed', $post_id, 'html', 'file not writable');
             // エラーログ削除済み
            return false;
        }
    }
    return true;
}
function plpm_delete_pdf_file_by_id( $post_id ) {
     if ( empty($post_id) || !is_numeric($post_id) ) return false;
    $output_dir_path = PLPM_PAPER_DIR;
    $output_file_path = $output_dir_path . $post_id . '.pdf';
    if ( file_exists( $output_file_path ) ) {
        if ( is_writable( $output_file_path ) ) {
            if( unlink( $output_file_path ) ){
                 do_action('plpm_deletion_success', $post_id, 'pdf');

                 return true;
            } else {
                 do_action('plpm_deletion_failed', $post_id, 'pdf', 'unlink failed');
                 // エラーログ削除済み
                 return false;
            }
        } else {
             do_action('plpm_deletion_failed', $post_id, 'pdf', 'file not writable');
             // エラーログ削除済み
            return false;
        }
    }
    return true;
}
// plpm_delete_list_html_file() は削除済み - 動的テンプレート処理では不要
function plpm_delete_associated_files( $post_id ) {
    if ( get_post_type( $post_id ) !== 'preprint_page' ) {
        return;
    }
    $post = get_post($post_id);
    if ($post) {
        plpm_delete_html_file_by_id( $post_id );
        plpm_delete_pdf_file_by_id( $post_id );
        delete_post_meta($post_id, '_plpm_generated_url');
        delete_post_meta($post_id, '_plpm_generation_status');
         delete_post_meta($post_id, '_plpm_pdf_url');
         delete_post_meta($post_id, '_plpm_pdf_attachment_id');
    }
}
