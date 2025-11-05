<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-base font-medium text-muted-foreground">
                {{ t('user_card.listed_by') }}
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
            <div class="flex items-center space-x-4">
                <Avatar class="h-16 w-16">
                    <AvatarImage
                        :src="user.profile_photo_path as string"
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
                <!-- <Link :href="route('user.contact', user.id)">
                </Link> -->
                {{ t('user_card.contact_button') }}
            </Button>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Address, User } from '@/types';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    user: User;
}>();

const { t } = useI18n();

const address = computed<Address | null>(() => {
    return props.user.addresses?.length > 0 ? props.user.addresses[0] : null;
});

const userRole = computed<string | null>(() => {
    return props.user.roles?.length > 0 ? props.user.roles[0].name : null;
});
const memberSince = computed<number>(() => {
    return new Date(props.user.created_at).getFullYear();
});
const userInitials = computed<string>(() => {
    const names = props.user.name.split(' ');
    if (names.length > 1) {
        return `${names[0][0]}${names[names.length - 1][0]}`.toUpperCase();
    }
    return props.user.name.substring(0, 2).toUpperCase();
});
</script>
