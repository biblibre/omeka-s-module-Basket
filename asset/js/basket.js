(function() {
    $(document).ready(function() {
        $('body').on('click', '.basket-update', function(e) {
            e.preventDefault();
            e.stopPropagation();
            // Check if the main omeka js is loaded to get translations.
            var isOmeka = typeof Omeka !== 'undefined' && typeof Omeka.jsTranslate !== 'undefined';
            var button = $(this);
            var url = button.attr('data-url');
            $.ajax(url)
            .done(function(data) {
                if (data.status === 'success') {
                    let basketItem = data.data.basket_item;
                    if (basketItem.status === 'success') {
                        // See template basket-button.phtml.
                        let basketText = basketItem.inside ? 'Remove from basket' : 'Add to basket';
                        basketText = isOmeka ? Omeka.jsTranslate(basketText) : basketText;
                        button
                            .prop('class', 'basket-update ' + (basketItem.inside ? 'basket-delete btn-danger' : 'basket-add btn-primary'))
                            .html('<i class="fas fa-shopping-basket"></i> ' + basketText);
                    }
                }
            });
        });
    });
})();
