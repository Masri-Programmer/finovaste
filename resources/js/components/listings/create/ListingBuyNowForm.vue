<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
                {{ $t('listing.createListing.fields.price.label') }}
            </Label>
            <Input
                id="price"
                v-model.number="form.price"
                type="number"
                step="0.01"
                :placeholder="
                    $t('listing.createListing.fields.price.placeholder')
                "
                required
            />
        </div>
        <div class="space-y-2">
            <Label for="quantity">
                {{ $t('listing.createListing.fields.quantity.label') }}
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
        <div class="space-y-2">
            <Label for="condition">
                {{ $t('listing.createListing.fields.condition.label') }}
            </Label>
            <Select v-model="form.condition">
                <SelectTrigger>
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="new">
                        {{
                            $t(
                                'listing.createListing.fields.condition.options.new',
                            )
                        }}
                    </SelectItem>
                    <SelectItem value="used">
                        {{
                            $t(
                                'listing.createListing.fields.condition.options.used',
                            )
                        }}
                    </SelectItem>
                    <SelectItem value="refurbished">
                        {{
                            $t(
                                'listing.createListing.fields.condition.options.refurbished',
                            )
                        }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>
    </div>
</template>
