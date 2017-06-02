<?php
/**
 * Created by PhpStorm.
 * User: thanh
 * Date: 2017-05-30
 * Time: 3:14 PM
 */

namespace auth\rbac\db;


class Migration extends \yii\db\Migration
{
    const TYPE_JSON = 'json';

    const TYPE_TINYINT= 'tinyint';
    /**
     * Creates a json column.
     * @param int $length column size or precision definition.
     * This parameter will be ignored if not supported by the DBMS.
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     * @since 2.0.11
     */
    public function json($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(self::TYPE_JSON, $length);
    }


    public function tinyInteger($length = null)
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder(self::TYPE_TINYINT, $length);

    }

}