import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Address {
    id: number;
    addressable_type: string;
    addressable_id: number;
    street: string;
    city: string;
    state: string;
    zip: string;
    country: string;
    latitude: number | null;
    longitude: number | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
}

export interface Role {
    id: number;
    name: string;
    slug: string;
    created_at: string;
    updated_at: string;
    pivot: {
        user_id: number;
        role_id: number;
    };
}

/**
 * Represents the User object. Note that it contains
 * the *full* Role and Address objects.
 */
export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    locale: string;
    profile_photo_path: string | null;
    created_at: string;
    updated_at: string;
    two_factor_confirmed_at: string | null;
    address_id: number | null;

    roles: Role[];
    addresses: Address[];
}

export interface Auth {
    user: User;
    roles: string[];
    permissions: string[];
    addresses: Address[];
}

export interface Roles {
    role: string;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};
