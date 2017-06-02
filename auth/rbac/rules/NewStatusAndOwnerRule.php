<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-31
 * Time: 5:45 PM
 */

namespace auth\rbac\rules;


use yii\rbac\Item;
use yii\rbac\Rule;

class NewStatusAndOwnerRule extends Rule
{

    const STATUS_NEW_CODE = 'NW';

    public $name = 'NewStatus';

    /**
     * Executes the rule.
     *
     * @param string|int $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($userId, $item, $params)
    {
        $status = isset($params['status']) ? $params['status'] == self::STATUS_NEW_CODE : false;
        $result = isset($params['created_by']) ? $params['created_by'] == $userId : false;

        if(! $result || ! $status){
            $parts = explode(' ', $item->name);
            $msg = sprintf("You have permission '%s' but you are not an owner of this '%s' and status is not New", $item->name, end($parts));
            throw new \yii\web\UnauthorizedHttpException($msg);
        }

        return true;
    }


}