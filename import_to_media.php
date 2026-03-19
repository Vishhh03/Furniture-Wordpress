<?php
/**
 * Alter Ego Beds — Bulk Media Importer
 * Run inside the WordPress Docker container:
 *   docker exec furniture_wp php /tmp/import_to_media.php
 */

// Bootstrap WordPress
// Suppress notices about constants if any
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';

require_once('/var/www/html/wp-load.php');

// ── Config ────────────────────────────────────────────────────────────────────

$source_dir    = '/tmp/alterego_beds_images';
$admin_user_id = 1; // user ID to attribute uploads to

// ── Helpers ───────────────────────────────────────────────────────────────────

function log_msg($msg) {
    echo date('[H:i:s] ') . $msg . "\n";
    flush();
}

// ── Main ─────────────────────────────────────────────────────────────────────

if (!is_dir($source_dir)) {
    die("ERROR: Source directory not found: $source_dir\n");
}

require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

$upload_dir = wp_upload_dir();
log_msg("WordPress upload dir: " . $upload_dir['basedir']);

$success = 0;
$skipped = 0;
$errors  = 0;
$total   = 0;

// Walk through each product subfolder
$product_dirs = glob($source_dir . '/*', GLOB_ONLYDIR);
if (empty($product_dirs)) {
    // No subfolders — try flat
    $product_dirs = [$source_dir];
}

foreach ($product_dirs as $product_dir) {
    $product_name = basename($product_dir);
    log_msg("--- Product: $product_name ---");

    $images = glob($product_dir . '/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);
    if (empty($images)) {
        log_msg("  No images found, skipping.");
        continue;
    }

    foreach ($images as $img_path) {
        $total++;
        $filename = basename($img_path);

        // Check if already imported (by original filename in post meta)
        $existing = get_posts([
            'post_type'  => 'attachment',
            'meta_key'   => '_alterego_original_file',
            'meta_value' => $filename,
            'posts_per_page' => 1,
        ]);

        if (!empty($existing)) {
            log_msg("  [skip] Already in media library: $filename");
            $skipped++;
            continue;
        }

        // Copy to a temp upload location
        $file_array = [
            'name'     => $filename,
            'tmp_name' => $img_path,
        ];

        // Get MIME type
        $filetype = wp_check_filetype($filename);
        $mime     = $filetype['type'] ?: 'image/jpeg';

        // Read file and put into uploads dir
        $upload = wp_upload_bits($filename, null, file_get_contents($img_path));

        if ($upload['error']) {
            log_msg("  [error] Upload failed for $filename: " . $upload['error']);
            $errors++;
            continue;
        }

        // Create attachment post
        $attachment = [
            'guid'           => $upload['url'],
            'post_mime_type' => $mime,
            'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_author'    => $admin_user_id,
        ];

        $attach_id = wp_insert_attachment($attachment, $upload['file']);

        if (is_wp_error($attach_id)) {
            log_msg("  [error] Could not create attachment: " . $attach_id->get_error_message());
            $errors++;
            continue;
        }

        // Generate thumbnails & metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $attach_data);

        // Store original filename as meta so we can detect duplicates on re-run
        update_post_meta($attach_id, '_alterego_original_file', $filename);
        update_post_meta($attach_id, '_alterego_product',        $product_name);

        log_msg("  [ok] Imported: $filename  (ID: $attach_id)");
        $success++;
    }
}

// ── Summary ───────────────────────────────────────────────────────────────────

echo "\n";
echo "============================================================\n";
echo "Import complete!\n";
echo "  Total processed : $total\n";
echo "  Successfully imported: $success\n";
echo "  Already existed (skipped): $skipped\n";
echo "  Errors: $errors\n";
echo "============================================================\n";
