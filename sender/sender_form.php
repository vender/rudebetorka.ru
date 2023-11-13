<fieldset>
    <div class="form-floating mb-3">
          <input type="text" name="name" value="<?php echo htmlspecialchars($edit ? $sender['name'] : '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Название рассылки" class="form-control" required="required" id="name" >
          <label for="floatingInput">Название рассылки</label>
    </div>
    
    <div class="mb-3 form-floating input-group">
        <select class="form-select" name="template" placeholder="Шаблон письма" required>
            <option value="" disabled selected>- выбрать -</option>
            <?php foreach($templates as $k => $template) { ?>
                <option value="<?php echo $template['id'] ?>" <?php echo ($edit && $sender['template'] == $template['id'] ? 'selected' : '') ?> ><?php echo $template['name'] ?></option>
            <?php } ?>
        </select>
        <label for="floatingSelect">Шаблон письма</label>
        <button class="btn btn-outline-secondary" type="button"><i class="bi bi-eye-fill"></i></button>
    </div>

    <div id="type-wrapp" class="mb-3 form-floating">
        <select id="send-type" class="form-select" name="send-type" placeholder="Кому" required>
            <option value="" disabled selected>- выбрать -</option>
            <option value="1" <?php echo ($edit && $sender['send-type'] == 1 ? 'selected' : '') ?> >По региону</option>
            <option value="2" <?php echo ($edit && $sender['send-type'] == 2 ? 'selected' : '') ?> >Выбрать из списка</option>
            <option value="3" <?php echo ($edit && $sender['send-type'] == 3 ? 'selected' : '') ?> >Всем</option>
        </select>
        <label for="floatingSelect">Кому</label>
    </div>
    
    <div id="region-wrapp" class="mb-3 form-floating <?php echo ($edit && $sender['send-type'] == 1 ? '' : 'visually-hidden') ?>">
        <select id="region-id" class="form-select" placeholder="Регионы" <?php echo ($edit && $sender['send-type'] == 1 ? 'name="region-id" required="required"' : '') ?>>
            <option value="" disabled selected>- выбрать -</option>
            <?php foreach($filteredRegions as $k => $region) { ?>
                <option value="<?php echo $region[1] ?>" <?php echo ($edit && $sender['region-id'] == $region[1] ? 'selected' : '') ?> ><?php echo $region[0] ?></option>
            <?php } ?>
        </select>
        <label for="floatingSelect">Регионы</label>
    </div>

    <div id="user-list-wrapp" class="mb-3 <?php echo ($edit && $sender['send-type'] == 2 ? '' : 'visually-hidden') ?>">
        <select id="user-list" class="form-control selectpicker" multiple data-live-search="true" data-container="#page-wrapper" data-style="btn-outline-primary" multiple data-selected-text-format="count" placeholder="Список площадок" <?php echo ($edit && $sender['send-type'] == 1 ? 'name[]="user-list" required="required"' : '') ?>>
            <?php foreach($allCustomers as $k => $customer) { ?>
                <option value="<?php echo $customer['id'] ?>" <?php echo ($edit && !empty($sender['user-list']) ? in_array($customer['id'], json_decode($sender['user-list'])) ? 'selected' : '' : '') ?> ><?php echo $customer['name'] ?></option>
            <?php } ?>
        </select>
    </div>

    <a href="sender" class="btn btn-secondary">Отменить</a>
    <button type="submit" class="btn btn-primary">Сохранить</button>

    <?php if($edit) { ?>  
        <div class="d-flex justify-content-end">
            <button id="send-start" class="btn btn-warning">Начать отправку</button>
        </div>
    <?php } ?>

    <div id="message" class="h6"></div>

</fieldset>

<script>
    const typeSelector = document.querySelector("#send-type");
    const regionWrapp = document.querySelector("#region-wrapp");
    const regionSelector = document.querySelector("#region-id");
    const userList = document.querySelector("#user-list");

    typeSelector.addEventListener("change", (e) => {
        const sendType = e.target.selectedOptions[0].value;

        regionWrapp.classList.add("visually-hidden");
        regionSelector.removeAttribute('name');
        regionSelector.removeAttribute('required');

        document.querySelector("#user-list-wrapp").classList.add("visually-hidden");
        userList.removeAttribute('name');
        userList.removeAttribute('required');

        if(sendType == 1) {
            regionWrapp.classList.remove("visually-hidden");
            regionSelector.setAttribute('name', 'region-id');
            regionSelector.setAttribute('required', 'required');
        } else if(sendType == 2) {
            document.querySelector("#user-list-wrapp").classList.remove("visually-hidden");
            userList.setAttribute('name', 'user-list[]');
            userList.setAttribute('required', 'required');
        }
    });
    
    <?php if($edit) { ?>
        const startBtn = document.querySelector("#send-start");
        const form = document.getElementById("sender_form");
        userList.setAttribute('name', 'user-list[]');

        startBtn.addEventListener("click", (e) => {
            e.preventDefault();

            // for (var i = 0, len = form.elements.length; i < len; ++i) {
            //     form.elements[i].readOnly = true;
            // }

            const formData = new FormData(form);
            formData.append("senderId", "<?php echo $sender['id'] ?>");
            // for (const pair of formData.entries()) {
            //     console.log(`${pair[0]}, ${pair[1]}`);
            // }

            document.querySelector('#send-start').innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка писем`;
            document.querySelector('#send-start').disabled = true;
            email(formData);
        });
    
    <?php } ?>

    async function email(data) {
        try {
            const response = await fetch("sender/mailer.php", {
                method: "POST",
                body: data,
                headers: {
                    'X-Requested-With' : 'XMLHttpRequest'
                }
            });
            const result = await response.text();
            console.log(result);
            document.querySelector('#send-start').disabled = true;
            document.querySelector('#send-start').innerHTML = `Начать отправку`;

            document.querySelector('#message').innerHTML = result;
            // window.scrollTo({
            //     top: 0,
            //     behavior: "smooth"
            // });
            // console.log(JSON.stringify(result));
        } catch (error) {
            console.error('Ошибка:', error);
        }
    }

</script>