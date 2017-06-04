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

class Permission extends  \yii\rbac\Permission  implements ResourceInterface
{
    use ResourceTrait;

    use CheckedTrait;
}