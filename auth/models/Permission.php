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

    protected $type = 'permission';

    public function getFeaturesAndPermissions(string $roleName){
        $this->permissions = \Yii::$app->authManager->getFeaturesAndPermissionsWithRole($roleName);
        $this->type = 'Feature';
        return $this;
    }


    public function getPermissionsByUser(int $userId){
        $this->permissions = \Yii::$app->authManager->getPermissionsByUser($userId);
        $this->permissions =  $this->permissions ? array_keys( $this->permissions) : null;
        return $this;
    }

    public function getPermissionsByRole(string $roleName){
        $features = (array)\Yii::$app->authManager->getFeaturesAndPermissionsWithRole($roleName);
        $features = array_filter($features, function($feature){
            $feature->permissions =  array_filter($feature->permissions, function($permission){
                return $permission->checked;
            });
            return $feature->checked;
        });

        $this->permissions = $features;
        $this->type = 'Feature';
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getId()
    {
        return 'name';
    }


    public function getResourceAttributes(array $fields = [])
    {
        return  $this->permissions;
    }
}