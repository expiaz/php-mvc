<?php

namespace Core\Database\Orm\Schema;

interface Schematizable{

    function schematize(): array;

}