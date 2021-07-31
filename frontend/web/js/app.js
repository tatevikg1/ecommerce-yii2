

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


    const $itemCuantities = $('.item-quantity');

    $itemCuantities.change(ev => {

        const $this = $(ev.target);
        const $tr = $this.closest('tr');
        const id = $tr.data('id');

        $.ajax({
            method: 'POST',
            url:    $tr.data('url'),
            data: {
                id,
                quantity: $this.val()
            },
            success: function(res){
                console.log(res.totalQuantity)
                $cartCuantity.text(res.totalQuantity)
            }
        })

    })
})