<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

namespace yihai\core\db;


class Migration extends \yii\db\Migration
{
    protected $tableOptions = [];
    public function init()
    {
        parent::init();
        $this->mySQL_UTF8_unicode_InnoDB();
    }

    /**
     * @return bool
     */
    protected function isMSSQL()
    {
        return $this->db->driverName === 'mssql' || $this->db->driverName === 'sqlsrv' || $this->db->driverName === 'dblib';
    }

    protected function isOracle()
    {
        return $this->db->driverName === 'oci';
    }
    protected function isMySQL(){
        return $this->db->driverName === 'mysql';
    }

    /**
     * definisikan pilihan untuk database mysql, bahwa memakai engine InnoDB dan set utf8_unicode_ci
     * // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
     */
    protected function mySQL_UTF8_unicode_InnoDB(){
        if($this->isMySQL()){
            $this->tableOptions[] = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

    protected function buildFkClause($delete = '', $update = '')
    {
        if ($this->isMSSQL()) {
            return '';
        }

        if ($this->isOracle()) {
            return ' ' . $delete;
        }

        return implode(' ', ['', $delete, $update]);
    }
    protected function getTableOptions(){
        return implode(" ", $this->tableOptions);
    }

    protected function columnCreatedBy(){
        return $this->string(64);
    }

    protected function columnUpdatedBy(){
        return $this->string(64);
    }

    protected function columnCreatedAt(){
        return $this->integer();
    }

    protected function columnUpdatedAt(){
        return $this->integer();
    }

    /**
     * @return \yii\db\ColumnSchemaBuilder
     */
    public function uid()
    {
        return $this->char(36)->notNull()->defaultValue('0');
    }



}