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
            'only'=>['account'] //Перечислите для контроллера методы, требующие аутентификации
            //здесь метод actionAccount()
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

    public function actionLogin(){
        $request=Yii::$app->request->post();//Здесь не объект, а ассоциативный массив
        $loginForm=new LoginForm($request);
        if (!$loginForm->validate()) return $this->validation($loginForm);
        $user=User::find()->where(['login'=>$request['login']])->one();
        if (isset($user) && Yii::$app->getSecurity()->validatePassword($request['password'], $user->password)){
            $user->tocken=Yii::$app->getSecurity()->generateRandomString();
            $user->save(false);
            return $this->send(200, ['content'=>['tocken'=>$user->tocken]]);
        }
        return $this->send(401, ['content'=>['code'=>401, 'message'=>'Неверный email или пароль']]);
    }

    /*
     * Личный кабинет пользователя
     */
    public function actionAccount(){
        $total_price=0;
        $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
        $charts=$user->getCharts()->all();
        $chartItems=[];
        foreach ($charts as $chart){
            $chart=new Chart($chart);
            $product=new Product($chart->getProduct()->one());
            $chartItems[]=['amount'=>$chart->amount, 'product'=>$product->name, 'image'=>$product->image, 'price'=>$product->price];
            $total_price+=$chart->amount*$product->price;
        }
        return $this->send(200, ['content'=> ['user'=>$user, 'order'=>$chartItems, 'TOTAL'=>$total_price]]);
    }
}
