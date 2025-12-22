<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { type UseForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { de } from 'date-fns/locale';
import { trans } from 'laravel-vue-i18n';
import { Calendar as CalendarIcon } from 'lucide-vue-next';
import { computed, PropType } from 'vue';

const props = defineProps({
    form: {
        type: Object as PropType<UseForm<any>>,
        required: true,
    },
});

const formattedStartsAt = computed(() => {
    return props.form.starts_at
        ? format(props.form.starts_at, 'PPP', { locale: de })
        : trans('createListing.fields.starts_at.placeholder');
});
const formattedEndsAt = computed(() => {
    return props.form.ends_at
        ? format(props.form.ends_at, 'PPP', { locale: de })
        : trans('createListing.fields.ends_at.placeholder');
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <Label for="start_price">
                {{ $t('createListing.fields.start_price.label') }}
            </Label>
            <Input
                id="start_price"
                v-model.number="form.start_price"
                type="number"
                step="0.01"
                :placeholder="
                    $t('createListing.fields.start_price.placeholder')
                "
                required
            />
        </div>
        <div class="space-y-2">
            <Label for="reserve_price">
                {{ $t('createListing.fields.reserve_price.label') }}
            </Label>
            <Input
                id="reserve_price"
                v-model.number="form.reserve_price"
                type="number"
                step="0.01"
                :placeholder="
                    $t('createListing.fields.reserve_price.placeholder')
                "
            />
        </div>
        <div class="space-y-2">
            <Label for="purchase_price">
                {{ $t('createListing.fields.buy_it_now_price.label') }}
            </Label>
            <Input
                id="purchase_price"
                v-model.number="form.purchase_price"
                type="number"
                step="0.01"
                :placeholder="
                    $t('createListing.fields.buy_it_now_price.placeholder')
                "
            />
        </div>
        <div></div>
        <div class="space-y-2">
            <Label for="starts_at">
                {{ $t('createListing.fields.starts_at.label') }}
            </Label>
            <Popover>
                <PopoverTrigger as-child>
                    <Button
                        variant="outline"
                        :class="
                            cn(
                                'w-full justify-start text-left font-normal',
                                !form.starts_at && 'text-muted-foreground',
                            )
                        "
                    >
                        <CalendarIcon class="mr-2 h-4 w-4" />
                        {{ formattedStartsAt }}
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-auto p-0">
                    <Calendar v-model="form.starts_at" />
                </PopoverContent>
            </Popover>
        </div>
        <div class="space-y-2">
            <Label for="ends_at">
                {{ $t('createListing.fields.ends_at.label') }}
                <span class="text-destructive">*</span>
            </Label>
            <Popover>
                <PopoverTrigger as-child>
                    <Button
                        variant="outline"
                        :class="
                            cn(
                                'w-full justify-start text-left font-normal',
                                !form.ends_at && 'text-muted-foreground',
                            )
                        "
                    >
                        <CalendarIcon class="mr-2 h-4 w-4" />
                        {{ formattedEndsAt }}
                    </Button>
                </PopoverTrigger>
                <PopoverContent class="w-auto p-0">
                    <Calendar v-model="form.ends_at" />
                </PopoverContent>
            </Popover>
        </div>
    </div>
</template>
