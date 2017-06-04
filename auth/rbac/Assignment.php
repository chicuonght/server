<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-06-05
 * Time: 12:33 AM
 */

namespace auth\rbac;

use tuyakhov\jsonapi\ResourceTrait;
use tuyakhov\jsonapi\ResourceInterface;
use yii\helpers\Inflector;


class Assignment extends  \yii\rbac\Assignment implements ResourceInterface
{
    use ResourceTrait;
    public $createdBy;


    public function toArray(){
        return [
            'user_id' => $this->userId,
            'rule_name' => Inflector::titleize( $this->roleName),
            'created_at' => date('Y-m-d', $this->createdAt),
            'created_by' => $this->createdBy
        ];
    }



    public function getResourceAttributes(array $fields = [])
    {
        return $this->toArray();
    }
}