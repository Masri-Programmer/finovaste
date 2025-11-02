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
    listings: PaginatedResponse<Listing>;
    categories: Category[];
};

interface LocaleString {
    en: string;
    de: string;
}

interface Category {
    id: number;
    name: LocaleString;
    slug: string;
}

// Listable Types
interface InvestmentListable {
    id: number;
    investment_goal: number;
    minimum_investment: number;
    amount_raised: number;
    investors_count: number;
}

interface BuyNowListable {
    id: number;
    price: string;
    quantity: number;
}

interface AuctionListable {
    id: number;
    start_price: number;
    reserve_price: number | null;
    buy_it_now_price: number | null;
    current_bid: number | null;
    starts_at: string | null;
    ends_at: string;
}

interface DonationListable {
    id: number;
    donation_goal: string;
    amount_raised: string;
    donors_count: number;
    is_goal_flexible: boolean;
}

// Main Listing Interface
interface Listing {
    id: number;
    uuid: string;
    image_url: string;
    user_id: number;
    category_id: number;
    title: LocaleString;
    description: LocaleString;
    status: string;
    listable_type:
        | 'App\\Models\\InvestmentListing'
        | 'App\\Models\\BuyNowListing'
        | 'App\\Models\\AuctionListing'
        | 'App\\Models\\DonationListing';
    listable:
        | InvestmentListable
        | BuyNowListable
        | AuctionListable
        | DonationListable;
    user: User;
    category: Category;
    views_count: number;
    likes_count: number;
    average_rating: number;
    reviews_count: number;
}

// Inertia Paginated Response
interface PaginatedResponse<T> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}
