import { Address, Category, LocaleString, User } from './../index.d';

export type TransactionStatus = 'pending' | 'completed' | 'failed' | 'refunded';
export type TransactionType =
    | 'buy_now'
    | 'donation'
    | 'investment'
    | 'auction_win';

export type BuyNowMetadata = {
    snapshot_price: number;
    product_name: string;
    condition: string | null;
};

export type DonationMetadata = {
    campaign_title: string;
    donor_note?: string;
};

export type InvestmentMetadata = {
    share_price_at_booking: number;
    project_name: string;
};

export interface Transaction {
    id: number;
    uuid: string;
    user_id: number;

    payable_type:
        | 'App\\Models\\InvestmentListing'
        | 'App\\Models\\BuyNowListing'
        | 'App\\Models\\AuctionListing'
        | 'App\\Models\\DonationListing';

    payable_id: number;

    payable?:
        | InvestmentListable
        | BuyNowListable
        | AuctionListable
        | DonationListable;

    type: TransactionType;

    amount: number;
    fee: number;
    currency: string;

    quantity: number | null;

    payment_method: string | null;
    transaction_ref: string | null;

    metadata: BuyNowMetadata | DonationMetadata | InvestmentMetadata | any;

    status: TransactionStatus;
    paid_at: string | null;
    created_at: string;
    updated_at: string;

    user?: User;
    faqs?: ListingFaq[];
}

export type BidStatus = 'active' | 'retracted' | 'outbid' | 'won';

export interface Bid {
    id: number;
    user_id: number;
    listing_id: number;

    amount: number;

    status: BidStatus;
    ip_address?: string;

    created_at: string;
    updated_at: string;

    // Relationships
    user?: User;
    listing?: Listing;
}

export interface PageProps {
    listing: Listing;
    transactions?: Transaction[];
    locale: 'en' | 'de';
    errors: object;
    user: User;
    category: Category;
}

export interface Listing {
    id: number;
    uuid: string;
    user_id: number;
    category_id: number;
    bids_count?: number;

    address_id: number | null;
    address: Address | null;

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
    image_url?: string;

    media: ListingMediaCollection;

    listable:
        | InvestmentListable
        | BuyNowListable
        | AuctionListable
        | DonationListable;

    user: User;
    category: Category;
    is_liked_by_current_user: boolean;

    likers_count?: number;
}

// ... [Rest of your existing Listable/Media interfaces remain the same] ...

export interface InvestmentListable {
    id: number;
    investment_goal: number;
    minimum_investment: number;
    shares_offered: number;
    share_price: number;
    amount_raised: number;
    investors_count: number;
    created_at: string | null;
    updated_at: string | null;
    transactions_count?: number;
}

export interface BuyNowListable {
    id: number;
    price: string;
    quantity: number;
    condition: string | null;
    created_at: string;
    transactions_count?: number;
    updated_at: string;
}

export interface AuctionListable {
    id: number;
    start_price: number;
    reserve_price: number | null;
    buy_it_now_price: number | null;
    current_bid: number | null;
    starts_at: string | null;
    ends_at: string;
    transactions_count?: number;
}

export interface DonationListable {
    id: number;
    donation_goal: string;
    amount_raised: string;
    donors_count: number;
    is_goal_flexible: boolean;
    created_at: string;
    updated_at: string;
    transactions_count?: number;
}

export interface ListingMediaCollection {
    images: ListingImage[];
    videos: ListingVideo[];
    documents: ListingDocument[];
}

export interface ListingImage {
    id: number;
    url: string;
    thumbnail: string;
    mime_type: string;
}

export interface ListingVideo {
    id: number;
    url: string;
    mime_type: string;
}

export interface ListingDocument {
    id: number;
    url: string;
    file_name: string;
    size: string;
}

export interface ListingFaq {
    id: number;
    listing_id: number;
    user_id: number;

    question: LocaleString;
    answer: LocaleString | null;

    is_visible: boolean;
    created_at: string;
    updated_at: string;

    user?: User; // The user who asked the question
}
