<?php

namespace App\Controller;

use App\Model\ActeurModel;
use Core\Exception\SqlAlterException;
use Core\Facade\Contracts\FormFacade;
use Core\Facade\Contracts\RouterFacade;
use Core\Facade\Contracts\UrlFacade;
use Core\Facade\Contracts\ViewFacade;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class ActeurController extends Controller{

    public function index(Request $request, Response $response){
        return $this->all($request, $response);
    }

    public function all(Request $request, Response $response){
        try{
            $acteurs = $this->getRepository()->getAll();
        } catch (NoDataFoundException $e){
            $acteurs = [];
        }

        return ViewFacade::render('acteur/index', [
            'acteurs' => $acteurs,
            'addLink' => UrlFacade::create('/acteur/add')
        ]);
    }

    public function ensureExistsMiddleware(callable $next, Request $request, Response $response){
        try{
            $exists = $this->getRepository()->getById($request->getParameter('id'));
        } catch (NoDataFoundException $e){
            $response->withStatus(404);
            return ViewFacade::render('error/404', [
                'error' => '404 Not Found',
                'message' => "No acteur for id {$request->getParameter('id')}"
            ]);
        }
        return $next($request, $response, $exists);
    }

    public function profile(Request $request, Response $response, ActeurModel $acteur){
        return ViewFacade::render('acteur/profile', [
            'acteur' => $acteur,
            'films' => $acteur->getFilmsModel()
        ]);

    }

    public function edit(Request $request, Response $response, ActeurModel $acteur){

        $form = FormFacade::create($acteur);

        $form->handleRequest($request);

        if($form->isSubmitted()){

            try{
                $this->getRepository()->update($acteur);
                return RouterFacade::redirect(UrlFacade::create("/acteur/{$acteur->getId()}"));
            } catch (SqlAlterException $e){
                return ViewFacade::render('acteur/edit', [
                    'editForm' => $form->build(),
                    'acteur' => $acteur,
                    'error' => $e->getMessage()
                ]);
            }

        }

        return ViewFacade::render('acteur/edit', [
            'editForm' => $form,
            'acteur' => $acteur,
        ]);
    }

    public function add(Request $request, Response $response){

        $dummy = $this->getRepository()->getModel();

        $form = FormFacade::create($dummy);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            try{
                $this->getRepository()->insert($form->getData());
            } catch (SqlAlterException $e){
                return ViewFacade::render('acteur/add', [
                    'addForm' => $form,
                    'error' => $e->getMessage()
                ]);
            }

            return RouterFacade::redirect(UrlFacade::create("/acteur/{$dummy->getId()}"));
        }

        return ViewFacade::render('acteur/add', [
            'addForm' => $form
        ]);

    }

}