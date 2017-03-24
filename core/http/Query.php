<?php
namespace Core\Http;

abstract class Query{

    private static $_controller;
    private static $_action;
    private static $_param;
    private static $_http;
    private static $_url;


    public static function setController($controller){
        self::$_controller = $controller;
    }

    public static function setAction($action){
        self::$_action = $action;
    }

    public static function setParam($param){
        self::$_param = $param;
    }

    public static function setUrl($url){
        self::$_url = $url;
    }

    public static function setHttp($http)
    {
        self::$_http = $http;
    }


    public static function getController(){
        return self::$_controller;
    }

    public static function getAction(){
        return self::$_action;
    }

    public static function getParam(){
        return self::$_param;
    }

    public static function getHttp()
    {
        return self::$_http;
    }

    public static function getDisplayedUrl(){
        return self::$_url;
    }

    public static function getQueriedUrl(){
        return implode('/', array_merge([self::$_controller,self::$_action],self::$_param));
    }

    public static function getHttpHeaders(){
        return 'POST : ' . implode(', ',self::$_http['POST']) . ' & GET : ' . implode(', ',self::$_http['GET']);
    }

    public static function build($controller = null, $action = null, $param = null, $get = null){

        if(!is_array($controller)){
            $controller = [$controller,$action,$param];
        }
        return WEBROOT . implode('/',
            array_map(
                function($e){
                    return is_array($e)
                        ? implode('/',$e)
                        : $e;
                },
                array_filter(
                    array_values($controller),
                    function($e){
                        return $e !== null;
                    }
                )
            )
        ) . ($get !== null ? '&' . http_build_query($get) : '');
    }


}