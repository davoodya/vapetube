// Vape Tube Website JavaScript
let fuse;

class VapeTube {
    constructor() {
        this.apiBase = 'http://localhost/vape-tube/api/';
        this.currentUser = null;
        this.cart = [];
        this.products = [];
        this.categories = [];
        this.currentPage = 1;
        this.totalPages = 1;

        this.init();
    }

    init() {
        this.bindEvents();
        this.checkAuth();
        this.loadCategories();
        this.loadFeaturedProducts();
        this.loadAllProducts();  // بارگذاری همه محصولات
        this.loadCart();
    }

        bindEvents() {
        // Authentication
        document.getElementById('loginBtn')?.addEventListener('click', () => this.showModal('loginModal'));
        document.getElementById('registerBtn')?.addEventListener('click', () => this.showModal('registerModal'));
        document.getElementById('logoutBtn')?.addEventListener('click', () => this.logout());
        document.getElementById('switchToRegister')?.addEventListener('click', () => {
            this.hideModal('loginModal');
            this.showModal('registerModal');
        });
        document.getElementById('switchToLogin')?.addEventListener('click', () => {
            this.hideModal('registerModal');
            this.showModal('loginModal');
        });
        
        // Forms
        document.getElementById('loginForm')?.addEventListener('submit', (e) => this.handleLogin(e));
        document.getElementById('registerForm')?.addEventListener('submit', (e) => this.handleRegister(e));
        document.getElementById('searchForm')?.addEventListener('submit', (e) => vapeTube.handleSearch(e));
        
        // Cart
        document.getElementById('cartToggle')?.addEventListener('click', () => this.toggleCart());
        document.getElementById('cartClose')?.addEventListener('click', () => this.hideCart());
        document.getElementById('checkoutBtn')?.addEventListener('click', () => this.checkout());
        
        // Filters
        document.getElementById('categoryFilter')?.addEventListener('change', () => this.filterProducts());
        document.getElementById('sortFilter')?.addEventListener('change', () => this.filterProducts());
        
        // Modal close
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                this.hideModal(modal.id);
            });
        });
        
        // Overlay
        document.getElementById('overlay')?.addEventListener('click', () => {
            this.hideCart();
            this.hideAllModals();
        });
        
        // // Mobile menu
        // document.querySelector('.mobile-menu-toggle')?.addEventListener('click', () => {
        //     this.toggleMobileMenu();
        // });
        
        // Product categories hover (desktop)
        if (window.innerWidth > 768) {
            this.setupMegaMenu();
        }
    }
    
    setupMegaMenu() {
        const productsMenu = document.querySelector('.products-menu');
        const megaMenu = document.querySelector('.mega-menu');
        
        if (productsMenu && megaMenu) {
            productsMenu.addEventListener('mouseenter', () => {
                megaMenu.style.display = 'block';
            });
            
            productsMenu.addEventListener('mouseleave', () => {
                megaMenu.style.display = 'none';
            });
        }
    }
    
    // API Methods
    async apiCall(endpoint, method = 'GET', data = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
            },
        };
        
        if (data) {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(this.apiBase + endpoint, options);
            const result = await response.json();

            return result;
        } catch (error) {
            console.error('API Error:', error);
            this.showNotification('خطا در ارتباط با سرور', 'error');
            return { success: false, message: 'خطا در ارتباط با سرور' };
        }
    }
    
    // Authentication Methods
    async checkAuth() {
        const result = await this.apiCall('auth.php?action=check');
        if (result.success && result.logged_in) {
            this.currentUser = result.user;
            this.updateAuthUI(true);
        } else {
            this.updateAuthUI(false);
        }
    }
    
    async handleLogin(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        const result = await this.apiCall('auth.php?action=login', 'POST', data);
        
        if (result.success) {
            this.currentUser = result.user;
            this.updateAuthUI(true);
            this.hideModal('loginModal');
            this.showNotification('ورود موفقیت آمیز', 'success');
            this.loadCart();
        } else {
            this.showNotification(result.message, 'error');
        }
    }
    
    async handleRegister(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        const result = await this.apiCall('auth.php?action=register', 'POST', data);
        
        if (result.success) {
            this.hideModal('registerModal');
            this.showNotification('ثبت نام موفقیت آمیز. لطفا وارد شوید.', 'success');
            this.showModal('loginModal');
        } else {
            this.showNotification(result.message, 'error');
        }
    }
    
    async logout() {
        const result = await this.apiCall('auth.php?action=logout', 'POST');
        
        if (result.success) {
            this.currentUser = null;
            this.cart = [];
            this.updateAuthUI(false);
            this.updateCartUI();
            this.showNotification('خروج موفقیت آمیز', 'success');
        }
    }
    
    updateAuthUI(isLoggedIn) {
        const authButtons = document.getElementById('authButtons');
        const userMenu = document.getElementById('userMenu');
        const userName = document.getElementById('userName');
        
        if (isLoggedIn && this.currentUser) {
            authButtons.style.display = 'none';
            userMenu.style.display = 'block';
            userName.textContent = this.currentUser.name;
        } else {
            authButtons.style.display = 'block';
            userMenu.style.display = 'none';
        }
    }
    
    // Product Methods
    async loadCategories() {
        const result = await this.apiCall('products.php?action=categories');
        if (result.success) {
            this.categories = result.categories;
            this.renderCategories();
            this.populateCategoryFilter();
        }
    }
    
    async loadFeaturedProducts() {
        const result = await this.apiCall('products.php?action=featured');
        if (result.success) {
            this.renderProducts(result.products, 'featuredProducts');
        }
    }

    // بارگذاری همه محصولات
    async loadAllProducts(page = 1, categoryId = '', search = '', sort = '') {
        const params = new URLSearchParams({
            action: 'all',
            page: page,
            category_id: categoryId,
            sort: sort
        });

        const result = await this.apiCall(`products.php?${params}`);
        if (result.success) {
            this.products = result.products;
            this.currentPage = result.pagination.current_page;
            this.totalPages = result.pagination.total_pages;

            // ایجاد یک شی Fuse برای جستجو در محصولات
            const options = {
                includeScore: true,  // نمایش امتیاز تطابق
                keys: ['name_fa', 'description']  // کلیدهایی که می‌خواهیم جستجو در آن‌ها انجام شود
            };
            fuse = new Fuse(this.products, options);  // ایجاد نمونه Fuse با محصولات
        }
    }
    renderCategories() {
        const container = document.getElementById('categoriesGrid');
        if (!container) return;
        
        container.innerHTML = this.categories.map(category => `
            <div class="category-card" onclick="vapeTube.filterByCategory(${category.id})">
                <div class="category-icon">
                    <i class="fas fa-smoking"></i>
                </div>
                <div class="category-name">${category.name_fa}</div>
            </div>
        `).join('');
    }

    // متد برای رسم محصولات
    renderProducts(products, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (products.length === 0) {
            container.innerHTML = '<div class="text-center">محصولی یافت نشد</div>';
            return;
        }

        container.innerHTML = products.map(product => `
            <div class="product-card">
                <div class="product-image">
                    <img src="${product.image_url || 'assets/images/placeholder.jpg'}" alt="${product.name_fa}">
                    ${product.is_featured ? '<div class="product-badge">ویژه</div>' : ''}
                </div>
                <div class="product-info">
                    <div class="product-title">${product.name_fa}</div>
                    <div class="product-price">
                        <span class="current-price">${this.formatPrice(product.sale_price || product.price)}</span>
                        ${product.sale_price ? `<span class="original-price">${this.formatPrice(product.price)}</span>` : ''}
                    </div>
                    <div class="product-actions">
                        <button class="add-to-cart" onclick="vapeTube.addToCart(${product.id})">
                            افزودن به سبد
                        </button>
                        <button class="quick-view" onclick="vapeTube.quickView(${product.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderPagination(pagination) {
        const container = document.getElementById('pagination');
        if (!container) return;
        
        let html = '';
        
        if (pagination.has_previous) {
            html += `<a href="#" onclick="vapeTube.changePage(${pagination.previous_page})">قبلی</a>`;
        }
        
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (i === pagination.current_page) {
                html += `<span class="current">${i}</span>`;
            } else {
                html += `<a href="#" onclick="vapeTube.changePage(${i})">${i}</a>`;
            }
        }
        
        if (pagination.has_next) {
            html += `<a href="#" onclick="vapeTube.changePage(${pagination.next_page})">بعدی</a>`;
        }
        
        container.innerHTML = html;
    }
    
    populateCategoryFilter() {
        const select = document.getElementById('categoryFilter');
        if (!select) return;
        
        select.innerHTML = '<option value="">همه دسته‌ها</option>' +
            this.categories.map(category => 
                `<option value="${category.id}">${category.name_fa}</option>`
            ).join('');
    }
    
    // Cart Methods
    async loadCart() {
        if (!this.currentUser) {
            this.loadSessionCart();
            return;
        }
        
        const result = await this.apiCall('cart.php?action=get');
        if (result.success) {
            this.cart = result.items;
            this.updateCartUI();
        }
    }
    
    loadSessionCart() {
        const sessionCart = localStorage.getItem('vape_tube_cart');
        if (sessionCart) {
            this.cart = JSON.parse(sessionCart);
            this.updateCartUI();
        }
    }
    
    async addToCart(productId, quantity = 1) {
        if (!this.currentUser) {
            this.addToSessionCart(productId, quantity);
            return;
        }
        
        const result = await this.apiCall('cart.php?action=add', 'POST', {
            product_id: productId,
            quantity: quantity
        });
        
        if (result.success) {
            this.showNotification('محصول به سبد خرید اضافه شد', 'success');
            this.loadCart();
        } else {
            this.showNotification(result.message, 'error');
        }
    }
    
    addToSessionCart(productId, quantity) {
        const existingItem = this.cart.find(item => item.product_id == productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            // Find product details
            const product = this.products.find(p => p.id == productId);
            if (product) {
                this.cart.push({
                    product_id: productId,
                    name_fa: product.name_fa,
                    price: product.price,
                    sale_price: product.sale_price,
                    image_url: product.image_url,
                    quantity: quantity,
                    current_price: product.sale_price || product.price,
                    total_price: (product.sale_price || product.price) * quantity
                });
            }
        }
        
        localStorage.setItem('vape_tube_cart', JSON.stringify(this.cart));
        this.updateCartUI();
        this.showNotification('محصول به سبد خرید اضافه شد', 'success');
    }
    
    async updateCartItem(productId, quantity) {
        if (!this.currentUser) {
            this.updateSessionCartItem(productId, quantity);
            return;
        }
        
        const result = await this.apiCall('cart.php?action=update', 'POST', {
            product_id: productId,
            quantity: quantity
        });
        
        if (result.success) {
            this.loadCart();
        } else {
            this.showNotification(result.message, 'error');
        }
    }
    
    updateSessionCartItem(productId, quantity) {
        const item = this.cart.find(item => item.product_id == productId);
        if (item) {
            if (quantity <= 0) {
                this.cart = this.cart.filter(item => item.product_id != productId);
            } else {
                item.quantity = quantity;
                item.total_price = item.current_price * quantity;
            }
            localStorage.setItem('vape_tube_cart', JSON.stringify(this.cart));
            this.updateCartUI();
        }
    }
    
    async removeFromCart(productId) {
        if (!this.currentUser) {
            this.updateSessionCartItem(productId, 0);
            return;
        }
        
        const result = await this.apiCall('cart.php?action=remove', 'POST', {
            product_id: productId
        });
        
        if (result.success) {
            this.showNotification('محصول از سبد خرید حذف شد', 'success');
            this.loadCart();
        } else {
            this.showNotification(result.message, 'error');
        }
    }
    
    updateCartUI() {
        const cartCount = document.getElementById('cartCount');
        const cartContent = document.getElementById('cartContent');
        const cartTotal = document.getElementById('cartTotal');
        
        // Update cart count
        const totalItems = this.cart.reduce((sum, item) => sum + item.quantity, 0);
        if (cartCount) cartCount.textContent = totalItems;
        
        // Update cart content
        if (cartContent) {
            if (this.cart.length === 0) {
                cartContent.innerHTML = '<div class="text-center">سبد خرید خالی است</div>';
            } else {
                cartContent.innerHTML = this.cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <img src="${item.image_url || 'assets/images/placeholder.jpg'}" alt="${item.name_fa}">
                        </div>
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name_fa}</div>
                            <div class="cart-item-price">${this.formatPrice(item.current_price)}</div>
                            <div class="cart-item-controls">
                                <button class="quantity-btn" onclick="vapeTube.updateCartItem(${item.product_id}, ${item.quantity - 1})">-</button>
                                <input type="number" class="quantity-input" value="${item.quantity}" min="1" 
                                       onchange="vapeTube.updateCartItem(${item.product_id}, this.value)">
                                <button class="quantity-btn" onclick="vapeTube.updateCartItem(${item.product_id}, ${item.quantity + 1})">+</button>
                                <i class="fas fa-trash remove-item" onclick="vapeTube.removeFromCart(${item.product_id})"></i>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }
        
        // Update cart total
        const total = this.cart.reduce((sum, item) => sum + (item.total_price || item.current_price * item.quantity), 0);
        if (cartTotal) cartTotal.textContent = this.formatPrice(total);
    }

    // جستجو در محصولات (Real-Time)
    async handleSearch(e) {
        e.preventDefault();  // جلوگیری از ارسال فرم

        const searchInput = document.getElementById('searchInput');
        const query = searchInput.value.trim();  // دریافت کلمه جستجو

        // اگر کلمه جستجو خالی نباشد
        if (query) {
            // جستجو با Fuse.js
            const results = fuse.search(query);  // انجام جستجو با Fuse.js

            // نمایش نتایج جستجو
            this.displaySearchResults(results);
        } else {
            // اگر کلمه جستجو خالی بود، نتایج را مخفی کنیم
            document.getElementById('searchResults').style.display = 'none';
        }
    }

    // نمایش نتایج جستجو به صورت Real-Time
    displaySearchResults(results) {
        const searchResultsContainer = document.getElementById('searchResults');

        // اگر نتایج جستجو وجود داشته باشند
        if (results.length > 0) {
            searchResultsContainer.innerHTML = results.map(result => `
            <div class="result-item" onclick="window.location.href='pages/products/product_view.php?id=${result.item.id}'">
                <img src="${result.item.image_url || 'assets/images/placeholder.jpg'}" alt="${result.item.name_fa}" class="result-item-image">
                <span class="result-item-name">${result.item.name_fa}</span>
            </div>
        `).join('');
            searchResultsContainer.style.display = 'grid';  // نمایش نتایج به صورت grid
        } else {
            searchResultsContainer.innerHTML = '<div class="text-center">محصولی یافت نشد</div>';
            searchResultsContainer.style.display = 'block';  // نمایش پیام عدم یافتن نتایج
        }
    }
    async filterProducts() {
        const categoryFilter = document.getElementById('categoryFilter');
        const sortFilter = document.getElementById('sortFilter');
        
        const categoryId = categoryFilter?.value || '';
        const sort = sortFilter?.value || '';
        
        await this.loadAllProducts(1, categoryId, '', sort);
    }
    
    async filterByCategory(categoryId) {
        const categoryFilter = document.getElementById('categoryFilter');
        if (categoryFilter) {
            categoryFilter.value = categoryId;
        }
        
        await this.loadAllProducts(1, categoryId);
        document.getElementById('allProductsSection').scrollIntoView({ behavior: 'smooth' });
    }
    
    async changePage(page) {
        const categoryFilter = document.getElementById('categoryFilter');
        const sortFilter = document.getElementById('sortFilter');
        const searchInput = document.getElementById('searchInput');
        
        const categoryId = categoryFilter?.value || '';
        const sort = sortFilter?.value || '';
        const search = searchInput?.value || '';
        
        await this.loadAllProducts(page, categoryId, search, sort);
        document.getElementById('allProductsSection').scrollIntoView({ behavior: 'smooth' });
    }
    
    // UI Methods
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById('overlay');
        
        if (modal) {
            modal.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById('overlay');
        
        if (modal) {
            modal.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    hideAllModals() {
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.remove('active');
        });
        document.getElementById('overlay').classList.remove('active');
        document.body.style.overflow = '';
    }
    
    toggleCart() {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');
        
        if (cartSidebar.classList.contains('active')) {
            this.hideCart();
        } else {
            cartSidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    hideCart() {
        const cartSidebar = document.getElementById('cartSidebar');
        const overlay = document.getElementById('overlay');
        
        cartSidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // toggleMobileMenu() {
    //     const navLeft = document.querySelector('.nav-left');
    //     const navRight = document.querySelector('.nav-right');
    //
    //     navLeft?.classList.toggle('active');
    //     navRight?.classList.toggle('active');
    // }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">&times;</button>
        `;
        
        // Add styles if not already added
        if (!document.getElementById('notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 20px;
                    border-radius: 8px;
                    color: white;
                    z-index: 10000;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    animation: slideIn 0.3s ease;
                }
                .notification-success { background-color: #00b894; }
                .notification-error { background-color: #e17055; }
                .notification-info { background-color: #6c5ce7; }
                .notification button {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 18px;
                    cursor: pointer;
                }
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    // Utility Methods
    formatPrice(price) {
        return new Intl.NumberFormat('fa-IR').format(price) + ' تومان';
    }
    
    quickView(productId) {
        // Implement quick view modal
        this.showNotification('نمایش سریع محصول', 'info');
    }
    
    async checkout() {
        if (!this.currentUser) {
            this.showNotification('لطفا ابتدا وارد شوید', 'error');
            this.showModal('loginModal');
            return;
        }
        
        if (this.cart.length === 0) {
            this.showNotification('سبد خرید خالی است', 'error');
            return;
        }
        
        // Implement checkout process
        this.showNotification('در حال پیاده سازی...', 'info');
    }
}
// document.addEventListener('DOMContentLoaded', () => {
//     const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
//     const navLeft = document.querySelector('.nav-left');
//     const navRight = document.querySelector('.nav-right');
//     const productsLink = document.querySelector('.products-link');
//     const megaMenu = document.querySelector('.mega-menu');
//
//     if (!mobileMenuToggle || !navLeft || !navRight || !productsLink || !megaMenu) {
//         console.error('یکی از المنت های ناوبری پیدا نشد!');
//         return;
//     }
//
//     // باز/بستن منوی کلی موبایل
//     mobileMenuToggle.addEventListener('click', () => {
//         const expanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
//         mobileMenuToggle.setAttribute('aria-expanded', !expanded);
//         mobileMenuToggle.classList.toggle('active');
//         navLeft.classList.toggle('active');
//         navRight.classList.toggle('active');
//
//         // همزمان زیرمنوی محصولات بسته شود
//         megaMenu.classList.remove('active');
//         productsLink.classList.remove('active');
//         productsLink.setAttribute('aria-expanded', false);
//     });
//
//     // باز/بستن زیرمنوی محصولات در موبایل
//     productsLink.addEventListener('click', (e) => {
//         e.preventDefault(); // جلوگیری از رفتن به لینک
//
//         const isActive = megaMenu.classList.contains('active');
//         if (isActive) {
//             megaMenu.classList.remove('active');
//             productsLink.classList.remove('active');
//             productsLink.setAttribute('aria-expanded', false);
//         } else {
//             megaMenu.classList.add('active');
//             productsLink.classList.add('active');
//             productsLink.setAttribute('aria-expanded', true);
//         }
//     });
//
//     // بستن منو هنگام کلیک بیرون
//     document.addEventListener('click', (e) => {
//         if (!e.target.closest('.navbar-content') && !e.target.closest('.mobile-menu-toggle')) {
//             mobileMenuToggle.classList.remove('active');
//             mobileMenuToggle.setAttribute('aria-expanded', false);
//             navLeft.classList.remove('active');
//             navRight.classList.remove('active');
//             megaMenu.classList.remove('active');
//             productsLink.classList.remove('active');
//             productsLink.setAttribute('aria-expanded', false);
//         }
//     });
// });
//

document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLeft = document.querySelector('.nav-left');
    const navRight = document.querySelector('.nav-right');
    const productsLink = document.querySelector('.products-link');
    const megaMenu = document.querySelector('.mega-menu');

    if (!mobileMenuToggle || !navLeft || !navRight || !productsLink || !megaMenu) {
        console.error('یکی از المنت های ناوبری پیدا نشد!');
        return;
    }

    // باز/بستن منوی کلی موبایل
    const toggleMainMenu = () => {
        const expanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
        mobileMenuToggle.setAttribute('aria-expanded', !expanded);
        mobileMenuToggle.classList.toggle('active');
        navLeft.classList.toggle('active');
        navRight.classList.toggle('active');

        // بستن زیرمنوی محصولات همزمان
        megaMenu.classList.remove('active');
        productsLink.classList.remove('active');
        productsLink.setAttribute('aria-expanded', false);
    };

    mobileMenuToggle.addEventListener('click', toggleMainMenu);
    mobileMenuToggle.addEventListener('touchstart', toggleMainMenu);

    // باز/بستن زیرمنوی محصولات (فقط موبایل ≤ 992px)
    const toggleProductSubMenu = (e) => {
        if (window.innerWidth > 992) return;

        e.preventDefault();

        const isActive = megaMenu.classList.contains('active');
        if (isActive) {
            megaMenu.classList.remove('active');
            productsLink.classList.remove('active');
            productsLink.setAttribute('aria-expanded', false);
        } else {
            megaMenu.classList.add('active');
            productsLink.classList.add('active');
            productsLink.setAttribute('aria-expanded', true);
        }
    };

    productsLink.addEventListener('click', toggleProductSubMenu);
    productsLink.addEventListener('touchstart', toggleProductSubMenu);

    // بستن منو هنگام کلیک بیرون
    const closeMenusOnOutsideClick = (e) => {
        if (!e.target.closest('.navbar-content') && !e.target.closest('.mobile-menu-toggle')) {
            mobileMenuToggle.classList.remove('active');
            mobileMenuToggle.setAttribute('aria-expanded', false);
            navLeft.classList.remove('active');
            navRight.classList.remove('active');
            megaMenu.classList.remove('active');
            productsLink.classList.remove('active');
            productsLink.setAttribute('aria-expanded', false);
        }
    };

    document.addEventListener('click', closeMenusOnOutsideClick);
    document.addEventListener('touchstart', closeMenusOnOutsideClick);
});

// Tablet style product
// کنترل باز و بسته شدن منوی محصولات با کلیک فقط در موبایل و تبلت (≤ 992px)

// افزودن رویداد به ورودی جستجو برای Real-Time
document.getElementById('searchInput').addEventListener('input', (e) => vapeTube.handleSearch(e));


// Initialize the application
const vapeTube = new VapeTube();

// Make it globally available
window.vapeTube = vapeTube;


