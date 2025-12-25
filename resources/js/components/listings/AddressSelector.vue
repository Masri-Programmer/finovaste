<script setup lang="ts">
import AddressController from '@/actions/App/Http/Controllers/Settings/AddressController';
import AddressAutocomplete, {
    type AddressResult,
} from '@/components/AddressAutocomplete.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { type Address } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    modelValue: number | null;
    addresses: Address[];
    error?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const isDialogOpen = ref(false);

const form = useForm({
    street: '',
    city: '',
    state: '',
    zip: '',
    country: '',
    is_primary: false,
});

const handleAddressSelected = (result: AddressResult) => {
    form.street = `${result.route} ${result.street_number}`.trim();
    form.city = result.locality;
    form.state = result.administrative_area_level_1;
    form.zip = result.postal_code;
    form.country = result.country;
};

const submitNewAddress = () => {
    form.post(AddressController.store.url(), {
        onSuccess: (page) => {
            isDialogOpen.value = false;
            form.reset();
            // The addresses prop should update via Inertia reload
            // We want to select the newly created address, but we might need
            // to find its ID. Usually it would be the latest one or we could
            // try to find it in the new props.
        },
    });
};

const selectedAddress = computed(() => {
    return props.addresses.find((a) => a.id === props.modelValue);
});
</script>

<template>
    <div class="space-y-2">
        <div class="flex items-end gap-2">
            <div class="flex-1 space-y-2">
                <Select
                    :model-value="props.modelValue?.toString()"
                    @update:model-value="
                        (val) => emit('update:modelValue', Number(val))
                    "
                >
                    <SelectTrigger class="w-full">
                        <SelectValue
                            :placeholder="$t('address.select_placeholder')"
                        />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem
                            v-for="address in props.addresses"
                            :key="address.id"
                            :value="address.id.toString()"
                        >
                            <div class="flex flex-col text-left">
                                <span class="font-medium">{{
                                    address.street
                                }}</span>
                                <span class="text-xs text-muted-foreground"
                                    >{{ address.zip }} {{ address.city }},
                                    {{ address.country }}</span
                                >
                            </div>
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <Button
                type="button"
                variant="outline"
                size="icon"
                @click="isDialogOpen = true"
                class="mb-3 shrink-0"
                :title="$t('address.add_new')"
            >
                <Plus class="h-4 w-4" />
            </Button>
        </div>

        <!-- <div
            v-if="selectedAddress"
            class="mt-2 rounded-lg border bg-muted/30 p-3 text-sm"
        >
            <div class="flex items-start gap-2">
                <MapPin class="mt-0.5 h-4 w-4 text-muted-foreground" />
                <div>
                    <p class="font-medium">{{ selectedAddress.street }}</p>
                    <p class="text-muted-foreground">
                        {{ selectedAddress.zip }} {{ selectedAddress.city
                        }}<br />
                        {{ selectedAddress.country }}
                    </p>
                </div>
            </div>
        </div> -->

        <InputError :message="props.error" />

        <Dialog :open="isDialogOpen" @update:open="isDialogOpen = $event">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>{{ $t('address.add_new') }}</DialogTitle>
                    <DialogDescription>
                        {{ $t('address.description') }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4 py-4">
                    <div class="grid gap-2">
                        <Label for="street">{{ $t('address.street') }}</Label>
                        <AddressAutocomplete
                            id="street"
                            :model-value="form.street"
                            @update:model-value="form.street = $event"
                            @address-selected="handleAddressSelected"
                            :placeholder="$t('address.street')"
                            required
                        />
                        <InputError :message="form.errors.street" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="city">{{ $t('address.city') }}</Label>
                            <Input id="city" v-model="form.city" required />
                            <InputError :message="form.errors.city" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="state">{{ $t('address.state') }}</Label>
                            <Input id="state" v-model="form.state" required />
                            <InputError :message="form.errors.state" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="zip">{{ $t('address.zip') }}</Label>
                            <Input id="zip" v-model="form.zip" required />
                            <InputError :message="form.errors.zip" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="country">{{
                                $t('address.country')
                            }}</Label>
                            <Input
                                id="country"
                                v-model="form.country"
                                required
                            />
                            <InputError :message="form.errors.country" />
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="isDialogOpen = false"
                    >
                        {{ $t('address.cancel') }}
                    </Button>
                    <Button
                        type="button"
                        @click="submitNewAddress"
                        :disabled="form.processing"
                    >
                        {{ $t('address.save') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
