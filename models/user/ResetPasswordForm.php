<?php
namespace app\models\user;

use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;
    public $password_repeat;

    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Üres token!');
        }
        $this->_user = User::findIdentityByAccessToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Hibás token!');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            ['password', 'string', 'min' => 8],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'A két jelszónak egyeznie kell!'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('user', 'Új jelszó'),
            'password_repeat' => Yii::t('user', 'Új jelszó (újra)'),
        ];
    }

    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $this->user->password = Yii::$app->security->generatePasswordHash($this->password);
        $this->user->auth_key = Yii::$app->security->generateRandomString(64);

        return $this->user->save();
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
