<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */
use yihai\core\db\Migration;

class m190400_100121_auto_number extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%sys_auto_number}}', [
            'group' => $this->string(32)->notNull(),
            'number' => $this->integer(),
            'optimistic_lock' => $this->integer(),
            'update_time' => $this->integer(),
            'PRIMARY KEY ([[group]])'
        ], $this->getTableOptions());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%sys_auto_number}}');
    }
}