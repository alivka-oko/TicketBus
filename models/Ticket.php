<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id_ticket
 * @property int $user_id
 * @property int $trip_id
 * @property int $seat
 *
 * @property Trip $trip
 * @property User $user
 */
class Ticket extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'seat'], 'required'],
            [['user_id', 'trip_id', 'seat'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id_user']],
            [['trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trip::className(), 'targetAttribute' => ['trip_id' => 'id_trip']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_ticket' => 'Id Ticket',
            'user_id' => 'User ID',
            'trip_id' => 'Trip ID',
            'seat' => 'Seat',
        ];
    }

    /**
     * Gets query for [[Trip]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrip()
    {
        return $this->hasOne(Trip::className(), ['id_trip' => 'trip_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id_user' => 'user_id']);
    }
}
