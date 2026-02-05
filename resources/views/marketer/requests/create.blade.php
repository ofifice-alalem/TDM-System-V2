@extends('layouts.app')

@section('title', 'طلب بضاعة جديد')

@section('content')
<h2 class="mb-4">طلب بضاعة جديد</h2>

<div class="card">
    <div class="card-body">
        <form action="{{ route('marketer.requests.store') }}" method="POST">
            @csrf
            
            <div id="items-container">
                <div class="item-row mb-3 border p-3 rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">المنتج</label>
                            <select name="items[0][product_id]" class="form-select product-select" required>
                                <option value="">اختر المنتج</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">{{ $product->name }} - {{ $product->current_price }} دينار (متوفر: {{ $product->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">الكمية</label>
                            <input type="number" name="items[0][quantity]" class="form-control quantity-input" min="1" max="" placeholder="اختر المنتج أولاً" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-item" style="display:none;">حذف</button>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary mb-3" id="add-item">إضافة منتج</button>

            <div class="mb-3">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">إرسال الطلب</button>
            <a href="{{ route('marketer.requests.index') }}" class="btn btn-secondary">إلغاء</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;

// Update available products in all dropdowns
function updateAvailableProducts() {
    const selectedProducts = [];
    document.querySelectorAll('.product-select').forEach(select => {
        if (select.value) {
            selectedProducts.push(select.value);
        }
    });
    
    document.querySelectorAll('.product-select').forEach(select => {
        const currentValue = select.value;
        Array.from(select.options).forEach(option => {
            if (option.value && option.value !== currentValue) {
                option.style.display = selectedProducts.includes(option.value) ? 'none' : 'block';
            }
        });
    });
}

// Update max quantity when product is selected
function updateMaxQuantity(selectElement) {
    const row = selectElement.closest('.item-row');
    const quantityInput = row.querySelector('.quantity-input');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const stock = selectedOption?.dataset.stock || 0;
    
    quantityInput.max = stock;
    quantityInput.value = '';
    quantityInput.placeholder = `الحد الأقصى: ${stock}`;
    quantityInput.disabled = !stock || stock == 0;
    
    updateAvailableProducts();
}

// Prevent entering value greater than max
function enforceMaxValue(input) {
    const max = parseInt(input.max);
    const value = parseInt(input.value);
    
    if (value > max) {
        input.value = max;
    }
}

// Initialize first row
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-select').forEach(select => {
        select.addEventListener('change', function() {
            updateMaxQuantity(this);
        });
    });
    
    // Attach input validation to all quantity inputs
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            enforceMaxValue(this);
        });
    });
});

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const newItem = container.firstElementChild.cloneNode(true);
    
    newItem.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        el.value = '';
        if(el.classList.contains('quantity-input')) {
            el.max = '';
            el.placeholder = 'اختر المنتج أولاً';
            el.disabled = false;
        }
    });
    
    newItem.querySelector('.remove-item').style.display = 'block';
    container.appendChild(newItem);
    
    // Attach events to new elements
    newItem.querySelector('.product-select').addEventListener('change', function() {
        updateMaxQuantity(this);
    });
    
    newItem.querySelector('.quantity-input').addEventListener('input', function() {
        enforceMaxValue(this);
    });
    
    updateAvailableProducts();
    itemIndex++;
});

document.addEventListener('click', function(e) {
    if(e.target.classList.contains('remove-item')) {
        e.target.closest('.item-row').remove();
        updateAvailableProducts();
    }
});
</script>
@endpush
