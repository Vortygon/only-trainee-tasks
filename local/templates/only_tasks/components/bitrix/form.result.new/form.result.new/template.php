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
    <form class="contact-form__form" action="<?=POST_FORM_ACTION_URI?>" method="POST">
        <?
        foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion)
        {
            if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden')
            {
                echo $arQuestion["HTML_CODE"];
            }
            else
            {
        ?>
            <div class="input contact-form__input"><label class="input__label" for="medicine_name">
                <div class="input__label-text">
                    <?=$arQuestion["CAPTION"]?>
                    <?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
                </div>
                <input 
                    class="input__input" type="text" id="<?=$FIELD_SID?>" name="<?=$FIELD_SID?>" value=""
                    <?if ($arQuestion["REQUIRED"] == "Y"):?>
                    required=""
                    <?endif;?>
                    <?if ($FIELD_SID == "medicine_phone"):?>
                    data-inputmask="'mask': '+79999999999', 'clearIncomplete': 'true'" maxlength="12" x-autocompletetype="phone-full"
                    <?endif;?>
                >
                <?if (isset($arResult["FORM_ERRORS"][$FIELD_SID])):?>
                <div class="input__notification">
                    <?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>
                </div>
                <?endif;?>
            </label></div>

            <!-- <tr>
            <td>
                <?if (isset($arResult["FORM_ERRORS"][$FIELD_SID])):?>
                <span class="error-fld" title="<?=htmlspecialcharsbx($arResult["FORM_ERRORS"][$FIELD_SID])?>"></span>
                <?endif;?>
                <?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?>
                <?=$arQuestion["IS_INPUT_CAPTION_IMAGE"] == "Y" ? "<br />".$arQuestion["IMAGE"]["HTML_CODE"] : ""?>
            </td>
            <td><?=$arQuestion["HTML_CODE"]?></td>
            </tr> -->
        <?
            }
        } //endwhile
        ?>
        <input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" name="web_form_submit" value="<?=htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" />
        <?if ($arResult["F_RIGHT"] >= 15):?>
        &nbsp;<input type="hidden" name="web_form_apply" value="Y" /><input type="submit" name="web_form_apply" value="<?=GetMessage("FORM_APPLY")?>" />
        <?endif;?>
        &nbsp;<input type="reset" value="<?=GetMessage("FORM_RESET");?>" />
    </form>
</div>
<?=$arResult["FORM_FOOTER"]?>
<?
} //endif (isFormNote)




