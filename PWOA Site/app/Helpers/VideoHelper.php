<?php

if (!function_exists('youtube_embed')) {
    function youtube_embed($url)
    {
        if (!$url)
            return null;

        $id = null;

        if (preg_match('/youtu\.be\/([^\?]+)/', $url, $matches)) {
            $id = $matches[1];
        } elseif (preg_match('/v=([^\&]+)/', $url, $matches)) {
            $id = $matches[1];
        }

        return $id ? "https://www.youtube.com/embed/$id" : null;
    }
}