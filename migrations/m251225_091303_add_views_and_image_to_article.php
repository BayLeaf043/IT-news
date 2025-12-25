<?php

use yii\db\Migration;

class m251225_091303_add_views_and_image_to_article extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%article}}', 'views', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%article}}', 'image', $this->string(255)->null()); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251225_091303_add_views_and_image_to_article cannot be reverted.\n";

        $this->dropColumn('{{%article}}', 'image');
        $this->dropColumn('{{%article}}', 'views');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251225_091303_add_views_and_image_to_article cannot be reverted.\n";

        return false;
    }
    */
}
