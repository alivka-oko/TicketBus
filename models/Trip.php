<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "trip".
 *
 * @property int $id_trip
 * @property string $city_from
 * @property string $city_to
 * @property string $date_trip
 * @property string $timestart
 * @property string $timefinish
 * @property string $bus_name
 * @property int $ridecode
 * @property int $seats
 *
 * @property Ticket[] $tickets
 */
class Trip extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trip';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_from', 'city_to', 'date_trip', 'timestart', 'timefinish', 'bus_name', 'ridecode', 'seats'], 'required'],
            [['date_trip', 'timestart', 'timefinish'], 'safe'],
            [['ridecode'], 'unique'],
            [['seats'],'integer'],
            [['city_from', 'city_to'], 'string', 'max' => 40],
            [['bus_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_trip' => 'Id Trip',
            'city_from' => 'City From',
            'city_to' => 'City To',
            'date_trip' => 'Date Trip',
            'timestart' => 'Timestart',
            'timefinish' => 'Timefinish',
            'bus_name' => 'Bus Name',
            'ridecode' => 'Ridecode',
            'seats' => 'Seats',
        ];
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::className(), ['trip_id' => 'id_trip']);
    }
}
