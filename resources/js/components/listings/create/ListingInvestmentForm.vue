<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { PropType } from 'vue';

defineProps({
    form: {
        type: Object as PropType<any>,
        required: true,
    },
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <Label for="investment_goal">
                {{ $t('createListing.fields.investment_goal') }}
            </Label>
            <Input
                id="investment_goal"
                v-model="form.investment_goal"
                type="number"
                step="0.01"
                min="0"
                :placeholder="
                    $t('createListing.placeholders.investment_goal', {
                        amount: '50000',
                    })
                "
            />
            <p v-if="form.errors.investment_goal" class="text-sm text-red-600">
                {{ form.errors.investment_goal }}
            </p>
        </div>

        <div class="space-y-2">
            <Label for="minimum_investment">
                {{ $t('createListing.fields.minimum_investment') }}
            </Label>
            <Input
                id="minimum_investment"
                v-model="form.minimum_investment"
                type="number"
                step="0.01"
                min="0"
                :placeholder="
                    $t('createListing.placeholders.minimum_investment', {
                        amount: '500',
                    })
                "
            />
            <p
                v-if="form.errors.minimum_investment"
                class="text-sm text-red-600"
            >
                {{ form.errors.minimum_investment }}
            </p>
        </div>

        <div class="space-y-2">
            <Label for="shares_offered">
                {{ $t('createListing.fields.shares_offered') }}
            </Label>
            <Input
                id="shares_offered"
                v-model="form.shares_offered"
                type="number"
                min="1"
                step="1"
                :placeholder="
                    $t('createListing.placeholders.shares_offered', {
                        amount: '1000',
                    })
                "
            />
            <p v-if="form.errors.shares_offered" class="text-sm text-red-600">
                {{ form.errors.shares_offered }}
            </p>
        </div>

        <div class="space-y-2">
            <Label for="share_price">
                {{ $t('createListing.fields.share_price') }}
            </Label>
            <Input
                id="share_price"
                v-model="form.share_price"
                type="number"
                step="0.01"
                min="0"
                :placeholder="
                    $t('createListing.placeholders.share_price', {
                        amount: '50',
                    })
                "
            />
            <p v-if="form.errors.share_price" class="text-sm text-red-600">
                {{ form.errors.share_price }}
            </p>
        </div>
    </div>
</template>
