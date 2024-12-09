document.addEventListener('DOMContentLoaded', function () {
    console.log('Page loaded successfully!');
    // Example for dynamic filtering
    const filterInput = document.querySelector('#filterInput');
    if (filterInput) {
        filterInput.addEventListener('input', function () {
            const filterValue = filterInput.value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');
            productItems.forEach(item => {
                const name = item.querySelector('.product-name').textContent.toLowerCase();
                item.style.display = name.includes(filterValue) ? '' : 'none';
            });
        });
    }
});
