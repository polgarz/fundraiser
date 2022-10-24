<?php

namespace app\models\user;

use Yii;
use yii\base\Model;

/**
 * Profile form
 *
 * @property User|null $user This property is read-only.
 *
 */
class ProfileForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $password_repeat;

    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param User $user
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($user, $config = [])
    {
        $this->_user = $user;
        $this->setAttributes($user->getAttributes());
        $this->password = null;

        parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email'], 'required'],
            [['first_name', 'last_name', 'email', 'password_repeat'], 'string', 'max' => 255],
            ['password', 'string', 'min' => 8],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'A két jelszó nem egyezik'],
            ['phone', 'match', 'pattern' => '/^\+[0-9-]+$/'],
        ];
    }

    /**
     * Validates the email.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {

            $exists = User::find()
                ->where(['!=', 'id', $this->user->id])
                ->andWhere(['email' => $this->$attribute])
                ->exists();

            if ($exists) {
                $this->addError($attribute, 'Ez az email cím már használatban van!');
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'first_name' => Yii::t('user', 'Vezetéknév'),
            'last_name' => Yii::t('user', 'Keresztnév'),
            'email' => Yii::t('user', 'Email cím'),
            'phone' => Yii::t('user', 'Telefonszám'),
            'password' => Yii::t('user', 'Jelszó'),
            'password_repeat' => Yii::t('user', 'Jelszó (újra)'),
        ];
    }


    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->user->setAttributes($this->getAttributes());

        // if there's no password change, then we write the old one.
        if (empty($this->user->dirtyAttributes['password'])) {
            $this->user->password = $this->user->oldAttributes['password'];
        } else {
            $this->user->password = Yii::$app->security->generatePasswordHash($this->user->password);
            $this->user->auth_key = Yii::$app->security->generateRandomString(64);
        }

        if (!$this->user->save()) {
            throw new \Exception('Váratlan hiba az adatmódosítás során!');
        }

        return true;
    }

    /**
     * Getter for newly created user
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->_user;
    }
}