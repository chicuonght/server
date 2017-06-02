<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-31
 * Time: 11:49 PM
 */

namespace auth\models;


use tuyakhov\jsonapi\ResourceInterface;
use tuyakhov\jsonapi\ResourceTrait;
use yii\base\Model;

class Permission extends Model implements ResourceInterface
{
    use ResourceTrait;

    public $permissions = [];

    public function getPermissions($roleName){
        $this->permissions = \Yii::$app->authManager->getFeaturesAndPermissionsWithRole($roleName);
        return $this;
    }

    public function getId()
    {
        return 'name';
    }


    public function getResourceAttributes(array $fields = [])
    {
        return $this->permissions;
    }
}