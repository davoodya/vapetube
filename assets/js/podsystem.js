// Loading Header
fetch('pages/components/header.html')
    .then(response => response.text())
    .then(data => {
        document.getElementById('header-container').innerHTML = data;
    })
    .catch(error => {
        console.error('Error loading header:', error);
    });

// Loading Footer
fetch('pages/components/footer.html')
    .then(response => response.text())
    .then(data => {
        document.getElementById('footer-container').innerHTML = data;
    })
    .catch(error => {
        console.error('Error loading footer:', error);
    });

