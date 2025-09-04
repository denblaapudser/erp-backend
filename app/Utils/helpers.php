<?php

namespace App\Utils;

class Helpers{
    public static function isRequestFrom($app){
        return match ($app) {
            'adminApp' => env('ADMIN_APP_DOMAIN') == self::getRequestHost(),
            'employeeApp' => env('EMPLOYEE_APP_DOMAIN') == self::getRequestHost(),
        };
    }

    private static function getRequestHost(){
        $request = request();
        $host = $request->headers->get('origin') 
            ? parse_url($request->headers->get('origin'), PHP_URL_HOST)
            : ($request->headers->get('referer') 
            ? parse_url($request->headers->get('referer'), PHP_URL_HOST)
            : $request->getHost());
        return $host;
    }
}