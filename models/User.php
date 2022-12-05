<?php


namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id_user
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $document_number
 * @property string $login
 * @property string $password
 * @property string $tocken
 *
 * @property Ticket[] $tickets
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'document_number', 'login', 'password', 'tocken'], 'required'],
            [['first_name', 'last_name'], 'string', 'max' => 30],
            [['phone'], 'string', 'max' => 20],
            [['document_number'], 'string', 'max' => 11],
            [['login', 'password'], 'string', 'max' => 50],
            [['tocken'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'Id User',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'document_number' => 'Document Number',
            'login' => 'Login',
            'password' => 'Password',
            'tocken' => 'Tocken',
        ];
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['user_id' => 'id_user']);
    }
    public function fields()
    {
        $fields = parent::fields();
// удаляем небезопасные поля
        unset($fields['password'],$fields['id'], $fields['token']);
        return $fields;
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login]);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return ;
    }

    public function validateAuthKey($authKey)
    {
        return ;
    }
    public function validatePassword($password)

    {
        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);

        if (Yii::$app->getSecurity()->validatePassword($password, $hash)) {
            return $this;
        } else {
            return 0;
        }


    }
}
