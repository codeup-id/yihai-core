<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\db\Migration;

class m190400_100200_uploaded_files extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%sys_uploaded_files}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'path' => $this->string(100),
            'filename' => $this->string(),
            'ext' => $this->string(20),
            'hash' => $this->string(100),
            'size' => $this->integer(50),
            'type' => $this->string(32),
            'group' => $this->string(32),
            'created_at' => $this->columnCreatedAt(),
            'created_by' => $this->columnCreatedBy(),
            'updated_at' => $this->columnUpdatedAt(),
            'updated_by' => $this->columnUpdatedBy(),
        ], $this->getTableOptions());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%sys_uploaded_files}}');
    }
}