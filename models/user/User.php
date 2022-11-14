<?php

namespace app\models\user;

use app\models\donation\Donation;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property string $auth_key
 * @property string|null $activation_hash
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const GROUP_ADMIN = 'admin';

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
    public function behaviors()
    {
        return [
            BlameableBehavior::class,
            ['class' => TimestampBehavior::class, 'value' => new \yii\db\Expression('NOW()')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'auth_key'], 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            ['phone', 'match', 'pattern' => '/^\+[0-9-]+$/'],
            [['group'], 'string'],
            ['group', 'default', 'value' => null],
            ['group', 'in', 'range' => Yii::$app->authManager->defaultRoles],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by', 'password_reset_code'], 'integer'],
            [['first_name', 'last_name', 'email', 'phone', 'password', 'auth_key', 'activation_hash'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'Vezetéknév',
            'last_name' => 'Keresztnév',
            'email' => 'Email cím',
            'phone' => 'Telefonszám',
            'password' => 'Jelszó',
            'auth_key' => 'API kulcs',
            'activation_hash' => 'Aktivált',
            'group' => 'Jogosultság',
            'created_at' => 'Létrehozva',
            'updated_at' => 'Módosítás dátuma',
            'created_by' => 'Létrehozta',
            'updated_by' => 'Módosította',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['auth_key' => $token]);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getDonations()
    {
        return $this->hasMany(Donation::class, ['user_id' => 'id']);
    }
}
