<?php

namespace App\Controller;

class Router
{
    public static function get($appRoute, $appCallback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }

        self::on($appRoute, $appCallback);
    }

    public static function post($appRoute, $appCallback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }

        self::on($appRoute, $appCallback);
    }

    public static function delete($appRoute, $appCallback) {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE') !== 0) {
            return;
        }

        self::on($appRoute, $appCallback);
    }

    private static function on($exprr, $call_back)
    {
        $paramtrs = $_SERVER['REQUEST_URI'];
        $paramtrs = (stripos($paramtrs, "/") !== 0) ? "/" . $paramtrs : $paramtrs;
        $exprr = str_replace('/', '\/', $exprr);
        $matched = preg_match('/^' . ($exprr) . '$/', $paramtrs, $is_matched, PREG_OFFSET_CAPTURE);

        if ($matched) {
            array_shift($is_matched);
            $paramtrs = array_map(function ($paramtr) {
                return $paramtr[0];
            }, $is_matched);
            $call_back(new Request($paramtrs), new Response());
        }
    }
}
