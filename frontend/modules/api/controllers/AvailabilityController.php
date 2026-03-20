<?php

namespace frontend\modules\api\controllers;

use common\models\Translator;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class AvailabilityController extends Controller
{
    public function actionIndex(?string $type = null): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $type = $type ?: $this->resolveTypeFromCurrentDay();
        $count = Translator::findAvailableByType($type)->count();

        return [
            'message' => ((int) $count > 0)
                ? 'Список переводчиков готов'
                : 'Нет свободных переводчиков',
            'type' => $type,
        ];
    }

    private function resolveTypeFromCurrentDay(): string
    {
        $dayOfWeek = (int) date('N');
        return $dayOfWeek >= 6 ? Translator::TYPE_WEEKEND : Translator::TYPE_WEEKDAY;
    }
}
