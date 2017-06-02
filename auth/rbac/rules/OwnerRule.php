<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-29
 * Time: 10:58 AM
 */

namespace auth\rbac\rules;


use yii\base\InvalidParamException;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\rbac\Item;
use yii\rbac\Rule;

class OwnerRule extends  Rule
{
    public $name = 'Owner';
    /**
     * Executes the rule.
     *
     * @param string|int $userId the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($userId, $item, $params)
    {
        if(! isset($params['created_by'])){
            throw new InvalidParamException("Param 'data' missing created_by value!");
        }

        $result = $params['created_by'] == $userId;
        if(! $result){
            $parts = explode(' ', $item->name);
            $msg = sprintf("You have permission '%s' but you are not an owner of this '%s'", $item->name, end($parts));
            throw new \yii\web\UnauthorizedHttpException($msg);
        }
        return $result;
    }
}