<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 8:46 PM
 */

namespace auth\rbac;
use tuyakhov\jsonapi\ResourceTrait;
use tuyakhov\jsonapi\ResourceInterface;

class Feature extends Item implements ResourceInterface
{
    use ResourceTrait;

    public $type = self::TYPE_FEATURE;

    public $permissions = [];

}