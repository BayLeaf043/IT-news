<?php

use yii\db\Migration;

class m251225_180202_alert_comment_create_at_default extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%comment}}', 'created_at',
            $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%comment}}', 'created_at',
            $this->integer()->notNull()
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251225_180202_alert_comment_create_at_default cannot be reverted.\n";

        return false;
    }
    */
}
