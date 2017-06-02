<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-30
 * Time: 10:24 PM
 */

namespace auth\models;

use tuyakhov\jsonapi\ResourceIdentifierInterface;
use tuyakhov\jsonapi\ResourceTrait;
use tuyakhov\jsonapi\ResourceInterface;
use yii\db\ActiveRecord;


class Role implements ResourceInterface
{
    use ResourceTrait;

    public $roles = [];

   public function getId()
   {
       return 'name';
   }

    /**
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

}