<li class="user-details cyan darken-2">
    <div class="row">
        <div class="col col s4 m4 l4">
            <img src="<?php echo Yii::$app->user->identity->getAvatar(true);?>" alt="" class="circle responsive-img valign profile-image">
        </div>
        <div class="col col s8 m8 l8">

            <ul id="profile-dropdown" class="dropdown-content">
                <li>
                    <a href="<?php echo Yii::$app->frontendUrlManager->createAbsoluteUrl(['/site/index']) ?>">
                        <i class="mdi-action-home"></i> 
                        <?php echo Yii::t('admin' , 'На сайт');?>
                    </a>
                </li>
                 <li class="divider"></li>
                <li>
<!--                    <a href="">-->
<!--                        <i class="mdi-hardware-keyboard-tab"></i> -->
<!--                        --><?php //echo Yii::t('admin' , 'Выход');?>
<!--                    </a>-->

                </li>
            </ul>
            <a class="btn-flat dropdown-button waves-effect waves-light white-text profile-btn" href="#" data-activates="profile-dropdown">
                <?php echo Yii::$app->user->identity->email ;?>
                <i class="mdi-navigation-arrow-drop-down right"></i>
            </a>
        </div>
    </div>
</li>