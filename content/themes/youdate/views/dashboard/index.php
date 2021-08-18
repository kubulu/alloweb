<?php

use youdate\helpers\HtmlHelper;
use youdate\widgets\EmptyState;
use youdate\widgets\DirectoryListView;
use youdate\widgets\SpotlightWidget;
use app\helpers\Html;
use app\managers\LikeManager;

/** @var $this \app\base\View */
/** @var $newMembersDataProvider \yii\data\ActiveDataProvider */
/** @var $mutualOnline \app\models\User[] */
/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */

$this->title = Yii::t('youdate', 'Dashboard');
$this->context->layout = 'page-main';
?>
<div class="dashboard-block mb-3">
    <h3><?= Yii::t('youdate', 'Spotlight') ?></h3>
    <?= SpotlightWidget::widget([
        'count' => 9,
        'user' => $user,
        'profile' => $profile,
    ]) ?>
</div>

<?php if (isset($this->params['user.ads.hide']) && !$this->params['user.ads.hide']): ?>
    <div class="mb-3"><?= $this->themeSetting('adsHeader') ?></div>
<?php endif; ?>

<div class="row row-eq-height mb-7">
    <div class="col-12 col-md-12 col-lg-7">
        <div class="dashboard-block dashboard-block-encounters">
            <h3><?= Yii::t('youdate', 'Encounters') ?></h3>
            <?= $this->render('//connections/_encounters', ['showQueue' => false]) ?>
            <?= Html::a(Yii::t('youdate', 'Play now'), ['/connections/encounters'], [
                'class' => 'btn btn-block btn-link text-gray mt-2',
            ]) ?>
        </div>
    </div>
    <div class="col-12 col-md-12 col-lg-5">
        <div class="dashboard-block dashboard-block-online h-100 d-flex flex-column">
            <h3><?= Yii::t('youdate', 'Your Matches') ?></h3>
            <div class="card d-flex flex-fill mb-0">
                <?php if (count($mutualOnline)): ?>
                    <ul class="list-group list-group-flush">
                    <?php foreach ($mutualOnline as $user): ?>
                        <li class="list-group-item d-flex flex-row align-items-center">
                            <div class="photo">
                                <div class="avatar avatar-md" style="background-image: url('<?= $user->profile->getAvatarUrl() ?>')">
                                    <?php if ($user->isOnline): ?>
                                        <span class="avatar-status bg-green"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info px-2">
                                <div class="first-line text-bolder">
                                    <?= Html::a(Html::encode($user->profile->getDisplayName()), ['/profile/view', 'username' => $user->username], [
                                        'class' => 'text-dark',
                                    ]) ?>
                                    <span class="ml-2" rel="tooltip" title="<?= $user->profile->getSexTitle() ?>">
                                        <?= HtmlHelper::sexToIcon($user->profile->sexModel) ?>
                                    </span>
                                    <?php if ($user->profile->is_verified): ?>
                                        <span rel="tooltip" title="<?= Yii::t('youdate', 'Verified') ?>">
                                        <i class="fa fa-check ml-2"></i>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <div class="second-line">
                                    <span class="location text-muted">
                                        <?= Html::encode($user->profile->getDisplayLocation()) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="actions ml-auto">
                                <?= Html::button(Yii::t('youdate', 'Message'), [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#profile-new-message',
                                    'data-contact-id' => $user->id,
                                    'data-title' => Yii::t('youdate', 'Message to {0}', [Html::encode($user->profile->getDisplayName())]),
                                    'class' => 'btn btn-sm btn-azure btn-pill btn-new-message',
                                ]) ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <?= EmptyState::widget([
                        'icon' => 'fe fe-users',
                        'title' => Yii::t('youdate', 'Users not found'),
                        'subTitle' => Yii::t('youdate', 'Seems like you have no mutual likes yet'),
                        'options' => ['class' => 'my-auto'],
                    ]) ?>
                <?php endif; ?>
            </div>
            <?= Html::a(Yii::t('youdate', 'View all'), ['/connections/likes', 'type' => LikeManager::TYPE_MUTUAL], [
                'class' => 'btn btn-block btn-link text-gray mt-2',
            ]) ?>
        </div>
    </div>
</div>

<div class="dashboard-block dashboard-block-newest">
    <h3><?= Yii::t('youdate', 'New members') ?></h3>
    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-dashboard-list-view', 'options' => ['data-pjax-scroll-to' => '.dashboard-block-newest']]) ?>
        <?php if ($newMembersDataProvider->getTotalCount()): ?>
            <?= DirectoryListView::widget([
                'dataProvider' => $newMembersDataProvider,
                'itemView' => '_item_new',
                'itemOptions' => ['tag' => false],
            ]) ?>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <?= EmptyState::widget([
                        'icon' => 'fe fe-users',
                        'title' => Yii::t('youdate', 'Users not found'),
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
    <?php \yii\widgets\Pjax::end() ?>
</div>

<?= $this->render('//profile/_message', ['newMessageForm' => new \app\forms\MessageForm()]) ?>