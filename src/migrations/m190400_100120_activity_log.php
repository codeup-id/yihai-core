<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


use yihai\core\db\Migration;

class m190400_100120_activity_log extends Migration
{
    public $tableName = '{{%sys_activity_logs}}';
    public function up()
    {
        $this->createTable($this->tableName, [
            'id'    => $this->primaryKey(),
            'action' => $this->string()->notNull(),
            'model' => $this->string(),
            'type' => $this->string(20)->notNull(),
            'user' => $this->string()->notNull(),
            'time' => $this->integer()->notNull(),
            'ip' => $this->string(45),
            'msg' => $this->binary(),

        ], $this->getTableOptions());
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}