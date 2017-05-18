<?php

namespace App\Controller;

use Core\Exception\NoDataFoundException;
use Core\Form\Field\Input\PasswordInput;
use Core\Form\Field\Input\SubmitInput;
use Core\Form\Field\Input\TextInput;
use Core\Form\Form;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class UserController extends Controller{

    public function index(Request $request, Response $response){

    }

    public function middleware(Request $request, Response $response, $next){
        if(! \Session::exists('connected_as'))
            return \Router::redirect(\Url::create('/auth'));
        return $next($request, $response);
    }

    public function auth(Request $request, Response $response){
        $f = new Form();

        $f->field((new TextInput())
            ->name('login')
            ->required()
            ->placeholder('login')
            ->label());

        $f->field((new PasswordInput())
            ->name('password')
            ->required()
            ->placeholder('password')
            ->label());

        $f->field((new SubmitInput())
            ->name('submit')
            ->value('envoyer'));

        $f->handleRequest($request);

        if($f->isSubmitted()){
            $credentials = $f->getData();

            try{
                $user = $this->getRepository()->auth($credentials['login'], $credentials['password']);
                \Session::set('connected_as', $user->getId());
                return \Router::redirect(\Url::create('/'));
            } catch (NoDataFoundException $e){
                return \View::render('auth', [
                    'authForm' => $f,
                    'error' => 'Bad credentials'
                ]);
            }

        }

        return \View::render('auth', [
            'authForm' => $f
        ]);
    }

}