<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Latest Updates</h3>
        </div>

        <div v-if="isOwner" class="rounded-lg border bg-muted/50 p-4">
            <h4 class="mb-3 text-sm font-medium">Post an Update</h4>
            <form @submit.prevent="submitUpdate" class="space-y-3">
                <Input
                    v-model="form.title"
                    placeholder="Title (e.g. New Milestone Reached)"
                />
                <Textarea
                    v-model="form.content"
                    placeholder="Share details with your subscribers..."
                />
                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        Post Update
                    </Button>
                </div>
            </form>
        </div>

        <div class="relative ml-3 space-y-8 border-l border-muted pb-4">
            <div
                v-if="updates.length === 0"
                class="pl-6 text-sm text-muted-foreground"
            >
                No updates yet.
            </div>

            <div
                v-for="update in updates"
                :key="update.id"
                class="relative pl-6"
            >
                <div class="flex flex-col space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold">
                            {{
                                update.type === 'manual'
                                    ? update.title
                                    : $t('updates.system_update_label')
                            }}
                        </span>
                        <span class="text-xs text-muted-foreground">{{
                            formatDate(update.created_at)
                        }}</span>
                    </div>

                    <p class="text-sm whitespace-pre-wrap text-foreground/80">
                        <template v-if="update.type === 'manual'">
                            {{ update.content }}
                        </template>
                        <template v-else>
                            {{ $t(update.translation_key, update.attributes) }}
                        </template>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { formatDistanceToNow } from 'date-fns';

const props = defineProps<{
    listingId: number;
    updates: Array<any>;
    isOwner: boolean;
}>();

const form = useForm({
    title: '',
    content: '',
});

function submitUpdate() {
    form.post(`/listings/${props.listingId}/updates`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function formatDate(date: string) {
    return formatDistanceToNow(new Date(date), { addSuffix: true });
}
</script>
