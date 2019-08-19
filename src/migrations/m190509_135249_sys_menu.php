<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\db\Migration;


/**
 * Class m190509_135249_sys_menu
 */
class m190509_135249_sys_menu extends Migration
{
    public $tableName = '{{%sys_menu}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'route' => $this->string(100)->unique()->notNull(),
//            'module' => $this->string(32)->unique()->notNull(),
//            'controller' => $this->string(32)->unique()->notNull(),
//            'action' => $this->string(32)->unique()->notNull(),
            'is_menu' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'is_group' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'parent' => $this->integer(),
            'backend' => $this->tinyInteger(1)->notNull(),
            'public' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'pos' => $this->integer(5),
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
