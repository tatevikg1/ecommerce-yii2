

$(function(){

    const $addToCart = $('.btn-add-to-cart');
    const $cartCuantity = $('#cart-quantity');

    $addToCart.click(ev => {
        ev.preventDefault();
        const $this = $(ev.target);
        const id = $this.closest('.product-item').data('key');
        console.log(id);

        $.ajax({
            method: 'POST',
            url:    $this.attr('href'),
            data: {id},
            success: function(){
                console.log('success');
                $cartCuantity.text(parseInt($cartCuantity.text() || 0) + 1);
            }
        })
    })
})