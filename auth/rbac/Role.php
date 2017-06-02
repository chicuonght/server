<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 11:22 PM
 */

namespace auth\rbac;

use tuyakhov\jsonapi\ResourceTrait;
use tuyakhov\jsonapi\ResourceInterface;

class Role extends \yii\rbac\Role implements ResourceInterface
{
    use CheckedTrait;
    use ResourceTrait;

    public $permissions = [];

    public function getId()
    {

        return (string) $this->name;
    }
}