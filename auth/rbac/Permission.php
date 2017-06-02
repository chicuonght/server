<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 11:22 PM
 */

namespace auth\rbac;


class Permission extends  \yii\rbac\Permission 
{
    use CheckedTrait;
}