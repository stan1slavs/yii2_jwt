<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var array|null $userData */

$this->title = 'Главная';
?>

<h1>Данные пользователя</h1>

<?php if (!empty($userData)): ?>
    <p><strong>Username:</strong> <?= Html::encode($userData['username']) ?>!</p>
    <p><strong>Email:</strong> <?= Html::encode($userData['email']) ?></p>
    <p><strong>Дата регистрации:</strong> <?= Yii::$app->formatter->asDate($userData['created_at']) ?></p>
    <?= Html::a('Обновить', ['user/update', 'id' => $userData['id'], 'access_token' => Yii::$app->user->identity->access_token,], ['class' => 'btn btn-primary']) ?>
    <button onclick="handlerDelete(<?= $userData['id'] ?>)" class="btn btn-danger">Удалить аккаунт</button>
<?php else: ?>
    <p>Вы не авторизованы. Пожалуйста, войдите или зарегистрируйтесь.</p>
<?php endif; ?>

<script>
function handlerDelete(userId) {
    const accessToken = '<?= Yii::$app->user->identity ? Yii::$app->user->identity->access_token : '' ?>';
    if (confirm("Вы действительно хотите удалить аккаунт? Это действие необратимо.")) {
        $.ajax({
            url: `/myapi/web/user/delete?id=${userId}`,
            type: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + accessToken
            },
        });
    }
}
</script>
