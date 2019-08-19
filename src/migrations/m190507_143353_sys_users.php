<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\db\Migration;


/**
 * Class m190507_143353_sys_users
 */
class m190507_143353_sys_users extends Migration
{
    public $tableName = '{{%sys_users}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'username' => $this->string(64)->notNull(),
            'email' => $this->string(100)->notNull(),
            'password' => $this->string(64)->notNull(),
            'group' => $this->string(32)->notNull()->defaultValue('system'),
            'data' => $this->integer(11)->notNull(),
            'avatar' => $this->integer(),
            'status' => $this->tinyInteger(1)->defaultValue(1)->notNull(),
            'access_token' => $this->string(32),
            'auth_key' => $this->string(32),
            'reset_token' => $this->string(64),
            'last_time' => $this->integer(11),
            'created_by' => $this->columnCreatedBy(),
            'created_at' => $this->columnCreatedAt(),
            'updated_by' => $this->columnUpdatedBy(),
            'updated_at' => $this->columnUpdatedAt(),

            'FOREIGN KEY ([[avatar]]) REFERENCES  {{%sys_uploaded_files}} ([[id]])' .
            $this->buildFkClause('ON DELETE SET NULL', 'ON UPDATE CASCADE'),

        ], $this->getTableOptions());
        $this->createIndex('group-email', $this->tableName, ['group', 'email'], true);
        $this->createIndex('group-data', $this->tableName, ['group', 'data'], true);
        $this->createIndex('group-username', $this->tableName, ['group', 'username'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
