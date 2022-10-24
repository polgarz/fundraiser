<?php
namespace app\models\user;

use yii\base\Model;
use Yii;
use kekaadrenalin\recaptcha3\ReCaptchaValidator;

/**
 * Password reset form
 */
class ResetPasswordConfirmationForm extends Model
{
    public $code;
    public $captcha;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['code', 'required'],
            ['code', 'integer'],
            ['code', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'password_reset_code'],
            [['captcha'], ReCaptchaValidator::class, 'acceptance_score' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => Yii::t('user', 'Ellenőrző kód'),
        ];
    }

    public function confirm()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findOne(['password_reset_code' => $this->code]);
        $user->password_reset_code = null;

        $this->_user = $user;

        return $user->save();
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
