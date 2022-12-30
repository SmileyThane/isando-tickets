<template>
    <tinymce
        :init="{
         height: 300,
         menubar: true,
         branding: false,
         plugins: [
           'advlist autolink lists link image charmap print preview anchor',
           'searchreplace visualblocks code fullscreen',
           'insertdatetime media table paste code wordcount searchreplace emoticons'
         ],
         toolbar:
           'undo redo | formatselect fontsizeselect | bold italic image backcolor forecolor| \
           alignleft aligncenter alignright alignjustify searchreplace| \
           bullist numlist outdent indent emoticons| removeformat',
           image_title: true,
           file_picker_types: 'image',
           file_picker_callback: pickerCallback
       }"

        api-key="2v20ajrzng0ftoh79hde6tj4xavm9ej7au8gq939q914sb0w"
        v-bind:value="value"
        @input="handleInput"
    />
</template>

<script>

import Editor from '@tinymce/tinymce-vue'

export default {
    name: "Tinymce",
    props: ['value'],
    components: {
        'tinymce': Editor
    },
    methods: {
        handleInput(e) {
            // console.log(e);
            this.$emit('input', e)
        },
        pickerCallback(cb, value, meta) {
            let input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
                let file = this.files[0];
                let reader = new FileReader();

                reader.onload = function () {
                    let id = 'blobid' + (new Date()).getTime();
                    let blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    let base64 = reader.result.split(',')[1];
                    let blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), {title: file.name});
                };

                reader.readAsDataURL(file);
            };

            input.click();
        }
    }
}
</script>
