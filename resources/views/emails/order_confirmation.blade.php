<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Thank you for your order!</h1>
    <p>Order ID: {{ $order->id }}</p>
    <p>Total Amount: ${{ number_format($order->total_amount, 2) }}</p>
    <h3>Order Details:</h3>
    <ul>
        @foreach ($order->orderItems as $item)
            <li>{{ $item->quantity }} x {{ $item->product->name }} @ ${{ number_format($item->price, 2) }}</li>
        @endforeach
    </ul>
    <p>We will notify you when your order is shipped.</p>
</body>
</html>
