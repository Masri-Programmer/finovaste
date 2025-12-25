<template>
    <Card
        v-if="listing.type === 'private'"
        class="border-primary/20 bg-primary/5"
    >
        <CardHeader class="pb-2">
            <CardTitle class="flex items-center gap-2 text-sm font-bold">
                <Lock class="h-4 w-4 text-primary" />
                {{ $t('showListing.private.title') }}
            </CardTitle>
            <CardDescription class="text-xs">
                {{ $t('showListing.private.description') }}
            </CardDescription>
        </CardHeader>
        <CardContent class="space-y-3">
            <div class="flex items-center gap-2">
                <Input
                    readonly
                    :value="shareUrl"
                    class="h-8 bg-background text-xs"
                />
                <Button
                    size="sm"
                    variant="outline"
                    class="h-8 px-2"
                    @click="copyLink"
                >
                    <Copy v-if="!copied" class="h-3.5 w-3.5" />
                    <Check v-else class="h-3.5 w-3.5 text-green-500" />
                </Button>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Listing } from '@/types/listings';
import { trans } from 'laravel-vue-i18n';
import { Check, Copy, Lock } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useToast } from 'vue-toastification';

const props = defineProps<{
    listing: Listing;
}>();

const toast = useToast();
const copied = ref(false);

const shareUrl = computed(() => window.location.href);

const copyLink = async () => {
    try {
        await navigator.clipboard.writeText(shareUrl.value);
        copied.value = true;
        toast.success(trans('showListing.private.copied'));
        setTimeout(() => (copied.value = false), 2000);
    } catch (err) {
        toast.error(trans('showListing.private.copy_failed'));
    }
};
</script>
