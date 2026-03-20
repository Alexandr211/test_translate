<?php

use yii\db\Migration;

class m260320_000002_seed_translator_table extends Migration
{
    public function safeUp(): void
    {
        $this->batchInsert('{{%translator}}', ['name', 'language_pair', 'employment_type', 'is_available'], [
            ['Anna Smirnova', 'EN-RU', 'weekday', 1],
            ['Ivan Petrov', 'DE-RU', 'weekday', 0],
            ['Olga Vasileva', 'FR-RU', 'weekday', 1],
            ['Maksim Volkov', 'EN-RU', 'weekend', 1],
            ['Svetlana Orlova', 'ES-RU', 'weekend', 1],
            ['Pavel Romanov', 'IT-RU', 'weekend', 0],
        ]);
    }

    public function safeDown(): void
    {
        $this->delete('{{%translator}}');
    }
}
