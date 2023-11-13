<style>
    .ck-editor__editable {min-height: 300px;}
</style>

<fieldset>
    <div class="mb-3">
          <input type="text" name="name" value="<?php echo htmlspecialchars($edit ? $sender_templates['name'] : '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Тема письма" class="form-control" required="required" id="name" >
    </div>
    <div class="mb-3">
        <textarea id="editor" name="text" class="form-control" id="FormControlTextarea1"><?php echo htmlspecialchars($edit ? $sender_templates['text'] : '', ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>

    <div class="input-group mb-3">
        <label class="input-group-text" for="image-input">Вложения</label>
        <input id="file-input" type="file" name="atachments[]" class="form-control" multiple value="<?php print_r($atach_value) ?>">
    </div>

    <ol id="coba" class="list-group mb-3">
        <?php
            $atachments = json_decode($edit ? $sender_templates['atachments'] : '');
            $atach_value = '';
            if(!empty($atachments)) {
                foreach($atachments as $k => $atachment) {
                    $atach_value .= $atachment . (++$k == count($atachments) ? '' : ',');
                    $path = explode('/',parse_url($atachment)['path']);
                    $file_name = end($path);
                    echo '<li class="list-group-item list-group-item-info"><a href="sender/'.$atachment.'" target="_blank">'.$file_name.'</a></li>';
                }
            }
        ?>
        <input id="file-input" type="hidden" name="atachments" class="form-control" multiple value="<?php print_r($atach_value) ?>">
    </ol>


    <a href="sender/templates/" class="btn btn-secondary">Отменить</a>
    <button id="submit_btn" type="submit" class="btn btn-primary">Сохранить</button>           
</fieldset>

<script src="sender/templates/cke/ckeditor.js"></script>
<script>
ClassicEditor.create( document.querySelector( '#editor' ), {
        licenseKey: '',
    })
    .then( editor => {
        window.editor = editor;
        editor.ui.view.editable.element.style.height = '300px';
    })
    .catch( error => {
        console.error( 'Oops, something went wrong!' );
        console.warn( 'Build id: qvpthr2w49y0-c7oha24tu5my' );
        console.error( error );
    });

    // Отображение вложенных файлов
    const selectedFile = document.getElementById('file-input');
    const form = document.querySelector("form");

    const handleFiles = async (event) => {
        const files = event.target.files;
        const submit_btn = document.querySelector('#submit_btn');
        document.getElementById('coba').innerHTML = '';
        submit_btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Загрузка Файлов`;
        submit_btn.disabled = true;

        for (var i = 0; i < files.length; i++) {
            await appendFile(files[i]);
        }

        submit_btn.innerHTML = `Сохранить`;
        submit_btn.disabled = false;
    }

    function appendFile(file) {
        return new Promise((resolve, reject) => {
            let item = document.createElement("li");
            item.classList.add("list-group-item", "list-group-item-info");
            item.innerHTML = file.name;
            resolve(item);
            document.getElementById('coba').appendChild(item);
        });
    }

    selectedFile.addEventListener("change", handleFiles, false);

</script>