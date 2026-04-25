<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id  = (int) $_POST['item_id'];
    $quantity = (int) $_POST['quantity'];

    // get item details
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item   = $result->fetch_assoc();
    $stmt->close();

    // validation for item existence and quantity
    if ($item && $quantity > 0 && $quantity <= $item['quantity']) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // check item already in cart, if so update quantity
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $item_id) {
                $cart_item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        unset($cart_item); // fix: break reference after foreach-by-reference

        if (!$found) {
            $_SESSION['cart'][] = [
                'id'       => $item['id'],
                'name'     => $item['name'],
                'price'    => $item['price'],
                'quantity' => $quantity,
                'image'    => $item['image']
            ];
        }

        // alam mo pag success ka e mararamdaman mo siya e
        $_SESSION['message'] = "Item added to cart successfully!";
    } else {
        // provide feedback if invalid yung stock and quantity
        $_SESSION['message'] = "Could not add item to cart. Please check the available stock.";
    }
}

header("Location: index.php#products");
exit();
?>
