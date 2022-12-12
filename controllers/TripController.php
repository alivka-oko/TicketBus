<?php

namespace app\controllers;

use app\models\User;
use app\models\LoginForm;
use Yii;
use app\controllers\FunctionController;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;

use app\models\Trip;
use yii\rest\ActiveController;
class TripController extends FunctionController
{
    public $modelClass = 'app\models\Trip';

    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['add', 'del', 'red', 'alltrip'], //Перечислите для контроллера методы, требующие аутентификации

        ];
        return $behaviors;
    }

    /*Просмотр рейсов*/
    public function actionTrip()
    {
        $trip = Trip::find()
            ->where(['>', 'seats', 0])
            ->orderBy('id_trip')
            ->all();
        return $this->send(200, ['Trips' => $trip]);
    }

    public function actionAlltrip()
    {
        if (!$this->is_admin())
            return $this->send(401, ['content' => ['code' => 401, 'message' => 'Вы не являетесь администратором']]);
        $trip = Trip::find()
            ->IndexBy('id_trip')
            ->all();
        return $this->send(200, ['Trips' => $trip]);
    }

    /*Добавление рейса*/
    public function actionAdd()
    {
        if (!$this->is_admin())
            return $this->send(401, ['content' => ['code' => 401, 'message' => 'Вы не являетесь администратором']]);
        $request = Yii::$app->request->post(); //получение данных из post запроса
        $trip = new Trip($request); // Создание модели на основе присланных данных
        if (!$trip->validate()) return $this->validation($trip); //Валидация модели
        $trip->save();//Сохранение модели в БД
        return $this->send(200, ['content' => ['code' => 200, 'message' => 'Рейс добавлен']]);
    }

    /*Удалить рейсы*/
    public function actionDel($id_trip)
    {
        $trip = Trip::findOne($id_trip);
        if (!$trip) return $this->send(404, ['content' => ['code' => 404, 'message' => 'Рейс не найден']]);

        if (!$this->is_admin())
            return $this->send(401, ['content' => ['code' => 401, 'message' => 'Вы не являетесь администратором']]);
        $trip = Trip::findOne($id_trip);
        $trip->delete();
        return $this->send(200, ['content' => ['Status' => 'ok']]);
    }

    public function actionRed($id_trip)
    {
        if (!$this->is_admin())
            return $this->send(403, ['content' => ['code' => 403, 'message' => 'Вы не являетесь администратором']]);

        $request = Yii::$app->request->getBodyParams();
        $trip = Trip::findOne($id_trip);
        //die($trip-$id_trip);
        if (!$trip) return $this->send(404, ['content' => ['code' => 404, 'message' => 'Рейс не найден']]);
        // return $this->send(200, $flight);
        if (isset($request['city_from'])) $trip->city_from = $request['city_from'];
        if (isset($request['city_to'])) $trip->city_to = $request['city_to'];
        if (isset($request['date_trip'])) $trip->date_trip = $request['date_trip'];
        if (isset($request['timestart'])) $trip->timestart = $request['timestart'];
        if (isset($request['timefinish'])) $trip->timefinish = $request['timefinish'];
        if (isset($request['bus_name'])) $trip->bus_name = $request['bus_name'];
        if (isset($request['ridecode'])) $trip->ridecode = $request['ridecode'];
        if (isset($request['seats'])) $trip->seats = $request['seats'];

        if (!$trip->validate()) return $this->validation($trip);
        $trip->save();
        return $this->send(200, ['content' => ['code' => 200, 'message' => 'Данные обновлены']]);
    }

    public function actionFind()//поиск билета по городам
    {
        $request = Yii::$app->request->post();//получение данных из post запроса

        $trip = Trip::find()
            ->where(['city_from' => $request, 'city_to' => $request])
            ->all();

        if (!$trip) return $this->send(404, ['content' => ['code' => 404, 'message' => 'Рейсы не найдены']]);
        return $this->send(200, ['Trips' => $trip]);
    }
}