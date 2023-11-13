<div class="cd-cart cd-cart--empty js-cd-cart">
    <a href="#0" class="cd-cart__trigger text-replace">
        <ul class="cd-cart__count"> <!-- cart items count -->
            <li><span class='cart-items-count'>0</span></li>
            <li>0</li>
        </ul> <!-- .cd-cart__count -->
    </a>

    <div class="cd-cart__content">
        <div class="cd-cart__layout">
            <header class="cd-cart__header">
                
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <h2>Корзина</h2>
                    </div>
                    <div class="col-auto">
                        <input type="tel" id="telephone" class="form-control" placeholder="Введите телефон" require>
                    </div>
                </div>
            </header>
            
            <div class="spinner-border text-primary" role="status" style="margin: 0 auto;margin-top: 50px;display: none;"></div>

            <div class="cd-cart__body">
                <ul class='cart-line-items'>
                
                </ul>
            </div>

            <footer class="cd-cart__footer">
                <a href="#0" class="cd-cart__checkout">
                    <em>Отправить - <span class="cart-subtotal">0</span>
                        <svg class="icon icon--sm" viewBox="0 0 24 24"><g fill="none" stroke="currentColor"><line stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x1="3" y1="12" x2="21" y2="12"/><polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="15,6 21,12 15,18 "/></g>
                        </svg>
                    </em>
                </a>
            </footer>
        </div>
    </div> <!-- .cd-cart__content -->
</div> <!-- cd-cart -->
<script src="/assets/cart.js"></script>
<script type="text/javascript">
    $(function(){
        Cart.initJQuery();
        $(".cd-cart__checkout").on('click', function(e){
            e.preventDefault();
            if($("#telephone")[0].value.length > 5){
                $('#telephone').removeClass('is-invalid');
                $('#telephone').addClass('is-valid');
                submit();
            } else {
                $('#telephone').removeClass('is-valid');
                $('#telephone').addClass('is-invalid');
            }
        })

        function submit() {
            var data = $('.cart-line-items').html();
            let messagebody = '';
            
            $(data).each(function(ind,el) {
                messagebody += "<tr>" +
                            "<td><img src='"+$(el).find(".cd-cart__image img").attr('src')+"' width='100px' /></td>" + 
                            "<td>"+$(el).find(".truncate a").text()+"</td>" + 
                            "<td>"+$(el).find(".cd-cart__quantity").text()+"</td>" + 
                            "<td>"+$(el).find(".cd-cart__price").text()+"</td>" + 
                            "</tr>";
            });

            let message = "<table border='1' bordercolor='black'>"+ 
                            messagebody +
                            "<tr><td colspan='4'>Всего на сумму: "+$('.cart-subtotal').text()+"</td></tr>" +
                            "<tr><td colspan='4'>Телефон: "+$('#telephone')[0].value+"</td></tr>" +
                            "</table>";

            $.ajax({
                type: 'POST',
                contentType: "application/json; charset=utf-8",
                async: true,
                url: '/lib/process.php',
                data: message,
                datatype: 'html',
                global: false,
                beforeSend: function() { 
                    $('.cd-cart__layout .spinner-border').show();
                    $('.cart-line-items').hide();
                },
                success: function(data) {
                    $('.cd-cart__layout .spinner-border').hide();
                    $('.cd-cart__body').html("<h3>Ваш заказ отправлен. Спасибо!</h3>");
                },
                complete: function() {
                    setTimeout(Cart.empty, 3000)
                }
            });
        }
    });
</script>