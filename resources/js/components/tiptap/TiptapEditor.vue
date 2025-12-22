<script setup lang="ts">
import { Color } from '@tiptap/extension-color';
import { TextStyle } from '@tiptap/extension-text-style';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { onBeforeUnmount, ref, watch } from 'vue';
import TiptapToolbar from './TiptapToolbar.vue';

const props = defineProps<{
    content: string;
    placeholder?: string;
}>();

const emit = defineEmits(['update:content']);

const isHtmlMode = ref(false);
const rawHtml = ref(props.content);

const editor = useEditor({
    content: props.content,
    extensions: [StarterKit, TextStyle, Color],
    editorProps: {
        attributes: {
            class: 'prose dark:prose-invert max-w-none focus:outline-none min-h-[250px] p-4',
        },
    },
    onUpdate: ({ editor }) => {
        const html = editor.getHTML();
        rawHtml.value = html;
        emit('update:content', html);
    },
});

function toggleHtmlMode() {
    isHtmlMode.value = !isHtmlMode.value;

    if (isHtmlMode.value) {
        // Sync Editor -> Textarea
        rawHtml.value = editor.value?.getHTML() || '';
    } else {
        // Sync Textarea -> Editor
        editor.value?.commands.setContent(rawHtml.value);
    }
}

// Watch for external content changes (e.g. database reload)
watch(
    () => props.content,
    (value) => {
        if (
            editor.value &&
            editor.value.getHTML() !== value &&
            !isHtmlMode.value
        ) {
            editor.value.commands.setContent(value, false);
            rawHtml.value = value;
        }
    },
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});
</script>

<template>
    <div
        class="flex flex-col rounded-md border border-input bg-card shadow-sm ring-offset-background focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2"
    >
        <TiptapToolbar
            :editor="editor"
            :is-html-mode="isHtmlMode"
            @toggle-html="toggleHtmlMode"
        />

        <div class="relative min-h-[250px] w-full bg-background">
            <EditorContent v-show="!isHtmlMode" :editor="editor" />
            <textarea
                v-show="isHtmlMode"
                v-model="rawHtml"
                class="absolute inset-0 h-full w-full resize-none bg-background p-4 font-mono text-sm focus:outline-none"
                @change="emit('update:content', rawHtml)"
            ></textarea>
        </div>
    </div>
</template>

<style>
/* Ensure these styles are present globally or scoped here.
   These leverage your app.css variables for Shadcn/Tailwind.
*/
.ProseMirror {
    outline: none;
    min-height: 250px;
}

.prose p {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
    line-height: 1.6;
}

.prose ul,
.prose ol {
    padding-left: 1.5rem;
}

.prose ul {
    list-style-type: disc;
}

.prose ol {
    list-style-type: decimal;
}

.prose blockquote {
    border-left: 3px solid hsl(var(--primary));
    padding-left: 1rem;
    font-style: italic;
    color: hsl(var(--muted-foreground));
}
</style>
