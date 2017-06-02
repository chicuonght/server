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


    public function actionInit()
    {

        $auth = new DbManager();
        $ownerRule  = new OwnerRule();
        $auth->add($ownerRule);

        // add "post" Feature
        $post = $auth->createFeature('Post');
        $post->description = 'Access post';
        $auth->add($post);

        // add "managePost" permission
        $managePost = $auth->createPermission('managePost');
        $managePost->description = 'List posts';
        $auth->add($managePost);
        $auth->addChild($post, $managePost);

        // add "createPost" permission
        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);
        $auth->addChild($post, $createPost);

        // add "viewPost" permission
        $viewPost = $auth->createPermission('viewPost');
        $viewPost->description = 'View a post';
        $auth->add($viewPost);
        $auth->addChild($post, $viewPost);

        // add "updatePost" permission
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update a post';
        $auth->add($updatePost);
        $auth->addChild($post, $updatePost);

        // add the "updateOwnPost" permission and associate the rule with it.
        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $ownerRule->name;
        $auth->add($updateOwnPost);
        // "updateOwnPost" will be used from "updatePost"
        $auth->addChild($updateOwnPost, $updatePost);
        $auth->addChild($post, $updateOwnPost);

        // add "deletePost" permission
        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = 'Delete a post';
        $auth->add($deletePost);
        $auth->addChild($post, $deletePost);

        // add the "deleteOwnPost" permission and associate the rule with it.
        $deleteOwnPost = $auth->createPermission('deleteOwnPost');
        $deleteOwnPost->description = 'Delete own post';
        $deleteOwnPost->ruleName = $ownerRule->name;
        $auth->add($deleteOwnPost);
        // "updateOwnPost" will be used from "updatePost"
        $auth->addChild($deleteOwnPost, $deletePost);
        $auth->addChild($post, $deleteOwnPost);

        // add "author" role and give this role the "createPost" permission
        $author = $auth->createRole('author');
        $author->description = 'Writer creates a post';
        $auth->add($author);
        //$auth->addChild($author, $createPost);
        // allow "author" to update their own posts
        $auth->addChild($author, $updateOwnPost);

        // add "admin" role and give this role the "updatePost" permission
        // as well as the permissions of the "author" role
        $admin = $auth->createRole('admin');
        $admin->description = 'Supper Admin';
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);

        $auth->addChild($author, $post);
        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        $auth->assign($author, 2);
        $auth->assign($admin, 1);
    }
}