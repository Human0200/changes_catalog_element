<?php
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('STOP_STATISTICS', true);
define('PUBLIC_AJAX_MODE', true);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('catalog');

header('Content-Type: application/json; charset=UTF-8');

$request = \Bitrix\Main\Context::getCurrent()->getRequest();

if (!check_bitrix_sessid()) {
    echo json_encode(['status' => 'error', 'message' => 'Сессия истекла. Обновите страницу.']);
    die();
}

$action   = (string)$request->getPost('action');
$productId= (int)$request->getPost('id');

if ($action !== 'CHECK_BASKET' || $productId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Неверные параметры.']);
    die();
}

// Получаем корзину
$basket = \Bitrix\Sale\Basket::loadItemsForFUser(
    \Bitrix\Sale\Fuser::getId(),
    \Bitrix\Main\Context::getCurrent()->getSite()
);

$inBasket = false;
$quantity = 0;

foreach ($basket as $basketItem) {
    if ($basketItem->getProductId() == $productId) {
        $inBasket = true;
        $quantity = (float)$basketItem->getQuantity();
        break;
    }
}

echo json_encode([
    'status' => 'success',
    'inBasket' => $inBasket,
    'quantity' => $quantity
]);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php';