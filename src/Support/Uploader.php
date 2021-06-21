<?php

namespace AhmedAliraqi\LaravelMediaUploader\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Uploader
{
    /**
     * Get the formatted name of the given file.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public static function formatName(UploadedFile $file): string
    {
        $extension = '.'.$file->getClientOriginalExtension();

        $name = trim($file->getClientOriginalName(), $extension);

        $name = self::replaceNumbers($name);

        return Str::slug($name).$extension;
    }

    /**
     * Convert arabic & persian decimal to valid decimal.
     *
     * @param string $string
     * @return string
     */
    public static function replaceNumbers(string $string): string
    {
        $newNumbers = range(0, 9);

        // 1. Persian HTML decimal
        $persianDecimal = [
            '&#1776;',
            '&#1777;',
            '&#1778;',
            '&#1779;',
            '&#1780;',
            '&#1781;',
            '&#1782;',
            '&#1783;',
            '&#1784;',
            '&#1785;',
        ];
        // 2. Arabic HTML decimal
        $arabicDecimal = [
            '&#1632;',
            '&#1633;',
            '&#1634;',
            '&#1635;',
            '&#1636;',
            '&#1637;',
            '&#1638;',
            '&#1639;',
            '&#1640;',
            '&#1641;',
        ];
        // 3. Arabic Numeric
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        // 4. Persian Numeric
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $string = str_replace($persianDecimal, $newNumbers, $string);
        $string = str_replace($arabicDecimal, $newNumbers, $string);
        $string = str_replace($arabic, $newNumbers, $string);

        return str_replace($persian, $newNumbers, $string);
    }
}
