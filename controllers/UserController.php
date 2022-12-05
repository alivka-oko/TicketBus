<?php
namespace app\controllers;
use app\models\User;
use app\models\LoginForm;
use yii\rest\ActiveController;
use Yii;
use app\controllers\FunctionController;

use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;

class UserController extends FunctionController
{
    public $modelClass = 'app\models\User';
    public function behaviors()
    {
        /*
         * Указание на аутентификации по токену
         */
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['cab'] //Перечислите для контроллера методы, требующие аутентификации
            //здесь метод actionAccount()
        ];
        return $behaviors;
    }



    public function actionCreate(){

        $request=Yii::$app->request->post();      //получение данных из post запроса
        $user=new User($request); // Создание модели на основе присланных данных
        if (!$user->validate()) return $this->validation($user); //Валидация модели
        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password); //хэширование пароля
        $user->save();//Сохранение модели в БД
        return $this->send(204, $user);//Отправка сообщения пользователю
    }
    public function actionAccount(){
        $user=Yii::$app->user->identity;
        return $this->send(200, $user);
    }

}
