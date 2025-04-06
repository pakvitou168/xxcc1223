<template>
  <ckeditor ref="ckeditorRef" :editor="editor" :config="editorConfig" :modelValue="modelValue" @input="updateValue">
  </ckeditor>
</template>

<script setup>
import CKEditor from '@ckeditor/ckeditor5-vue';
import ClassicEditor from "@ckeditor/ckeditor5-editor-classic/src/classiceditor";

import Alignment from "@ckeditor/ckeditor5-alignment/src/alignment.js";
import Essentials from "@ckeditor/ckeditor5-essentials/src/essentials";
import Autoformat from "@ckeditor/ckeditor5-autoformat/src/autoformat";
import Bold from "@ckeditor/ckeditor5-basic-styles/src/bold";
import Italic from "@ckeditor/ckeditor5-basic-styles/src/italic";
import Strikethrough from "@ckeditor/ckeditor5-basic-styles/src/strikethrough";
import Underline from "@ckeditor/ckeditor5-basic-styles/src/underline";
import BlockQuote from "@ckeditor/ckeditor5-block-quote/src/blockquote";
import Heading from "@ckeditor/ckeditor5-heading/src/heading";
import Indent from "@ckeditor/ckeditor5-indent/src/indent";
import Link from "@ckeditor/ckeditor5-link/src/link";
import List from "@ckeditor/ckeditor5-list/src/list";
import ListProperties from '@ckeditor/ckeditor5-list/src/listproperties';
import MediaEmbed from "@ckeditor/ckeditor5-media-embed/src/mediaembed";
import Paragraph from "@ckeditor/ckeditor5-paragraph/src/paragraph";
import Table from "@ckeditor/ckeditor5-table/src/table";
import TableToolbar from "@ckeditor/ckeditor5-table/src/tabletoolbar";
import TableProperties from '@ckeditor/ckeditor5-table/src/tableproperties';
import TableCellProperties from '@ckeditor/ckeditor5-table/src/tablecellproperties';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import Base64UploadAdapter from "@ckeditor/ckeditor5-upload/src/adapters/base64uploadadapter";
import ImageResizeEditing from '@ckeditor/ckeditor5-image/src/imageresize/imageresizeediting';
import ImageResizeButtons from '@ckeditor/ckeditor5-image/src/imageresize/imageresizebuttons';
import ImageResizeHandles from '@ckeditor/ckeditor5-image/src/imageresize/imageresizehandles';
import { reactive, ref, watch, computed, provide, onMounted, defineComponent } from "vue";

const ckeditor = CKEditor.component
const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: ''
  }
});
const emits = defineEmits(['update:modelValue'])
const editor = ClassicEditor;
const content = ref('');
const ckeditorRef = ref({});


const customColorPalette = [
  {
    color: 'hsl(4, 90%, 58%)',
    label: 'Red'
  },
  {
    color: 'hsl(340, 82%, 52%)',
    label: 'Pink'
  },
  {
    color: 'hsl(291, 64%, 42%)',
    label: 'Purple'
  },
  {
    color: 'hsl(262, 52%, 47%)',
    label: 'Deep Purple'
  },
  {
    color: 'hsl(231, 48%, 48%)',
    label: 'Indigo'
  },
  {
    color: 'hsl(207, 90%, 54%)',
    label: 'Blue'
  },
];
const editorConfig = computed(() => {
  return {
    placeholder: props.placeholder,
    plugins: [
      Alignment,
      Essentials,
      Autoformat,
      Bold,
      Italic,
      Strikethrough,
      Underline,
      BlockQuote,
      Heading,
      Image,
      ImageUpload,
      ImageStyle,
      ImageCaption,
      ImageToolbar,
      ImageResizeEditing,
      ImageResizeButtons,
      ImageResizeHandles,
      Base64UploadAdapter,
      Indent,
      Link,
      List,
      MediaEmbed,
      Paragraph,
      Table,
      TableToolbar,
      TableProperties,
      TableCellProperties,
      ListProperties
    ],
    image: {
      resize: true,
      resizeOptions: [
        {
          name: 'resizeImage:original',
          value: null,
          icon: 'original'
        },
        {
          name: 'resizeImage:50',
          value: '50',
          icon: 'medium'
        },
        {
          name: 'resizeImage:75',
          value: '75',
          icon: 'large'
        },
        {
          name: 'resizeImage:100',
          value: '100',
          icon: 'large'
        },
      ],
      toolbar: [
        'imageStyle:inline',
        'imageStyle:block',
        'imageStyle:side',
        '|',
        'toggleImageCaption',
        'imageTextAlternative',
        'resizeImage'
      ]
    },

    toolbar: {
      items: [
        "heading",
        "|",
        "bold",
        "italic",
        "underline",
        "strikethrough",
        "link",
        "bulletedList",
        "numberedList",
        "alignment",
        "|",
        "outdent",
        "indent",
        "|",
        "uploadImage",
        "blockQuote",
        "insertTable",
        "mediaEmbed",
        "undo",
        "redo",
      ],
    },
    list: {
      properties: {
        styles: true,
        startIndex: true,
        reversed: true
      }
    },
    table: {
      contentToolbar: ["tableColumn", "tableRow", "mergeTableCells", 'tableProperties', 'tableCellProperties'],
      tableProperties: {
        borderColors: customColorPalette,
        backgroundColors: customColorPalette,
        // The default styles for tables in the editor.
        // They should be synchronized with the content styles.
        defaultProperties: {
          borderStyle: 'dashed',
          borderColor: 'hsl(90, 75%, 60%)',
          borderWidth: '3px',
          alignment: 'left',
          width: '550px',
          height: '450px'
        },
        // The default styles for table cells in the editor.
        // They should be synchronized with the content styles.
        tableCellProperties: {
          defaultProperties: {
            horizontalAlignment: 'center',
            verticalAlignment: 'bottom',
            padding: '10px'
          }
        }
      },
      tableCellProperties: {
        borderColors: customColorPalette,
        backgroundColors: customColorPalette
      }
    },

    heading: {
      options: [
        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
        { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
        { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
      ]
    }
  }
});
const updateValue = (val) => {
  emits("update:modelValue", val);
};
onMounted(() => {
})

</script>
<style lang="css">
.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline {
  min-height: 20rem;
}

.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline>h1 {
  font-size: 5rem;
}

.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline>h2 {
  font-size: 4.5rem;
}

.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline>h3 {
  font-size: 3.5rem;
}

.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline>h4 {
  font-size: 3rem;
}

.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline>h5 {
  font-size: 2.5rem;
}

/* fix style order and un order list in ckeditor  */
.ck.ck-content ul,
.ck.ck-content ul li {
  list-style-type: inherit;
}

.ck.ck-content ul {
  /* Default user agent stylesheet, you can change it to your needs. */
  padding-left: 40px;
}

.ck.ck-content ol,
.ck.ck-content ol li {
  list-style-type: decimal;
}

.ck.ck-content ol {
  /* Default user agent stylesheet, you can change it to your needs. */
  padding-left: 40px;
}
</style>
