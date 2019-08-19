<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

class m190400_100100_init extends \yihai\core\db\Migration
{

    protected $tableSettings = '{{%sys_settings}}';
    protected $tableReports = '{{%sys_reports}}';

    public function up()
    {
        $this->createTable($this->tableSettings, [
            'id' => $this->primaryKey(),
            'key' => $this->string(50)->notNull(),
            'module' => $this->string(50),
            'value' => $this->binary(),
            'created_by' => $this->columnCreatedBy(),
            'created_at' => $this->columnCreatedAt(),
            'updated_by' => $this->columnUpdatedBy(),
            'updated_at' => $this->columnUpdatedAt()
        ], $this->getTableOptions());
        $this->createIndex('key-module-idx', $this->tableSettings, ['key','module'], true);

        // reports
        $this->createTable($this->tableReports, [
            'id' => $this->primaryKey(),
            'key' => $this->string(50)->notNull(),
            'module' => $this->string(50),
            'class' => $this->text()->notNull(),
            'desc' => $this->string()->notNull(),
            'template' => $this->binary(),
            'is_sys' => $this->tinyInteger(1)->defaultValue(0)->notNull(),
            'set_use_watermark' => $this->tinyInteger(1)->defaultValue(0)->notNull(),
            'set_use_watermark_image_system' => $this->tinyInteger(1)->defaultValue(1)->notNull(),
            'set_watermark_image' => $this->integer(11)->defaultValue(0),
            'set_header_use_system' => $this->tinyInteger(1)->defaultValue(1)->notNull(),
            'set_page_format' => $this->string('20')->defaultValue('A4')->notNull(),
            'set_page_orientation' => $this->string('1')->defaultValue('P')->notNull(),
            'created_by' => $this->columnCreatedBy(),
            'created_at' => $this->columnCreatedAt(),
            'updated_by' => $this->columnUpdatedBy(),
            'updated_at' => $this->columnUpdatedAt()
        ], $this->getTableOptions());
        $this->createIndex('key-idx', $this->tableReports, ['key'], true);
    }
    public function down()
    {
        $this->dropTable($this->tableReports);
        $this->dropTable($this->tableSettings);
    }

}