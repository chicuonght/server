<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 8:46 PM
 */

namespace auth\rbac;


class Feature extends Item
{
    public $type = self::TYPE_FEATURE;

    public $permissions = [];

}