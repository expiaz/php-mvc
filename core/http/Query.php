<?php
namespace Core\Http;

abstract class Query{

    private static $_controller;
    private static $_action;
    private static $_param;
    private static $_request;
    private static $_url;


    public static function setController($controller){
        static::$_controller = $controller;
    }

    public static function setAction($action){
        static::$_action = $action;
    }

    public static function setParam($param){
        static::$_param = $param;
    }

    public static function setUrl($url){
        static::$_url = $url;
    }

    public static function setRequest($request)
    {
        static::$_request = $request;
    }


    public static function getController(){
        return static::$_controller;
    }

    public static function getAction(){
        return static::$_action;
    }

    public static function getParam(){
        return static::$_param;
    }

    public static function getRequest()
    {
        return static::$_request;
    }

    public static function getDisplayedUrl(){
        return static::$_url;
    }

    public static function getQueriedUrl(){
        return implode('/', array_merge([static::$_controller,static::$_action],static::$_param));
    }

    public static function getHttpHeaders(){
        return 'POST : ' . implode(', ',static::$_http['POST']) . ' & GET : ' . implode(', ',static::$_http['GET']);
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