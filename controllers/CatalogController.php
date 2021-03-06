<?php

namespace app\controllers;
use Yii;
use app\models\User;
use yii\filters\AccessControl;
use app\commands\Rbac;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\ForbiddenHttpException;

use app\models\Item;
use app\models\Category;

class CatalogController extends AppController{
    public $layout = 'main';

    public function actionIndex(){
        $item_model = Item::find()->all();
        $cat_model = Category::find()->all();

        return $this->render('catalog', [
            'item_model' => $item_model,
            'cat_model' => $cat_model,
        ]);
    }
    
    public function actionCategory($category)
    {
        $item_model = Item::find()
	    ->select('item.id, item.name, item.cat_id, item.price, item.img')
            ->from('item, category')
            ->where('category.link = :link AND item.cat_id = category.id', ["link" => $category])
            ->all();
            
        $cat_model = Category::find()->all();    
            
        return $this->render('catalog', [
            'item_model' => $item_model, 
            'cat_model' => $cat_model,
        ]);
    }

    public function actionItem($id){
        $item_model = Item::find()->where(['id'=> $id])->one();
	$cat_model = Category::find()->all();
	$all_item = Item::find()->all();
        // Отправка формы из карточки товара
        $form_model = new ContactForm();
        if ($form_model->load(Yii::$app->request->post()) && $form_model->sendEmail(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }

        return $this->render('item', [
            'item_model' => $item_model,
            'cat_model' => $cat_model,
            'all_item' => $all_item,
        ]);
    }
}
