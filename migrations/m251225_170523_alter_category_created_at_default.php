<?php

use yii\db\Migration;

class m251225_170523_alter_category_created_at_default extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%category}}', 'created_at',
            $this->integer()->notNull()->defaultExpression('UNIX_TIMESTAMP()')
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%category}}', 'created_at',
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
        echo "m251225_170523_alter_category_created_at_default cannot be reverted.\n";

        return false;
    }
    */
}
