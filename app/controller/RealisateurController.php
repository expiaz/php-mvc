<?php

namespace App\Controller;

use App\Model\RealisateurModel;
use Core\Exception\SqlAlterException;
use Core\Facade\Contracts\FormFacade;
use Core\Facade\Contracts\RouterFacade;
use Core\Facade\Contracts\UrlFacade;
use Core\Facade\Contracts\ViewFacade;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class RealisateurController extends Controller{

    public function index(Request $request, Response $response){
        return $this->all($request, $response);
    }

    public function all(Request $request, Response $response){
        try{
            $realisateurs = $this->getRepository()->getAll();
        } catch (NoDataFoundException $e){
            $realisateurs = [];
        }

        return ViewFacade::render('realisateur/index', [
            'realisateurs' => $realisateurs,
            'addLink' => UrlFacade::create('/realisateur/add')
        ]);
    }

    public function ensureExistsMiddleware(callable $next, Request $request, Response $response){
        try{
            $exists = $this->getRepository()->getById($request->getParameter('id'));
        } catch (NoDataFoundException $e){
            $response->withStatus(404);
            return ViewFacade::render('error/404', [
                'error' => '404 Not Found',
                'message' => "No realisateur for id {$request->getParameter('id')}"
            ]);
        }
        return $next($request, $response, $exists);
    }

    public function profile(Request $request, Response $response, RealisateurModel $realisateur){
        return ViewFacade::render('realisateur/profile', [
            'realisateur' => $realisateur,
            'films' => $realisateur->getFilmsModel()
        ]);

    }

    public function edit(Request $request, Response $response, RealisateurModel $realisateur){

        $form = FormFacade::create($realisateur);

        $form->handleRequest($request);

        if($form->isSubmitted()){

            try{
                $this->getRepository()->update($realisateur);
                return RouterFacade::redirect(UrlFacade::create("/realisateur/{$realisateur->getId()}"));
            } catch (SqlAlterException $e){
                return ViewFacade::render('realisateur/edit', [
                    'editForm' => $form->build(),
                    'realisateur' => $realisateur,
                    'error' => $e->getMessage()
                ]);
            }

        }

        return ViewFacade::render('realisateur/edit', [
            'editForm' => $form,
            'realisateur' => $realisateur,
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
                return ViewFacade::render('realisateur/add', [
                    'addForm' => $form,
                    'error' => $e->getMessage()
                ]);
            }

            return RouterFacade::redirect(UrlFacade::create("/realisateur/{$dummy->getId()}"));
        }

        return ViewFacade::render('realisateur/add', [
            'addForm' => $form
        ]);

    }

}