<?php
namespace app\controllers;
use app\models\Ticket;
use app\models\Trip;
use yii\rest\ActiveController;
use app\controllers\FunctionController;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class TicketController extends FunctionController

{
    public $modelClass = 'app\models\Ticket';

    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only' => ['order','show'] //Перечислите для контроллера методы, требующие аутентификации
        ];
        return $behaviors;
    }

    public function actionOrder()
    { //заказ билета
        //Проверка Ridecode и какое хочет место
        $user = Yii::$app->user->identity;
        $request = Yii::$app->request->post();
        if (isset($request['ridecode'])) $ridecode = $request['ridecode']; else return $this->send(404, ['content' => ['code' => 404, 'message' => 'Рейс не найден']]);
        $trip = Trip::findOne(['ridecode' => $ridecode]);
        if (!$trip) return $this->send(404, ['content' => ['code' => 404, 'message' => 'Рейс не найден']]);
        $trip->seats = -1;

        $ticket = new Ticket();// Создание модели на основе присланных данных
        $ticket->user_id = $user->id_user;
        $ticket->trip_id = $trip->id_trip;
        if (isset($request['seat'])) $ticket->seat = $request['seat']; else return $this->send(422, ['content' => ['code' => 422, 'message' => 'Введите место']]);


        if (!$ticket->validate()) return $this->validation($ticket); //Валидация модели
        $ticket->save();//Сохранение модели в БД
        return $this->send(200, ['content' => ['Ticket' => $trip,'message' => 'Вы купили билет!', 'Код бронирования' => $ticket->id_ticket]]);//Отправка сообщения пользователю

    }

    public function actionShow()
    {
        $user = Yii::$app->user->identity; //массив пользователя

        $ticket = Ticket::findAll([
            'user_id'=>$user->id_user
        ]);

        return $this->send(200, ['Билеты' => $ticket]);
    }
}