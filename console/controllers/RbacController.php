<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 8:25 PM
 */

namespace console\controllers;

use auth\rbac\DbManager;
use auth\rbac\rules\OwnerRule;
use Yii;
use yii\console\Controller;

/**
 * Class RbacController
 * @package console\controllers
 * @property  $auth \auth\rbac\DbManager
 */

class RbacController extends Controller
{
    /*
     * @var auth\rbac\DbManager
     */


    public function actionUp()
    {

        $auth = new DbManager();
        $ownerRule  = new OwnerRule();
        $auth->add($ownerRule);

        $admin = $auth->createRole('admin');
        $admin->description = 'Supper Admin';
        $auth->add($admin);

        $user = $auth->createRole('user');
        $user->description = 'User can read';
        $auth->add($user);

        // add "read" permission
        $read = $auth->createPermission('read');
        $read->description = 'Read site';
        $auth->add($read);
        $auth->addChild($user, $read);

        $auth->addChild($admin, $user);
        $auth->assign($admin, 1);

        // add "post" Feature
        $Roles = $auth->createFeature('Roles');
        $Roles->description = 'Access Roles';
        $auth->add($Roles);
        $auth->addChild($admin, $Roles);

        // add "manageRole" permission
        $manageRole = $auth->createPermission('manageRole');
        $manageRole->description = 'List Roles';
        $auth->add($manageRole);
        $auth->addChild($Roles, $manageRole);

        // add "createRole" permission
        $createRole = $auth->createPermission('createRole');
        $createRole->description = 'Create a Role';
        $auth->add($createRole);
        $auth->addChild($Roles, $createRole);

        // add "managePermission" permission
        $managePermission = $auth->createPermission('managePermission');
        $managePermission->description = 'List Permissions';
        $auth->add($managePermission);
        $auth->addChild($createRole, $managePermission);


        // add "viewRole" permission
        $viewRole = $auth->createPermission('viewRole');
        $viewRole->description = 'Create a Role';
        $auth->add($viewRole);
        $auth->addChild($Roles, $viewRole);
        $auth->addChild($viewRole, $manageRole);
        $auth->addChild($createRole, $viewRole);

        // add "updateRole" permission
        $updateRole = $auth->createPermission('updateRole');
        $updateRole->description = 'Create a Role';
        $auth->add($updateRole);
        $auth->addChild($Roles, $updateRole);
        $auth->addChild($updateRole, $viewRole);
        $auth->addChild($updateRole, $managePermission);
        $auth->addChild($admin, $updateRole);

        // add the "updateOwnRole" permission and associate the rule with it.
        $updateOwnRole = $auth->createPermission('updateOwnRole');
        $updateOwnRole->description = 'Update own Role';
        $updateOwnRole->ruleName = $ownerRule->name;
        $auth->add($updateOwnRole);
        $auth->addChild($Roles, $updateOwnRole);
        $auth->addChild($updateOwnRole, $updateRole);


        // add "deleteRole" permission
        $deleteRole = $auth->createPermission('deleteRole');
        $deleteRole->description = 'Delete a Role';
        $auth->add($deleteRole);
        $auth->addChild($Roles, $deleteRole);
        $auth->addChild($deleteRole, $viewRole);
        $auth->addChild($admin, $deleteRole);

        // add the "deleteOwnRole" permission and associate the rule with it.
        $deleteOwnRole = $auth->createPermission('deleteOwnRole');
        $deleteOwnRole->description = 'Delete own Role';
        $deleteOwnRole->ruleName = $ownerRule->name;
        $auth->add($deleteOwnRole);
        $auth->addChild($Roles, $deleteOwnRole);
        $auth->addChild($deleteOwnRole, $deleteRole);


        // add "assignRole" permission
        $assignRole = $auth->createPermission('assignRole');
        $assignRole->description = 'Assign role to user';
        $auth->add($assignRole);
        $auth->addChild($Roles, $assignRole);
        $auth->addChild($admin, $assignRole);

        // add "assignOwnRole" permission
        $assignOwnRole = $auth->createPermission('assignOwnRole');
        $assignOwnRole->description = 'Assign own role to user';
        $assignOwnRole->ruleName = $ownerRule->name;
        $auth->add($assignOwnRole);
        $auth->addChild($Roles, $assignOwnRole);
        $auth->addChild($assignOwnRole, $assignRole);


        // add "unassignRole" permission
        $unassignRole = $auth->createPermission('unassignRole');
        $unassignRole->description = 'Unassign role to user';
        $auth->add($unassignRole);
        $auth->addChild($Roles, $unassignRole);
        $auth->addChild($admin, $unassignRole);

        // add "unassignOwnRole" permission
        $unassignOwnRole = $auth->createPermission('unassignOwnRole');
        $unassignOwnRole->description = 'Assign own role to user';
        $unassignOwnRole->ruleName = $ownerRule->name;
        $auth->add($unassignOwnRole);
        $auth->addChild($Roles, $unassignOwnRole);
        $auth->addChild($unassignOwnRole, $unassignRole);

    }


    public function actionDown()
    {
        $auth = new DbManager();
        $auth->removeAll();
    }
}