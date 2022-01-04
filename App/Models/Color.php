<?php

namespace App\Models;

use App\ORM\Entity;

class Color extends Entity
{
    protected $tableName = 'colors';

    public $id;

    public $name;

    public $hexValue;
}
