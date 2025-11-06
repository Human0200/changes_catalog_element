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
$quantity = (float)$request->getPost('quantity');

if ($action !== 'ADD2BASKET' || $productId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Неверные параметры.']);
    die();
}

if ($quantity <= 0) {
    $quantity = 1;
}

// Можно учитывать складские остатки при желании:
// $product = \CCatalogProduct::GetByID($productId);
// if ($product && isset($product['QUANTITY']) && $product['QUANTITY'] < $quantity) {
//     $quantity = (float)$product['QUANTITY'];
// }

global $USER;

// === ПРОСТОЙ способ (устойчивый): legacy API ===
$result = Add2BasketByProductID($productId, $quantity, [], []);

// === Альтернатива (D7) — если нужно:
// $result = \Bitrix\Catalog\Product\Basket::addProduct([
//     'PRODUCT_ID' => $productId,
//     'QUANTITY'   => $quantity,
//     'CHECK_PERMISSIONS' => 'N',
// ]);

if ($result) {
    echo json_encode(['status' => 'success']);
} else {
    global $APPLICATION;
    $ex = $APPLICATION->GetException();
    $msg = $ex ? $ex->GetString() : 'Не удалось добавить товар в корзину';
    echo json_encode(['status' => 'error', 'message' => $msg]);
}

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php';
