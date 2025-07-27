// New Function for change image in the background of categories section
document.addEventListener('DOMContentLoaded', () => {
    const section = document.querySelector('.categories');

    const bgImages = [
        'assets/images/bg/thelemaSolo.webp',
        'assets/images/bg/uwell.webp',
        'assets/images/bg/Berseker.jpg',
        'assets/images/bg/sxmini.jpeg',
        'assets/images/bg/ripevape2.jpg',
        'assets/images/bg/ripevape.png',
        'assets/images/bg/Kayfunx.jpg',
        'assets/images/bg/Centaurus.jpg'
    ];

    // پیش‌بارگذاری تصاویر
    bgImages.forEach(img => {
        const preload = new Image();
        preload.src = img;
    });

    let currentIndex = 0;

    function changeBackground() {
        section.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('${bgImages[currentIndex]}')`;
        currentIndex = (currentIndex + 1) % bgImages.length;
    }

    // مقدار اولیه
    changeBackground();

    // تعویض هر 2 ثانیه
    setInterval(changeBackground, 4000);
});