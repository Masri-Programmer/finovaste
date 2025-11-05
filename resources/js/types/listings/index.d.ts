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
    amount_raised: number;
    investors_count: number;
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
