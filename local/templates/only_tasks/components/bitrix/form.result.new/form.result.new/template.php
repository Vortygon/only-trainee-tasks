<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arResult
 */

if ($arResult['isFormNote'] != 'Y') {
    ?>
    <div class="contact-form">
        <div class="contact-form__head">
            <?php if ($arResult['isFormTitle']): ?>
                <div class="contact-form__head-title"><?= $arResult['FORM_TITLE'] ?></div>
            <?php endif; ?>
            <?php if ($arResult['isFormDescription']): ?>
                <div class="contact-form__head-text"><?= $arResult['FORM_DESCRIPTION'] ?></div>
            <?php endif; ?>
        </div>
        <form name="<?= $arResult['WEB_FORM_NAME'] ?>" class="contact-form__form" action="<?= POST_FORM_ACTION_URI ?>" method="POST">
			<input type="hidden" name="WEB_FORM_ID" value="<?=$arParams['WEB_FORM_ID']?>">
			<input type="hidden" name="web_form_submit" value="Y">
			<?=bitrix_sessid_post()?>
            <div class="contact-form__form-inputs">
                <?php
                foreach ($arResult['QUESTIONS'] as $FIELD_SID => $arQuestion) {
                    if ($FIELD_SID == 'medicine_message') {
                        continue;
                    }
                    
                    if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
                        echo $arQuestion['HTML_CODE'];
                    } else {
                        ?>
                        <div class="input contact-form__input">
                            <label class="input__label" for="<?= $FIELD_SID ?>">
                                <div class="input__label-text">
                                    <?= $arQuestion['CAPTION'] ?>
                                    <?php if ($arQuestion['REQUIRED'] == 'Y'): ?>
                                        <?= $arResult['REQUIRED_SIGN']; ?>
                                    <?php endif; ?>
                                </div>
                                <?php
                                switch ($FIELD_SID) {
                                    case 'medicine_phone':
										echo str_replace(
                                            '<input',
                                            '<input 
												class="input__input" 
												type="tel" 
												data-inputmask="\'mask\': \'+79999999999\', \'clearIncomplete\': \'true\'" 
												maxlength="12" 
												x-autocompletetype="phone-full"
                                        	',
                                            $arResult["QUESTIONS"][$FIELD_SID]['HTML_CODE']
                                        );
                                        ?>
                                        <!-- <input 
                                            class="input__input" 
                                            type="tel" 
                                            id="<?= $FIELD_SID ?>" 
                                            name="form" 
                                            value=""
                                            data-inputmask="'mask': '+79999999999', 'clearIncomplete': 'true'" 
                                            maxlength="12" 
                                            x-autocompletetype="phone-full"
                                            <?= (intval($arQuestion['REQUIRED'] == 'Y') ? 'required=""' : '') ?>
                                        > -->
                                        <?php
                                        break;
                                    default:
                                        echo str_replace(
                                            '<input',
                                            '<input class="input__input" ',
                                            $arResult["QUESTIONS"][$FIELD_SID]['HTML_CODE']
                                        );
                                        ?>
                                        <!-- <input 
                                            class="input__input" 
                                            type="<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] ?>" 
                                            id="<?= $FIELD_SID ?>" 
                                            name="<?= $FIELD_SID ?>" 
                                            value=""
                                            <?= (intval($arQuestion['REQUIRED'] == 'Y') ? 'required=""' : '') ?>
                                        > -->
                                        <?php
                                        break;
                                }
                                ?>
                                <?php if (isset($arResult['FORM_ERRORS'][$FIELD_SID])): ?>
                                    <div class="input__notification">
                                        <?= htmlspecialcharsbx($arResult['FORM_ERRORS'][$FIELD_SID]) ?>
                                    </div>
                                <?php endif; ?>
                            </label>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="contact-form__form-message">
                <div class="input">
                    <label class="input__label">
                        <div class="input__label-text"><?= $arResult['QUESTIONS']['medicine_message']['CAPTION'] ?></div>
                        <?php
                        $textareaHtml = $arResult['QUESTIONS']['medicine_message']['HTML_CODE'];
                        $textareaHtml = str_replace('<textarea', '<textarea class="input__input"', $textareaHtml);
                        echo $textareaHtml;
                        ?>
                        <?php if (isset($arResult['FORM_ERRORS']['medicine_message'])): ?>
                            <div class="input__notification">
                                <?= htmlspecialcharsbx($arResult['FORM_ERRORS']['medicine_message']) ?>
                            </div>
                        <?php endif; ?>
                    </label>
                </div>
            </div>
            <div class="contact-form__bottom">
                <div class="contact-form__bottom-policy">
                    <?= GetMessage('AGREEMENT') ?>
                    <?= $arResult['FORM_ERRORS_TEXT']; ?>
                </div>
                <button 
                    class="form-button contact-form__bottom-button" 
                    data-success="Отправлено"
                    data-error="Ошибка отправки"
                >
                    <div class="form-button__title">
                        <?= htmlspecialcharsbx(trim($arResult['arForm']['BUTTON']) == '' ? GetMessage('FORM_ADD') : $arResult['arForm']['BUTTON']); ?>
                    </div>
                </button>
            </div>
        </form>
    </div>
    <?php
}