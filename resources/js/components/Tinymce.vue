<template>
  <tinymce
    tinymce-script-src="/js/tinymce.min.js"
    :init="{
      height: 150,
      content_style:
        'body { font-family:  Roboto, sans-serif; font-size: 10pt; font-weight: 400; line-height: 1rem; letter-spacing: .0071428571em; color: #00000099; } a { color: #1976d2; }',
      menubar: true,
      branding: false,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen',
        'insertdatetime media table paste code wordcount searchreplace emoticons textcolor',
      ],
      toolbar:
        'undo redo | formatselect fontsizeselect fontsize | bold italic image forecolor | \
           alignleft aligncenter alignright alignjustify searchreplace| \
           bullist numlist outdent indent emoticons| removeformat',
      image_title: true,
      relative_urls: false,
      file_picker_types: 'image',
      file_picker_callback: pickerCallback,
      block_formats:
        'Normal=p; Header 1=h1; Header 2=h2; Header 3=h3; Header 4=h4; Header 5=h5; Header 6=h6; Code=code',
      color_map: [
        '000000',
        'Black',
        'FFFFFF',
        'White',
        'FF0000',
        'Red',
        '00FF00',
        'Green',
        '0000FF',
        'Blue',
        'FFFF00',
        'Yellow',
        'FF00FF',
        'Magenta',
        '00FFFF',
        'Cyan',
      ],
    }"
    api-key="2v20ajrzng0ftoh79hde6tj4xavm9ej7au8gq939q914sb0w"
    v-bind:value="value"
    @input="handleInput"
  />
</template>

<script>
import Editor from '@tinymce/tinymce-vue';

export default {
  name: 'Tinymce',
  props: ['value'],
  components: {
    tinymce: Editor,
  },
  methods: {
    handleInput(e) {
      this.$emit('input', e);
    },
    pickerCallback(cb, value, meta) {
      let input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');

      input.onchange = function () {
        let file = this.files[0];
        let reader = new FileReader();

        reader.onload = function () {
          let id = 'blobid' + new Date().getTime();
          let blobCache = tinymce.activeEditor.editorUpload.blobCache;
          let base64 = reader.result.split(',')[1];
          let blobInfo = blobCache.create(id, file, base64);
          blobCache.add(blobInfo);
          cb(blobInfo.blobUri(), { title: file.name });
        };

        reader.readAsDataURL(file);
      };

      input.click();
    },
  },
};
</script>
