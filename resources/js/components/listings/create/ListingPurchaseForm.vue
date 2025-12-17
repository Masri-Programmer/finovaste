<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="space-y-2">
            <Label for="price">
                {{ $t('createListing.fields.price.label') }}
            </Label>
            <Input
                id="price"
                v-model.number="form.price"
                type="number"
                step="0.01"
                :placeholder="$t('createListing.fields.price.placeholder')"
                required
            />
        </div>
        <div class="space-y-2">
            <Label for="quantity">
                {{ $t('createListing.fields.quantity.label') }}
            </Label>
            <Input
                id="quantity"
                v-model.number="form.quantity"
                type="number"
                step="1"
                min="1"
                required
            />
        </div>
        <!-- <div class="space-y-2">
            <Label for="condition">
                {{ $t('createListing.fields.condition.label') }}
            </Label>
            <Select v-model="form.condition">
                <SelectTrigger>
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="new">
                        {{ $t('createListing.fields.condition.options.new') }}
                    </SelectItem>
                    <SelectItem value="used">
                        {{ $t('createListing.fields.condition.options.used') }}
                    </SelectItem>
                    <SelectItem value="refurbished">
                        {{
                            $t(
                                'createListing.fields.condition.options.refurbished',
                            )
                        }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div> -->
    </div>
</template>
