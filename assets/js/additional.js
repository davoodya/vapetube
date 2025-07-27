// New Function for change image in the background of categories section

document.addEventListener('DOMContentLoaded', () => {
    const section = document.querySelector('.categories');

    const bgImages = [
        '/assets/bg/bg1.jpg',
        '/assets/bg/bg2.jpg',
        '/assets/bg/bg3.jpg',
        '/assets/bg/bg4.jpg',
        '/assets/bg/bg5.jpg',
        '/assets/bg/bg6.jpg',
    ];

    let currentIndex = 0;

    function changeBackground() {
        section.style.backgroundImage = `url('${bgImages[currentIndex]}')`;
        currentIndex = (currentIndex + 1) % bgImages.length;
    }

    // مقدار اولیه
    changeBackground();

    // تعویض هر 2 ثانیه
    setInterval(changeBackground, 2000);
});