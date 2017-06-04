<?php

namespace App\Controller;

use App\Model\ConventionModel;
use App\Repository\ConventionRepository;
use App\Repository\EtudiantRepository;
use Core\App;
use Core\Exception\NoDataFoundException;
use Core\Exception\SqlAlterException;
use Core\Facade\Contracts\FormFacade;
use Core\Facade\Contracts\ViewFacade;
use Core\Form\Field\Input\DateInput;
use Core\Form\Field\Input\NumberInput;
use Core\Form\Field\Input\RadioInput;
use Core\Form\Field\Input\SubmitInput;
use Core\Form\Field\Input\TextInput;
use Core\Form\Field\Select\OptionField;
use Core\Form\Field\SelectField;
use Core\Form\Form;
use Core\Http\Request;
use Core\Http\Response;
use Core\Mvc\Controller\Controller;

class ConventionController extends Controller
{

    public function index(Request $request, Response $response)
    {
        return ViewFacade::render('index', [
            'content' => 'Convention::index fails'
        ]);
    }

    public function ensureExists(callable $next, Request $request, Response $response){
        try{
            $convention = $this->getRepository()->getById($request->getParameter('id'));
            return $next($request, $response, $convention);
        } catch (NoDataFoundException $e){
            $response->withStatus(404);
            return ViewFacade::render('error/404', [
                'error' => 'Convention not found',
                'message' => "no convention for id {$request->getParameter('id')}"
            ]);
        }
    }

    public function all(Request $request, Response $response){
        return ViewFacade::render('projet/all', [
            'projets' => 'hi'
        ]);
    }

    public function single(Request $request, Response $response, ConventionModel $convention){
        return ViewFacade::render('projet/single', [
            'projet' => $convention
        ]);
    }

    public function createForm(ConventionModel $model){
        $template = FormFacade::create($model);

        $form = new Form();

        /*
         * Titre
         */
        $titleField = (new TextInput())
            ->name('titre')
            ->label('titre du projet')
            ->placeholder('les 3 mousquetaires')
            ->required()
            ->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>');

        if(! is_null($model->getTitre())){
            $titleField->value($model->getTitre());
        }

        $form->field($titleField);

        /*
         * Durée
         */
        $dureeField = (new NumberInput())
            ->name('duree')
            ->label('temps estimé (en jours)')
            ->max(365)
            ->min(1)
            ->placeholder(34)
            ->required()
            ->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>');

        if(! is_null($model->getDuree())){
            $dureeField->value($model->getDuree());
        }

        $form->field($dureeField);


        /*
         * DeadLine
         */
        $deadlineField = (new DateInput())
            ->name('date_fin')
            ->label('deadline')
            ->required()
            ->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>');

        if(! is_null($model->getDateFin())){
            $deadlineField->value($model->getDateFin());
        }

        $form->field($deadlineField);


        /*
         * PRIX/J
         */
        $prixField = (new NumberInput())
            ->name('prix_j')
            ->label('prix journalier')
            ->min(40)
            ->placeholder(40)
            ->required()
            ->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>');

        if(! is_null($model->getPrixJ())){
            $prixField->value($model->getPrixJ());
        }

        $form->field($prixField);


        /*
         * Entreprise
         */
        $entrepriseField = $template->getField('entreprise');
        $entrepriseField->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>');

        if(! is_null($model->getEntreprise())){
            $entrepriseField->value($model->getEntreprise());
        }

        $form->field($entrepriseField);


        /*
         * ETUDIANTS
         */
        try{
            $etudiants = App::make(EtudiantRepository::class)->getAll();
        } catch (NoDataFoundException $e){
            $etudiants = [];
        }

        $form->raw('<div class="col-md-12">Etudiants : </div>');

        /*
         * TEMPLATE
         */
        $f = new SelectField();
        $f->name('etudiants[]')
            ->inline()
            ->required()
            ->before('<div class="etudiant-register row"><h4 class="col-md-12">Etudiant #<span class="etudiant-number">1</span></h4><div class="form-group col-md-4">')
            ->after('</div>')
            ->class('form-control');

        foreach ($etudiants as $etudiant){
            $f->option(
                (new OptionField())
                    ->value($etudiant->getId())
                    ->content($etudiant->getFullName())
            );
        }

        $form->field($f);

        /*
         * REMUNERATION
         */
        $remunerationField = new NumberInput();
        $remunerationField->name('remunerations[]')
            ->placeholder('rémunération par jour')
            ->inline()
            ->required()
            ->before('<div class="form-group col-md-4">')
            ->after('</div>')
            ->class('form-control');

        $form->field($remunerationField);

        /*
         * CHEF
         */
        $isChef = new RadioInput();
        $isChef->name('chef')
            ->value(1)
            ->inline()
            ->required()
            ->before('<div class="form-check col-md-1"><label class="form-check-label">Chef</label>')
            ->after('</div></div>')
            ->class('form-check-input');

        $form->field($isChef);

        if(count($model->getEtudiants())){
            $etudiantModel = $this->getContainer()->get(EtudiantRepository::class)->getById($model->getEtudiants()[0]);
            $f->value($etudiantModel->getId());
            $etude = $etudiantModel->getEtudeFor($model->getId());
            $remunerationField->value($etude->remuneration);
            if($etude->grade){
                $isChef->checked();
            }

        }

        /*
         * OTHERS
         */
        $others = array_slice($model->getEtudiants(), 1);
        if(count($others)){
            $etuRepo = $this->getContainer()->get(EtudiantRepository::class);
            foreach ($others as $i => $etuId){

                $f = new SelectField();
                $f->name('etudiants[]')
                    ->inline()
                    ->required()
                    ->before('<div class="etudiant-register need-overload row"><h4 class="col-md-12">Etudiant #<span class="etudiant-number">' . ($i+2) . '</span></h4><div class="form-group col-md-4">')
                    ->after('</div>')
                    ->class('form-control');

                foreach ($etudiants as $etudiant){
                    $f->option(
                        (new OptionField())
                            ->value($etudiant->getId())
                            ->content($etudiant->getFullName())
                    );
                }

                $form->field($f);

                /*
                 * REMUNERATION
                 */
                $remunerationField = new NumberInput();
                $remunerationField->name('remunerations[]')
                    ->placeholder('rémunération par jour')
                    ->inline()
                    ->required()
                    ->before('<div class="form-group col-md-4">')
                    ->after('</div>')
                    ->class('form-control');

                $form->field($remunerationField);

                /*
                 * CHEF
                 */
                $isChef = new RadioInput();
                $isChef->name('chef')
                    ->value($i + 2)
                    ->inline()
                    ->required()
                    ->before('<div class="form-check col-md-1"><label class="form-check-label">Chef</label>')
                    ->after('</div></div>')
                    ->class('form-check-input');

                $form->field($isChef);

                $f->value($etuId);
                $etudiantModel = $etuRepo->getById($etuId);
                $etude = $etudiantModel->getEtudeFor($model->getId());
                $remunerationField->value($etude->remuneration);
                if($etude->grade){
                    $isChef->checked();
                }
            }

        }

        $form->raw('<div><a href="#" id="add-etu-button">Ajouter un étudiant</a></div>');

        $form->field(
            (new SubmitInput())
                ->name('sub')
                ->class('btn btn-primary')
                ->value('envoyer')
        );

        return $form;
    }

    public function add(Request $request, Response $response){
        /*$dummy = $this->getRepository()->getModel();
        $template = FormFacade::create($dummy);

        $form = new Form();

        $form->field(
            (new TextInput())
            ->name('titre')
            ->label('titre du projet')
            ->placeholder('les 3 mousquetaires')
            ->required()
            ->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>')
        );

        $form->field(
            (new NumberInput())
            ->name('duree')
            ->label('temps estimé (en jours)')
            ->max(365)
            ->min(1)
            ->placeholder(34)
            ->required()
            ->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>')
        );

        $form->field(
            (new DateInput())
                ->name('date_fin')
                ->label('deadline')
                ->required()
                ->class('form-control')
                ->before('<div class="form-group">')
                ->after('</div>')
        );

        $form->field(
            (new NumberInput())
                ->name('prix_j')
                ->label('prix journalier')
                ->min(40)
                ->placeholder(40)
                ->required()
                ->class('form-control')
                ->before('<div class="form-group">')
                ->after('</div>')
        );

        $entrepriseField = $template->getField('entreprise');
        $entrepriseField->class('form-control')
            ->before('<div class="form-group">')
            ->after('</div>');
        $form->field($entrepriseField);

        try{
            $etudiants = App::make(EtudiantRepository::class)->getAll();
        } catch (NoDataFoundException $e){
            $etudiants = [];
        }

        //$form->raw('Etudiants : ');

        $f = new SelectField();
        $f->name('etudiants[]')
            ->inline()
            ->required()
            ->before('<div class="etudiant-register row"><h4 class="col-md-12">Etudiant #<span class="etudiant-number">1</span></h4><div class="form-group col-md-4">')
            ->after('</div>')
            ->class('form-control');

        foreach ($etudiants as $etudiant){
            $f->option(
                (new OptionField())
                ->value($etudiant->getId())
                ->content($etudiant->getFullName())
            );
        }

        $form->field($f);

        $remunerationField = new NumberInput();
        $remunerationField->name('remunerations[]')
            ->placeholder('rémunération par jour')
            ->inline()
            ->required()
            ->before('<div class="form-group col-md-4">')
            ->after('</div>')
            ->class('form-control');

        $form->field($remunerationField);

        $isChef = new RadioInput();
        $isChef->name('chef')
            ->value(1)
            ->inline()
            ->required()
            ->before('<div class="form-check col-md-1"><label class="form-check-label">Chef</label>')
            ->after('</div></div>')
            ->class('form-check-input');

        $form->field($isChef);

        $form->raw('<div><a href="#" id="add-etu-button">Ajouter un étudiant</a></div>');

        $form->field(
            (new SubmitInput())
                ->name('sub')
                ->class('btn btn-primary')
                ->value('envoyer')
        );*/

        $form = $this->createForm($this->getRepository()->getModel());

        $form->handleRequest($request);

        if($form->isSubmitted()){
            try{
                $this->getRepository()->insert($form->getData());
            } catch (SqlAlterException $e){
                return ViewFacade::render('projet/add', [
                    'addForm' => $form,
                    'error' => $e->getMessage()
                ]);
            }

        }

        return ViewFacade::render('projet/add', [
            'addForm' => $form
        ]);
    }

    public function edit(Request $request, Response $response, ConventionModel $convention){
        $form = $this->createForm($convention);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            var_dump($form->getData());
        }

        return ViewFacade::render('projet/edit', [
            'editForm' => $form
        ]);
    }

}