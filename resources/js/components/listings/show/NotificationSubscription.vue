<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div class="space-y-1.5">
            <div class="flex items-center space-x-3">
                <Mail class="h-6 w-6 text-muted-foreground" />
                <h2 class="text-2xl font-semibold text-foreground">
                    {{ t('notifications.title') }}
                </h2>
            </div>
            <p class="text-sm text-muted-foreground">
                {{ t('notifications.subtitle') }}
            </p>
        </div>

        <div>
            <Label for="email" class="text-sm font-medium">{{
                t('notifications.email_label')
            }}</Label>
            <Input
                id="email"
                type="email"
                vFoc.auto
                :placeholder="t('notifications.email_placeholder')"
                v-model="form.email"
                class="mt-2"
                required
            />
        </div>

        <fieldset class="space-y-3">
            <legend class="text-sm font-medium text-foreground">
                {{ t('notifications.categories_title') }}
            </legend>
            <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="flex items-center space-x-2"
                >
                    <Checkbox
                        :id="category.id"
                        :checked="form.categories.includes(category.id)"
                        @update:checked="
                            (checked: boolean) =>
                                toggleCategory(checked, category.id)
                        "
                    />
                    <label
                        :for="category.id"
                        class="text-sm leading-none font-medium text-muted-foreground peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                    >
                        {{ t(category.labelKey) }}
                    </label>
                </div>
            </div>
        </fieldset>

        <fieldset class="space-y-3">
            <legend class="text-sm font-medium text-foreground">
                {{ t('notifications.organizations_title') }}
            </legend>
            <div class="space-y-2">
                <div
                    v-for="org in organizations"
                    :key="org.id"
                    @click="setPriority(org.id)"
                    class="flex cursor-pointer items-center space-x-3 rounded-lg border bg-background p-3 transition-colors hover:bg-accent hover:text-accent-foreground"
                    :class="{
                        'border-primary ring-2 ring-ring/50':
                            form.priorityOrg === org.id,
                    }"
                >
                    <img
                        :src="org.logo"
                        :alt="org.name"
                        class="h-6 w-6 flex-shrink-0 rounded-full"
                    />
                    <span class="flex-1 text-sm font-medium">{{
                        org.name
                    }}</span>
                    <Star
                        class="h-5 w-5 text-muted-foreground transition-colors"
                        :class="{
                            '!text-primary': form.priorityOrg === org.id,
                        }"
                        :fill="
                            form.priorityOrg === org.id
                                ? 'currentColor'
                                : 'none'
                        "
                    />
                </div>
            </div>
            <p class="text-xs text-muted-foreground">
                {{ t('notifications.priority_helper') }}
            </p>
        </fieldset>

        <Button type="submit" class="w-full" :disabled="form.processing">
            <Loader2 v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
            {{ t('notifications.subscribe_button') }}
        </Button>
    </form>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Loader2, Mail, Star } from 'lucide-vue-next';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

// Shadcn-vue components
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

// --- Mocks (replace with your actual data/helpers) ---
// Mock i18n
// const { t } = useI18n();
const t = (key: string) => {
    const keys: Record<string, string> = {
        'notifications.title': 'Bleiben Sie auf dem Laufenden',
        'notifications.subtitle':
            'Abonnieren Sie, um Updates über neue Inserate und Kampagnen zu erhalten',
        'notifications.email_label': 'E-Mail',
        'notifications.email_placeholder': 'Geben Sie Ihre E-Mail ein',
        'notifications.categories_title': 'Kategorien',
        'notifications.categories.real_estate': 'Immobilien',
        'notifications.categories.vehicles': 'Fahrzeuge',
        'notifications.categories.startups': 'Startups',
        'notifications.categories.art': 'Kunst & Sammlerstücke',
        'notifications.organizations_title': 'Organisationen',
        'notifications.priority_helper':
            'Prioritätsbenachrichtigungen: Erhalten Sie sofortige Benachrichtigungen für Ihre Prioritätsorganisation.',
        'notifications.subscribe_button': 'Abonnieren',
        'notifications.subscribe_success': 'Erfolgreich abonniert!',
        'notifications.subscribe_error':
            'Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.',
    };
    return keys[key] || key;
};

// Mock route helper
const route = (name: string) => `/mock-route/${name}`;

// Mock toast
const toast = useToast();

// --- Component State ---

// Static data (could come from props or API)
const categories = [
    { id: 'real_estate', labelKey: 'notifications.categories.real_estate' },
    { id: 'vehicles', labelKey: 'notifications.categories.vehicles' },
    { id: 'startups', labelKey: 'notifications.categories.startups' },
    { id: 'art', labelKey: 'notifications.categories.art' },
];

const organizations = [
    {
        id: 'drk',
        name: 'Deutsches Rotes Kreuz',
        logo: 'https://via.placeholder.com/40/E6001A/FFFFFF?text=DRK',
    },
    {
        id: 'ir',
        name: 'Islamic Relief',
        logo: 'https://via.placeholder.com/40/008851/FFFFFF?text=IR',
    },
    {
        id: 'unicef',
        name: 'UNICEF',
        logo: 'https://via.placeholder.com/40/00ADEF/FFFFFF?text=U',
    },
    {
        id: 'rh',
        name: 'Roter Halbmond',
        logo: 'https://via.placeholder.com/40/E6001A/FFFFFF?text=RH',
    },
];

// Form state
const form = ref({
    email: '',
    categories: [] as string[],
    priorityOrg: null as string | null,
    processing: false,
});

// --- Methods ---

/**
 * Toggles a category in the form state.
 */
function toggleCategory(checked: boolean, categoryId: string) {
    if (checked) {
        form.value.categories.push(categoryId);
    } else {
        form.value.categories = form.value.categories.filter(
            (id) => id !== categoryId,
        );
    }
}

/**
 * Sets the priority organization.
 * Can be toggled off by clicking the active one.
 */
function setPriority(orgId: string) {
    if (form.value.priorityOrg === orgId) {
        form.value.priorityOrg = null;
    } else {
        form.value.priorityOrg = orgId;
    }
}

/**
 * Handles the form submission using Inertia.
 */
function handleSubmit() {
    form.value.processing = true;

    router.post(route('notifications.subscribe'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(t('notifications.subscribe_success'));
            // Reset form if needed
            // form.value.email = '';
            // form.value.categories = [];
            // form.value.priorityOrg = null;
        },
        onError: (errors) => {
            // This assumes a generic error. If you have specific
            // field errors, you'd handle them differently.
            toast.error(t('notifications.subscribe_error'));
            console.error('Form Errors:', errors);
        },
        onFinish: () => {
            form.value.processing = false;
        },
    });
}
</script>
