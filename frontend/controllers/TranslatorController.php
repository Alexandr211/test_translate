<?php

namespace frontend\controllers;

use common\models\Translator;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class TranslatorController extends Controller
{
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionListJson(?string $type = null): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $type = $type ?: Translator::TYPE_WEEKDAY;
        if (!in_array($type, [Translator::TYPE_WEEKDAY, Translator::TYPE_WEEKEND], true)) {
            return [
                'ok' => false,
                'message' => 'Unsupported translator type',
                'items' => [],
            ];
        }

        $items = Translator::findAvailableByType($type)
            ->select(['id', 'name', 'language_pair', 'employment_type'])
            ->asArray()
            ->all();

        return [
            'ok' => true,
            'items' => $items,
        ];
    }
}
