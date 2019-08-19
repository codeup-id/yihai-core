<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */

use yihai\core\rbac\DbManager;
use yihai\core\rbac\PhpManager;
use yii\base\InvalidConfigException;

/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 *  @author  Upik Saleh <upik@codeup.id>
 */


class m190507_143370_rbac extends \yihai\core\db\Migration
{
    private $authManager;

    public function up(){
        $this->authManager = Yihai::$app->getAuthManager();
        if ($this->authManager instanceof DbManager) {
            $this->_db_up();
        }elseif($this->authManager instanceof PhpManager){

        }
    }
    public function down(){

        $this->authManager = Yihai::$app->getAuthManager();
        if ($this->authManager instanceof DbManager) {
            $this->_db_down();
        }elseif($this->authManager instanceof PhpManager){
            $this->authManager->removeAll();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _db_up()
    {
        $authManager = $this->authManager;
        $this->db = $authManager->db;
        $schema = $this->db->getSchema()->defaultSchema;

        $tableOptions = $this->getTableOptions();

        $this->createTable($authManager->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        $this->createTable($authManager->itemTable, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
            'FOREIGN KEY ([[rule_name]]) REFERENCES ' . $authManager->ruleTable . ' ([[name]])' .
            $this->buildFkClause('ON DELETE SET NULL', 'ON UPDATE CASCADE'),
        ], $tableOptions);
        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
            'FOREIGN KEY ([[parent]]) REFERENCES ' . $authManager->itemTable . ' ([[name]])' .
            $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            'FOREIGN KEY ([[child]]) REFERENCES ' . $authManager->itemTable . ' ([[name]])' .
            $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
        ], $tableOptions);

        $this->createTable($authManager->assignmentTable, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
            'FOREIGN KEY ([[item_name]]) REFERENCES ' . $authManager->itemTable . ' ([[name]])' .
            $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%sys_users}} ([[id]])' .
            $this->buildFkClause('ON DELETE CASCADE', 'ON UPDATE CASCADE'),
        ], $tableOptions);

        $this->createIndex('{{%idx-auth_assignment-user_id}}', $authManager->assignmentTable, 'user_id');

        if ($this->isMSSQL()) {
            $this->execute("CREATE TRIGGER {$schema}.trigger_auth_item_child
            ON {$schema}.{$authManager->itemTable}
            INSTEAD OF DELETE, UPDATE
            AS
            DECLARE @old_name VARCHAR (64) = (SELECT name FROM deleted)
            DECLARE @new_name VARCHAR (64) = (SELECT name FROM inserted)
            BEGIN
            IF COLUMNS_UPDATED() > 0
                BEGIN
                    IF @old_name <> @new_name
                    BEGIN
                        ALTER TABLE {$authManager->itemChildTable} NOCHECK CONSTRAINT FK__auth_item__child;
                        UPDATE {$authManager->itemChildTable} SET child = @new_name WHERE child = @old_name;
                    END
                UPDATE {$authManager->itemTable}
                SET name = (SELECT name FROM inserted),
                type = (SELECT type FROM inserted),
                description = (SELECT description FROM inserted),
                rule_name = (SELECT rule_name FROM inserted),
                data = (SELECT data FROM inserted),
                created_at = (SELECT created_at FROM inserted),
                updated_at = (SELECT updated_at FROM inserted)
                WHERE name IN (SELECT name FROM deleted)
                IF @old_name <> @new_name
                    BEGIN
                        ALTER TABLE {$authManager->itemChildTable} CHECK CONSTRAINT FK__auth_item__child;
                    END
                END
                ELSE
                    BEGIN
                        DELETE FROM {$schema}.{$authManager->itemChildTable} WHERE parent IN (SELECT name FROM deleted) OR child IN (SELECT name FROM deleted);
                        DELETE FROM {$schema}.{$authManager->itemTable} WHERE name IN (SELECT name FROM deleted);
                    END
            END;");
        }
        if(!$authManager->getRole('_user-system')){
            $role = $authManager->createRole('_user-system');
            $authManager->add($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _db_down()
    {
        $authManager = $this->getAuthManagerDB();
        $this->db = $authManager->db;

        if ($this->isMSSQL()) {
            $this->execute('DROP TRIGGER {$schema}.trigger_auth_item_child;');
        }

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
    }


}