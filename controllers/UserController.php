<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\User;
use app\models\LoginForm;
use Yii;
use yii\web\UnauthorizedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class UserController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => \yii\filters\AccessControl::class,
            'rules' => [
                [
                    'actions' => ['index', 'signup', 'create', 'login'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['index', 'logout', 'update', 'delete'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        return $this->goHome();
    }

    public function actionSignup()
    {
        $user = new User();
        return $this->render('signup', ['model' => $user]);
    }

    public function actionCreate()
    {
        $user = new User();
        $user->load(Yii::$app->request->post());
        $user->setPassword(Yii::$app->request->post('User')['password']);

        if ($user->validate() && $user->save()) {
            $user->refresh();
            Yii::$app->user->login($user);
            return $this->redirect(['index']);
        }

        return $this->render('signup', ['model' => $user]);
    }

    public function actionUpdate($id, $access_token)
    {

        $user = $this->findUserByToken($access_token, $id);
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $accessToken = Yii::$app->request->headers->get('Authorization');
        $accessToken = str_replace('Bearer ', '', $accessToken);

        $user = $this->findUserByToken($accessToken, $id);
        $model = $this->findModel($id);

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Аккаунт успешно удалён.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить аккаунт.');
        }

        return $this->goHome();
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = $model->getUser();
            $user->generateAccessToken();
            $user->save(false);

            return $this->redirect(['index']);
        }

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout($access_token = null)
    {
        $user = $this->findUserByToken($access_token);

        if ($user && Yii::$app->user->logout()) {
            Yii::$app->session->setFlash('success', 'Вы успешно вышли из системы.');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось выйти.');
        }

        return $this->goHome();
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException("Пользователь не найден");
    }

    protected function findUserByToken($token, $id = null)
    {
        $user = User::findIdentityByAccessToken($token);

        if (!$user || ($id && $user->id !== (int)$id)) {
            throw new ForbiddenHttpException("Доступ запрещен");
        }

        return $user;
    }
}

