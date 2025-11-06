<?php

namespace Aspro\Lite\Product;

use Aspro\Functions\CAsproLite as SolutionFunctions;
use Aspro\Lite\Product\Price as SolutionPrice;
use Aspro\Lite\Utils as SolutionUtils;
use Bitrix\Main\Config\Option;
use CLite as Solution;

class Basket
{
    public static $catalogInclude;
    public static $btnClasses = [
        'BTN_CLASS' => 'btn-sm',
        'BTN_IN_CART_CLASS' => 'btn-sm',
        'BTN_CLASS_MORE' => '',
        'BTN_CLASS_SUBSCRIBE' => 'btn-wide',
        'BTN_ORDER_CLASS' => 'btn-sm btn-wide btn-transparent-border',
        'BTN_CALLBACK_CLASS' => 'btn-sm btn-transparent-border',
        'BTN_OCB_CLASS' => 'btn-sm btn-transparent',
        'TO_BASKET_LINK_CLASS' => 'btn-sm',
    ];

    public static function checkCatalogModule()
    {
        if (self::$catalogInclude === null) {
            self::$catalogInclude = Solution::isSaleMode();
        }
    }

    public static function getConfig()
    {
        static $arAddToBasketOptions;
        if($arAddToBasketOptions === null) {
            $arAddToBasketOptions = [
                'SHOW_BASKET_ONADDTOCART' => Option::get(Solution::moduleID, 'SHOW_BASKET_ONADDTOCART', 'Y', SITE_ID) == 'Y',
                'BUYNOPRICEGGOODS' => Option::get(Solution::moduleID, 'BUYNOPRICEGGOODS', 'NOTHING', SITE_ID),
                'BUYMISSINGGOODS' => Option::get(Solution::moduleID, 'BUYMISSINGGOODS', 'ADD', SITE_ID),
                'EXPRESSION_ORDER_BUTTON' => Option::get(Solution::moduleID, 'EXPRESSION_ORDER_BUTTON', GetMessage('EXPRESSION_ORDER_BUTTON_DEFAULT'), SITE_ID),
                'EXPRESSION_ORDER_TEXT' => Option::get(Solution::moduleID, 'EXPRESSION_ORDER_TEXT', GetMessage('EXPRESSION_ORDER_TEXT_DEFAULT'), SITE_ID),
                'EXPRESSION_SUBSCRIBE_BUTTON' => Option::get(Solution::moduleID, 'EXPRESSION_SUBSCRIBE_BUTTON', GetMessage('EXPRESSION_SUBSCRIBE_BUTTON_DEFAULT'), SITE_ID),
                'EXPRESSION_SUBSCRIBED_BUTTON' => Option::get(Solution::moduleID, 'EXPRESSION_SUBSCRIBED_BUTTON', GetMessage('EXPRESSION_SUBSCRIBED_BUTTON_DEFAULT'), SITE_ID),
                'EXPRESSION_ADDTOBASKET_BUTTON' => Option::get(Solution::moduleID, 'EXPRESSION_ADDTOBASKET_BUTTON_DEFAULT', GetMessage('EXPRESSION_ADDTOBASKET_BUTTON_DEFAULT'), SITE_ID),
                'EXPRESSION_ADDEDTOBASKET_BUTTON' => Option::get(Solution::moduleID, 'EXPRESSION_ADDEDTOBASKET_BUTTON_DEFAULT', GetMessage('EXPRESSION_ADDEDTOBASKET_BUTTON_DEFAULT'), SITE_ID),
                'EXPRESSION_READ_MORE_OFFERS' => Option::get(Solution::moduleID, 'EXPRESSION_READ_MORE_OFFERS_DEFAULT', GetMessage('EXPRESSION_READ_MORE_OFFERS_DEFAULT'), SITE_ID),
                'EXPRESSION_ONE_CLICK_BUY' => Option::get(Solution::moduleID, 'EXPRESSION_ONE_CLICK_BUY', GetMessage('EXPRESSION_ONE_CLICK_BUY_DEFAULT'), SITE_ID),
                'IS_AUTORIZED' => $GLOBALS['USER']->IsAuthorized(),
            ];

            if (!strlen($arAddToBasketOptions['EXPRESSION_ORDER_BUTTON'])) {
                $arAddToBasketOptions['EXPRESSION_ORDER_BUTTON'] = GetMessage('EXPRESSION_ORDER_BUTTON_DEFAULT');
            }
            if (!strlen($arAddToBasketOptions['EXPRESSION_SUBSCRIBE_BUTTON'])) {
                $arAddToBasketOptions['EXPRESSION_SUBSCRIBE_BUTTON'] = GetMessage('EXPRESSION_SUBSCRIBE_BUTTON_DEFAULT');
            }
            if (!strlen($arAddToBasketOptions['EXPRESSION_SUBSCRIBED_BUTTON'])) {
                $arAddToBasketOptions['EXPRESSION_SUBSCRIBED_BUTTON'] = GetMessage('EXPRESSION_SUBSCRIBED_BUTTON_DEFAULT');
            }
            if (!strlen($arAddToBasketOptions['EXPRESSION_READ_MORE_OFFERS'])) {
                $arAddToBasketOptions['EXPRESSION_READ_MORE_OFFERS'] = GetMessage('EXPRESSION_READ_MORE_OFFERS_DEFAULT');
            }
        }

        return $arAddToBasketOptions;
    }

    /**
     * @param array $arOptions
     *
     * @var 'TYPE'                 => 'catalog-block'
     * @var 'WRAPPER'              => false
     * @var 'WRAPPER_CLASS'        => '',
     * @var 'BASKET'               => false,
     * @var 'DETAIL_PAGE'          => false,
     * @var 'ORDER_BTN'            => false,
     * @var 'ONE_CLICK_BUY'        => false,
     * @var 'QUESTION_BTN'         => false,
     * @var 'DISPLAY_COMPARE'      => false,
     * @var 'INFO_BTN_ICONS'       => false,
     * @var 'SHOW_COUNTER'         => true,
     * @var 'SHOW_BASKET_LINK'     => false,
     * @var 'RETURN'               => false,
     * @var 'JS_CLASS'             => false,
     * @var 'BASKET_URL'           => SITE_DIR.'cart/',
     * @var 'BTN_CLASS'            => 'btn-md btn-transparent-border',
     * @var 'BTN_IN_CART_CLASS'    => 'btn-md',
     * @var 'BTN_CLASS_MORE'       => '',
     * @var 'BTN_CALLBACK_CLASS'   => 'btn-sm btn-transparent-border',
     * @var 'BTN_OCB_CLASS'        => 'btn-sm btn-transparent',
     * @var 'TO_BASKET_LINK_CLASS' => 'btn-sm',
     * @var 'ORDER_FORM_ID'        => 'aspro_lite_order_product',
     * @var 'ITEM'                 => [],
     * @var 'PARAMS'               => [],
     * @var 'TOTAL_COUNT'          => 0,
     * @var 'SHOW_MORE'            => false,
     * @var 'CONFIG'               => []
     *
     * @return array [
     *               'HTML' => $basketHTML,
     *               'CAN_BUY' => can buy
     *               'ACTION' => $basketButton
     *               ]
     */
public static function getOptions($arOptions = [])
{
    $arAddToBasketOptions = self::getConfig();
    self::checkCatalogModule();

    $arDefaultOptions = array_merge(
        [
            'TYPE' => 'catalog-block',
            'WRAPPER' => false,
            'WRAPPER_CLASS' => '',
            'BASKET' => false,
            'DETAIL_PAGE' => false,
            'ORDER_BTN' => false,
            'ONE_CLICK_BUY' => false,
            'QUESTION_BTN' => false,
            'DISPLAY_COMPARE' => false,
            'INFO_BTN_ICONS' => false,
            'SHOW_COUNTER' => true,
            'RETURN' => false,
            'JS_CLASS' => false,
            'BASKET_URL' => SITE_DIR.'basket/',
            'ORDER_FORM_ID' => 'aspro_lite_order_product',
            'ONE_CLICK_BUY_FORM_ID' => 'aspro_lite_quick_buy',
            'ITEM' => [],
            'IS_OFFER' => false,
            'PARAMS' => [],
            'TOTAL_COUNT' => 0,
            'SHOW_MORE' => false,
            'TARGET' => '',
            'NOINDEX' => false,
            'CONFIG' => $arAddToBasketOptions,
            'JS_EVENT' => 'Y',
            'SHOW_NOTIFICATION' => 'Y',
        ],
        self::$btnClasses
    );
    $arConfig = array_merge($arDefaultOptions, $arOptions);

    if ($handler = SolutionFunctions::getCustomFunc(__FUNCTION__)) {
        return call_user_func_array($handler, [$arConfig]);
    }

    $arConfig['CAN_BUY'] = self::getCanBuy($arConfig);
    
    // УБИРАЕМ ПРОВЕРКУ ЦЕНЫ - всегда считаем что есть цена
    $arConfig['HAS_PRICE'] = true;

    $bOrderViewBasket = $arConfig['BASKET'] !== false && $arConfig['BASKET'] !== 'N' && $arConfig['BASKET'] !== 'false';

    $arConfig['ONE_CLICK_BUY'] = $arConfig['ONE_CLICK_BUY'] === true || $arConfig['ONE_CLICK_BUY'] === 'true';

    $basketButton = $buttonACTION = '';

    // ПРОСТАЯ ЛОГИКА - ВСЕГДА ПОКАЗЫВАЕМ КНОПКУ "В КОРЗИНУ"
    // независимо от наличия цены, количества и других условий
    if ($bOrderViewBasket) {
        $buttonACTION = 'ADD';
        $basketButton = self::getToCartButton($arConfig);
    } else {
        $buttonACTION = 'ORDER';
        $basketButton = self::getOrderButton($arConfig);
    }

    if (self::isShopWindowMode()) {
        $arInfo = self::showMarketUrl([
            'config' => $arConfig,
            'basketButton' => $basketButton,
            'buttonACTION' => $buttonACTION,
        ]);
        $basketButton = $arInfo['basketButton'];
        $buttonACTION = $arInfo['buttonACTION'];
        $arConfig = $arInfo['config'];
    }

    if ($basketButton) {?>
        <?ob_start(); ?>
            <?if ($arConfig['JS_CLASS']):?>
                <div class="<?=$arConfig['JS_CLASS']; ?>">
            <?endif; ?>
            <?if ($arConfig['WRAPPER']):?>
                <div class="footer-button btn-actions<?= $arConfig['INFO_BTN_ICONS'] ? ' btn-actions--with-icons' : ''; ?> <?=$arConfig['WRAPPER_CLASS']; ?>">
            <?endif; ?>
            <?=$basketButton; ?>
            <?if ($arConfig['WRAPPER']):?>
            </div>
            <?endif; ?>
            <?if ($arConfig['JS_CLASS']):?>
            </div>
            <?endif; ?>
        <?$basketButton = ob_get_contents();
        ob_end_clean(); ?>
    <?}

    $basketButton = trim($basketButton);

    return [
        'HTML' => $basketButton,
        'CAN_BUY' => $arConfig['CAN_BUY'],
        'ACTION' => $buttonACTION,
    ];
}

    public static function getToCartButton($arConfig)
    {
        $arModuleOptions = Solution::GetFrontParametrsValues(SITE_ID);
        $maxQuantity = $minPriceRangeQty = 0;

        $totalCount = $arConfig['TOTAL_COUNT'];
        $quantity = $ratio = ($arConfig['ITEM']['CATALOG_MEASURE_RATIO'] ?: 1);
        $bFloatRatio = is_double($ratio);

        if ($arConfig['ITEM']['CATALOG_QUANTITY_TRACE'] === 'Y') {
            if ($totalCount < $quantity) {
                $quantity = ($totalCount > $ratio ? $totalCount : $ratio);
            }

            if ($arConfig['ITEM']['CATALOG_CAN_BUY_ZERO'] !== 'Y') {
                $maxQuantity = $totalCount;
            }
        }

        $minQuantity = '';
        if(isset($arConfig['ITEM']['ITEM_PRICE_SELECTED'])) {
            $minQuantity = $arConfig['ITEM']['ITEM_PRICES'][$arConfig['ITEM']['ITEM_PRICE_SELECTED']]['MIN_QUANTITY'];
            if($quantity < $minQuantity) {
                $quantity = $minQuantity;
            }
        }

        $arItemProps = $arConfig['IS_OFFER'] ? ($arConfig['PARAMS']['OFFERS_CART_PROPERTIES'] ? implode(';', $arConfig['PARAMS']['OFFERS_CART_PROPERTIES']) : '') : ($arConfig['PARAMS']['PRODUCT_PROPERTIES'] ? implode(';', $arConfig['PARAMS']['PRODUCT_PROPERTIES']) : '');
        $addProp = $arConfig['PARAMS']['ADD_PROPERTIES_TO_BASKET'] === 'Y' ? 'Y' : 'N';
        $partProp = $arConfig['PARAMS']['PARTIAL_PRODUCT_PROPERTIES'] === 'Y' ? 'Y' : 'N';
        $emptyProp = $arConfig['ITEM']['PRODUCT_PROPERTIES'] ? 'N' : 'Y';
        $bUseQuantityDetail = $arConfig['IS_DETAIL'] && $arConfig['SHOW_BASKET_COUNTER'];
        $bUseQuantityList = $arConfig['IS_SECTION'] && $arConfig['SHOW_BASKET_COUNTER'];
        $bShowBasketCounter = $bUseQuantityDetail || $bUseQuantityList;
        $pageBasket = $arModuleOptions['BASKET_PAGE_URL'];

        // show "unsubscribe" button with "to cart" button in list of /personal/subscribe/ page
        $bShowUnsubscribe = $arConfig['PARAMS']['DISPLAY_UNSUBSCRIBE'] === 'Y' && $arConfig['ITEM']['CATALOG_SUBSCRIBE'] === 'Y';

        $html = '';
        ob_start();
        ?>
        <div class="buy_block btn-actions__inner">
            <?if ($arConfig['SHOW_COUNTER']):?>
                <div class="counter">
                    <div class="wrap">
                        <span class="minus ctrl bgtransition"></span>
                        <div class="input"><input type="text" value="<?=$quantity; ?>" class="count" maxlength="20" /></div>
                        <span class="plus ctrl bgtransition"></span>
                    </div>
                </div>
            <?endif; ?>
            <div class="buttons">
                <div class="line-block line-block--gap line-block--gap-12 line-block--align-normal flexbox--direction-column">
                    <div class="line-block__item<?=$bShowUnsubscribe ? '' : ' item-action-container'; ?>">

                        <?if ($bShowUnsubscribe):?>
                            <div class="line-block line-block--gap-8 line-block--flex-wrap flexbox--direction-row">
                                <div class="line-block__item item-action-container">
                        <?endif; ?>

                        <span class="item-action item-action--basket">
                            <span class="btn btn-default <?=$arConfig['BTN_CLASS']; ?> to_cart animate-load <?=$arConfig['JS_EVENT'] === 'Y' ? 'js-item-action' : 'js-offer-modal'; ?>" data-action="basket" data-id="<?=$arConfig['ITEM']['ID']; ?>" data-ratio="<?=$ratio; ?>" data-float_ratio="<?=$bFloatRatio; ?>" data-quantity="<?=$quantity; ?>"<?= $minQuantity && $minQuantity != $ratio ? ' data-min="'.$minQuantity.'"' : ''; ?><?= $maxQuantity ? ' data-max="'.$maxQuantity.'"' : ''; ?> data-bakset_div="bx_basket_div_<?=$arConfig['ITEM']['ID']; ?>" data-props="<?=$arItemProps; ?>" data-add_props="<?=$addProp; ?>" data-part_props="<?=$partProp; ?>" data-empty_props="<?=$emptyProp; ?>" data-offers="<?= $arConfig['IS_OFFER'] ? 'Y' : ''; ?>" title="<?=htmlspecialcharsbx($arConfig['CONFIG']['EXPRESSION_ADDTOBASKET_BUTTON']); ?>" data-title="<?=htmlspecialcharsbx($arConfig['CONFIG']['EXPRESSION_ADDTOBASKET_BUTTON']); ?>" data-title_added="<?=htmlspecialcharsbx($arConfig['CONFIG']['EXPRESSION_ADDEDTOBASKET_BUTTON']); ?>" data-notice="<?=$arConfig['SHOW_NOTIFICATION'] === 'Y'; ?>"><?=$arConfig['CONFIG']['EXPRESSION_ADDTOBASKET_BUTTON']; ?></span>
                        </span>

                        <?if ($bShowBasketCounter):?>
                            <div class="btn btn-default in_cart <?=$arConfig['BTN_IN_CART_CLASS'];?>">
                                <div class="counter js-ajax">
                                    <span class="counter__action counter__action--minus"<?= $minQuantity && $minQuantity != $ratio ? ' data-min="'.$minQuantity.'"' : ''; ?>></span>
                                    <div class="counter__count-wrapper">
                                        <input type="text" value="<?=$quantity; ?>" class="counter__count" maxlength="6">
                                    </div>
                                    <span class="counter__action counter__action--plus"<?= $maxQuantity ? ' data-max="'.$maxQuantity.'"' : ''; ?>></span>
                                </div>
                            </div>
                            <?if ($arConfig['SHOW_BASKET_LINK']):?>
                                <?=static::getBasketLink($arConfig);?>
                            <?endif; ?>
                        <?else:?>
                            <a href="<?=$pageBasket?>" class="btn btn-wide in_cart <?=$arConfig['BTN_IN_CART_CLASS'];?> btn-transparent-border has-ripple">
                                <?=$arConfig['CONFIG']['EXPRESSION_ADDEDTOBASKET_BUTTON'];?>
                            </a>
                        <?endif; ?>

                        <?if ($bShowUnsubscribe):?>
                                </div>
                                <?=self::getSubsribeButton($arConfig, true); ?>
                            </div>
                        <?endif; ?>

                    </div>
                    <?=self::getOneClickBuyButton($arConfig); ?>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function getOrderButton($arConfig)
    {?>
        <?$html = ''; ?>
        <?ob_start(); ?>
            <div class="buy_block btn-actions__inner">
                <div class="buttons">
                    <div class="line-block line-block--gap line-block--gap-12 line-block--align-normal flexbox--direction-column">
                        <div class="line-block__item">
                            <span class="btn btn-default <?=$arConfig['BTN_ORDER_CLASS']; ?> animate-load" data-event="jqm" data-param-id="<?=Solution::getFormID($arConfig['ORDER_FORM_ID']); ?>" data-autoload-product="<?=Solution::formatJsName($arConfig['ITEM']['NAME']); ?>" data-autoload-service="<?=Solution::formatJsName($arConfig['ITEM']['NAME']); ?>" data-autoload-project="<?=Solution::formatJsName($arConfig['ITEM']['NAME']); ?>" data-autoload-news="<?=Solution::formatJsName($arConfig['ITEM']['NAME']); ?>" data-autoload-sale="<?=Solution::formatJsName($arConfig['ITEM']['NAME']); ?>" data-name="order_product_<?=$arConfig['ITEM']['ID']; ?>">
                                <?=$arConfig['CONFIG']['EXPRESSION_ORDER_BUTTON']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function showOneClickBuyButton($arConfig)
    {
        $arAddToBasketOptions = self::getConfig();

        $arDefaultOptions = array_merge(
            [
                'ONE_CLICK_BUY' => $arConfig['ONE_CLICK_BUY'],
                'BTN_OCB_CLASS' => 'btn-wide btn-transparent btn-md btn-ocb btn-lg',
                'ONE_CLICK_BUY_FORM_ID' => 'aspro_lite_quick_buy',
                'CONFIG' => [
                    'EXPRESSION_ONE_CLICK_BUY' => $arAddToBasketOptions['EXPRESSION_ONE_CLICK_BUY'],
                ],
            ]
        );
        $arConfigOcb = array_merge($arDefaultOptions, $arConfig);

        $html = '';
        ob_start(); ?>
            <div class="js-config-ocb js-replace-ocb" data-ocb-config='<?= str_replace('\'', '"', \CUtil::PhpToJSObject($arConfig, false, true)); ?>'>
                <?php if ($arConfig['ONE_CLICK_BUY'] && $arConfig['ONE_CLICK_BUY_MODULE'] !== 'false') {
                    echo self::getOneClickBuyButton($arConfigOcb);
                } ?>
            </div>
            <?php $html = ob_get_contents();
        ob_end_clean();

        return $html; ?>
    <?}

    public static function getOneClickBuyButton($arConfig)
    {
        $html = '';
        ob_start();
        ?>
        <?if ($arConfig['ONE_CLICK_BUY']):?>
            <div class="line-block__item">
                <span
                    class="btn btn-default <?=$arConfig['BTN_OCB_CLASS']; ?> animate-load"
                    data-event="jqm"
                    data-name="ocb"
                    <?if(Solution::isSaleMode()):?>
                        data-param-form_id="ocb"
                    <?else:?>
                        data-param-id="<?=Solution::getFormID($arConfig['ONE_CLICK_BUY_FORM_ID']); ?>"
                        data-autoload-product_name="<?=Solution::formatJsName($arConfig['ITEM']['NAME']); ?>"
                    <?endif; ?>
                    >
                    <?=$arConfig['CONFIG']['EXPRESSION_ONE_CLICK_BUY']; ?>
                </span>
            </div>
        <?endif; ?>
        <?php
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function getSubsribeButton($arConfig, $bExt = false)
    {
        static $bUserAuthorized;

        if (!isset($bUserAuthorized)) {
            $bUserAuthorized = $GLOBALS['USER']->IsAuthorized();
        }
        ?>
        <?$html = ''; ?>
        <?ob_start(); ?>
            <?if (!$bExt):?>
                <div class="buy_block btn-actions__inner">
                    <div class="buttons">
                        <div class="line-block line-block--gap line-block--gap-12 line-block--align-normal flexbox--direction-column">
            <?endif; ?>
                            <div class="line-block__item">
                                <span class="item-action item-action--subscribe">
                                    <span
                                        class="btn btn-transparent <?=$arConfig['BTN_CLASS_SUBSCRIBE']; ?> animate-load <?= $bUserAuthorized ? 'js-item-action' : 'auth'; ?>" <?= $bUserAuthorized ? 'data-action="subscribe" data-id="'.$arConfig['ITEM']['ID'].'" title="'.htmlspecialcharsbx($arConfig['CONFIG']['EXPRESSION_SUBSCRIBE_BUTTON']).'" data-title="'.htmlspecialcharsbx($arConfig['CONFIG']['EXPRESSION_SUBSCRIBE_BUTTON']).'" data-title_added="'.htmlspecialcharsbx($arConfig['CONFIG']['EXPRESSION_SUBSCRIBED_BUTTON']).'"' : 'data-event="jqm" data-name="subscribe" data-param-form_id="subscribe" data-param-id="'.$arConfig['ITEM']['ID'].'" data-item="'.$arConfig['ITEM']['ID'].'"'; ?>>
                                        <?=$arConfig['CONFIG']['EXPRESSION_SUBSCRIBE_BUTTON']; ?>
                                    </span>
                                </span>
                            </div>
            <?if (!$bExt):?>
                        </div>
                    </div>
                </div>
            <?endif; ?>
        <?$html = ob_get_contents();
        ob_end_clean(); ?>

        <?return $html;
    }

    public static function getMoreButton($arConfig)
    {
        if (!$arConfig['CONFIG']) {
            $arConfig['CONFIG'] = self::getConfig();
        }
        if (!isset($arConfig['BTN_CLASS_MORE'])) {
            $arConfig += self::$btnClasses;
        }

        $buttonClass = ['btn btn-default btn-actions__inner btn-wide js-replace-more'];
        if ($arConfig['BTN_CLASS_MORE']) {
            $buttonClass[] = $arConfig['BTN_CLASS_MORE'];
        }
        if ($arConfig['JS_EVENT'] !== 'Y') {
            $buttonClass[] = 'js-offer-modal';
        }

        $buttonClass = SolutionUtils::implodeClasses($buttonClass);
        ?>
        <?$html = ''; ?>
        <?ob_start(); ?>
            <?if ($arConfig['NOINDEX']):?>
                <!--noindex-->
            <?endif; ?>
            <?if ($arConfig['JS_EVENT'] !== 'N'):?>
                <a href="<?=$arConfig['ITEM']['DETAIL_PAGE_URL']; ?>" class="<?=$buttonClass; ?>" <?=$arConfig['TARGET']; ?>>
                    <span class="showcase linecamp-2"><?=$arConfig['CONFIG']['EXPRESSION_READ_MORE_OFFERS']; ?></span>
                </a>
            <?else:?>
                <button type="button" class="<?=$buttonClass; ?>" data-action="basket" data-id="<?=$arConfig['ITEM']['ID']; ?>">
                    <span class="showcase linecamp-2"><?=$arConfig['CONFIG']['EXPRESSION_ADDTOBASKET_BUTTON']; ?></span>
                </button>
            <?endif; ?>
            <?if ($arConfig['NOINDEX']):?>
                <!--/noindex-->
            <?endif; ?>
        <?$html = ob_get_contents();
        ob_end_clean(); ?>

        <?return $html;
    }

    public static function getBasketLink(array $arConfig)
    {
        $arAddToBasketOptions = self::getConfig();
        $buttonClass = ['to_cart_link btn btn-default', $arConfig['TO_BASKET_LINK_CLASS']];
        $buttonClass = SolutionUtils::implodeClasses($buttonClass);
        ?>
        <?ob_start(); ?>
            <a class="<?=$buttonClass; ?>" href="<?=$arConfig['BASKET_URL']; ?>" title="<?=$arAddToBasketOptions['EXPRESSION_ADDTOBASKET_BUTTON']; ?>">
                <?=Solution::showSpriteIconSvg(SITE_TEMPLATE_PATH.'/images/svg/arrows.svg#right-pointer', 'wrapper stroke-use-fff', ['HEIGHT' => 13, 'WIDTH' => 14]); ?>
            </a>
        <?php
        $html = trim(ob_get_clean());

        return $html;
    }

    public static function getAnchorButton($arProps)
    {
        SolutionFunctions::showBlockHtml([
            'TYPE' => 'CATALOG',
            'FILE' => 'catalog/sku2_anchor_button.php',
            'PARAMS' => $arProps,
        ]);
    }

    public static function getCanBuy($arOptions)
    {
        $arItem = $arOptions['ITEM'];
        $totalCount = $arOptions['TOTAL_COUNT'];
        $arParams = $arOptions['PARAMS'];

        if (self::$catalogInclude) {
            $useRegion = $arParams['USE_REGION'] === 'Y';

            if (!array_key_exists('CAN_BUY', $arItem)) {
                $arItem['CAN_BUY'] = (
                    $totalCount
                    || $arItem['CATALOG_QUANTITY_TRACE'] === 'N'
                    || (
                        $arItem['CATALOG_QUANTITY_TRACE'] === 'Y'
                        && $arItem['CATALOG_CAN_BUY_ZERO'] === 'Y'
                    )
                );
            }

            $canBuy = $arItem['CAN_BUY'];

            if ($useRegion && $arParams['STORES']) {
                $canBuy = (
                    ($totalCount && ($arItem['OFFERS'] || $arItem['CAN_BUY']))
                    || (
                        !$totalCount
                        && (
                            $arItem['CATALOG_QUANTITY_TRACE'] === 'N'
                            || (
                                $arItem['CATALOG_QUANTITY_TRACE'] === 'Y'
                                && $arItem['CATALOG_CAN_BUY_ZERO'] === 'Y'
                            )
                        )
                    )
                );
            }

            return $canBuy;
        }

        return (
            isset($arItem['DISPLAY_PROPERTIES']) && isset($arItem['DISPLAY_PROPERTIES']['FORM_ORDER'])
                ? ($arItem['DISPLAY_PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES')
                : ($arItem['PROPERTIES']['FORM_ORDER']['VALUE_XML_ID'] == 'YES')
        );
    }

    public static function checkAllowDelivery($summ, $currency)
    {
        $ERROR = false;
        $min_price = Option::get(Solution::moduleID, 'MIN_ORDER_PRICE', 1000, SITE_ID);
        $error_text = '';
        if ($summ < $min_price) {
            $ERROR = true;
            $error_text = Option::get(Solution::moduleID, 'MIN_ORDER_PRICE_TEXT', GetMessage('MIN_ORDER_PRICE_TEXT_EXAMPLE'));
            $error_text = str_replace('#PRICE#', SaleFormatCurrency($min_price, $currency), $error_text);
            if($currency) {
                $error_text = str_replace('#PRICE#', SaleFormatCurrency($min_price, $currency), $error_text);
            } else {
                $error_text = str_replace('#PRICE#', $min_price, $error_text);
            }
        }

        return [
            'ERROR' => $ERROR,
            'TEXT' => $error_text,
        ];
    }

    public static function isShopWindowMode()
    {
        return Solution::GetFrontParametrValue('SHOP_WINDOW_MODE') === 'Y';
    }

    public static function showMarketUrl($config)
    {
        $arConfig = $config['config'];
        $basketButton = $config['basketButton'];
        $buttonACTION = $config['buttonACTION'];

        $buttonACTION = 'MORE';
        $basketButton = self::getMoreButton($arConfig);

        if ($arConfig['DETAIL_PAGE']) {
            $replacedUrl = (isset($arConfig['ITEM']['PROPERTIES']['OZON_FBS']['VALUE']) ? $arConfig['ITEM']['PROPERTIES']['OZON_FBS']['VALUE'] : '');
            if ($replacedUrl) {
                $arConfig['ITEM']['DETAIL_PAGE_URL'] = 'https://www.ozon.ru/product/'.$replacedUrl;
                $arConfig['ITEM']['DETAIL_PAGE_URL'] .= '?oos_search=false';
                $arConfig['TARGET'] = 'target="_blank" rel="nofollow"';
                $arConfig['NOINDEX'] = true;
                $arConfig['CONFIG']['EXPRESSION_READ_MORE_OFFERS'] = strip_tags(Option::get(Solution::moduleID, 'EXPRESSION_MORE_TEXT', GetMessage('EXPRESSION_MORE_TEXT_VALUE'), SITE_ID));

                $buttonACTION = 'MORE';
                $basketButton = self::getMoreButton($arConfig);
            } else {
                $buttonACTION = 'NONE';
                $basketButton = '';
            }
        }

        return [
            'basketButton' => $basketButton,
            'buttonACTION' => $buttonACTION,
            'config' => $arConfig,
        ];
    }

    protected static function isBuyButtonDisabledInCorpMode(bool $canBuy): bool
    {
        return !static::$catalogInclude && !$canBuy;
    }
}
?>
