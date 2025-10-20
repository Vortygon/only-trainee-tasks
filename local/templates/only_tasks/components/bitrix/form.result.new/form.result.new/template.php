<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

/**
 * @var array $arResult
 */

// if ($arResult["isFormErrors"] == "Y") 
// {
//     echo $arResult["FORM_ERRORS_TEXT"];
// }

echo $arResult["FORM_NOTE"] ?? '';

if ($arResult["isFormNote"] != "Y")
{
?>
<?=$arResult["FORM_HEADER"]?>
<div class="contact-form">
    <div class="contact-form__head">
        <? if ($arResult["isFormTitle"]): ?>
            <div class="contact-form__head-title"><?=$arResult["FORM_TITLE"]?></div>
        <? endif; ?>
        <? if ($arResult["isFormDescription"]): ?>
            <div class="contact-form__head-text"><?=$arResult["FORM_DESCRIPTION"]?></div>
        <? endif; ?>
    </div>
    <form name="<?=$arResult['WEB_FORM_NAME']?>" class="contact-form__form" action="<?=POST_FORM_ACTION_URI?>" method="POST">
        <div class="contact-form__form-inputs">
            <?
            foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
            {
                if ($FIELD_SID == 'medicine_message') continue;
                if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
                {
                    echo $arQuestion["HTML_CODE"];
                }
                else
                {
            ?>
                    <div class="input contact-form__input"><label class="input__label" for="<?=$FIELD_SID?>">
                        <div class="input__label-text">
                            <?=$arQuestion["CAPTION"]?>
                            <?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
                        </div>
                        <? switch ($FIELD_SID) {
                            case "medicine_phone":
                        ?>
                                <input 
                                    class="input__input" type="tel" id="<?=$FIELD_SID?>" name="<?=$FIELD_SID?>" value=""
                                    data-inputmask="'mask': '+79999999999', 'clearIncomplete': 'true'" maxlength="12" x-autocompletetype="phone-full"
                                    <?=(intval($arQuestion["REQUIRED"] == "Y" ? "required=\"\"" : ""))?>
                                >
                        <?
                                break;
                            default:
                        ?>
                                <input 
                                    class="input__input" type="<?=$arQuestion['STRUCTURE'][0]['FIELD_TYPE']?>" id="<?=$FIELD_SID?>" name="<?=$FIELD_SID?>" value=""
                                    <?=(intval($arQuestion["REQUIRED"] == "Y" ? "required=\"\"" : ""))?>
                                >
                        <?
                                break;
                        } 
                        ?>
                        <?if (isset($arResult["FORM_ERRORS"][$FIELD_SID])):?>
                        <div class="input__notification">
                            <?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>
                        </div>
                        <?endif;?>
                    </label></div>
            <?
                }
            } //endwhile
            ?>
        </div>
        <div class="contact-form__form-message">
            <div class="input"><label class="input__label" for="medicine_message">
                <div class="input__label-text"><?=$arResult["QUESTIONS"]["medicine_message"]["CAPTION"]?></div>
                <textarea class="input__input" type="text" id="medicine_message" name="medicine_message"
                          value=""></textarea>
                <div class="input__notification"></div>
            </label></div>
        </div>
        
        <!-- <input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" />
        <?if ($arResult["F_RIGHT"] >= 15):?>
        &nbsp;<input type="hidden" name="web_form_apply" value="Y" /><input type="submit" name="web_form_apply" value="<?=GetMessage("FORM_APPLY")?>" />
        <?endif;?>
        &nbsp;<input type="reset" value="<?=GetMessage("FORM_RESET");?>" /> -->
        
        <div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">Нажимая &laquo;Отправить&raquo;, Вы&nbsp;подтверждаете, что
                ознакомлены, полностью согласны и&nbsp;принимаете условия &laquo;Согласия на&nbsp;обработку персональных
                данных&raquo;.
            </div>
            <button class="form-button contact-form__bottom-button" data-success="Отправлено"
                    data-error="Ошибка отправки">
                <div class="form-button__title">
                    <?=htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>
                </div>
            </button>
        </div>
    </form>
</div>
<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)




