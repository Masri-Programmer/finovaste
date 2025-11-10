<template>
    <Card>
        <CardHeader>
            <CardTitle class="text-base font-medium text-muted-foreground">
                {{ $t('listings.listed_by') }}
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
                    <div class="flex items-center gap-1">
                        <Star class="h-4 w-4 fill-yellow-400 text-yellow-400" />
                        <span class="text-sm font-medium">4.9</span>
                        <span class="text-sm text-muted-foreground"
                            >(127 {{ $t('listings.reviews') }})</span
                        >
                    </div>
                </div>
            </div>

            <Separator />

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{
                        $t('listings.member_since')
                    }}</span>
                    <span class="font-medium text-foreground">{{
                        memberSince
                    }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{
                        $t('listings.response_time')
                    }}</span>
                    <span class="font-medium text-foreground">~2 Stunden</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">{{
                        $t('listings.total_listings')
                    }}</span>
                    <span class="font-medium text-foreground">24</span>
                </div>
            </div>

            <Button as-child class="w-full" size="lg">
                {{ $t('listings.contact_button') }}
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
import { Star } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    user: User;
}>();

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
