<?php

namespace app\models\user;

use Yii;
use yii\base\Model;
use kekaadrenalin\recaptcha3\ReCaptchaValidator;

/**
 * Registration form
 *
 * @property User|null $user This property is read-only.
 *
 */
class RegistrationForm extends Model
{
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password;
    public $password_repeat;
    public $privacy_policy;
    public $captcha;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'password', 'password_repeat'], 'required'],
            [['first_name', 'last_name', 'email', 'password_repeat'], 'string', 'max' => 255],
            ['password', 'string', 'min' => 8],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'email'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'A két jelszó nem egyezik'],
            ['phone', 'match', 'pattern' => '/^\+[0-9-]+$/'],
            [['privacy_policy'], 'required', 'requiredValue' => 1, 'message' => 'A regisztrációhoz kötelező elfogadni az adatvédelmi tájékoztatóban foglaltakat'],
            [['captcha'], ReCaptchaValidator::class, 'acceptance_score' => 0],
        ];
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


    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = new User([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Yii::$app->security->generatePasswordHash($this->password),
            'auth_key' => Yii::$app->security->generateRandomString(64),
            'activation_hash' => Yii::$app->security->generateRandomString(64),
        ]);

        if ($user->save()) {
            $this->_user = $user;

            return true;
        } else {
            throw new \Exception('Váratlan hiba a regisztráció során!');
        }
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
