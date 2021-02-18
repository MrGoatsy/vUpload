<?php
/**
 * Copyright (c) 2019 free-open-source
*
*Permission is hereby granted, free of charge, to any person obtaining a copy
*of this software and associated documentation files (the "Software"), to deal
*in the Software without restriction, including without limitation the rights
*to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
*copies of the Software, and to permit persons to whom the Software is
*furnished to do so, subject to the following conditions:
*
*The above copyright notice and this permission notice shall be included in all
*copies or substantial portions of the Software.
*
*THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
*IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
*FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
*AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
*LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
*OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
*SOFTWARE.
 */

    class ImageConverter{
        /** @var array */
        private $imageFormat = [
            'gif',
            'jpeg',
            'jpg',
            'png',
            'webp',
        ];

        /** @var array */
        private $constImageFormat = [
            IMAGETYPE_GIF => 'gif',
            IMAGETYPE_JPEG => 'jpeg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
        ];

        /**
         * Do image conversion work
         *
         * @param string $from
         * @param string $to
         *
         * @return resource
         * @throws \InvalidArgumentException
         */
        public function convert($from, $to, $quality = null)
        {
            $image = $this->loadImage($from);
            if (!$image) {
                throw new \InvalidArgumentException(sprintf('Cannot load image from %s', $from));
            }

            $image = imagescale($image, 1920, 1080);
            
            return $this->saveImage($to, $image, $quality);
        }

        private function loadImage($from)
        {
            $extension = $this->getRealExtension($from);

            if (!array_key_exists($extension, $this->constImageFormat)) {
                throw new \InvalidArgumentException(sprintf('The %s extension is unsupported', $extension));
            }

            $format = $this->constImageFormat[$extension];

            switch ($format) {
                case 'gif':
                    $image = imagecreatefromgif($from);
                    break;
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($from);
                    break;
                case 'png':
                    $image = imagecreatefrompng($from);
                    break;
                case 'webp':
                    $image = imagecreatefromwebp($from);
                    break;
                default:
                    $image = null;
            }
            return $image;
        }

        private function saveImage($to, $image, $quality)
        {
            $extension = $this->getExtension($to);

            if ($extension === 'jpg') {
                $extension = 'jpeg';
            }

            if (!in_array($extension, $this->imageFormat)) {
                throw new \InvalidArgumentException(sprintf('The %s extension is unsupported', $extension));
            }
            if (!file_exists(dirname($to))) {
                $this->makeDirectory($to);
            }


            if(isset($quality) && !is_int($quality)) {
            throw new \InvalidArgumentException(sprintf('The %s quality has to be an integer', $quality));
            }

            switch ($extension) {
            case 'gif':
                $image = imagegif($image, $to);
                break;
            case 'jpg':
            case 'jpeg':
                if ($quality < -1 && $quality > 100) {
                    throw new \InvalidArgumentException(sprintf('The %s quality is out of range', $quality));
                }
                $image = imagejpeg($image, $to, $quality);
                break;          
            case 'png':
                if ($quality < -1 && $quality > 9) {
                    throw new \InvalidArgumentException(sprintf('The %s quality is out of range', $quality));
                }
                $image = imagepng($image, $to, $quality);
                break;
            case 'webp':
                if ($quality < 0 || $quality > 100) {
                    throw new \InvalidArgumentException(sprintf('The %s quality is out of range', $quality));
                }
                $image = imagewebp($image, $to, $quality);
                break;
            default:
                $image = null;
            }

            return $image;
        }

        /**
         * Given specific $path to detect current image extension
         */
        private function getRealExtension($path)
        {
            $extension = exif_imagetype($path);

            if (!array_key_exists($extension, $this->constImageFormat)) {
                throw new \InvalidArgumentException(sprintf('Cannot detect %s extension', $path));
            }

            return $extension;
        }

        /**
         * Get image extension from specific $path
         *
         * @param string $path
         *
         * @return string
         */
        private function getExtension($path)
        {
            $pathInfo = pathinfo($path);

            if (!array_key_exists('extension', $pathInfo)) {
                throw new \InvalidArgumentException(sprintf('Cannot find extension from %s', $path));
            }

            return $pathInfo['extension'];
        }

        /**
         * Try creating the directory
         *
         * @return bool
         * @throws \InvalidArgumentException
         */
        private function makeDirectory($to)
        {
            $result = @mkdir(dirname($to), 0755);

            if (!$result) {
                throw new \InvalidArgumentException(\sprintf('Cannot create %s directory', $to));
            }

            return $result;
        }
    }

    /**
     * Helper function
     *
     * @param string $from
     * @param string $to
     *
     * @return resource
     * @throws \InvalidArgumentException
     */
    function convert($from, $to, $quality = null) {
    $converter = new ImageConverter();
    return $converter->convert($from, $to, $quality);
    }
?>