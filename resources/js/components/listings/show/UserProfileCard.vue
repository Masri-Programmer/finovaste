<template>
    <Card class="w-full max-w-sm shadow-lg">
        <CardHeader>
            <CardTitle class="text-base font-medium text-muted-foreground">
                {{ t('user_card.listed_by') }}
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
            <div class="flex items-center space-x-4">
                <Avatar class="h-16 w-16">
                    <AvatarImage
                        :src="user.profile_photo_path"
                        :alt="user.name"
                    />
                    <AvatarFallback>{{ userInitials }}</AvatarFallback>
                </Avatar>
                <div>
                    <div class="text-xl font-semibold text-foreground">
                        {{ user.name }}
                    </div>
                </div>
            </div>

            <Separator />

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{
                        t('user_card.member_since')
                    }}</span>
                    <span class="font-medium text-foreground">{{
                        memberSince
                    }}</span>
                </div>
                <div v-if="address" class="flex justify-between">
                    <span class="text-muted-foreground">{{
                        t('user_card.location')
                    }}</span>
                    <span class="font-medium text-foreground"
                        >{{ address.city }}, {{ address.country }}</span
                    >
                </div>
                <div v-if="userRole" class="flex justify-between">
                    <span class="text-muted-foreground">{{
                        t('user_card.role')
                    }}</span>
                    <span class="font-medium text-foreground capitalize">{{
                        userRole
                    }}</span>
                </div>
            </div>

            <Button as-child class="w-full" size="lg">
                <Link :href="route('user.contact', user.id)">
                    {{ t('user_card.contact_button') }}
                </Link>
            </Button>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { route } from 'laravel-typed-wayfinder';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

// shadcn/ui component imports
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

// --- Type Definitions ---
// Based on the JSON data you provided
interface Role {
    id: number;
    name: string;
    slug: string;
}

interface Address {
    id: number;
    addressable_type: string;
    addressable_id: number;
    street: string;
    city: string;
    state: string;
    zip: string;
    country: string;
    latitude: number;
    longitude: number;
}

interface User {
    id: number;
    name: string;
    email: string;
    profile_photo_path: string | null;
    created_at: string;
    roles: Role[];
    addresses: Address[];
}

// --- Props ---
const props = defineProps<{
    user: User;
}>();

// --- i18n ---
const { t } = useI18n();

// --- Computed Properties ---

/**
 * Safely gets the first address from the user's addresses array.
 */
const address = computed<Address | null>(() => {
    return props.user.addresses?.length > 0 ? props.user.addresses[0] : null;
});

/**
 * Safely gets the first role name from the user's roles array.
 */
const userRole = computed<string | null>(() => {
    return props.user.roles?.length > 0 ? props.user.roles[0].name : null;
});

/**
 * Formats the created_at date to just the year, like the example.
 */
const memberSince = computed<number>(() => {
    return new Date(props.user.created_at).getFullYear();
});

/**
 * Creates fallback initials for the avatar (e.g., "Admin User" -> "AU").
 */
const userInitials = computed<string>(() => {
    const names = props.user.name.split(' ');
    if (names.length > 1) {
        return `${names[0][0]}${names[names.length - 1][0]}`.toUpperCase();
    }
    return props.user.name.substring(0, 2).toUpperCase();
});
</script>
