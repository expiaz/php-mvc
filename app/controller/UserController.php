<?php

namespace App\Controller;

use Core\Exception\NoDataFoundException;
use Core\Facade\Contracts\FormFacade;
use Core\Facade\Contracts\RouterFacade;
use Core\Facade\Contracts\SessionFacade;
use Core\Facade\Contracts\UrlFacade;
use Core\Facade\Contracts\ViewFacade;
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

    public function middleware(callable $next, Request $request, Response $response){
        if(! SessionFacade::exists('connected_as'))
            return RouterFacade::redirect(UrlFacade::create('/auth'));
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
                SessionFacade::set('connected_as', $user->getId());
                return RouterFacade::redirect(UrlFacade::create('/'));
            } catch (NoDataFoundException $e){
                return ViewFacade::render('user/auth', [
                    'authForm' => $f,
                    'subscribeLink' => UrlFacade::create('/subscribe'),
                    'error' => 'Bad credentials'
                ]);
            }

        }

        return \View::render('user/auth', [
            'authForm' => $f,
            'subscribeLink' => UrlFacade::create('/subscribe'),
        ]);
    }

    public function deco(Request $request, Response $response){
        SessionFacade::delete('connected_as');
        return RouterFacade::redirect(UrlFacade::create('/auth'));
    }

    public function subscribe(Request $request, Response $response){
        $repo = $this->getRepository();

        $dummy = $repo->getModel();
        $form = FormFacade::create($dummy);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            try{
                $repo->find([
                    'select' => '*',
                    'from' => 'user',
                    'where' => 'name LIKE :name OR login LIKE :login'
                ], [
                    'name' => $dummy->getName(),
                    'login' => $dummy->getLogin()
                ]);

                return ViewFacade::render('user/inscription', [
                    'subForm' => $form->build(),
                    'error' => 'credentials already taken'
                ]);
            } catch (NoDataFoundException $e){
                $this->getRepository()->insert($dummy);
                SessionFacade::set('connected_as', $dummy->getId());
                return RouterFacade::redirect(UrlFacade::create('/'));
            }

        }

        return ViewFacade::render('user/inscription', [
            'subForm' => $form->build()
        ]);
    }

    public function alreadyCoMiddleware(callable $next, Request $request, Response $response) {
        if(SessionFacade::exists('connected_as')){
            return RouterFacade::redirect(UrlFacade::create('/'));
        }
        return $next($request, $response);
    }

}