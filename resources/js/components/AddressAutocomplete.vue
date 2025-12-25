<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Loader } from '@googlemaps/js-api-loader';
import { onMounted, ref } from 'vue';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
    id?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'address-selected', payload: AddressResult): void;
}>();

export interface AddressResult {
    formatted_address: string;
    street_number: string;
    route: string;
    locality: string;
    administrative_area_level_1: string;
    country: string;
    postal_code: string;
}

const inputRef = ref<InstanceType<typeof Input> | null>(null);
const autocomplete = ref<google.maps.places.Autocomplete | null>(null);

const initAutocomplete = async () => {
    try {
        const loader = new Loader({
            apiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY,
            version: 'weekly',
            libraries: ['places'],
        });

        const { Autocomplete } = (await loader.importLibrary(
            'places',
        )) as google.maps.PlacesLibrary;

        if (inputRef.value?.$el) {
            // Unwrap the actual input element from the CleanVue Input component if necessary
            // The CleanVue Input component likely renders an input tag directly or wraps it.
            // We need the raw HTMLInputElement.
            const inputElement =
                inputRef.value.$el.tagName === 'INPUT'
                    ? inputRef.value.$el
                    : inputRef.value.$el.querySelector('input');

            if (!inputElement) return;

            autocomplete.value = new Autocomplete(inputElement, {
                fields: ['address_components', 'formatted_address', 'geometry'],
                types: ['address'],
            });

            autocomplete.value.addListener('place_changed', () => {
                const place = autocomplete.value?.getPlace();
                if (!place) return;

                emit('update:modelValue', place.formatted_address || '');

                // Parse address components
                const result: AddressResult = {
                    formatted_address: place.formatted_address || '',
                    street_number: '',
                    route: '',
                    locality: '',
                    administrative_area_level_1: '',
                    country: '',
                    postal_code: '',
                };

                place.address_components?.forEach((component) => {
                    const types = component.types;
                    if (types.includes('street_number')) {
                        result.street_number = component.long_name;
                    }
                    if (types.includes('route')) {
                        result.route = component.long_name;
                    }
                    if (types.includes('locality')) {
                        result.locality = component.long_name;
                    }
                    if (types.includes('administrative_area_level_1')) {
                        result.administrative_area_level_1 =
                            component.long_name;
                    }
                    if (types.includes('country')) {
                        result.country = component.long_name;
                    }
                    if (types.includes('postal_code')) {
                        result.postal_code = component.long_name;
                    }
                });

                emit('address-selected', result);
            });
        }
    } catch (error) {
        console.error('Error loading Google Maps API:', error);
    }
};

onMounted(() => {
    initAutocomplete();
});
</script>

<template>
    <Input
        ref="inputRef"
        :id="id"
        type="text"
        :model-value="modelValue"
        @input="
            emit('update:modelValue', ($event.target as HTMLInputElement).value)
        "
        :placeholder="placeholder"
        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
    />
</template>
