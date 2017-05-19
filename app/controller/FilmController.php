<?php

namespace App\Controller;

use App\Model\FilmModel;
use App\Repository\ActeurRepository;
use App\Repository\FilmRepository;
use Core\App;
use Core\Exception\NoDataFoundException;
use Core\Exception\SqlAlterException;
use Core\Facade\Contracts\FormFacade;
use Core\Facade\Contracts\RouterFacade;
use Core\Facade\Contracts\UrlFacade;
use Core\Facade\Contracts\ViewFacade;
use Core\Form\Field\Select\OptionField;
use Core\Form\Field\SelectField;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class FilmController extends Controller{

    public function index(Request $request, Response $response){
        return $this->all($request, $response);
    }

    public function all(Request $request, Response $response){
        try{
            $films = $this->getRepository()->getAll();
        } catch (NoDataFoundException $e){
            $films = [];
        }

        return ViewFacade::render('film/index', [
            'films' => $films,
            'addLink' => UrlFacade::create('/film/add')
        ]);
    }

    public function ensureExistsMiddleware(callable $next, Request $request, Response $response){
        try{
            $exists = $this->getRepository()->getById($request->getParameter('id'));
        } catch (NoDataFoundException $e){
            $response->withStatus(404);
            return ViewFacade::render('error/404', [
                'error' => '404 Not Found',
                'message' => "No film for id {$request->getParameter('id')}"
            ]);
        }
        return $next($request, $response, $exists);
    }

    public function profile(Request $request, Response $response, FilmModel $film){

        try{
            $acteurs = $film->getActeurModel();
        } catch (NoDataFoundException $e){
            $acteurs = [];
        }

        try{
            $realisateur = $film->getRealisateurModel();
        } catch (NoDataFoundException $e){
            $realisateur = 'No realisateur';
        }

        return ViewFacade::render('film/profile', [
            'film' => $film,
            'acteurs' => $acteurs,
            'realisateur' => $realisateur
        ]);
    }

    public function edit(Request $request, Response $response, FilmModel $film){

        $form = FormFacade::create($film);
        /*
        try{
            $acteurs = App::make(ActeurRepository::class)->getAll();
        } catch (NoDataFoundException $e){
            $acteurs = [];
        }

        $filmActeurs = $film->getActeurs();

        $field = new SelectField();

        foreach ($acteurs as $acteur){
            $o = new OptionField();
            $o->value($acteur->getId());
            $o->content($acteur->getName());
            if(in_array($acteur->getId(), $filmActeurs)){
                $o->selected();
            }
            $field->option($o);
        }

        $field->multiple();
        $field->id('acteurs');
        $field->label('acteurs');
        $field->name('acteurs');
        $field->required();

        $form->field($field);*/

        $form->handleRequest($request);

        if($form->isSubmitted()){
            try{
                $this->getRepository()->update($form->getData());
            } catch (SqlAlterException $e){
                return ViewFacade::render('film/edit', [
                    'editForm' => $form,
                    'film' => $film,
                    'error' => $e->getMessage()
                ]);
            }

            return RouterFacade::redirect(UrlFacade::create("/film/{$film->getId()}"));
        }

        return ViewFacade::render('film/edit', [
            'editForm' => $form,
            'film' => $film,
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
                return ViewFacade::render('film/add', [
                    'addForm' => $form,
                    'error' => $e->getMessage()
                ]);
            }

            return RouterFacade::redirect(UrlFacade::create("/film/{$dummy->getId()}"));
        }

        return ViewFacade::render('film/add', [
            'addForm' => $form
        ]);

    }

}