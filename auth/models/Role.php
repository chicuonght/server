<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-30
 * Time: 10:24 PM
 */

namespace auth\models;


use auth\rbac\DbManager;
use tuyakhov\jsonapi\ResourceTrait;
use tuyakhov\jsonapi\ResourceInterface;
use yii\base\Model;


/**
 * Class Role
 * @package auth\models
 *
 * @property $roleName string
 */
class Role extends Model implements ResourceInterface
{
    use ResourceTrait;

    public $roles = [];

    private $role;

    public $roleName;

    public $userId;

    const SCENARIO_DELETE = 'delete';
    const SCENARIO_ASSIGN = 'assign';
    const SCENARIO_UNASSIGN = 'unassign';

   public function getId()
   {
       return '';
   }

    /**
     * Get all roles
     * @return $this
     */
    public function getRoles()
    {
        $this->roles = \Yii::$app->authManager->getRoles();
        return $this;

    }

    public function getResourceAttributes(array $fields = [])
    {
        return $this->roles;
    }

    public function rules(){
        return [
            ['roleName', 'required'],
            ['roleName',  'string', 'max' => 64],
            ['roleName', 'validateExistRole'],
            ['userId', 'validateAssignUser', 'on' => self::SCENARIO_ASSIGN],
            ['roleName', 'validateDeleteRole', 'on' => self::SCENARIO_DELETE],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_DELETE] = ['roleName'];
        $scenarios[self::SCENARIO_ASSIGN] = ['userId', 'roleName'];
        return $scenarios;
    }

    /**
     * Check role before assigning role to user
     * @param $attribute
     * @param $params
     * @param $validator
     * @return bool
     */
    public function validateAssignUser($attribute, $params, $validator)
    {
        if(\Yii::$app->authManager->getAssignment($this->roleName, $this->userId)){
            $msg = sprintf("User has been assigned already!");
            $this->addError($attribute, $msg);
            return false;
        }
    }

    /**
     * Check role existed
     * @param $attribute
     * @param $params
     * @param $validator
     * @return bool
     */
    public function validateExistRole($attribute, $params, $validator)
    {
       //Check role existed
        $this->role = \Yii::$app->authManager->checkRole($this->roleName);
        if (is_null($this->role)) {
            $msg = sprintf("Role name '%s' not existed!", $this->roleName);
            $this->addError($attribute, $msg);
            return false;
        }

    }

    /**
     * Validate role before delete
     * @param $attribute
     * @param $params
     * @param $validator
     * @return bool
     */
    public function validateDeleteRole($attribute, $params, $validator)
    {
        //Check admin role cannot be updated
        if(DbManager::ROLE_ADMIN == strtolower($this->roleName)){
            $this->addError($attribute, 'Role admin cannot be deleted!');
            return false;
        }

    }

    /**
     * Get current role
     * @return \auth\rbac\Role
     */
    public function getRole(){
        return $this->role;
    }


    /**
     * Delete a role
     * @return mixed
     */
    public function deleteRole(){
       return \Yii::$app->authManager->deleteRole($this->role);
    }


    /**
     * Assign a role to user
     * @param int $userId
     * @return \yii\rbac\Assignment
     */
    public function assignUser(int $userId){
        return \Yii::$app->authManager->assign($this->role, $userId);
    }


    /**
     * Remove role from user
     * @param int $userId
     * @return bool
     */
    public function unassignUser(int $userId){
        return \Yii::$app->authManager->revoke($this->role, $userId);
    }

}