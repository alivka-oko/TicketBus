<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    public $login;
    public $password;

    public function rules()
    {
        return [
            // username and password are both required
            [['login', 'password'], 'required'],
        ];
    }

}
