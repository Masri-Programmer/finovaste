<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Separator } from '@/components/ui/separator';
import { useClipboard, useToggle } from '@vueuse/core';
import {
    Copy,
    Facebook,
    Linkedin,
    Mail,
    Share2,
    Twitter,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { useToast } from 'vue-toastification';

import { trans } from 'laravel-vue-i18n';

const props = defineProps<{
    shareUrl: string;
    listingTitle: string;
}>();

const toast = useToast();

const [showShareDialog, toggleShareDialog] = useToggle(false);

const { copy, isSupported: clipboardSupported } = useClipboard({
    source: props.shareUrl,
});

const shareText = computed(() =>
    trans('listings.share.share_text', { title: props.listingTitle }),
);

const handleCopyLink = () => {
    if (!clipboardSupported.value) {
        toast.error(trans('listings.share.clipboard_not_supported'));
        return;
    }

    copy(props.shareUrl);
    toast.success(trans('listings.share.link_copied'));
};

const handleShare = (platform: string) => {
    let url = '';
    const encodedUrl = encodeURIComponent(props.shareUrl);
    const encodedText = encodeURIComponent(shareText.value);

    switch (platform) {
        case 'facebook':
            url = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
            break;
        case 'twitter':
            url = `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedText}`;
            break;
        case 'linkedin':
            url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`;
            break;
        case 'email':
            url = `mailto:?subject=${encodeURIComponent(
                props.listingTitle,
            )}&body=${encodeURIComponent(shareText.value + ' ' + props.shareUrl)}`;
            break;
    }

    if (url) {
        window.open(url, '_blank', 'width=600,height=400,noopener,noreferrer');
    }
};
</script>

<template>
    <Dialog :open="showShareDialog" @update:open="toggleShareDialog">
        <DialogTrigger as-child @click.prevent>
            <Button
                variant="secondary"
                class="relative flex h-10 w-10 items-center justify-center rounded-full bg-background/70 text-gray-600 backdrop-blur-sm hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100"
            >
                <span class="sr-only">Share</span>
                <Share2 class="h-5 w-5" />
            </Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ $t('listings.share.title') }}</DialogTitle>
                <DialogDescription>
                    {{ $t('listings.share.description') }}
                </DialogDescription>
            </DialogHeader>

            <div class="space-y-4 py-2">
                <div class="flex items-center gap-2">
                    <Input
                        :model-value="shareUrl"
                        read-only
                        class="flex-1"
                        :aria-label="$t('listings.share.share_link_label')"
                    />
                    <Button
                        variant="outline"
                        type="button"
                        @click="handleCopyLink"
                    >
                        <Copy class="mr-2 h-4 w-4" />
                        {{ $t('listings.share.copy_link') }}
                    </Button>
                </div>

                <Separator />

                <div class="grid grid-cols-2 gap-3">
                    <Button
                        variant="outline"
                        class="gap-2"
                        @click="handleShare('facebook')"
                    >
                        <Facebook class="h-4 w-4" />
                        Facebook
                    </Button>
                    <Button
                        variant="outline"
                        class="gap-2"
                        @click="handleShare('twitter')"
                    >
                        <Twitter class="h-4 w-4" />
                        Twitter
                    </Button>
                    <Button
                        variant="outline"
                        class="gap-2"
                        @click="handleShare('linkedin')"
                    >
                        <Linkedin class="h-4 w-4" />
                        LinkedIn
                    </Button>
                    <Button
                        variant="outline"
                        class="gap-2"
                        @click="handleShare('email')"
                    >
                        <Mail class="h-4 w-4" />
                        Email
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
