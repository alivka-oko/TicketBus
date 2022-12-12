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
            'only'=>['account','users','del','red','buy'] //Перечислите для контроллера методы, требующие аутентификации

        ];
        return $behaviors;
    }

    /*Регистрация пользователя*/
    public function actionCreate(){
        $request=Yii::$app->request->post(); //получение данных из post запроса
        $user=new User($request); // Создание модели на основе присланных данных
        if (!$user->validate()) return $this->validation($user); //Валидация модели
        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password); //хэширование пароля
        $user->save();//Сохранение модели в БД
        return $this->send(201, ['content'=>['code'=>201, 'message'=>'Вы зарегистрировались']]);//Отправка сообщения пользователю
    }

    /*Авторизация*/

    public function actionLogin(){
        $request=Yii::$app->request->post();//Здесь не объект, а ассоциативный массив
        $loginForm=new LoginForm($request);
        if (!$loginForm->validate()) return $this->validation($loginForm);
        $user=User::find()->where(['login'=>$request['login']])->one();
        if (isset($user) && Yii::$app->getSecurity()->validatePassword($request['password'], $user->password)){
            $user->token=Yii::$app->getSecurity()->generateRandomString();
            $user->save(false);
            return $this->send(200, ['content'=>['token'=>$user->token]]);
        }
        return $this->send(401, ['content'=>['code'=>401, 'message'=>'Неверный email или пароль']]);
    }

    /*Личный кабинет*/
    public function actionAccount(){
        $user=Yii::$app->user->identity;
        return $this->send(200, ['content'=> ['user'=>$user, 'message'=>'Ваш личный кабинет']]);
    }

    public function actionUsers(){
        if (!$this->is_admin())
            return $this->send(403, ['content'=> ['code'=>403, 'message'=>'Вы не являетесь администратором']]);
        /*Просмотр рейсов*/
        $user=User::find()->indexBy('id_user')->all();
        return $this->send(200, ['Users'=>$user]);

    }

    /*Редакторовать профиль*/
    public function actionRed(){/*
        $request=Yii::$app->request->getBodyParams();
        $user=Yii::$app->user->identity;

        if (isset($request['login'])) $user->login = $request['login'];
        if (isset($request['password'])) $user->password = $request['password']  = Yii::$app->getSecurity()->generatePasswordHash($user->password);
        if (isset($request['first_name'])) $user->first_name = $request['first_name'];
        if (isset($request['last_name'])) $user->last_name = $request['last_name'];
        if (isset($request['phone'])) $user->phone = $request['phone'];
        if (isset($request['document_number'])) $user->document_number = $request['document_number'];

        if (!$user->validate()) return $this->validation($user);
        $user->save();
        return $this->send(200, ['content'=>['code'=>200, 'message'=>'Данные обновлены!']]);*/

    }

    public function actionDel(){
        $user=Yii::$app->user->identity;
        $user->delete();
        return $this->send(200, ['message'=>'Пользователь удален!']);
    }




}
