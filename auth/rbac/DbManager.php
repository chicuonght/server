<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 8:43 PM
 */

namespace auth\rbac;
use auth\models\User;
use Prophecy\Util\StringUtil;
use yii\base\InvalidParamException;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;

use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;


/**
 * Class DbManager
 * @package auth\rbac
 */
class DbManager extends \yii\rbac\DbManager
{

    CONST SUPPER_ADMIN_ID = 1;
    const ROLE_ADMIN = 'admin';

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

    protected function getCreatedBy(){
        $userId = self::SUPPER_ADMIN_ID;

        if(User::$_instance){
            //Admin user ID = 1
            $userId = User::$_instance->getId();
        }
        return $userId;
    }

    /**
     * @param Item
     * @return bool
     */
    protected function addItem($item)
    {
        $time = time();
        $userId = $this->getCreatedBy();

        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }

        if ($item->createdBy === null) {
            $item->createdBy = $userId;
        }

        if ($item->updatedBy === null) {
            $item->updatedBy = $userId;
        }

        $this->db->createCommand()
            ->insert($this->itemTable, [
                'name' => $item->name,
                'type' => $item->type,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
                'created_by' => $item->createdBy,
                'updated_by' => $item->updatedBy,
            ])->execute();

        $this->invalidateCache();

        return true;
    }


    /**
     * @inheritdoc
     */
    public function assign($role, $userId)
    {
       $assignment = new Assignment([
            'userId' => $userId,
            'roleName' => $role->name,
            'createdAt' => time(),
            'createdBy' => $userId
        ]);

        $this->db->createCommand()
            ->insert($this->assignmentTable, [
                'user_id' => $assignment->userId,
                'item_name' => $assignment->roleName,
                'created_at' => $assignment->createdAt,
                'created_by' => $this->getCreatedBy(),
            ])->execute();

        return $assignment;
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
     * @inheritdoc
     */
    public function getRoles()
    {
        $roles = $this->getItems(Item::TYPE_ROLE);
        foreach ($roles as $role){
            $role->permissions = $this->getPermissionsByRole($role->name);
            $assignments  = $this->getAssignmentsByRole($role->name);
            $role->userIds = array_column($assignments, 'userId');
        }
        return $roles;
    }


    /**
     * Parse Database row to Item Object
     * @param $row
     * @return \yii\rbac\Item Permission | Role | Feature
     */
    protected function populateItem($row) : \yii\rbac\Item
    {
        $class = null;
        $userId = 1;
        switch ($row['type']){
            case Item::TYPE_PERMISSION;
                $class = Permission::className();
                break;
            case Item::TYPE_FEATURE;
                $class = Feature::className();

                break;
            case Item::TYPE_ROLE;
                $class = Role::className();
                break;
        }

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }

        if(! isset($row['created_by'])){
            $row['created_by'] = $userId;
        }

        if(! isset($row['updated_by'])){
            $row['updated_by'] = $userId;
        }

        return new $class([
            'name' =>  $row['name'],
            'type' => $row['type'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => date('Y-m-d', $row['created_at']),
            'updatedAt' => date('Y-m-d',$row['updated_at']),
            'createdBy' => $row['created_by'],
            'updatedBy' => $row['updated_by'],
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
            'createdBy' => $row['created_by'],
            'updatedBy' => $row['updated_by'],
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

        $permissionsByRole = $this->getPermissionsByRole($roleName);
        //$permissionsByRole = $this->getDirectPermissionsByRole($roleName);
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
     * Check Role existed
     * @param $name
     * @return null|\yii\rbac\Item
     */
    public function checkRole($name){
        $name= Inflector::variablize($name);
        return $this->getItem($name);
    }


    /**
     * @inheritdoc
     */
    protected function getItem($name)
    {
        if (empty($name)) {
            return null;
        }

        if (!empty($this->items[$name])) {
            return $this->items[$name];
        }

        $row = (new Query)->from($this->itemTable)
            ->where(['name' => $name])
            ->one($this->db);

        if ($row === false) {
            return null;
        }

        return $this->populateItem($row);
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
     * Delete a role
     * @param Role
     * @return bool
     * @throws NotAcceptableHttpException
     * @throws NotFoundHttpException
     */
    public function deleteRole(Role $role){


        if($c = count($this->getAssignmentsByRole($role->name))){
            $msg = \Yii::t('app', sprintf('Role %s has been being assinged to %d {n,plural,=1{user} other{users}}.', $role->name, $c), ['n' => $c]);
            throw new NotAcceptableHttpException($msg);
        }

       return $this->remove($role);
    }


    public function getAssignmentsByRole(string $roleName)
    {
        if (empty($roleName)) {
            return [];
        }

        $query = (new Query)
            ->from($this->assignmentTable)
            ->where(['item_name' => $roleName]);

        $assignments = [];
        foreach ($query->all($this->db) as $row) {
            $assignments[$row['item_name']] = new Assignment([
                'userId' => $row['user_id'],
                'roleName' => $row['item_name'],
                'createdAt' => $row['created_at'],
            ]);
        }

        return $assignments;
    }


    /**
     * @inheritdoc
     */
    public function getPermission($name)
    {
        $item = $this->getItem($name);
        return $item instanceof Permission ? $item : null;

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
            throw new NotFoundHttpException("Role \"$roleName\" not found.");
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