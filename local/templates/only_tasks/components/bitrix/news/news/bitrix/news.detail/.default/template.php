<?php 
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<div class="article-card">
    <?php if ($arParams["DISPLAY_NAME"] != "N" && $arResult["NAME"]): ?>
        <div class="article-card__title"><?= $arResult["NAME"] ?></div>
    <?php endif; ?>
    <?php if ($arParams["DISPLAY_DATE"] != "N" && $arResult["DISPLAY_ACTIVE_FROM"]): ?>
        <div class="article-card__date"><?= $arResult["DISPLAY_ACTIVE_FROM"] ?></div>
    <?php endif; ?>
    <div class="article-card__content">
        <?php if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arResult["DETAIL_PICTURE"])): ?>
            <div class="article-card__image sticky">
                <img 
                    src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" 
                    alt="<?= $arResult["DETAIL_PICTURE"]["ALT"] ?>"
                    title="<?= $arResult["DETAIL_PICTURE"]["TITLE"] ?>" 
                    data-object-fit="cover" 
                    />
            </div>
        <?php endif ?>
        <div class="article-card__text">
            <div class="block-content" data-anim="anim-3">
                <p>
                    <?php if ($arResult["DETAIL_TEXT"] <> ''): ?>
                        <?= $arResult["DETAIL_TEXT"]; ?>
                    <?php else: ?>
                        <?= $arResult["PREVIEW_TEXT"]; ?>
                    <?php endif ?>
                </p>
            </div>
            <a class="article-card__button" href="<?=$arResult["LIST_PAGE_URL"]?>">
                <?= GetMessage("RETURN_BUTTON_TEXT") ?>
            </a>
        </div>
    </div>
</div>