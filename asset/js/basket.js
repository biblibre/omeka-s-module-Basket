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
            let button = $('.basket-update[data-id=' + basketItem.id + ']');
            if (!button.length) {
                return;
            }
            button
                .prop('title', button.attr('data-title-' + basketItem.value))
                .removeClass('selected unselected')
                .addClass(basketItem.value);
        }

        var updateBasketList = function(basketItem) {
            let list = $('.basket-list .basket-items');
            if (!list.length) {
                return;
            }
            if (basketItem.value === 'selected') {
                if (!list.find('li[data-id=' + basketItem.id + ']').length) {
                    list.append(
                        $('<li>').attr('data-id', basketItem.id)
                            .append(
                                $('<a>').prop('href', basketItem.url).append(basketItem.title)
                            )
                            .append(
                                $('<span class="basket-delete">')
                                    .attr('data-id', basketItem.id)
                                    .attr('data-url', basketItem.url_remove)
                                    .attr('title', list.attr('data-text-remove'))
                                    .attr('aria-label', list.attr('data-text-remove'))
                            )
                    );
                }
            } else {
                list.find('li[data-id=' + basketItem.id + ']').remove();
            }
            if (list.find('li').length) {
                $('.basket-empty').removeClass('active');
            } else {
                $('.basket-empty').addClass('active');
            }
        }

    });
})();
