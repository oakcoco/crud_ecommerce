<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id  = (int) $_POST['item_id'];
    $quantity = (int) $_POST['quantity'];

    // Get item details
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item   = $result->fetch_assoc();
    $stmt->close();

    // FIX: Only proceed (and show success) when item exists and requested qty is valid
    if ($item && $quantity > 0 && $quantity <= $item['quantity']) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if item already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $item_id) {
                $cart_item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        unset($cart_item); // FIX: break reference after foreach-by-reference

        if (!$found) {
            $_SESSION['cart'][] = [
                'id'       => $item['id'],
                'name'     => $item['name'],
                'price'    => $item['price'],
                'quantity' => $quantity,
                'image'    => $item['image']
            ];
        }

        // FIX: Message only set on actual success
        $_SESSION['message'] = "Item added to cart successfully!";
    } else {
        // FIX: Provide feedback when add fails (item not found or qty exceeds stock)
        $_SESSION['message'] = "Could not add item to cart. Please check the available stock.";
    }
}

header("Location: index.php");
exit();
?>
