<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { Editor } from '@tiptap/vue-3';
import {
    Bold,
    Code,
    Heading2,
    Heading3,
    Italic,
    List,
    ListOrdered,
    Minus,
    Quote,
    Redo,
    Strikethrough,
    Undo,
} from 'lucide-vue-next';

defineProps<{
    editor: Editor | undefined;
    isHtmlMode: boolean;
}>();

const emit = defineEmits(['toggleHtml']);
</script>

<template>
    <div
        v-if="editor"
        class="flex flex-wrap items-center gap-1 rounded-t-md border border-input bg-muted/50 p-2"
    >
        <Button
            type="button"
            size="sm"
            :variant="editor.isActive('bold') ? 'secondary' : 'ghost'"
            @click="editor.chain().focus().toggleBold().run()"
            :title="$t('listings.editor.bold')"
        >
            <component :is="Bold" class="h-4 w-4" />
        </Button>
        <Button
            type="button"
            size="sm"
            :variant="editor.isActive('italic') ? 'secondary' : 'ghost'"
            @click="editor.chain().focus().toggleItalic().run()"
            :title="$t('listings.editor.italic')"
        >
            <component :is="Italic" class="h-4 w-4" />
        </Button>
        <Button
            type="button"
            size="sm"
            :variant="editor.isActive('strike') ? 'secondary' : 'ghost'"
            @click="editor.chain().focus().toggleStrike().run()"
            :title="$t('listings.editor.strike')"
        >
            <component :is="Strikethrough" class="h-4 w-4" />
        </Button>

        <div class="flex items-center justify-center px-1">
            <input
                type="color"
                class="h-6 w-6 cursor-pointer rounded border-none bg-transparent p-0"
                @input="
                    editor
                        .chain()
                        .focus()
                        .setColor(($event.target as HTMLInputElement).value)
                        .run()
                "
                :value="editor.getAttributes('textStyle').color || '#000000'"
                :title="$t('listings.editor.color')"
            />
        </div>

        <Button
            type="button"
            size="sm"
            :variant="
                editor.isActive('heading', { level: 2 }) ? 'secondary' : 'ghost'
            "
            @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
            :title="$t('listings.editor.h2')"
        >
            <component :is="Heading2" class="h-4 w-4" />
        </Button>
        <Button
            type="button"
            size="sm"
            :variant="
                editor.isActive('heading', { level: 3 }) ? 'secondary' : 'ghost'
            "
            @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
            :title="$t('listings.editor.h3')"
        >
            <component :is="Heading3" class="h-4 w-4" />
        </Button>

        <Button
            type="button"
            size="sm"
            :variant="editor.isActive('bulletList') ? 'secondary' : 'ghost'"
            @click="editor.chain().focus().toggleBulletList().run()"
            :title="$t('listings.editor.bullet_list')"
        >
            <component :is="List" class="h-4 w-4" />
        </Button>
        <Button
            type="button"
            size="sm"
            :variant="editor.isActive('orderedList') ? 'secondary' : 'ghost'"
            @click="editor.chain().focus().toggleOrderedList().run()"
            :title="$t('listings.editor.ordered_list')"
        >
            <component :is="ListOrdered" class="h-4 w-4" />
        </Button>

        <Button
            type="button"
            size="sm"
            :variant="editor.isActive('blockquote') ? 'secondary' : 'ghost'"
            @click="editor.chain().focus().toggleBlockquote().run()"
            :title="$t('listings.editor.quote')"
        >
            <component :is="Quote" class="h-4 w-4" />
        </Button>
        <Button
            type="button"
            size="sm"
            variant="ghost"
            @click="editor.chain().focus().setHorizontalRule().run()"
            :title="$t('listings.editor.hr')"
        >
            <component :is="Minus" class="h-4 w-4" />
        </Button>

        <div class="ml-auto flex items-center gap-1">
            <Button
                type="button"
                size="sm"
                variant="ghost"
                @click="editor.chain().focus().undo().run()"
                :disabled="!editor.can().chain().focus().undo().run()"
                :title="$t('listings.editor.undo')"
            >
                <component :is="Undo" class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                size="sm"
                variant="ghost"
                @click="editor.chain().focus().redo().run()"
                :disabled="!editor.can().chain().focus().redo().run()"
                :title="$t('listings.editor.redo')"
            >
                <component :is="Redo" class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                size="sm"
                :variant="isHtmlMode ? 'secondary' : 'ghost'"
                @click="emit('toggleHtml')"
                :title="$t('listings.editor.source')"
            >
                <component :is="Code" class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>
