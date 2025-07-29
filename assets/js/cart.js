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

// Show Shopping Card Modal
// نمونه‌ای از شیء Cart برای دسترسی در جاهای مختلف
const cart = new Cart();
cart.loadCart();

// تابع برای اضافه کردن محصول به سبد خرید
function addToCart(productId, quantity) {
    cart.addItem(productId, quantity);
}
document.addEventListener('DOMContentLoaded', () => {
    // شیء Cart که برای مدیریت سبد خرید استفاده می‌شود
    const cart = new Cart();

    // بارگذاری سبد خرید از localStorage
    cart.loadCart();

    // باز یا بسته کردن مدال سبد خرید
    cart.toggleCart = function () {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');

        // اگر مدال سبد خرید باز است، آن را ببندید، و اگر بسته است، آن را باز کنید
        if (cartSidebar.classList.contains('active')) {
            cartSidebar.classList.remove('active');
            overlay.classList.remove('active');
        } else {
            cartSidebar.classList.add('active');
            overlay.classList.add('active');
        }
    };

    // بستن سبد خرید
    cart.hideCart = function () {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
    };

    // افزودن محصول به سبد خرید
    function addToCart(productId, quantity) {
        cart.addItem(productId, quantity); // اضافه کردن محصول به سبد خرید
    }

    // به‌روزرسانی UI سبد خرید
    cart.updateCartUI = function () {
        const cartCount = document.getElementById('cartCount');
        const totalItems = cart.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;

        const cartTotal = document.getElementById('cartTotal');
        const totalPrice = cart.cartItems.reduce((sum, item) => sum + item.total_price, 0);
        cartTotal.textContent = totalPrice.toLocaleString() + ' تومان'; // نمایش مجموع قیمت
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
                            <button onclick="cart.removeItem(${item.product_id})">حذف</button>
                        </div>
                    </div>
                `).join('');
            }
        }
    };

    // بررسی کلیک روی دکمه سبد خرید
    document.getElementById('cartToggle').addEventListener('click', () => cart.toggleCart());
    document.getElementById('cartClose').addEventListener('click', () => cart.hideCart());
});
