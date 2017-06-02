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

class NewStatusRule extends Rule
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
        return isset($params['status']) ? $params['status'] == self::STATUS_NEW_CODE : false;

    }


}