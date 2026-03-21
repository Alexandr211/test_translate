<?php

namespace frontend\modules\api\controllers;

use common\models\Translator;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;

class AvailabilityController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET', 'HEAD'],
                'create' => ['POST'],
                'update' => ['PUT', 'PATCH', 'POST'],
            ],
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (in_array($action->id, ['create', 'update'], true)) {
            $this->enableCsrfValidation = false;
        }

        return true;
    }

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

    /**
     * Создать запись переводчика.
     *
     * POST /api/translator
     * Тело (JSON или form): name, employment_type, language_pair, is_available (опционально)
     */
    public function actionCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new Translator();
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->is_available === null || $model->is_available === '') {
            $model->is_available = true;
        }

        if ($model->save()) {
            return [
                'success' => true,
                'id' => $model->id,
                'translator' => $model->toArray(),
            ];
        }

        Yii::$app->response->statusCode = 422;

        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    /**
     * Обновить существующую запись переводчика.
     *
     * PUT|PATCH|POST /api/translator/{id}
     * Тело (JSON или form): поля модели (переданные будут обновлены)
     */
    public function actionUpdate(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = Translator::findOne($id);
        if ($model === null) {
            Yii::$app->response->statusCode = 404;

            return [
                'success' => false,
                'message' => 'Переводчик не найден',
            ];
        }

        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->save()) {
            return [
                'success' => true,
                'translator' => $model->toArray(),
            ];
        }

        Yii::$app->response->statusCode = 422;

        return [
            'success' => false,
            'errors' => $model->errors,
        ];
    }

    private function resolveTypeFromCurrentDay(): string
    {
        $dayOfWeek = (int) date('N');
        return $dayOfWeek >= 6 ? Translator::TYPE_WEEKEND : Translator::TYPE_WEEKDAY;
    }
}
