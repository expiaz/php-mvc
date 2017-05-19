<?php

namespace Core\Form\Field\Input;

use Core\Config;
use Core\Form\Field\Input;
use Core\Form\Field\AbstractInputField;
use FilesystemIterator;

class FileInput extends AbstractInputField {

    protected $multiple;

    protected $maxsize;
    protected $extensions;
    protected $path;

    public function __construct()
    {
        parent::__construct(AbstractInputField::FILE);

        $defaults = container(Config::class)['upload'];

        $this->multiple = false;
        $this->extensions = isset($defaults['extensions']) ? array_map(function($e){
            return '.' . ltrim($e, '.');
        },$defaults['extensions']) : [];

        $path = isset($defaults['path']) ? $defaults['path'] : 'upload';
        $path = rtrim($path, DS) . DS;
        $this->path = $path;
        $this->maxsize = isset($defaults['maxsize']) ? $defaults['maxsize'] : -1;
    }

    public function multiple(){
        $this->multiple = true;
        $this->name = "{$this->name}[]";
        return $this;
    }

    public function isMultiple(){
        return $this->multiple;
    }

    /**
     * @return mixed
     */
    public function getMaxsize()
    {
        return $this->maxsize;
    }

    /**
     * @param mixed $maxsize
     */
    public function maxsize($maxsize)
    {
        $this->maxsize = $maxsize;
        return $this;
    }


    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @param string $extension
     */
    public function extension($extension)
    {
        if(is_array($extension)){
            array_merge(array_map(function($e){
                return '.' . ltrim($e, '.');
            },$this->extensions), $extension);
        } else{
            $this->extensions[] = '.' . ltrim($extension);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function path($path)
    {
        $this->path = rtrim($path, DS) . DS;
        return $this;
    }


    public function build(): string
    {
        return $this->addBaseProps('');
    }

    public function validateEntry($entry): bool
    {
        //traitement du fichier
        if(! isset($_FILES[$this->name])){
            if($this->required){
                return false;
            }
            return true;
        }

        $fileData = $_FILES[$this->name];

        if($fileData['error'] !== UPLOAD_ERR_OK){
            return false;
        }

        $size = @filesize($fileData['tmp_name']);
        $extension = strrchr($fileData['name'], '.');

        if($this->maxsize > 0 && $size > $this->maxsize){
            return false;
        }

        if(count($this->extensions) && ! in_array($extension, $this->extensions)){
            return false;
        }

        $uploadDirectory = ASSET . $this->path;
        if(! is_dir($uploadDirectory) ){
            if(! mkdir($uploadDirectory)){
                throw new \Error("{$uploadDirectory} does not exists and can't be created, check the rights bro");
            }
        }

        if(! is_writable($uploadDirectory)){
            return false;
        }

        return true;
    }

    public function bindEntry($entry){
        if(! isset($_FILES[$this->name])){
            return;
        }



        $fileData = $_FILES[$this->name];
        $filename = basename($fileData['name']);

        /*
        $filename = strtr($filename,
            'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
            'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        $filename =  preg_replace('/([^.a-z0-9]+)/i', '-', $filename);
        */

        $uploadDirectory = ASSET . $this->path;

        $fi = new FilesystemIterator($uploadDirectory, FilesystemIterator::SKIP_DOTS);
        $numberOfFiles = iterator_count($fi);
        $extension = strrchr($filename, '.');
        $numberOfFiles += 1;
        $filename = $numberOfFiles . $extension;

        move_uploaded_file($fileData['tmp_name'], $uploadDirectory . $filename);

        $webPath = trim(str_replace(DS, '/',$this->path), '/');
        $this->value( $webPath . '/' . $filename);
    }

}