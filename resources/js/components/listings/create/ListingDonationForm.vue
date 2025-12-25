<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { type UseForm } from '@inertiajs/vue3';
import { PropType } from 'vue';

defineProps({
    form: {
        type: Object as PropType<UseForm<any>>,
        required: true,
    },
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <Label for="target">
                {{ $t('createListing.fields.target.label') }}
            </Label>
            <Input
                id="target"
                v-model.number="form.target"
                type="number"
                step="1"
                :placeholder="$t('createListing.fields.target.placeholder')"
                required
            />
        </div>
        <div class="flex items-center space-x-2 pt-6">
            <Switch id="is_capped" v-model="form.is_capped" />
            <Label for="is_capped">
                {{ $t('createListing.fields.is_capped.label') }}
            </Label>
        </div>
    </div>
</template>
