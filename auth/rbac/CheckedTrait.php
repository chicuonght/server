<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-28
 * Time: 11:23 PM
 */

namespace auth\rbac;

use yii\helpers\Inflector;
use yii\helpers\VarDumper;

/**
 * Trait CheckedTrait
 * @package auth\rbac
 *
 * @property $name
 *
 * @property $description
 *
 * @property $ruleName
 *
 * @property $data
 *
 * @property $createdAt
 *
 * @property $updatedAt
 */
trait CheckedTrait{
    public $checked = false;

    public $createdBy = null;
    public $updatedBy = null;

    public function toArray(){
        return [
            'name' => Inflector::titleize( $this->name),
            'description' => $this->description,
            'ruleName' => $this->ruleName,
            'data' => $this->data,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'created_by' => $this->createdBy,
            'updated_by' => $this->updatedBy,
        ];
    }
}