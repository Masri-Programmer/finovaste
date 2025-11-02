<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { type UseForm } from '@inertiajs/vue3';
import { PropType } from 'vue';
import { useI18n } from 'vue-i18n';

defineProps({
    form: {
        type: Object as PropType<UseForm<any>>,
        required: true,
    },
});

const { t } = useI18n();
</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <Label for="donation_goal">
                {{ t('listing.createListing.fields.donation_goal.label') }}
            </Label>
            <Input
                id="donation_goal"
                v-model.number="form.donation_goal"
                type="number"
                step="1"
                :placeholder="
                    t('listing.createListing.fields.donation_goal.placeholder')
                "
                required
            />
        </div>
        <div class="flex items-center space-x-2 pt-6">
            <Switch id="is_goal_flexible" v-model="form.is_goal_flexible" />
            <Label for="is_goal_flexible">
                {{ t('listing.createListing.fields.is_goal_flexible.label') }}
            </Label>
        </div>
    </div>
</template>
