<template>
    <tinymce
        :init="{
         height: 300,
         menubar: true,
         branding: false,
         plugins: [
           'advlist autolink lists link image charmap print preview anchor',
           'searchreplace visualblocks code fullscreen',
           'insertdatetime media table paste code wordcount'
         ],
         toolbar:
           'undo redo | formatselect | bold italic backcolor | \
           alignleft aligncenter alignright alignjustify | \
           bullist numlist outdent indent | removeformat'
       }"
        api-key="2v20ajrzng0ftoh79hde6tj4xavm9ej7au8gq939q914sb0w"
        @input="handleInput"
    />
</template>

<script>

import Editor from '@tinymce/tinymce-vue'

export default {
    name: "Tinymce",
    components: {
        'tinymce': Editor
    },
    methods: {
        handleInput(e) {
            // console.log(e);
            this.$emit('input', e)
        }
    }
}
</script>
