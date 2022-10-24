<?php

use yii\db\Migration;

/**
 * Class m200618_211754_init_rbac
 */
class m200618_211754_init_rbac extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $rule = new \app\rbac\UserGroupRule;
        $auth->add($rule);

        $role = $auth->createRole('admin');
        $role->ruleName = $rule->name;
        $auth->add($role);

        $role = $auth->createRole('editor');
        $role->ruleName = $rule->name;
        $auth->add($role);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();
    }
}
