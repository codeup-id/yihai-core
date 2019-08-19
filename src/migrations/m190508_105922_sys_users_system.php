<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


/**
 * Class m190508_105922_users
 */
class m190508_105922_sys_users_system extends \yihai\core\db\Migration
{
    public $tableName = '{{%sys_users_system}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'fullname' => $this->string(100),
            'created_by' => $this->columnCreatedBy(),
            'created_at' => $this->columnCreatedAt(),
            'updated_by' => $this->columnUpdatedBy(),
            'updated_at' => $this->columnUpdatedAt()
        ], $this->getTableOptions());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }

}
