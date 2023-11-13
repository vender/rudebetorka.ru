// user-info
$(document).ready(function() {
    const userModal = new bootstrap.Modal(document.getElementById('userInfoModal'));
    $('.user-info').on('click', function(e) {
        e.preventDefault();
        let userId = $(this).data('userid');
        stats(userId, userModal);
    });

    $('#submit-user').on('click', function(e) {
        e.preventDefault();
        const form = $('#user-info-form');
        let userId = form.data('userid');
        let regionid = form.find('#region').data('regionid');
        form.find('#region').val(regionid);

        $.ajax({
            type: "POST",
            url: `customers/edit_customer.php?customer_id=${userId}`,
            data: form.serialize(),
            success: function(data) {
                userModal.hide();
            }
        });

    });

});


async function stats(customer_id, userModal) {
    try {
        const response = await fetch("user-info.php", {
            method: "POST",
            body: JSON.stringify({ "customer_id": customer_id }),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await response.json();
        console.log(result);

        $('#userInfoModal').find('.modal-body').html(`
            <form id="user-info-form" data-userid="${customer_id ? customer_id : ''}" >
                <fieldset>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <input type="text" name="name" value='${result[0].name == null ? '' : result[0].name}' placeholder="Полное название" class="form-control" id="name" readonly>
                            </div> 

                            <div class="mb-3">
                                <input type="text" name="region" value='${result[0].regionName == null ? '' : result[0].regionName}' data-regionid="${result[0].region == null ? '' : result[0].region}" placeholder="Web сайт" class="form-control" id="region" readonly>
                            </div>

                            <div class="mb-3">
                                <input name="url" value='${result[0].url == null ? '' : result[0]?.url}' placeholder="Web сайт" class="form-control"  type="text" id="url" readonly>
                            </div>

                            <div class="mb-3">
                                <textarea name="socilas" rows="5" placeholder="Ссылки на страницы в соцсетях" class="form-control" id="address" readonly>${result[0].socilas == null ? '' : result[0].socilas}</textarea>
                            </div>

                            <div class="mb-3">
                                <input name="fio" value='${result[0].fio == null ? '' : result[0].fio}'  placeholder="ФИО ведущего (ответственного)" class="form-control"  type="fio" readonly>
                            </div>

                            <div class="mb-3">
                                <input name="phone" value='${result[0].phone == null ? '' : result[0].phone}' placeholder="Телефон ведущего (ответственного)" class="form-control"  type="text" id="phone" readonly>
                            </div>

                            <div class="mb-3">
                                <input  type="email" name="email" value='${result[0].email == null ? '' : result[0].email}' placeholder="Email ведущего (ответственного)" class="form-control" id="email" readonly>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Комментарий</label>
                                <textarea name="comment" rows="10" placeholder="Комментарий" class="form-control" id="comment">${result[0].comment == null ? '' : result[0].comment}</textarea>
                            </div>
                        </div>       
                    </div>
                </fieldset>
            </form>
        `)

        userModal.show();
        // document.querySelector('#stats').innerHTML = '';
        // window.scrollTo({
        //     top: 0,
        //     behavior: "smooth"
        // });
    } catch (error) {
        console.error('Ошибка:', error);
    }
}