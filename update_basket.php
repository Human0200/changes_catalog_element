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

if ($action !== 'UPDATE_BASKET' || $productId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Неверные параметры.']);
    die();
}

if ($quantity <= 0) {
    $quantity = 1;
}

// Проверяем максимальное количество (можно добавить проверку остатков)
$maxQuantity = 999; // Максимальное количество
if ($quantity > $maxQuantity) {
    $quantity = $maxQuantity;
}

global $USER;

// Находим товар в корзине и обновляем количество
$basket = \Bitrix\Sale\Basket::loadItemsForFUser(
    \Bitrix\Sale\Fuser::getId(),
    \Bitrix\Main\Context::getCurrent()->getSite()
);

$found = false;
foreach ($basket as $basketItem) {
    if ($basketItem->getProductId() == $productId) {
        // Проверяем доступное количество
        $product = \CCatalogProduct::GetByID($productId);
        if ($product && isset($product['QUANTITY']) && $product['QUANTITY'] < $quantity) {
            $quantity = (float)$product['QUANTITY'];
        }
        
        $basketItem->setField('QUANTITY', $quantity);
        $found = true;
        break;
    }
}

if ($found) {
    $saveResult = $basket->save();
    if ($saveResult->isSuccess()) {
        echo json_encode(['status' => 'success']);
    } else {
        $errors = $saveResult->getErrorMessages();
        echo json_encode(['status' => 'error', 'message' => implode(', ', $errors)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Товар не найден в корзине']);
}

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php';