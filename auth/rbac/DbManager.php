<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 8:43 PM
 */

namespace auth\rbac;
use Prophecy\Util\StringUtil;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;


/**
 * Class DbManager
 * @package auth\rbac
 */
class DbManager extends \yii\rbac\DbManager
{


    public $userTable = '{{%user}}';

    /**
     * Create Feature
     * @param $name
     * @return Feature
     */
    public function createFeature($name)
    {
        $name= Inflector::variablize($name);
        $feature = new Feature();
        $feature->name = $name;
        return $feature;
    }


    /**
     * @inheritdoc
     */
    public function createPermission($name)
    {
        $name= Inflector::variablize($name);
        $permission = new Permission();
        $permission->name = $name;
        return $permission;
    }


    /**
     * @inheritdoc
     */
    public function createRole($name)
    {
        $name= Inflector::variablize($name);
        $role = new Role();
        $role->name = $name;
        return $role;
    }

    /**
     * Get Feature by Name
     * @param $featureName
     * @return null | Feature
     */
    public function getFeature($featureName)
    {
        $item = (new Query)->from($this->itemTable)
            ->where([
                'name' => $featureName,
                'type' => Item::TYPE_FEATURE
                ])
            ->one($this->db);
        if (is_null($item)) {
            throw new InvalidParamException("Feature \"$featureName\" not found.");
        }
        $feature =  $this->populateItemName($item);
        return $feature;
    }



    /**
     * Get all Features
     * @return array Feature
     */
    public function getFeatures() : array
    {
        $features = (new Query)
            ->from($this->itemTable)
            ->where(['type' => Item::TYPE_FEATURE])
        ->all();

        if(is_null($features)){
            return [];
        }

        $items = [];
        foreach ($features as $row) {
            $items[$row['name']] = $this->populateItemName($row);
        }

        return $items;
    }


    /**
     * Parse Database row to Item Object
     * @param $row
     * @return \yii\rbac\Item Permission | Role | Feature
     */
    protected function populateItem($row) : \yii\rbac\Item
    {
        $class = Role::className();
        $type  = 'Role';
        switch ($row['type']){
            case Item::TYPE_PERMISSION;
                $class = Permission::className();
                $type = 'Permission';
                break;
            case Item::TYPE_FEATURE;
                $class = Feature::className();
                $type = 'Feature';
                break;
        }

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }


        return new $class([
            'name' => Inflector::titleize( $row['name']),
            'type' => $type,
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => date('Y-m-d', $row['created_at']),
            'updatedAt' => date('Y-m-d',$row['updated_at']),
        ]);
    }

    /**
     * Parse Database row to Item Object
     * @param $row
     * @return \yii\rbac\Item Permission | Role | Feature
     */
    protected function populateItemName($row) : \yii\rbac\Item
    {
        $class = Role::className();
        $type  = 'Role';
        switch ($row['type']){
            case Item::TYPE_PERMISSION;
                $class = Permission::className();
                $type = 'Permission';
                break;
            case Item::TYPE_FEATURE;
                $class = Feature::className();
                $type = 'Feature';
                break;
        }

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }


        return new $class([
            'name' => Inflector::titleize( $row['name']),
            'type' => $type,
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => date('Y-m-d', $row['created_at']),
            'updatedAt' => date('Y-m-d',$row['updated_at']),
        ]);
    }

    /**
     * @param $feature Feature or FeatureName
     * @return array Permission
     */
    public function getDirectPermissionsByFeature($feature):array
    {
        if( ($feature  instanceof Feature)){

        }else if (is_string($feature)){
            $featureName = $feature;
            $feature = $this->getFeature($featureName);
            if (is_null($feature)) {
                throw new InvalidParamException("Feature \"$featureName\" not found.");
            }
        }else{
            throw new InvalidParamException("Invalid param.");
        }

        $items = (new Query)->select('p.*')
        ->from([ 'c'  => $this->itemChildTable, 'p' => $this->itemTable])
            ->where('{{c}}.[[child]]={{p}}.[[name]]')
            ->andwhere(['{{c}}.[[parent]]' => $feature->name])
            ->andwhere(['{{p}}.[[type]]' => Item::TYPE_PERMISSION])
            ->all();
        if(is_null($items)){
            return [];
        }

        $permissions = [];
        foreach ($items as $key => $row){
            $permissions[$row['name']] = $this->populateItemName($row);
        }

        return $permissions;

    }


    /**
     * Get all Features with their permissions
     * @return array Feature
     */
    public function getFeaturesAndPermissions():array {
        $features = $this->getFeatures();
        $results = [];
        foreach ($features as $index => $feature){
            $feature->permissions = $this->getDirectPermissionsByFeature($feature);
            $results[$feature->name] = $feature ;
        }
        return $results;
    }


    /**
     * Get all checked Features with their checked permissions by one role
     * @param null $roleName
     * @return array Feature
     */
    public function getFeaturesAndPermissionsWithRole($roleName = null){
        $features = $this->getFeaturesAndPermissions();
        if(is_null($roleName)){
            return $features;
        }
        $permissionsByRole = $this->getDirectPermissionsByRole($roleName);
        $permissionsByRoleKeys = array_keys($permissionsByRole);

        foreach ($features as $f => $feature){
                foreach($feature->permissions as $p => $permission){
                    if(in_array($p, $permissionsByRoleKeys)){
                    $feature->checked = true;
                    $permission->checked = true;
                }
            }
        }
        return $features;
    }

    /**
     * @inheritdoc
     */
    public function getRole($name)
    {
        $item = $this->getItem($name);

        return $item instanceof Role  ? $item : null;
    }

    /**
     * Get Direct Permissions by one role
     * @param $roleName
     * @return array Permission
     */
    protected function getDirectPermissionsByRole($roleName):array
    {
        $role = $this->getRole($roleName);
        if (is_null($role)) {
            throw new InvalidParamException("Role \"$roleName\" not found.");
        }

        $items = (new Query)->select('p.*')
            ->from([ 'c'  => $this->itemChildTable, 'p' => $this->itemTable])
            ->where('{{c}}.[[child]]={{p}}.[[name]]')
            ->andwhere(['{{c}}.[[parent]]' => $role->name])
            ->andwhere(['{{p}}.[[type]]' => Item::TYPE_PERMISSION])
            ->all();

        if(is_null($items)){
            return [];
        }

        $permissions = [];
        foreach ($items as $key => $row){
            $permissions[$row['name']] = $this->populateItemName($row);
        }
        return $permissions;
    }



}