(function() {
    $(document).ready(function() {

        $('body').on('click', '.basket-update, .basket-delete', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var button = $(this);
            var url = button.attr('data-url');
            $.ajax(url)
            .done(function(data) {
                if (data.status === 'success') {
                    let basketItem = data.data.basket_item;
                    if (basketItem.status === 'success') {
                        updateBasketButton(basketItem);
                        updateBasketList(basketItem);
                    }
                }
            });
        });

        $('body').on('click', '.basket-list-toggle', function() {
            $(this).toggleClass('active');
            $('.basket-list').toggle().toggleClass('active');
           return false;
        });

        var updateBasketButton = function(basketItem) {
            let basketButton = $('.basket-update[data-id=' + basketItem.id + ']');
            if (!basketButton.length) {
                return;
            }
            // Check if the main omeka js is loaded to get translations.
            var isOmeka = typeof Omeka !== 'undefined' && typeof Omeka.jsTranslate !== 'undefined';
            // See template basket-button.phtml.
            let basketText = basketItem.inside ? 'Remove from basket' : 'Add to basket';
            basketText = isOmeka ? Omeka.jsTranslate(basketText) : basketText;
            basketButton
                .prop('class', 'basket-update ' + (basketItem.inside ? 'basket-delete btn-danger' : 'basket-add btn-primary'))
                .html('<i class="fas fa-shopping-basket"></i> ' + basketText);
        }

        var updateBasketList = function(basketItem) {
            let basketList = $('.basket-list .basket-items');
            if (!basketList.length) {
                return;
            }
            if (basketItem.inside) {
                if (!basketList.find('li[data-id=' + basketItem.id + ']').length) {
                    basketList.append(
                        $('<li>').attr('data-id', basketItem.id)
                            .append(
                                $('<a>').prop('href', basketItem.url).append(basketItem.title)
                            )
                            .append(
                                $('<span class="basket-delete">')
                                    .attr('data-id', basketItem.id)
                                    .attr('data-url', basketItem.url_remove)
                                    .attr('title', basketList.attr('data-text-remove'))
                                    .attr('aria-label', basketList.attr('data-text-remove'))
                                    .append('X')
                            )
                    );
                }
            } else {
                basketList.find('li[data-id=' + basketItem.id + ']').remove();
            }
            if (basketList.find('li').length) {
                $('.basket-empty').removeClass('active');
            } else {
                $('.basket-empty').addClass('active');
            }
        }

    });
})();
