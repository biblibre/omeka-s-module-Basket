(function() {
    $(document).ready(function() {
        $('body').on('click', '.basket-update', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var button = $(this);
            var url = button.attr('data-url');
            $.ajax(url)
            .done(function(data) {
                if (data.status === 'success') {
                    let basketItem = data.data.basket_item;
                    if (basketItem.status === 'success') {
                        button.replaceWith(basketItem.content);
                    }
                }
            });
        });
    });
})();
