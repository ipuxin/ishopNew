<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$screen_name = Yii::$app->session['screen_name'];
$profile_image_url = Yii::$app->session['profile_image_url'];
?>
<!-- ============================================================= HEADER : END ============================================================= -->        <!-- ========================================= MAIN ========================================= -->
<main id="authentication" class="inner-bottom-md">
    <div class="container">
        <div class="row">

            <div class="col-md-6">
                <section class="section sign-in inner-right-xs">
                    <h2 class="bordered">

                        <!--显示一个50像素的头像-->
                        <img src="<?= $profile_image_url ?>">
                        绑定微博登录, 快速登录
                    </h2>
                    <p>请填写一个用户名和密码</p>

                    <div class="social-auth-buttons">
                    </div>
                    <?php $form = ActiveForm::begin([
                        'fieldConfig' => [
                            'template' => '<div class="field-row">{label}{input}</div>{error}'
                        ],
                        'options' => [
                            'class' => 'login-form cf-style-1',
                            'role' => 'form',
                        ],
                        //'action' => ['member/auth'],
                    ]);
                    $loginname = Yii::$app->session['screen_name'];
                    ?>
                    <input type="text" value="<?= $screen_name ?>" class="le-input"><br>
                    <?php echo $form->field($model, 'username')
                        ->textInput(['class' => 'le-input', 'value' => $screen_name]); ?>
                    <?php echo $form->field($model, 'userpass')->passwordInput(['class' => 'le-input']); ?>
                    <?php echo $form->field($model, 'repass')->passwordInput(['class' => 'le-input']); ?>
                    <div class="field-row clearfix">
                    </div>

                    <div class="buttons-holder">
                        <?php echo Html::submitButton('完成绑定', ['class' => 'le-button huge']); ?>
                    </div><!-- /.buttons-holder -->

                    <?php ActiveForm::end(); ?><!-- /.cf-style-1 -->

                </section><!-- /.sign-in -->
            </div><!-- /.col -->

        </div><!-- /.row -->
    </div><!-- /.container -->
</main><!-- /.authentication -->
<!-- ========================================= MAIN : END ========================================= -->
<!-- ============================================================= FOOTER ============================================================= -->
<script>
    var qqbtn = document.getElementById("login_qq");
    qqbtn.onclick = function () {
        window.location.href = "<?php echo yii\helpers\Url::to(['member/qqlogin']) ?>";
    }
</script>





