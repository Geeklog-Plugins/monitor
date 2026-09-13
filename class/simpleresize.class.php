<?php
/**
 * Minimal image resize helper for Monitor.
 *
 * Keeps the original image format and transparency. The implementation uses
 * only GD functions available in the Monitor PHP 5.6-8.1 compatibility range.
 */
class SimpleImage
{
    var $image = null;
    var $image_type = null;

    function __construct($filename = null)
    {
        if (!empty($filename)) {
            $this->load($filename);
        }
    }

    function load($filename)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new Exception('Image file is not readable');
        }

        $imageInfo = @getimagesize($filename);
        if ($imageInfo === false || !isset($imageInfo[2])) {
            throw new Exception('Unable to read image information');
        }

        $this->image_type = (int) $imageInfo[2];
        $image = false;

        if ($this->image_type === IMAGETYPE_JPEG && function_exists('imagecreatefromjpeg')) {
            $image = @imagecreatefromjpeg($filename);
        } elseif ($this->image_type === IMAGETYPE_GIF && function_exists('imagecreatefromgif')) {
            $image = @imagecreatefromgif($filename);
        } elseif ($this->image_type === IMAGETYPE_PNG && function_exists('imagecreatefrompng')) {
            $image = @imagecreatefrompng($filename);
        } else {
            throw new Exception('Unsupported image type or missing GD support');
        }

        if ($image === false) {
            throw new Exception('Unable to decode image');
        }

        $this->destroy();
        $this->image = $image;

        return true;
    }

    function save($filename, $image_type = null, $compression = 85, $permissions = null)
    {
        if (!$this->isLoaded()) {
            return false;
        }

        if ($image_type === null) {
            $image_type = $this->image_type;
        }

        $saved = false;

        if ($image_type === IMAGETYPE_JPEG && function_exists('imagejpeg')) {
            $quality = max(0, min(100, (int) $compression));
            $saved = imagejpeg($this->image, $filename, $quality);
        } elseif ($image_type === IMAGETYPE_GIF && function_exists('imagegif')) {
            $saved = imagegif($this->image, $filename);
        } elseif ($image_type === IMAGETYPE_PNG && function_exists('imagepng')) {
            /* Convert JPEG-style quality (0-100) to PNG compression (9-0). */
            $quality = max(0, min(100, (int) $compression));
            $pngCompression = (int) round(9 - ($quality * 9 / 100));
            $saved = imagepng($this->image, $filename, $pngCompression);
        }

        if ($saved && $permissions !== null) {
            @chmod($filename, $permissions);
        }

        return (bool) $saved;
    }

    function getWidth()
    {
        return $this->isLoaded() ? imagesx($this->image) : 0;
    }

    function getHeight()
    {
        return $this->isLoaded() ? imagesy($this->image) : 0;
    }

    function resizeToHeight($height)
    {
        $height = (int) $height;
        $currentHeight = $this->getHeight();

        if ($height <= 0 || $currentHeight <= 0) {
            return false;
        }

        $ratio = $height / $currentHeight;
        $width = (int) round($this->getWidth() * $ratio);

        return $this->resize($width, $height);
    }

    function resizeToWidth($width)
    {
        $width = (int) $width;
        $currentWidth = $this->getWidth();

        if ($width <= 0 || $currentWidth <= 0) {
            return false;
        }

        $ratio = $width / $currentWidth;
        $height = (int) round($this->getHeight() * $ratio);

        return $this->resize($width, $height);
    }

    function resize($width, $height)
    {
        $width = (int) round($width);
        $height = (int) round($height);

        if (!$this->isLoaded() || $width <= 0 || $height <= 0) {
            return false;
        }

        $newImage = imagecreatetruecolor($width, $height);
        if ($newImage === false) {
            return false;
        }

        $this->prepareTransparency($newImage);

        $resized = imagecopyresampled(
            $newImage,
            $this->image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $this->getWidth(),
            $this->getHeight()
        );

        if (!$resized) {
            imagedestroy($newImage);
            return false;
        }

        imagedestroy($this->image);
        $this->image = $newImage;

        return true;
    }

    function prepareTransparency($image)
    {
        if ($this->image_type === IMAGETYPE_PNG) {
            imagealphablending($image, false);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
            imagesavealpha($image, true);
        } elseif ($this->image_type === IMAGETYPE_GIF) {
            $transparentIndex = imagecolortransparent($this->image);
            if ($transparentIndex >= 0) {
                $color = imagecolorsforindex($this->image, $transparentIndex);
                $transparent = imagecolorallocate(
                    $image,
                    $color['red'],
                    $color['green'],
                    $color['blue']
                );
                imagefill($image, 0, 0, $transparent);
                imagecolortransparent($image, $transparent);
            }
        }
    }

    function isLoaded()
    {
        return is_resource($this->image)
            || (class_exists('GdImage', false) && $this->image instanceof GdImage);
    }

    function destroy()
    {
        if ($this->isLoaded()) {
            imagedestroy($this->image);
        }
        $this->image = null;
    }

    function __destruct()
    {
        $this->destroy();
    }
}
