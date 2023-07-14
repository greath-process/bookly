<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class ImageGenerate
{
    protected const name = 'bookly';
    protected const path = 'i/';
    protected const width = 1200;
    protected const height = 630;
    protected const bookW = 200;
    protected const bookH = 300;
    public const stub = 'images/stubs/cover-';
    public const stubs = '/images/stubs/';
    public const covers = '/covers/';
    public const font = '/fonts/FiguralBook.ttf';
    protected const openLibCover = 'https://covers.openlibrary.org/b/isbn/';
    public const coverExt = '-L.jpg';
    public const basePath = '/public/';
    public const isbndbSite = '.isbndb.com';
    public const cloudfront = 'cloudfront.net';


    public function generateImageOpenGraph($user): void
    {
        if (!$user) {
            header('Content-Type: image/png');
        }

        $basePath = self::getBasePath();
        $DOCUMENT_ROOT = self::getRootPath();

        $x = $y = self::height;
        $books = $user
            ? $user->books()->limit(20)->select(['image', 'big_image'])->get()
            : Book::where('image', 'not like', "%placeholder%")->select(['image', 'big_image'])->get()->random(20);

        $covers = $books->map(function ($bk) {
            $big_image = $bk->big_image ? (file_exists($bk->big_image) ? $bk->big_image : null) : null;
            return $big_image ?  : $bk->image;
        })->toArray();

        $outputImage = imagecreatetruecolor(self::width, self::height);
        $white = imagecolorallocate($outputImage, 0, 0, 0);
        imagefill($outputImage, 0, 0, $white);

        foreach ($covers as $key => $cover) {
            if (str_contains($cover, '.png') || str_contains($cover, '.jpg') && !str_contains($cover, self::isbndbSite) && !str_contains($cover, self::cloudfront)) {
                $path = str_contains($cover, '.png') ? self::stubs : self::covers;
                if (!file_exists(public_path() . $path . basename($cover))) {
                    $cover = $this->generateCoverStub(self::name);
                }

                 $image = str_contains($cover, '.png')
                     ? imagecreatefrompng($DOCUMENT_ROOT. self::stubs . basename($cover))
                     : imagecreatefromjpeg($DOCUMENT_ROOT. self::covers . basename($cover));
            } else {
                $imagedata = file_get_contents($cover);
                $image = imagecreatefromstring($imagedata);
            }

            $positionX = (self::bookW * ($key % 6));
            $positionY = (self::bookH * floor($key / 6));
            $newY = (self::height / (imagesy($image) / 295)) + 5;
            $newX = imagesy($image) < 300 ? 970 : (self::width / (imagesx($image) / 102.5)) + 5;
            imagecopyresized($outputImage,$image,$positionX,$positionY,0,0, $newX, $newY,$x,$y);
        }

        if ($user && !file_exists($basePath . self::path . $user->slug)) {
            mkdir($basePath. self::path . $user->slug, 0777, true);
        }

        $filename = $user
            ? $basePath. self::path . $user->slug .'/'. self::name . '.png'
            : $basePath. self::path . self::name . '.png';

        imagepng($outputImage, $filename);

        imagedestroy($outputImage);
    }

    public static function generateCoverStub($title): string
    {
        header('Content-Type: image/png');

        $basePath = self::getBasePath();
        $DOCUMENT_ROOT = self::getRootPath();

        $outputImage = imagecreatetruecolor(self::bookW, self::bookH);
        $white = imagecolorallocate($outputImage, 245, 245, 245);
        imagefill($outputImage, 0, 0, $white);
        $black = imagecolorallocate($outputImage, 0, 0, 0);

        $text = str_replace(' ', PHP_EOL, $title);
        $font = $DOCUMENT_ROOT . self::font;

        imagettftext($outputImage, 10, 0, 35, 35, $black, $font, $text);

        $filename = $basePath. self::stub . base64_encode($title) .'.png';
        imagepng($outputImage, $filename);

        imagedestroy($outputImage);

        return asset($filename);
    }

    public function getLargeCover($ibsn): ?string
    {
        $url = self::openLibCover . $ibsn . self::coverExt;
        $check = get_headers($url, 1);
        if (current($check) != 'HTTP/1.1 404 Not Found') {
            $file_name = self::getBasePath() . self::covers . basename($url);
            file_put_contents($file_name, file_get_contents($url));

            return $_SERVER['DOCUMENT_ROOT'] ? asset($file_name) : $file_name;
        }

        return null;
    }

    public static function getBasePath(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'].'/' : base_path() . self::basePath;
    }

    public static function getRootPath(): string
    {
        return empty($_SERVER['DOCUMENT_ROOT']) ? self::getBasePath() : $_SERVER['DOCUMENT_ROOT'];
    }
}
