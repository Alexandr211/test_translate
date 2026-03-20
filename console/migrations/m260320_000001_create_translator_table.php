<?php

use yii\db\Migration;

class m260320_000001_create_translator_table extends Migration
{
    public function safeUp(): void
    {
        $this->createTable('{{%translator}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'language_pair' => $this->string(50)->notNull(),
            'employment_type' => "ENUM('weekday', 'weekend') NOT NULL",
            'is_available' => $this->boolean()->notNull()->defaultValue(1),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%translator}}');
    }
}
