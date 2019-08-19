<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\db\Migration;


/**
 * Class m190507_143357_sys_users_groups
 */
class m190507_143357_sys_users_groups extends Migration
{
    public $tableName = '{{%sys_users_groups}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'group' => $this->string(32)->notNull(),
            'data' => $this->integer(11)->notNull(),
//            'avatar' => $this->integer(),
            'status' => $this->tinyInteger(1)->defaultValue(1)->notNull(),
//            'access_token' => $this->string(32),
//            'auth_key' => $this->string(32),
//            'reset_token' => $this->string(64),
//            'last_time' => $this->integer(11),
            'created_by' => $this->columnCreatedBy(),
            'created_at' => $this->columnCreatedAt(),
            'updated_by' => $this->columnUpdatedBy(),
            'updated_at' => $this->columnUpdatedAt(),

            'FOREIGN KEY ([[user_id]]) REFERENCES  {{%sys_users}} ([[id]])' .
            $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
        ], $this->getTableOptions());
        $this->createIndex('group-user_id', $this->tableName, ['group', 'user_id'], true);
        $this->createIndex('group-data', $this->tableName, ['group', 'data'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
