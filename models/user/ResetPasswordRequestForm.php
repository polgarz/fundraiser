<?php
namespace app\models\user;

use Yii;
use yii\base\Model;
use kekaadrenalin\recaptcha3\ReCaptchaValidator;

/**
 * Password reset request form
 */
class ResetPasswordRequestForm extends Model
{
    public $email;
    public $captcha;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'email', 'message' => 'Nincs ilyen email cím a rendszerben'],
            [['captcha'], ReCaptchaValidator::class, 'acceptance_score' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'Email cím'),
        ];
    }

    /**
     * Generates a password reset code
     *
     * @return bool whether the generation success
     */
    public function generatePasswordResetCode()
    {
        if (!$this->validate()) {
            return false;
        }

        /* @var $user User */
        $user = User::findOne([
            'email' => $this->email,
        ]);

        if ($user) {
            $this->_user = $user;

            // generate unique code
            do {
                $user->password_reset_code = rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);
            } while(User::findOne(['password_reset_code' => $user->password_reset_code]));

            return $user->save();
        }

        return false;
    }

    /**
     * Getter for user
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->_user;
    }
}
