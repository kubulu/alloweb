<?php

$this->context->layout = 'page-main';
$this->title = 'Custom controller';

/** @var $this \app\base\View */
?>
<div class="card">
    <div class="card-body">
        <div class="text-wrap p-lg-6">
            <h2 class="mt-0 mb-4">Custom page</h2>
            <div class="alert alert-info">
                This is an example how to create your own controllers and views, which are not related to the application core files.
            </div>
            <p><strong>Files:</strong></p>
            <code>content/themes/custom/controllers/CustomController.php</code><br>
            <code>content/themes/custom/views/custom/index.php</code><br><br>

            <p><strong>Theme bootstrap file:</strong></p>
            <code>content/themes/custom/components/ThemeBootstrap.php</code><br><br>

            <p><strong>Added our new controller:</strong></p>
            <code>$app->controllerMap['custom'] = CustomController::class;</code><br><br>

            <p><strong>Added custom route for this page:</strong></p>
            <code>$app->urlManager->addRules(['demo-controller' => 'custom/index']);</code><br><br>

            <p><strong>And added a link to the header navigation:</strong></p>
            <code>content/themes/custom/views/partials/header-navigation.php</code><br>
        </div>
    </div>
</div>
