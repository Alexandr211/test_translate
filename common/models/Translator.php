<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $employment_type
 * @property bool $is_available
 * @property string $language_pair
 * @property string $updated_at
 */
class Translator extends ActiveRecord
{
    public const TYPE_WEEKDAY = 'weekday';
    public const TYPE_WEEKEND = 'weekend';

    public static function tableName(): string
    {
        return '{{%translator}}';
    }

    public function rules(): array
    {
        return [
            [['name', 'employment_type', 'language_pair'], 'required'],
            [['is_available'], 'boolean'],
            [['name'], 'string', 'max' => 100],
            [['language_pair'], 'string', 'max' => 50],
            [['employment_type'], 'in', 'range' => [self::TYPE_WEEKDAY, self::TYPE_WEEKEND]],
        ];
    }

    public static function findAvailableByType(string $type): ActiveQuery
    {
        return static::find()
            ->where(['employment_type' => $type, 'is_available' => 1])
            ->orderBy(['name' => SORT_ASC]);
    }
}
