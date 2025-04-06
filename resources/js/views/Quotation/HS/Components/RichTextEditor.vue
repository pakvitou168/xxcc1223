<!-- RichTextEditor.vue -->
<template>
  <div class="w-full">
    <!-- Add label above the editor -->
    <label class="block text-sm font-medium text-gray-700 mb-1">
      {{ label }}
    </label>
    <Editor
        v-model="editorContent"
        :placeholder="placeholder"
        :readonly="readonly"
        :modules="modules"
        :style="editorStyle"
        class="w-full"
        @text-change="handleTextChange"
        editorStyle="height: 100px"
    />
    <!-- Optional character count -->
    <div class="text-sm text-gray-500 mt-2" v-if="showCharCount">
      Characters: {{ stripHtml(editorContent).length }}
    </div>
  </div>
</template>

<script>
import { ref, defineProps, defineEmits, watch } from 'vue'
import Editor from 'primevue/editor'

export default {
  name: 'RichTextEditor',
  components: {
    Editor
  },
  props: {
    modelValue: {
      type: String,
      default: ''
    },
    label: {
      type: String,
      default: 'abc'
    },
    placeholder: {
      type: String,
      default: 'Start typing...'
    },
    readonly: {
      type: Boolean,
      default: false
    },
    showCharCount: {
      type: Boolean,
      default: true
    },
    modules: {
      type: Object,
      default: () => ({
        toolbar: [
          ['bold', 'italic', 'underline'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          ['link'],
          [{ 'align': [] }],
          ['clean']
        ]
      })
    }
  },
  emits: ['update:modelValue', 'text-change'],
  setup(props, { emit }) {
    const editorContent = ref(props.modelValue)
    const editorStyle = {
      // height: '200px'
    }

    watch(() => props.modelValue, (newValue) => {
      if (newValue !== editorContent.value) {
        editorContent.value = newValue
      }
    })

    watch(editorContent, (newValue) => {
      emit('update:modelValue', newValue)
    })

    const handleTextChange = (event) => {
      emit('text-change', event)
    }

    const stripHtml = (html) => {
      return html.replace(/<[^>]*>/g, '')
    }

    return {
      editorContent,
      editorStyle,
      handleTextChange,
      stripHtml
    }
  }
}
</script>

<style>
.p-editor-container .p-editor-toolbar {
  @apply border border-gray-300 rounded-t;
}

.p-editor-container .p-editor-content {
  @apply border border-t-0 border-gray-300 rounded-b;
}

.p-editor-container .p-editor-toolbar.ql-snow {
  @apply bg-gray-50;
}
</style>