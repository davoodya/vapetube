class Cart {
    constructor() {
        // اینجا اطلاعات سبد خرید در حافظه موقت (localStorage) ذخیره می‌شود
        this.cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    }

    // افزودن آیتم به سبد خرید
    addItem(productId, quantity = 1) {
        const existingItem = this.cartItems.find(item => item.product_id === productId);

        if (existingItem) {
            // اگر محصول در سبد خرید وجود داشته باشد، فقط تعداد آن را افزایش می‌دهیم
            existingItem.quantity += quantity;
            existingItem.total_price = existingItem.quantity * existingItem.price;
        } else {
            // اگر محصول در سبد خرید نباشد، آن را اضافه می‌کنیم
            const newItem = {
                product_id: productId,
                name: "Vape Mod", // نام محصول، باید از دیتابیس یا API گرفته شود
                price: 250000, // قیمت محصول، باید از دیتابیس یا API گرفته شود
                quantity: quantity,
                total_price: 250000 * quantity // قیمت کل محصول
            };
            this.cartItems.push(newItem);
        }

        // ذخیره سبد خرید در localStorage
        this.saveCartToLocalStorage();

        // به‌روزرسانی UI سبد خرید
        this.updateCartUI();
    }

    // حذف آیتم از سبد خرید
    removeItem(productId) {
        this.cartItems = this.cartItems.filter(item => item.product_id !== productId);

        // ذخیره تغییرات در localStorage
        this.saveCartToLocalStorage();

        // به‌روزرسانی UI سبد خرید
        this.updateCartUI();
    }

    // به‌روزرسانی تعداد محصول در سبد خرید
    updateItem(productId, quantity) {
        const item = this.cartItems.find(item => item.product_id === productId);
        if (item) {
            item.quantity = quantity;
            item.total_price = item.quantity * item.price;

            // ذخیره تغییرات در localStorage
            this.saveCartToLocalStorage();

            // به‌روزرسانی UI سبد خرید
            this.updateCartUI();
        }
    }

    // ذخیره‌سازی سبد خرید در localStorage
    saveCartToLocalStorage() {
        localStorage.setItem('cartItems', JSON.stringify(this.cartItems));
    }

    // به‌روزرسانی UI
    updateCartUI() {
        // تعداد آیتم‌ها در سبد خرید
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            const totalItems = this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems;
        }

        // مجموع قیمت
        const cartTotal = document.getElementById('cartTotal');
        if (cartTotal) {
            const totalPrice = this.cartItems.reduce((sum, item) => sum + item.total_price, 0);
            cartTotal.textContent = totalPrice.toLocaleString() + ' تومان';
        }

        // محتوای سبد خرید
        const cartContent = document.getElementById('cartContent');
        if (cartContent) {
            if (this.cartItems.length === 0) {
                cartContent.innerHTML = '<div class="text-center">سبد خرید خالی است</div>';
            } else {
                cartContent.innerHTML = this.cartItems.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <span>${item.name}</span>
                            <span>${item.quantity} x ${item.price} تومان</span>
                        </div>
                        <div class="cart-item-controls">
                            <button onclick="cart.updateItem(${item.product_id}, ${item.quantity - 1})">-</button>
                            <input type="number" value="${item.quantity}" min="1" onchange="cart.updateItem(${item.product_id}, this.value)">
                            <button onclick="cart.updateItem(${item.product_id}, ${item.quantity + 1})">+</button>
                            <button onclick="cart.removeItem(${item.product_id})">حذف</button>
                        </div>
                    </div>
                `).join('');
            }
        }
    }

    // بارگذاری سبد خرید از localStorage
    loadCart() {
        // اگر سبد خرید در localStorage موجود باشد، بارگذاری می‌شود
        const cartItemsFromLocalStorage = JSON.parse(localStorage.getItem('cartItems'));
        if (cartItemsFromLocalStorage) {
            this.cartItems = cartItemsFromLocalStorage;
            this.updateCartUI();
        }
    }
}

// نمونه‌ای از شیء Cart برای دسترسی در جاهای مختلف
const cart = new Cart();
cart.loadCart();

// تابع برای اضافه کردن محصول به سبد خرید
function addToCart(productId, quantity) {
    cart.addItem(productId, quantity);
}
