CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "components"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "name" varchar not null,
  "type" varchar not null,
  "is_active" tinyint(1) not null default '1',
  "meta" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime
);
CREATE UNIQUE INDEX "components_uuid_unique" on "components"("uuid");
CREATE UNIQUE INDEX "components_name_unique" on "components"("name");
CREATE TABLE IF NOT EXISTS "component_items"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "component_id" integer not null,
  "parent_id" integer,
  "translation_key" varchar,
  "display_text" varchar,
  "item_type" varchar not null default 'link',
  "value" varchar,
  "content" text,
  "sort_order" integer not null default '0',
  "meta" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("component_id") references "components"("id") on delete cascade,
  foreign key("parent_id") references "component_items"("id") on delete cascade
);
CREATE UNIQUE INDEX "component_items_uuid_unique" on "component_items"("uuid");
CREATE UNIQUE INDEX "component_items_translation_key_unique" on "component_items"(
  "translation_key"
);
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "name" text not null,
  "slug" varchar not null,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "meta" text,
  "parent_id" integer,
  "sort_order" integer not null default '0',
  "type" varchar not null default 'default',
  "icon" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("parent_id") references "categories"("id") on delete cascade
);
CREATE UNIQUE INDEX "categories_slug_unique" on "categories"("slug");
CREATE TABLE IF NOT EXISTS "addresses"(
  "id" integer primary key autoincrement not null,
  "addressable_type" varchar not null,
  "addressable_id" integer not null,
  "street" varchar not null,
  "city" varchar not null,
  "state" varchar not null,
  "zip" varchar not null,
  "country" varchar not null,
  "latitude" numeric,
  "longitude" numeric,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime
);
CREATE INDEX "addresses_addressable_type_addressable_id_index" on "addresses"(
  "addressable_type",
  "addressable_id"
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "locale" varchar not null default('de'),
  "profile_photo_path" varchar,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "two_factor_secret" text,
  "two_factor_recovery_codes" text,
  "two_factor_confirmed_at" datetime,
  "address_id" integer,
  "notification_settings" text,
  "stripe_id" varchar,
  "pm_type" varchar,
  "pm_last_four" varchar,
  "trial_ends_at" datetime,
  foreign key("address_id") references "addresses"("id") on delete set null
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "investment_listings"(
  "id" integer primary key autoincrement not null,
  "investment_goal" numeric not null,
  "minimum_investment" numeric not null default '0',
  "shares_offered" integer not null,
  "share_price" numeric not null,
  "amount_raised" numeric not null default '0',
  "investors_count" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "listings"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "user_id" integer not null,
  "category_id" integer not null,
  "address_id" integer,
  "title" text not null,
  "slug" varchar not null,
  "description" text not null,
  "status" varchar check("status" in('pending', 'rejected', 'active', 'expired', 'sold', 'withdrawn')) not null default 'pending',
  "is_featured" tinyint(1) not null default '0',
  "published_at" datetime,
  "expires_at" datetime,
  "listable_type" varchar not null,
  "listable_id" integer not null,
  "views_count" integer not null default '0',
  "likes_count" integer not null default '0',
  "average_rating" numeric not null default '0',
  "reviews_count" integer not null default '0',
  "comments_count" integer not null default '0',
  "meta" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("category_id") references "categories"("id") on delete cascade,
  foreign key("address_id") references "addresses"("id") on delete set null
);
CREATE INDEX "listings_listable_type_listable_id_index" on "listings"(
  "listable_type",
  "listable_id"
);
CREATE UNIQUE INDEX "listings_uuid_unique" on "listings"("uuid");
CREATE UNIQUE INDEX "listings_slug_unique" on "listings"("slug");
CREATE TABLE IF NOT EXISTS "purchase_listings"(
  "id" integer primary key autoincrement not null,
  "price" numeric not null,
  "quantity" integer not null default '1',
  "condition" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "auction_listings"(
  "id" integer primary key autoincrement not null,
  "start_price" numeric not null,
  "reserve_price" numeric,
  "buy_it_now_price" numeric,
  "current_bid" numeric,
  "starts_at" datetime,
  "ends_at" datetime
);
CREATE TABLE IF NOT EXISTS "donation_listings"(
  "id" integer primary key autoincrement not null,
  "donation_goal" numeric not null,
  "amount_raised" numeric not null default '0',
  "donors_count" integer not null default '0',
  "is_goal_flexible" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "reviews"(
  "id" integer primary key autoincrement not null,
  "listing_id" integer not null,
  "user_id" integer not null,
  "parent_id" integer,
  "body" text not null,
  "rating" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("listing_id") references "listings"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("parent_id") references "reviews"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "telescope_entries"(
  "sequence" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "batch_id" varchar not null,
  "family_hash" varchar,
  "should_display_on_index" tinyint(1) not null default '1',
  "type" varchar not null,
  "content" text not null,
  "created_at" datetime
);
CREATE UNIQUE INDEX "telescope_entries_uuid_unique" on "telescope_entries"(
  "uuid"
);
CREATE INDEX "telescope_entries_batch_id_index" on "telescope_entries"(
  "batch_id"
);
CREATE INDEX "telescope_entries_family_hash_index" on "telescope_entries"(
  "family_hash"
);
CREATE INDEX "telescope_entries_created_at_index" on "telescope_entries"(
  "created_at"
);
CREATE INDEX "telescope_entries_type_should_display_on_index_index" on "telescope_entries"(
  "type",
  "should_display_on_index"
);
CREATE TABLE IF NOT EXISTS "telescope_entries_tags"(
  "entry_uuid" varchar not null,
  "tag" varchar not null,
  foreign key("entry_uuid") references "telescope_entries"("uuid") on delete cascade,
  primary key("entry_uuid", "tag")
);
CREATE INDEX "telescope_entries_tags_tag_index" on "telescope_entries_tags"(
  "tag"
);
CREATE TABLE IF NOT EXISTS "telescope_monitoring"(
  "tag" varchar not null,
  primary key("tag")
);
CREATE TABLE IF NOT EXISTS "media"(
  "id" integer primary key autoincrement not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  "uuid" varchar,
  "collection_name" varchar not null,
  "name" varchar not null,
  "file_name" varchar not null,
  "mime_type" varchar,
  "disk" varchar not null,
  "conversions_disk" varchar,
  "size" integer not null,
  "manipulations" text not null,
  "custom_properties" text not null,
  "generated_conversions" text not null,
  "responsive_images" text not null,
  "order_column" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "media_model_type_model_id_index" on "media"(
  "model_type",
  "model_id"
);
CREATE UNIQUE INDEX "media_uuid_unique" on "media"("uuid");
CREATE INDEX "media_order_column_index" on "media"("order_column");
CREATE TABLE IF NOT EXISTS "listing_user"(
  "listing_id" integer not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("listing_id") references "listings"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  primary key("listing_id", "user_id")
);
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" on "permissions"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "roles_name_guard_name_unique" on "roles"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "model_has_permissions"(
  "permission_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  primary key("permission_id", "model_id", "model_type")
);
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "model_has_roles"(
  "role_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("role_id", "model_id", "model_type")
);
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "role_has_permissions"(
  "permission_id" integer not null,
  "role_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("permission_id", "role_id")
);
CREATE TABLE IF NOT EXISTS "settings"(
  "id" integer primary key autoincrement not null,
  "group" varchar not null,
  "name" varchar not null,
  "locked" tinyint(1) not null default '0',
  "payload" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "settings_group_name_unique" on "settings"(
  "group",
  "name"
);
CREATE TABLE IF NOT EXISTS "bids"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "listing_id" integer not null,
  "amount" numeric not null,
  "status" varchar check("status" in('active', 'retracted', 'outbid', 'won')) not null default 'active',
  "ip_address" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("listing_id") references "listings"("id") on delete cascade
);
CREATE INDEX "bids_listing_id_amount_index" on "bids"("listing_id", "amount");
CREATE TABLE IF NOT EXISTS "transactions"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "user_id" integer not null,
  "payable_type" varchar not null,
  "payable_id" integer not null,
  "amount" numeric not null,
  "fee" numeric not null default '0',
  "currency" varchar not null default 'USD',
  "payment_provider" varchar not null default 'stripe',
  "transaction_ref" varchar not null,
  "status" varchar not null default 'pending',
  "type" varchar not null,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "transactions_payable_type_payable_id_index" on "transactions"(
  "payable_type",
  "payable_id"
);
CREATE UNIQUE INDEX "transactions_uuid_unique" on "transactions"("uuid");
CREATE UNIQUE INDEX "transactions_transaction_ref_unique" on "transactions"(
  "transaction_ref"
);
CREATE INDEX "transactions_status_index" on "transactions"("status");
CREATE INDEX "transactions_type_index" on "transactions"("type");
CREATE TABLE IF NOT EXISTS "listing_faqs"(
  "id" integer primary key autoincrement not null,
  "listing_id" integer not null,
  "user_id" integer not null,
  "question" text not null,
  "answer" text,
  "is_visible" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("listing_id") references "listings"("id") on delete cascade,
  foreign key("user_id") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" varchar not null,
  "type" varchar not null,
  "notifiable_type" varchar not null,
  "notifiable_id" integer not null,
  "data" text not null,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications"(
  "notifiable_type",
  "notifiable_id"
);
CREATE INDEX "users_stripe_id_index" on "users"("stripe_id");
CREATE TABLE IF NOT EXISTS "subscriptions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "type" varchar not null,
  "stripe_id" varchar not null,
  "stripe_status" varchar not null,
  "stripe_price" varchar,
  "quantity" integer,
  "trial_ends_at" datetime,
  "ends_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "subscriptions_user_id_stripe_status_index" on "subscriptions"(
  "user_id",
  "stripe_status"
);
CREATE UNIQUE INDEX "subscriptions_stripe_id_unique" on "subscriptions"(
  "stripe_id"
);
CREATE TABLE IF NOT EXISTS "subscription_items"(
  "id" integer primary key autoincrement not null,
  "subscription_id" integer not null,
  "stripe_id" varchar not null,
  "stripe_product" varchar not null,
  "stripe_price" varchar not null,
  "quantity" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "meter_id" varchar,
  "meter_event_name" varchar
);
CREATE INDEX "subscription_items_subscription_id_stripe_price_index" on "subscription_items"(
  "subscription_id",
  "stripe_price"
);
CREATE UNIQUE INDEX "subscription_items_stripe_id_unique" on "subscription_items"(
  "stripe_id"
);
CREATE TABLE IF NOT EXISTS "listing_subscriptions"(
  "id" integer primary key autoincrement not null,
  "listing_id" integer not null,
  "email" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "locale" varchar not null default 'en',
  foreign key("listing_id") references "listings"("id") on delete cascade
);
CREATE UNIQUE INDEX "listing_subscriptions_listing_id_email_unique" on "listing_subscriptions"(
  "listing_id",
  "email"
);
CREATE TABLE IF NOT EXISTS "listing_updates"(
  "id" integer primary key autoincrement not null,
  "listing_id" integer not null,
  "title" varchar,
  "content" text,
  "type" varchar not null default('general'),
  "created_at" datetime,
  "updated_at" datetime,
  "translation_key" varchar,
  "attributes" text,
  foreign key("listing_id") references listings("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "seeders"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "created_at" datetime not null
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_08_14_170933_add_two_factor_columns_to_users_table',1);
INSERT INTO migrations VALUES(5,'2025_10_21_113424_create_components_table',1);
INSERT INTO migrations VALUES(6,'2025_10_21_113426_create_component_items_table',1);
INSERT INTO migrations VALUES(7,'2025_10_21_131203_create_categories_table',1);
INSERT INTO migrations VALUES(8,'2025_10_21_132201_create_addresses_table',1);
INSERT INTO migrations VALUES(9,'2025_10_21_132229_add_address_id_to_users_table',1);
INSERT INTO migrations VALUES(10,'2025_10_21_135452_create_investment_listings_table',1);
INSERT INTO migrations VALUES(11,'2025_10_21_135707_create_listings_table',1);
INSERT INTO migrations VALUES(12,'2025_10_21_140305_create_purchase_listings_table',1);
INSERT INTO migrations VALUES(13,'2025_10_21_140330_create_auction_listings_table',1);
INSERT INTO migrations VALUES(14,'2025_10_21_140349_create_donation_listings_table',1);
INSERT INTO migrations VALUES(15,'2025_10_21_140940_create_reviews_table',1);
INSERT INTO migrations VALUES(16,'2025_11_02_112833_create_telescope_entries_table',1);
INSERT INTO migrations VALUES(17,'2025_11_02_141300_create_media_table',1);
INSERT INTO migrations VALUES(18,'2025_11_10_123742_create_listing_user_table',1);
INSERT INTO migrations VALUES(19,'2025_11_15_162307_create_permission_tables',1);
INSERT INTO migrations VALUES(20,'2025_11_15_174407_create_settings_table',1);
INSERT INTO migrations VALUES(21,'2025_11_15_174449_create_default_general_settings',1);
INSERT INTO migrations VALUES(22,'2025_11_21_191039_create_bids_table',1);
INSERT INTO migrations VALUES(23,'2025_11_21_191301_create_transactions_table',1);
INSERT INTO migrations VALUES(24,'2025_11_23_160034_create_listing_faqs_table',1);
INSERT INTO migrations VALUES(25,'2025_11_26_192703_create_notifications_table',1);
INSERT INTO migrations VALUES(26,'2025_11_26_192716_add_notification_settings_to_users_table',1);
INSERT INTO migrations VALUES(27,'2025_12_01_175519_create_customer_columns',1);
INSERT INTO migrations VALUES(28,'2025_12_01_175520_create_subscriptions_table',1);
INSERT INTO migrations VALUES(29,'2025_12_01_175521_create_subscription_items_table',1);
INSERT INTO migrations VALUES(30,'2025_12_01_175522_add_meter_id_to_subscription_items_table',1);
INSERT INTO migrations VALUES(31,'2025_12_01_175523_add_meter_event_name_to_subscription_items_table',1);
INSERT INTO migrations VALUES(32,'2025_12_01_175806_create_listing_subscriptions_table',1);
INSERT INTO migrations VALUES(33,'2025_12_01_180549_create_listing_updates_table',1);
INSERT INTO migrations VALUES(34,'2025_12_01_182639_add_localization_to_subscriptions_and_updates',1);
