import { Category, LocaleString, User } from './../index.d';

export interface PageProps {
    listing: ListingData;
    locale: 'en' | 'de';
    errors: object;
    user: User;
    category: Category;
}

export interface InvestmentListable {
    id: number;
    investment_goal: number;
    minimum_investment: number;
    shares_offered: number;
    share_price: number;
    amount_raised: number;
    ar;
    investors_count: number;
    created_at: string | null;
    updated_at: string | null;
}

export interface BuyNowListable {
    id: number;
    price: string;
    quantity: number;
}

export interface AuctionListable {
    id: number;
    start_price: number;
    reserve_price: number | null;
    buy_it_now_price: number | null;
    current_bid: number | null;
    starts_at: string | null;
    ends_at: string;
}

export interface DonationListable {
    id: number;
    donation_goal: string;
    amount_raised: string;
    donors_count: number;
    is_goal_flexible: boolean;
}

export interface Listing {
    id: number;
    uuid: string;
    user_id: number;
    category_id: number;
    address_id: number;
    title: LocaleString;
    slug: string;
    description: LocaleString;
    status: string;
    is_featured: boolean;
    published_at: string | null;
    expires_at: string | null;
    listable_type:
        | 'App\\Models\\InvestmentListing'
        | 'App\\Models\\BuyNowListing'
        | 'App\\Models\\AuctionListing'
        | 'App\\Models\\DonationListing';
    listable_id: number;
    views_count: number;
    likes_count: number;
    average_rating: number;
    reviews_count: number;
    comments_count: number;
    meta: any | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;

    listable:
        | InvestmentListable
        | BuyNowListable
        | AuctionListable
        | DonationListable;
    user: User;
    category: Category;
    address: Address;
}
