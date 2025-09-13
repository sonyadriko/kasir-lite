<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->invoice_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            color: #000;
            background: #fff;
        }
        
        .receipt {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .outlet-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .outlet-address {
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .invoice-info {
            margin-bottom: 10px;
        }
        
        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .items {
            margin-bottom: 10px;
        }
        
        .item {
            margin-bottom: 5px;
        }
        
        .item-name {
            font-weight: bold;
            margin-bottom: 1px;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        
        .separator {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .totals {
            margin-bottom: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 3px;
        }
        
        .payments {
            margin-bottom: 10px;
        }
        
        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
        
        /* Print styles for thermal receipt */
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
                padding: 0;
            }
            
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                font-family: 'Courier New', monospace;
            }
            
            body {
                margin: 0;
                padding: 0;
                font-size: 9px;
                line-height: 1.1;
                background: white;
            }
            
            .receipt {
                width: 58mm;
                max-width: 58mm;
                padding: 1mm;
                margin: 0;
                box-shadow: none;
                background: white;
            }
            
            .header {
                text-align: center;
                margin-bottom: 2mm;
                border-bottom: 1px dashed #000;
                padding-bottom: 1mm;
            }
            
            .outlet-name {
                font-size: 12px;
                font-weight: bold;
                line-height: 1;
            }
            
            .outlet-address {
                font-size: 8px;
                line-height: 1;
                margin-bottom: 1mm;
            }
            
            .invoice-info {
                margin-bottom: 2mm;
                font-size: 8px;
            }
            
            .invoice-row {
                display: flex;
                justify-content: space-between;
                line-height: 1;
                margin-bottom: 1px;
            }
            
            .items {
                margin-bottom: 2mm;
            }
            
            .item {
                margin-bottom: 2mm;
            }
            
            .item-name {
                font-weight: bold;
                font-size: 9px;
                line-height: 1;
                margin-bottom: 1px;
            }
            
            .item-details {
                display: flex;
                justify-content: space-between;
                font-size: 8px;
                line-height: 1;
            }
            
            .separator {
                border-top: 1px dashed #000;
                margin: 2mm 0 1mm 0;
            }
            
            .totals {
                margin-bottom: 2mm;
            }
            
            .total-row {
                display: flex;
                justify-content: space-between;
                font-size: 8px;
                line-height: 1;
                margin-bottom: 1px;
            }
            
            .total-row.final {
                font-size: 10px;
                font-weight: bold;
                border-top: 1px solid #000;
                padding-top: 1mm;
                margin-top: 1mm;
            }
            
            .payments {
                margin-bottom: 2mm;
            }
            
            .payment-row {
                display: flex;
                justify-content: space-between;
                font-size: 8px;
                line-height: 1;
                margin-bottom: 1px;
            }
            
            .footer {
                text-align: center;
                font-size: 7px;
                border-top: 1px dashed #000;
                padding-top: 1mm;
                line-height: 1.1;
            }
            
            /* Hide print button */
            .print-btn {
                display: none !important;
            }
        }
        
        /* Hide elements when printing */
        @media print {
            button, .no-print {
                display: none !important;
            }
        }
        
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        /* Dynamic size classes */
        .receipt.size-48mm {
            width: 48mm;
            max-width: 48mm;
            font-size: 8px;
        }
        
        .receipt.size-58mm {
            width: 58mm;
            max-width: 58mm;
            font-size: 9px;
        }
        
        .receipt.size-80mm {
            width: 80mm;
            max-width: 80mm;
            font-size: 11px;
        }
        
        .receipt.size-48mm .outlet-name { font-size: 10px; }
        .receipt.size-48mm .item-name { font-size: 8px; }
        .receipt.size-48mm .item-details { font-size: 7px; }
        .receipt.size-48mm .total-row.final { font-size: 9px; }
        .receipt.size-48mm .footer { font-size: 6px; }
        
        .receipt.size-58mm .outlet-name { font-size: 12px; }
        .receipt.size-58mm .item-name { font-size: 9px; }
        .receipt.size-58mm .item-details { font-size: 8px; }
        .receipt.size-58mm .total-row.final { font-size: 10px; }
        .receipt.size-58mm .footer { font-size: 7px; }
        
        .receipt.size-80mm .outlet-name { font-size: 14px; }
        .receipt.size-80mm .item-name { font-size: 11px; }
        .receipt.size-80mm .item-details { font-size: 10px; }
        .receipt.size-80mm .total-row.final { font-size: 13px; }
        .receipt.size-80mm .footer { font-size: 9px; }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 10px; right: 10px; display: flex; gap: 10px; z-index: 1000;">
        <select id="receipt-size" onchange="changeReceiptSize()" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <option value="80mm">80mm (Normal)</option>
            <option value="58mm" selected>58mm (Thermal)</option>
            <option value="48mm">48mm (Mini)</option>
        </select>
        <button class="print-btn" onclick="printReceipt()" 
                style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            Print Receipt
        </button>
    </div>
    
    <div class="receipt size-58mm">
        <!-- Header -->
        <div class="header">
            <div class="outlet-name">{{ $sale->outlet ? $sale->outlet->name : 'Unknown Outlet' }}</div>
            @if($sale->outlet && $sale->outlet->address)
                <div class="outlet-address">{{ $sale->outlet->address }}</div>
            @endif
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-row">
                <span>Invoice:</span>
                <span>{{ $sale->invoice_no }}</span>
            </div>
            <div class="invoice-row">
                <span>Date:</span>
                <span>{{ $sale->sold_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="invoice-row">
                <span>Cashier:</span>
                <span>{{ $sale->cashier ? $sale->cashier->name : 'Unknown Cashier' }}</span>
            </div>
        </div>
        
        <!-- Items -->
        <div class="separator"></div>
        <div class="items">
            @foreach($sale->items as $item)
                <div class="item">
                    <div class="item-name">{{ $item->name_snapshot }}</div>
                    <div class="item-details">
                        <span>{{ number_format($item->qty, 0) }} x Rp {{ number_format($item->price, 0) }}</span>
                        <span>Rp {{ number_format($item->total, 0) }}</span>
                    </div>
                    @if($item->discount_amount > 0)
                        <div class="item-details" style="font-size: 10px; color: #666;">
                            <span>Discount:</span>
                            <span>-Rp {{ number_format($item->discount_amount, 0) }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        <!-- Totals -->
        <div class="separator"></div>
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($sale->subtotal, 0) }}</span>
            </div>
            
            @if($sale->discount_amount > 0)
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-Rp {{ number_format($sale->discount_amount, 0) }}</span>
                </div>
            @endif
            
            @if($sale->tax_amount > 0)
                <div class="total-row">
                    <span>Tax:</span>
                    <span>Rp {{ number_format($sale->tax_amount, 0) }}</span>
                </div>
            @endif
            
            @if($sale->rounding != 0)
                <div class="total-row">
                    <span>Rounding:</span>
                    <span>{{ $sale->rounding > 0 ? '+' : '' }}Rp {{ number_format($sale->rounding, 0) }}</span>
                </div>
            @endif
            
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($sale->total, 0) }}</span>
            </div>
        </div>
        
        <!-- Payments -->
        <div class="payments">
            @foreach($sale->payments as $payment)
                <div class="payment-row">
                    <span>{{ $payment->method }}:</span>
                    <span>Rp {{ number_format($payment->amount, 0) }}</span>
                </div>
            @endforeach
            
            @if($sale->change_amount > 0)
                <div class="payment-row" style="font-weight: bold;">
                    <span>CHANGE:</span>
                    <span>Rp {{ number_format($sale->change_amount, 0) }}</span>
                </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            @if($sale->note)
                <div style="margin-bottom: 5px;">{{ $sale->note }}</div>
            @endif
            <div>Thank you for your purchase!</div>
            <div>{{ now()->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>
    
    <script>
        // Auto-focus for immediate printing
        window.addEventListener('load', function() {
            const printBtn = document.querySelector('.print-btn');
            if (printBtn) {
                printBtn.focus();
            }
        });
        
        // Print on Ctrl/Cmd+P or Enter when button is focused
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printReceipt();
            } else if (e.key === 'Enter' && document.activeElement.classList.contains('print-btn')) {
                e.preventDefault();
                printReceipt();
            }
        });
        
        // Print function with error handling
        function printReceipt() {
            try {
                window.print();
            } catch (error) {
                console.error('Print failed:', error);
                alert('Failed to print. Please check your printer settings.');
            }
        }
        
        // Auto-print functionality (can be enabled by adding ?autoprint=1 to URL)
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(function() {
                    printReceipt();
                }, 500); // Small delay to ensure page is fully loaded
            }
            
            // Initialize receipt size
            const sizeSelect = document.getElementById('receipt-size');
            updatePrintStyles(sizeSelect.value);
        });
        
        // Change receipt size dynamically
        function changeReceiptSize() {
            const sizeSelect = document.getElementById('receipt-size');
            const receipt = document.querySelector('.receipt');
            const size = sizeSelect.value;
            
            // Remove existing size classes
            receipt.classList.remove('size-80mm', 'size-58mm', 'size-48mm');
            
            // Add new size class
            receipt.classList.add('size-' + size);
            
            // Update print styles dynamically
            updatePrintStyles(size);
        }
        
        function updatePrintStyles(size) {
            // Remove existing print style
            const existingStyle = document.getElementById('dynamic-print-style');
            if (existingStyle) {
                existingStyle.remove();
            }
            
            // Create new print style
            const style = document.createElement('style');
            style.id = 'dynamic-print-style';
            
            let css = '';
            if (size === '48mm') {
                css = `
                    @media print {
                        @page { size: 48mm auto; margin: 0; }
                        .receipt { width: 48mm; max-width: 48mm; padding: 0.5mm; }
                        body { font-size: 8px; }
                        .outlet-name { font-size: 10px; }
                        .item-name { font-size: 8px; }
                        .item-details { font-size: 7px; }
                        .total-row.final { font-size: 9px; }
                        .footer { font-size: 6px; }
                    }
                `;
            } else if (size === '80mm') {
                css = `
                    @media print {
                        @page { size: 80mm auto; margin: 0; }
                        .receipt { width: 80mm; max-width: 80mm; padding: 2mm; }
                        body { font-size: 11px; }
                        .outlet-name { font-size: 14px; }
                        .item-name { font-size: 11px; }
                        .item-details { font-size: 10px; }
                        .total-row.final { font-size: 13px; }
                        .footer { font-size: 9px; }
                    }
                `;
            }
            
            style.textContent = css;
            document.head.appendChild(style);
        }
    </script>
</body>
</html>