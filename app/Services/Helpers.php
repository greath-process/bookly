<?php

namespace App\Services;

class Helpers
{
    public static function csvToArray($filename='', $delimiter=','): array|bool
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, null, $delimiter)) !== FALSE)
            {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }

    public static function getIbsnFromText($text)
    {
        $text = str_replace("-", "", $text);
        preg_match_all('/\b\d{8}(?!\d)|\b\d{9}(?!\d)|\b\d{10}(?!\d)|\b\d{13}(?!\d)/', $text, $matches);

        return $matches[0];
    }

    public static function clearTheTag(string $rawTags): string
    {
        if (str_contains('#', $rawTags)) {
            $arr = explode('#', $rawTags);
            $rawTags = $arr[0];
        }

        if (str_contains(
            str_replace(" ", "-", config('books.non_public_tag')), $rawTags)) {
            $messTag = str_replace(" ", "-", config('books.non_public_tag'));
            $rawTags = str_replace($messTag, config('books.non_public_tag'), $rawTags);
        }

        return $rawTags;
    }
}
