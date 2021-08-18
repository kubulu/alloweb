<?php

use yii\widgets\Menu;
use app\helpers\Html;
use app\helpers\Url;
use youdate\widgets\Gallery;
use youdate\helpers\HtmlHelper;
use youdate\widgets\PopularityWidget;
use youdate\widgets\ProfileExtraFields;

/** @var $this \app\base\View */
/** @var $content string */
/** @var $user \app\models\User */
/** @var $profile \app\models\Profile */
/** @var $newMessageForm \app\forms\MessageForm */
/** @var $reportForm \app\forms\ReportForm */
/** @var $photos \app\models\Photo[] */
/** @var $likeByCurrentUser \app\models\Like|null */
/** @var $blockByCurrentUser bool */

$title = Html::encode($profile->getDisplayName());
$this->title = $title;
$this->context->layout = 'page-main';
$this->registerJsFile('@themeUrl/static/js/vendors/viewport.js', ['depends' => \youdate\assets\CoreAsset::class]);
$this->registerJsFile('@themeUrl/static/js/profile.js', ['depends' => \youdate\assets\CoreAsset::class]);

$galleryItems = [];
foreach ($photos as $photo) {
    $galleryItems[] = [
        'url' => $photo->getUrl(),
        'src' => $photo->getThumbnail(200, 200),
        'options' => ['class' => 'col-4 gallery-item'],
        'wrapperOptions' => ['class' => 'gallery-item-wrapper'],
        'hiddenPhotosOptions' => ['class' => 'd-flex justify-content-center align-items-center'],
    ];
}
$mainPhotoSelector = count($galleryItems) > 1 ? '.profile-photos-row a' : '.profile-main-photo a';
?>
<div class="row" data-user-id="<?= $user->id ?>">
    <div id="profile-column-left" class="col-sm-12 col-md-5 col-lg-3 order-md-1 order-lg-1">
        <div class="sidebar-wrapper">
            <div class="card">
                <div class="profile-photos">
                    <div class="profile-main-photo">
                        <div class="photo-wrapper d-flex justify-content-center">
                            <div class="loader">
                                <i class="fa fa-spin fa-spin"></i>
                            </div>
                            <?php if ($profile->photo !== null): ?>
                                <a href="<?= $profile->photo->getUrl() ?>">
                                    <img class="profile-photo-img" src="<?= $profile->getAvatarUrl(600, 600) ?>"
                                         style="cursor: pointer" onclick="blueimp.Gallery($('<?= $mainPhotoSelector ?>')); return false">
                                </a>
                            <?php else: ?>
                                <img class="profile-photo-img" src="<?= $profile->getAvatarUrl(400, 400) ?>"
                                     style="cursor: pointer">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="profile-other-photos pt-0">
                        <?= Gallery::widget([
                            'items' => $galleryItems,
                            'options' => [
                                'class' => 'profile-photos-row row',
                            ],
                        ]);?>
                    </div>
                </div>
            </div>
            <div class="profile-navigation">
                <?php /** TODO: add this with feed feature */ ?>
                <?php Menu::widget([
                    'options' => [
                        'tag' => 'div',
                    ],
                    'itemOptions' => [
                        'tag' => 'div',
                        'class' => 'navigation-item',
                    ],
                    'items' => [
                        [
                            'label' => Yii::t('youdate', 'Information'),
                            'url' => ['/profile/view', 'username' => $user->username]
                        ],
                        [
                            'label' => Yii::t('youdate', 'Feed'),
                            'url' => ['/profile/feed', 'username' => $user->username]
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div id="profile-column-content" class="col-sm-12 col-md-7 col-lg-6 order-md-2 order-lg-2">
        <div class="card card-main-info">
            <div class="profile-info">
                <div class="profile-info-block profile-main-info">
                    <div class="d-flex">
                        <div class="name-location justify-content-end">
                            <div class="first-line ">
                                <h1 class="display-name"><?= Html::encode($profile->getDisplayName()) ?></h1>
                                <span class="px-1">&middot;</span>
                                <span class="age"><?= $profile->getAge() ?></span>
                                <?= $this->render('/partials/online-status', ['model' => $user]) ?>
                            </div>
                            <div class="second-line d-flex align-content-center flex-column flex-sm-row">
                                <div class="user-badges d-flex flex-row">
                                    <div class="user-badge user-sex-badge sex-<?= $profile->sexModel->alias ?? '' ?> d-flex align-items-center justify-content-center mr-2" rel="tooltip"
                                         title="<?= $profile->getSexTitle() ?>">
                                        <?= HtmlHelper::sexToIcon($profile->sexModel) ?>
                                    </div>
                                    <?php if ($profile->is_verified): ?>
                                        <div class="user-badge user-verified-badge d-flex align-items-center justify-content-center mr-2" rel="tooltip"
                                             title="<?= Yii::t('youdate', 'Verified user') ?>">
                                            <i class="fe fe-check"></i>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($user->isPremium): ?>
                                        <div class="user-badge user-premium-badge d-flex align-items-center justify-content-center" rel="tooltip"
                                             title="<?= Yii::t('youdate', 'Premium user') ?>">
                                            <i class="fe fe-star"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="user-location mt-2 mt-sm-0 ml-0 ml-sm-2">
                                    <?= $profile->getDisplayLocation() ?>
                                </div>
                            </div>
                        </div>
                        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->id !== $user->id): ?>
                            <div class="like-toggle align-self-center ml-auto">
                                <button class="btn btn-like-toggle btn-lg <?= $likeByCurrentUser == null ? 'btn-not-liked' : 'btn-liked' ?>"
                                        rel="tooltip"
                                        title="<?= $likeByCurrentUser == null ? Yii::t('youdate', 'Like') : Yii::t('youdate', 'Dislike') ?>"
                                        data-url="<?= Url::to(['/connections/toggle-like', 'toUserId' => $profile->user_id]) ?>"
                                        data-like-create-title="<?= Yii::t('youdate', 'Like' ) ?>"
                                        data-like-delete-title="<?= Yii::t('youdate', 'Dislike') ?>"
                                        data-liked="<?= $likeByCurrentUser == null ? 0 : 1 ?>">
                                    <i class="fa fa-heart<?= $likeByCurrentUser == null ? '-o' : '' ?>"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!Yii::$app->user->isGuest): ?>
                    <div class="profile-info-block profile-main-actions">
                        <?php if (Yii::$app->user->id == $user->id): ?>
                            <a href="<?= Url::to(['/settings/profile']) ?>" class="btn btn-blue">
                                <i class="fe fe-edit mr-2"></i>
                                <?= Yii::t('youdate', 'Edit profile') ?>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-blue mb-2 mb-sm-0" data-toggle="modal" data-target="#profile-new-message">
                                <i class="fe fe-mail mr-2"></i> <?= Yii::t('youdate', 'Message') ?>
                            </button>
                            <?php \yii\widgets\Pjax::begin(['id' => 'pjax-profile-actions', 'options' => ['tag' => 'span']]) ?>
                            <div class="dropdown">
                                <button type="button" class="btn btn-secondary dropdown-toggle mb-2 mb-sm-0" data-toggle="dropdown">
                                    <i class="fe fe-more-horizontal"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" href="#" data-toggle="modal" data-target="#profile-report">
                                        <?= Yii::t('youdate', 'Report') ?>
                                    </button>
                                    <button class="dropdown-item btn-ajax" data-action="<?= Url::to(['/block/toggle']) ?>"
                                            data-data="blockedUserId=<?= $user->id ?>"
                                            data-type="post"
                                            data-pjax="0"
                                            data-pjax-container="#pjax-profile-actions">
                                        <?php if ($blockByCurrentUser): ?>
                                            <?= Yii::t('youdate', 'Unblock') ?>
                                        <?php else: ?>
                                            <?= Yii::t('youdate', 'Block') ?>
                                        <?php endif; ?>
                                    </button>
                                </div>
                            </div>
                            <?php \yii\widgets\Pjax::end() ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="profile-info-block d-flex justify-content-center align-items-center">
                        <div class="pr-2">
                            <?= Html::encode(Yii::t('youdate', 'Do you like {name}? Join to chat', [
                                'name' => $profile->getDisplayName(),
                            ])) ?>
                        </div>
                        <?= Html::a(Yii::t('youdate', 'Sign up'), ['/registration/register'], [
                            'class' => 'btn btn-primary mr-2',
                        ]) ?>
                        <?= Html::a(Yii::t('youdate', 'Sign in'), ['/security/login'], [
                            'class' => 'btn btn-secondary',
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card">
            <div class="profile-info">
                <div class="profile-info-block">
                    <div class="row mb-0 mb-sm-4">
                        <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                            <div class="text-bold mb-2"><?= Yii::t('app', 'Sex') ?>:</div>
                            <div class="text-muted">
                                <?= $profile->getSexTitle() ?>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                            <div class="text-bold mb-2"><?= Yii::t('app', 'Status') ?>:</div>
                            <div class="text-muted">
                                <?= $profile->getStatusTitle() ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                            <div class="text-bold mb-2"><?= Yii::t('youdate', 'I am looking for') ?>:</div>
                            <div class="text-muted">
                                <?= $profile->getLookingForTitle() ?>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="text-bold mb-2"><?= Yii::t('youdate', 'Aged') ?>:</div>
                            <div class="text-muted">
                                <?= $profile->getLookingForAgeTitle() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-info-block">
                    <div class="text-bold mb-2"><?= Yii::t('youdate', 'Description') ?>:</div>
                    <div class="text-muted">
                        <?php if ($profile->description): ?>
                            <?= Html::prettyPrinted($profile->description) ?>
                        <?php else: ?>
                            <?= Yii::t('youdate', '{0} doesn\'t have profile description yet', [
                                Html::encode($profile->getDisplayName()),
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?= ProfileExtraFields::widget(['profile' => $profile]) ?>
            </div>
        </div>
    </div>
    <div id="profile-column-right" class="col-sm-12 col-md-5 col-lg-3 order-md-3 order-lg-3 ">
        <?= PopularityWidget::widget(['userId' => $user->id]) ?>
        <?php if (isset($this->params['user.ads.hide']) && !$this->params['user.ads.hide'] || Yii::$app->user->isGuest): ?>
            <div class="mb-5"><?= $this->themeSetting('adsSidebar') ?></div>
        <?php endif; ?>
    </div>
</div>

<?= $this->render('_message', ['user' => $user, 'profile' => $profile, 'newMessageForm' => $newMessageForm]) ?>
<?= $this->render('_report', ['user' => $user, 'profile' => $profile, 'reportForm' => $reportForm]) ?>
