<?php
/*
Plugin Name: Convert to WebP
Description: Convierte automáticamente las imágenes JPG, JPEG y PNG a WebP al subirlas y elimina las originales.
Version: 1.2
Author: Lukas Ibáñez Villagrán
Dependencies: GD Library or Imagick extension
*/

if (!defined('ABSPATH')) {
    exit;
}

function convert_to_webp($file) {
    $file_path = $file['file'];
    $file_info = pathinfo($file_path);

    if (in_array(strtolower($file_info['extension']), ['jpg', 'jpeg', 'png'])) {
        $webp_file_path = $file_info['dirname'] . '/' . $file_info['filename'] . '.webp';

        $image = null;
        switch (strtolower($file_info['extension'])) {
            case 'jpg':
            case 'jpeg':
                $image = imagecreatefromjpeg($file_path);
                break;
            case 'png':
                $image = imagecreatefrompng($file_path);
                break;
        }

        if ($image) {
            if (imagewebp($image, $webp_file_path)) {
                imagedestroy($image);

                // Eliminar la imagen original
                unlink($file_path);

                // Actualizar el array de archivo para que apunte al nuevo archivo webp
                $file['file'] = $webp_file_path;
                $file['url'] = str_replace($file_info['basename'], $file_info['filename'] . '.webp', $file['url']);
                $file['type'] = 'image/webp';
            }
        }
    }

    return $file;
}
add_filter('wp_handle_upload', 'convert_to_webp');