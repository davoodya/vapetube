class Cart {
    constructor() {
        // اطلاعات سبد خرید از localStorage بارگذاری می‌شود
        this.cartItems = JSON.parse(localStorage.getItem('cartItems')) || [];
    }



    // به‌روزرسانی تعداد یک محصول در سبد خرید
    updateItem(productId, quantity) {
        // پیدا کردن آیتمی که می‌خواهیم تغییر دهیم
        const item = this.cartItems.find(item => item.product_id === productId);

        if (item) {
            // اگر مقدار جدید کمتر از 1 باشد، باید تعداد محصول را به 1 حداقل محدود کنیم
            if (quantity < 1) {
                quantity = 1;
            }

            // به‌روزرسانی تعداد و قیمت کل محصول
            item.quantity = quantity;
            item.total_price = item.quantity * item.price;

            // ذخیره‌سازی تغییرات در localStorage
            this.saveCartToLocalStorage();

            // به‌روزرسانی UI سبد خرید
            this.updateCartUI();
        }
    }

    // حذف آیتم از سبد خرید
    removeItem(productId) {
        // حذف محصول از آرایه cartItems
        this.cartItems = this.cartItems.filter(item => item.product_id !== productId);

        // ذخیره‌سازی تغییرات در localStorage
        this.saveCartToLocalStorage();

        // به‌روزرسانی UI سبد خرید
        this.updateCartUI();
    }


    // افزودن آیتم به سبد خرید
    addItem(productId, quantity = 1, productPrice, productName) {
        const existingItem = this.cartItems.find(item => item.product_id === productId);

        if (existingItem) {
            // اگر محصول در سبد خرید موجود باشد، فقط تعداد آن را افزایش می‌دهیم
            existingItem.quantity += quantity;
            existingItem.total_price = existingItem.quantity * existingItem.price;
        } else {
            // اگر محصول در سبد خرید نباشد، آن را اضافه می‌کنیم
            const newItem = {
                product_id: productId,
                name: productName, // نام محصول
                price: productPrice, // قیمت محصول
                quantity: quantity,
                total_price: productPrice * quantity // محاسبه قیمت کل
            };
            this.cartItems.push(newItem);
        }

        // ذخیره سبد خرید در localStorage
        this.saveCartToLocalStorage();

        // به‌روزرسانی UI سبد خرید
        this.updateCartUI();

        // باز کردن مدال سبد خرید بعد از اضافه کردن محصول
        this.toggleCart();
    }

    // ذخیره‌سازی سبد خرید در localStorage
    saveCartToLocalStorage() {
        localStorage.setItem('cartItems', JSON.stringify(this.cartItems));
    }

    // به‌روزرسانی UI
    // به‌روزرسانی UI سبد خرید
    // به‌روزرسانی UI سبد خرید
    updateCartUI() {
        const cartCount = document.getElementById('cartCount');
        if (cartCount) {
            // محاسبه تعداد کل محصولات در سبد خرید
            const totalItems = this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems; // نمایش تعداد کل محصولات
        }

        const cartTotal = document.getElementById('cartTotal');
        if (cartTotal) {
            // محاسبه مجموع قیمت سبد خرید
            const totalPrice = this.cartItems.reduce((sum, item) => sum + item.total_price, 0);
            cartTotal.textContent = totalPrice.toLocaleString() + ' تومان'; // نمایش مجموع قیمت
        }

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
                        <button onclick="cart.removeItem(${item.product_id})">حذف</button> <!-- دکمه حذف -->
                    </div>
                </div>
            `).join('');
            }
        }
    }

    // بارگذاری سبد خرید از localStorage
    loadCart() {
        const cartItemsFromLocalStorage = JSON.parse(localStorage.getItem('cartItems'));
        if (cartItemsFromLocalStorage) {
            this.cartItems = cartItemsFromLocalStorage;
            this.updateCartUI();
        }
    }

    // باز یا بسته کردن مدال سبد خرید
    toggleCart() {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');

        if (cartSidebar.classList.contains('active')) {
            cartSidebar.classList.remove('active');
            overlay.classList.remove('active');
        } else {
            cartSidebar.classList.add('active');
            overlay.classList.add('active');
        }
    }

    // بستن مدال سبد خرید
    hideCart() {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
    }
}

// نمونه‌ای از شیء Cart برای دسترسی در جاهای مختلف
const cart = new Cart();
cart.loadCart();

// تابع برای اضافه کردن محصول به سبد خرید
function addToCart(productId, quantity, productPrice, productName) {
    cart.addItem(productId, quantity, productPrice, productName);
}

document.addEventListener('DOMContentLoaded', () => {
    // ایجاد شیء Cart
    const cart = new Cart();
    cart.loadCart();

    // تابع برای باز و بسته کردن مدال سبد خرید
    cart.toggleCart = function () {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');

        if (cartSidebar.classList.contains('active')) {
            cartSidebar.classList.remove('active');
            overlay.classList.remove('active');
        } else {
            cartSidebar.classList.add('active');
            overlay.classList.add('active');
        }
    };

    // بستن مدال سبد خرید
    cart.hideCart = function () {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
    };

    // افزودن محصول به سبد خرید
    function addToCart(productId, quantity, productPrice, productName) {
        cart.addItem(productId, quantity, productPrice, productName);  // اضافه کردن محصول به سبد خرید
        cart.toggleCart();  // باز کردن مدال سبد خرید بعد از اضافه کردن محصول
    }

    // مشاهده تعداد محصولات در سبد خرید
    cart.updateCartUI = function () {
        const cartCount = document.getElementById('cartCount');
        const totalItems = cart.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;

        const cartTotal = document.getElementById('cartTotal');
        const totalPrice = cart.cartItems.reduce((sum, item) => sum + item.total_price, 0);
        cartTotal.textContent = totalPrice.toLocaleString() + ' تومان';  // نمایش قیمت مجموع
    };

    // مشاهده محتوای سبد خرید
    cart.updateCartUI = function () {
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
                            <button onclick="cart.removeItem(${item.product_id})">حذف</button> <!-- دکمه حذف -->
                        </div>
                    </div>
                `).join('');
            }
        }
    };

    // اتصال رویداد به آیکون سبد خرید
    const cartToggle = document.getElementById('cartToggle');
    if (cartToggle) {
        cartToggle.addEventListener('click', () => cart.toggleCart());  // باز یا بسته کردن مدال
    }

    // رویداد بستن مدال
    const cartClose = document.getElementById('cartClose');
    if (cartClose) {
        cartClose.addEventListener('click', () => cart.hideCart());  // بستن مدال سبد خرید
    }
});
