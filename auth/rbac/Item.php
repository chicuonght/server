<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 8:45 PM
 */

namespace auth\rbac;


class Item extends \yii\rbac\Item
{
    use CheckedTrait;

    const TYPE_FEATURE = 3;



}