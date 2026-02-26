<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'ipaexg', sans-serif;
            font-size: 12px;
            color: #333;
        }

        h1 {
            font-size: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 24px;
        }

        .info-table td {
            padding: 4px 8px;
        }

        .info-table .label {
            color: #666;
            width: 100px;
        }

        table.products {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        table.products th {
            background-color: #e5e7eb;
            border: 1px solid #9ca3af;
            padding: 6px 12px;
            font-size: 11px;
            text-align: center;
        }

        table.products td {
            border: 1px solid #9ca3af;
            padding: 6px 12px;
            text-align: center;
        }

        .total-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 12px 16px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <h1>注文明細書</h1>

    <table class="info-table">
        <tr>
            <td class="label">注文ID：</td>
            <td>{{ $order->id }}</td>
        </tr>
        <tr>
            <td class="label">受注日：</td>
            <td>{{ $order->orderday->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="label">顧客名：</td>
            <td>{{ $order->customer->name }}</td>
        </tr>
    </table>

    <table class="products">
        <thead>
            <tr>
                <th>商品名</th>
                <th>価格</th>
                <th>税率</th>
                <th>注文数</th>
                <th>小計（税込）</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ number_format($product->price) }}円</td>
                    <td>{{ $product->tax }}%</td>
                    <td>{{ $product->pivot->quantity }}</td>
                    <td>{{ number_format(floor($product->price * $product->pivot->quantity * (1 + $product->tax / 100))) }}円
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        合計金額（税込）： {{ number_format($order->totalAmount) }}円
    </div>
</body>

</html>
