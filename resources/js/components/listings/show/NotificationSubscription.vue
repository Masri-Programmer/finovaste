<template>
    <form
        @submit.prevent="handleSubmit"
        class="space-y-6 rounded-lg border bg-card p-4 text-card-foreground shadow-sm"
    >
        <div class="space-y-1.5">
            <div class="flex items-center space-x-3">
                <Mail class="h-6 w-6 text-muted-foreground" />
                <h2 class="text-xl font-semibold text-foreground">
                    Get Updates
                </h2>
            </div>
            <p class="text-sm text-muted-foreground">
                Enter your email to receive notifications when this listing is
                updated.
            </p>
        </div>

        <div>
            <Label for="email" class="text-sm font-medium">Email Address</Label>
            <div class="mt-2 flex items-center space-x-2">
                <Input
                    id="email"
                    type="email"
                    placeholder="you@example.com"
                    v-model="form.email"
                    class="w-full grow"
                    required
                />
                <Button type="submit" :disabled="form.processing">
                    <Loader2
                        v-if="form.processing"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    Subscribe
                </Button>
            </div>
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">
                {{ form.errors.email }}
            </p>
        </div>
    </form>
</template>

<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/ListingSubscriptionController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm, usePage } from '@inertiajs/vue3';
import { Loader2, Mail } from 'lucide-vue-next';

const props = defineProps<{
    listingId: number;
}>();

const page = usePage();
const userEmail = page.props.auth?.user?.email || '';

const form = useForm({
    email: userEmail,
});

function handleSubmit() {
    form.post(store.url(props.listingId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
}
</script>
