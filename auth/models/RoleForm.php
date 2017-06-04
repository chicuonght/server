<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-06-03
 * Time: 6:30 PM
 */

namespace auth\models;


use auth\rbac\DbManager;
use function GuzzleHttp\Promise\queue;
use yii\base\Model;

use tuyakhov\jsonapi\ResourceTrait;
use tuyakhov\jsonapi\ResourceInterface;
use yii\helpers\VarDumper;

/**
 * Class CreateRole
 * @package auth\models
 */
class RoleForm extends Model implements ResourceInterface
{
    use ResourceTrait;

    /**
     * @var array \auth\rbac\Permission
     */
    public $permissions;

    public $roleName;

    public $description;

    /**
     * @var \auth\rbac\Role
     */
    private $role;


    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';


    public function rules()
    {
        return [
            [['permissions', 'roleName'], 'required'],
            ['roleName', 'string', 'max' => 64],
            ['description', 'string', 'max' => 250],
            ['permissions', 'validatePermissions'],
            ['roleName', 'validateUpdateRole', 'on' => self::SCENARIO_UPDATE],
            ['roleName', 'validateCreateRole', 'on' => self::SCENARIO_CREATE],
        ];
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['permissions', 'roleName', 'description'];
        $scenarios[self::SCENARIO_UPDATE] = ['permissions', 'roleName', 'description'];
        return $scenarios;
    }

    public function validateCreateRole($attribute, $params, $validator)
    {

        $role = \Yii::$app->authManager->checkRole($this->roleName);
        if ($role) {
            $msg = sprintf("Role name '%s' existed!", $this->roleName);
            $this->addError($attribute, $msg);
            return false;
        }

    }


    public function validateUpdateRole($attribute, $params, $validator)
    {
        //Check admin role cannot be updated
        if(DbManager::ROLE_ADMIN == strtolower($this->roleName)){
            $this->addError($attribute, 'Role admin cannot be changed!');
            return false;
        }

        //Check role existed
        $this->role = \Yii::$app->authManager->checkRole($this->roleName);
        if (is_null($this->role)) {
            $msg = sprintf("Role name '%s' not existed!", $this->roleName);
            $this->addError($attribute, $msg);
            return false;
        }

    }


    public function validatePermissions($attribute, $params, $validator)
    {
        if (!is_array($this->permissions)) {
            $this->addError($attribute, 'Permissions must be in array');
            return false;
        }

        $permissions = [];
        foreach ($this->permissions as $permission) {
            if (!is_string($permission)) {
                $this->addError($attribute, 'Permission must be string');
                return false;
            }

            $item = \Yii::$app->authManager->getPermission($permission);
            if (is_null($item)) {
                $msg = sprintf("Permission '%s' not found!", $permission);
                $this->addError($attribute, $msg);
                return false;
            }
            $permissions[] = $item;
        }

        $this->permissions = $permissions;
    }

    /**
     * Create Role and Permssions
     */
    public function create()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        $this->role = \Yii::$app->authManager->createRole($this->roleName);
        $this->role->description = $this->description;
        \Yii::$app->authManager->add($this->role);

        foreach ($this->permissions as $permission) {
            \Yii::$app->authManager->addChild($this->role, $permission);

        }

        $transaction->commit();

        return $this;
    }


    /**
     * Create Role and Permssions
     */
    public function update()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        $this->role->description = $this->description;

        \Yii::$app->authManager->removeChildren($this->role);

        foreach ($this->permissions as $permission) {
           \Yii::$app->authManager->addChild($this->role, $permission);
        }

        $transaction->commit();

        return $this;
    }


    public function getId()
    {
        return $this->role->name;
    }

    public function getType()
    {
        return 'Role';
    }

    public function getResourceAttributes(array $fields = [])
    {
        $data = $this->role->toArray();
        $data['permissions'] = $this->permissions;


        return $data;
    }


    public function getRole(){
        return $this->role;
    }


}