<?php
    namespace Controllers;
    use Lib\Pages;

    session_start();

    class ErrorController{
        public static function error404(){
            $pages = new Pages();
            $pages->render('Error/error404', ['titulo'=>'Pagina no Encontrada']);
        }
    }