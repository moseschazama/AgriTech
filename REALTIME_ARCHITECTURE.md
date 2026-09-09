# AgriTech Pro — Real-Time Architecture Documentation

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Technology Stack](#2-technology-stack)
3. [Architecture Diagram](#3-architecture-diagram)
4. [Real-Time Flow](#4-real-time-flow)
5. [WebSocket Channels](#5-websocket-channels)
6. [Broadcast Events Reference](#6-broadcast-events-reference)
7. [Client-Side Real-Time Engine](#7-client-side-real-time-engine)
8. [Notification System](#8-notification-system)
9. [Queue & Background Processing](#9-queue--background-processing)
10. [Controller Integration Map](#10-controller-integration-map)
11. [Setup & Running](#11-setup--running)
12. [File Reference](#12-file-reference)

---

## 1. System Overview

AgriTech Pro uses a **fully event-driven, real-time architecture** powered by Laravel Reverb (WebSocket server), Laravel Broadcasting, and Redis queues. Every significant action — placing orders, approving products, completing lessons, delivery tracking, payment confirmations, disease alerts — is broadcast instantly to all connected, authorized clients without page refreshes.

**Key principles:**
- All broadcasts use **private channels** — only authorized users receive events
- Heavy broadcasting is **queued via Redis** to avoid blocking HTTP requests
- GPS location updates and notification toasts are **synchronous** for minimum latency
- The client-side engine (`realtime.js`) handles toast notifications, badge counters, live DOM updates, and connection status

---

## 2. Technology Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| WebSocket Server | **Laravel Reverb** (port 8080) | Persistent WebSocket connections |
| Broadcasting | **Laravel Broadcasting** (Reverb driver) | Server-to-client event dispatch |
| Client | **Laravel Echo** + **Pusher.js** | WebSocket client protocol |
| Queue | **Redis** (`QUEUE_CONNECTION=redis`) | Background job processing |
| Session | **Redis** (`SESSION_DRIVER=redis`) | Shared session store |
| Cache | **Redis** (`CACHE_STORE=redis`) | Application cache |
| Build | **Vite 8** + **Tailwind CSS 4** | Frontend asset compilation |

---

## 3. Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER ACTIONS                             │
│  (Create product, Place order, Complete lesson, GPS update...)  │
└──────────────────────────────┬──────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                     LARAVEL CONTROLLERS                         │
│  MarketplaceController | AdminController | LearnController ...  │
│                                                                 │
│  1. Creates database record (Notification::create)              │
│  2. Dispatches queued notification (BroadcastNotification)      │
│  3. Fires broadcast event (event(new XxxEvent))                 │
└───────┬─────────────────────────┬───────────────────────────────┘
        │                         │
        ▼                         ▼
┌───────────────────┐    ┌────────────────────────────────────────┐
│   REDIS QUEUE     │    │         BROADCAST EVENT                │
│                   │    │  (ShouldBroadcast + ShouldQueue)       │
│  BroadcastNotif   │    │                                        │
│  Job (queued)     │    │  → Serializes model data               │
│                   │    │  → Resolves target channels             │
│  Creates DB       │    │  → Sends to Redis pub/sub              │
│  Notification     │    │                                        │
│  + fires          │    └──────────────┬─────────────────────────┘
│  NotificationCreated                 │
│                   │                   │
└───────┬───────────┘                   │
        │                               │
        ▼                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                     LARAVEL REVERB                              │
│                  (WebSocket Server :8080)                        │
│                                                                 │
│  Receives events from Redis pub/sub                             │
│  Authenticates channel subscriptions                            │
│  Pushes events to authorized WebSocket clients                  │
└───────┬───────────────┬───────────────┬─────────────────────────┘
        │               │               │
        ▼               ▼               ▼
┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
│ Browser  │  │ Browser  │  │ Mobile   │  │ Mobile   │
│ Tab 1    │  │ Tab 2    │  │ Device 1 │  │ Device 2 │
│ (User A) │  │ (User A) │  │ (User B) │  │ (Admin)  │
└────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘
     │              │              │              │
     ▼              ▼              ▼              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    REALTIME.JS ENGINE                            │
│                                                                 │
│  • Receives WebSocket events via Echo listeners                 │
│  • Shows toast notifications (bottom-right popup)               │
│  • Updates notification badge count                             │
│  • Prepends items to live lists/tables                          │
│  • Animates dashboard stat counters                             │
│  • Updates delivery map markers                                 │
│  • Shows connection status indicator                            │
│  • Updates page title with unread count                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. Real-Time Flow

### Flow 1: Marketplace Product Listing

```
1. Farmer creates product → MarketplaceController::store()
2. Notification::create() → DB record for seller
3. BroadcastNotification::dispatch() → queued → creates notification + broadcasts to seller
4. For each admin: Notification::create() + BroadcastNotification::dispatch()
5. event(new ProductCreated($product)) → queued broadcast
   ├── Channel: marketplace → all connected users see the listing
   └── Channel: admin.dashboard → admins see toast + badge increment
6. Client: realtime.js hears '.product.created'
   ├── Shows toast "New listing: {name}"
   ├── Increments admin notification badge
   └── Updates stat counter animation
```

### Flow 2: Order Placement

```
1. Buyer checks out → MarketplaceController::checkout()
2. Order + OrderItems + Delivery created in DB
3. Notification::create() + BroadcastNotification → buyer (queued)
4. Notification::create() + BroadcastNotification → seller (queued)
5. For each admin: Notification::create() + BroadcastNotification (queued)
6. event(new OrderPlaced($order)) → queued broadcast
   ├── Channel: user.{buyer_id} → buyer sees toast
   ├── Channel: user.{seller_id} → seller sees toast
   └── Channel: admin.dashboard → admins see toast + badge + counter
7. Payment initiated via PaymentService
8. Webhook received → PaymentService::handleWebhook()
   ├── On success: markAsPaid() + BroadcastNotification → buyer + seller
   ├── Fires OrderStatusChanged event → queued broadcast to buyer/seller/admins
   └── On failure: BroadcastNotification → buyer with error
```

### Flow 3: Delivery Tracking

```
1. Driver updates GPS → DeliveryController::updateLocation()
2. event(new DriverLocationUpdated($delivery, lat, lng)) → SYNCHRONOUS
   └── Channel: user.{buyer_id} → buyer's map marker moves in real-time
3. Driver advances status → DeliveryController::advanceStatus()
4. notifyBuyer() → Notification::create() + BroadcastNotification → buyer (queued)
5. event(new DeliveryStatusUpdated($delivery, newStatus)) → queued
   ├── Channel: admin.dashboard → admin sees status change
   ├── Channel: user.{buyer_id} → buyer sees toast
   └── Channel: user.{driver_id} → driver sees confirmation
6. Client: realtime.js hears '.delivery.status_updated'
   ├── Shows toast "Delivery TRK-xxx: in_transit"
   └── Calls window.updateTrackingMap() if on tracking page
```

### Flow 4: Learning Progress

```
1. User enrolls → LearnController::enroll()
2. Notification::create() + BroadcastNotification → user (queued)
3. event(new CourseEnrolled($enrollment)) → queued
   └── Channel: user.{user_id} → toast "Enrolled in: {course}"
4. User completes lesson → LearnController::completeLesson()
5. event(new LessonCompleted($progress, pct, done)) → queued
   └── Channel: user.{user_id} → toast "Lesson complete: {title} (75%)"
6. If course completed:
   event(new CourseCompleted($enrollment, 100)) → queued
   BroadcastNotification → user with certificate link
   └── Toast "Congratulations! Course completed!"
```

### Flow 5: Admin Actions

```
Admin approves product:
  1. ProductApproved event → queued → seller gets toast
  2. BroadcastNotification → seller gets persistent notification
  3. DB Notification::create() → notification page shows it

Admin rejects product:
  1. Notification::create() → seller gets DB notification
  2. BroadcastNotification → seller gets real-time toast + badge

Admin suspends farmer:
  1. BroadcastNotification → farmer gets real-time "Account Suspended" toast

Admin intervenes on farm:
  1. FarmAlertCreated event → queued → farmer + admins get alerts
  2. BroadcastNotification → farmer gets real-time toast
  3. DB Notification::create() → persistent notification
```

---

## 5. WebSocket Channels

All channels use **private authentication** (user must be logged in, channel authorization verified via `routes/channels.php`).

| Channel | Authorization | Audience | Purpose |
|---------|--------------|----------|---------|
| `user.{id}` | User ID matches `$id` | Individual user | Personal notifications, order updates, delivery status, course progress, farm alerts, product approvals |
| `admin.dashboard` | `isAdmin()` | All admins | New farmers, new products, order updates, disease alerts, delivery changes |
| `admin.farm-alerts` | `isAdmin()` | All admins | Farm decision-support alerts requiring intervention |
| `marketplace` | All authenticated users | All users | New product listings, product approvals |
| `innovation.hub` | All authenticated users | All users | Innovation vote count updates |
| `disease.alerts` | All authenticated users | All users | Disease outbreak alerts |

---

## 6. Broadcast Events Reference

### 14 Broadcast Events

| # | Event | Channels | Event Name | Queued? | Triggered By |
|---|-------|----------|------------|---------|-------------|
| 1 | `UserRegistered` | `admin.dashboard` | `user.registered` | Yes | AuthController::register() |
| 2 | `ProductCreated` | `marketplace`, `admin.dashboard` | `product.created` | Yes | MarketplaceController::store() |
| 3 | `ProductApproved` | `marketplace`, `user.{seller}` | `product.approved` | Yes | AdminController::approveProduct() |
| 4 | `OrderPlaced` | `user.{buyer}`, `user.{seller}`, `admin.dashboard` | `order.placed` | Yes | MarketplaceController::checkout() |
| 5 | `OrderStatusChanged` | `user.{buyer}`, `user.{seller}`, `admin.dashboard` | `order.status_changed` | Yes | AdminController, PaymentService |
| 6 | `DeliveryStatusUpdated` | `user.{buyer}`, `user.{driver}`, `admin.dashboard` | `delivery.status_updated` | Yes | DeliveryController::advanceStatus() |
| 7 | `DriverLocationUpdated` | `user.{buyer}` | `driver.location_updated` | **No** (sync) | DeliveryController::updateLocation() |
| 8 | `CourseEnrolled` | `user.{user}` | `course.enrolled` | Yes | LearnController::enroll() |
| 9 | `LessonCompleted` | `user.{user}` | `lesson.completed` | Yes | LearnController::completeLesson() |
| 10 | `CourseCompleted` | `user.{user}` | `course.completed` | Yes | LearnController::completeLesson() |
| 11 | `NotificationCreated` | `user.{user}` | `notification.created` | **No** (sync) | BroadcastNotification job |
| 12 | `InnovationVoteUpdated` | `innovation.hub` | `innovation.vote_updated` | Yes | InnovationController::vote() |
| 13 | `FarmAlertCreated` | `user.{farm_owner}`, `admin.farm-alerts` | `farm.alert_created` | Yes | FarmRecordsController |
| 14 | `DiseaseAlertCreated` | `admin.dashboard`, `disease.alerts` | `disease.alert_created` | Yes | AdminController::sendDiseaseAlert() |

**Design decision:** `DriverLocationUpdated` and `NotificationCreated` are kept synchronous (not queued) because GPS tracking requires minimum latency and notification toasts must appear instantly. All other events are queued via Redis for non-blocking request handling.

---

## 7. Client-Side Real-Time Engine

**File:** `resources/js/realtime.js` (loaded on every page via layout)

### Capabilities

| Feature | Implementation |
|---------|---------------|
| **Toast notifications** | `showToast(message, type, duration)` — slide-in from right, auto-dismiss |
| **Badge counter** | `incrementNotificationBadge()` / `updateNotificationBadge(count)` — updates `#notifBadge` |
| **Page title badge** | Updates document title with `(N)` unread count prefix |
| **Live DOM prepend** | `prependToList(containerId, html)` — adds animated rows to lists/tables |
| **Counter animation** | `animateCounter(elementId, newValue)` — smooth number transition with easing |
| **Notification dropdown** | `addNotificationToDropdown(data)` — prepends to `#notifList` |
| **Connection indicator** | `showConnectionStatus(status)` — bottom-left "Live" / "Reconnecting..." |
| **Map updates** | Calls `window.updateTrackingMap()` and `window.updateDriverMarker()` for delivery tracking |
| **Notification page** | Live-prepends new notifications to `#notifications-list` when on the notifications page |

### Event Listeners

| Channel | Event | Client Action |
|---------|-------|--------------|
| `user.{id}` | `notification.created` | Badge +1, toast, dropdown item, page list update |
| `user.{id}` | `order.placed` | Toast with order number + total |
| `user.{id}` | `order.status_changed` | Toast with new status |
| `user.{id}` | `delivery.status_updated` | Toast + map update |
| `user.{id}` | `driver.location_updated` | Map marker move + distance update |
| `user.{id}` | `course.enrolled` | Toast "Enrolled in: {title}" |
| `user.{id}` | `course.completed` | Toast "Congratulations!" |
| `user.{id}` | `lesson.completed` | Toast "Lesson complete: {title} ({pct}%)" |
| `user.{id}` | `farm.alert_created` | Toast (warning/urgent based on priority) |
| `user.{id}` | `product.approved` | Toast "Your product has been approved!" |
| `admin.dashboard` | `user.registered` | Badge +1, toast, animate farmers counter |
| `admin.dashboard` | `product.created` | Badge +1, toast, animate products counter |
| `admin.dashboard` | `order.placed` | Badge +1, toast, animate orders counter |
| `admin.dashboard` | `order.status_changed` | Badge +1, toast, animate orders counter |
| `admin.dashboard` | `delivery.status_updated` | Toast |
| `admin.dashboard` | `disease.alert_created` | Badge +1, toast (error type, 10s) |
| `admin.farm-alerts` | `farm.alert_created` | Badge +1, toast |
| `marketplace` | `product.created` | DOM update (prepared for live grid) |
| `innovation.hub` | `innovation.vote_updated` | Animate vote count element |
| `disease.alerts` | `disease.alert_created` | Toast (warning, 10s) |

---

## 8. Notification System

### Two-Layer Architecture

The notification system uses two complementary mechanisms:

**Layer 1: Database Notifications (Persistent)**
- Created via `Notification::create()` in controllers
- Stored in `notifications` table
- Rendered server-side in navbar dropdown and `/notifications` page
- Supports `is_read` flag, `action_url`, `icon`, `icon_color`

**Layer 2: Real-Time Broadcasts (Instant)**
- Dispatched via `BroadcastNotification::dispatch()` job
- The job creates a `Notification` DB record AND fires `NotificationCreated` event
- Broadcast to `user.{id}` channel via WebSocket
- Client receives and shows toast + updates badge + adds to dropdown

**Flow:**
```
Controller action
  ├── Notification::create()         → DB record (persistent)
  ├── BroadcastNotification::dispatch()  → Queued job → DB + WebSocket broadcast
  └── event(new XxxEvent())          → Queued broadcast → domain-specific UI updates
```

### Notification Types

| Type | Icon | Used For |
|------|------|----------|
| `order` | `fas fa-shopping-bag`, `fas fa-truck`, `fas fa-box` | Orders, payments, delivery |
| `marketplace` | `fas fa-clock`, `fas fa-check-circle`, `fas fa-times-circle` | Product listings |
| `lesson` | `fas fa-graduation-cap`, `fas fa-trophy` | Course enrollment, completion |
| `farm` | `fas fa-user-shield`, `fas fa-info-circle` | Farm alerts, admin intervention |
| `innovation` | `fas fa-check-circle`, `fas fa-times-circle` | Innovation approval/rejection |
| `account` | `fas fa-ban`, `fas fa-check-circle` | Account suspension/reactivation |

---

## 9. Queue & Background Processing

### Queue Driver: Redis

```
QUEUE_CONNECTION=redis
```

### BroadcastNotification Job

**File:** `app/Jobs/BroadcastNotification.php`

```php
class BroadcastNotification implements ShouldQueue
{
    public int $tries = 3;
    public int $backoff = 5;

    // Creates Notification DB record + fires NotificationCreated event
}
```

- Retries up to 3 times on failure
- 5-second backoff between retries
- Handles all notification creation + broadcast in one queued job

### Event Queuing

12 of 14 broadcast events implement `ShouldQueue` for background processing:

| Queued Events (12) | Synchronous Events (2) |
|---------------------|----------------------|
| UserRegistered, ProductCreated, ProductApproved, OrderPlaced, OrderStatusChanged, DeliveryStatusUpdated, CourseEnrolled, LessonCompleted, CourseCompleted, InnovationVoteUpdated, FarmAlertCreated, DiseaseAlertCreated | **DriverLocationUpdated** (GPS needs min latency), **NotificationCreated** (toast must be instant) |

### Running Queue Workers

```bash
# Development
php artisan queue:work redis --tries=3 --timeout=60

# Production (with sleep between jobs to reduce Redis polling)
php artisan queue:work redis --tries=3 --timeout=60 --sleep=3

# Using npm script
npm run queue:work
```

---

## 10. Controller Integration Map

### MarketplaceController

| Action | DB Notification | BroadcastNotification | Broadcast Event |
|--------|----------------|----------------------|-----------------|
| `store()` (seller) | Yes | Yes | `ProductCreated` |
| `store()` (each admin) | Yes | Yes | — |
| `checkout()` (buyer) | Yes | Yes | `OrderPlaced` |
| `checkout()` (seller) | Yes | Yes | `OrderPlaced` |
| `checkout()` (each admin) | Yes | Yes | `OrderPlaced` |
| `reportIssue()` (each admin) | Yes | Yes | — |

### AdminController

| Action | DB Notification | BroadcastNotification | Broadcast Event |
|--------|----------------|----------------------|-----------------|
| `approveProduct()` | Yes | Yes | `ProductApproved` |
| `rejectProduct()` | Yes | Yes | — |
| `updateOrderStatus()` | Yes | Yes | `OrderStatusChanged` |
| `suspendFarmer()` | — | Yes | — |
| `activateFarmer()` | — | Yes | — |
| `approveInnovation()` | Yes | Yes | — |
| `rejectInnovation()` | Yes | Yes | — |
| `sendDiseaseAlert()` | — | — | `DiseaseAlertCreated` |

### DeliveryController

| Action | DB Notification | BroadcastNotification | Broadcast Event |
|--------|----------------|----------------------|-----------------|
| `notifyBuyer()` (5 statuses) | Yes | Yes | — |
| `advanceStatus()` | — | — | `DeliveryStatusUpdated` |
| `updateLocation()` | — | — | `DriverLocationUpdated` (sync) |
| `confirmDelivery()` (buyer) | Yes | Yes | `DeliveryStatusUpdated` |
| `confirmDelivery()` (seller) | — | Yes | — |

### LearnController

| Action | DB Notification | BroadcastNotification | Broadcast Event |
|--------|----------------|----------------------|-----------------|
| `enroll()` | Yes | Yes | `CourseEnrolled` |
| `completeLesson()` | — | Yes (if course done) | `LessonCompleted`, `CourseCompleted` |

### FarmRecordsController

| Action | DB Notification | BroadcastNotification | Broadcast Event |
|--------|----------------|----------------------|-----------------|
| `createAlert()` | — | Yes | `FarmAlertCreated` |
| `adminIntervene()` | Yes | Yes | — |

### PaymentService

| Action | DB Notification | BroadcastNotification | Broadcast Event |
|--------|----------------|----------------------|-----------------|
| `handleWebhook()` success | — | Yes (buyer + seller) | `OrderStatusChanged` |
| `handleWebhook()` failure | — | Yes (buyer) | — |

---

## 11. Setup & Running

### Prerequisites

- PHP 8.3+
- Node.js 18+
- Redis server running
- Laravel dependencies installed

### Environment Variables (.env)

```env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
CACHE_STORE=redis

REVERB_APP_ID=agritech
REVERB_APP_KEY=agritech_key
REVERB_APP_SECRET=agritech_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_APP_ID="${REVERB_APP_ID}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Quick Start (Development)

```bash
# Install dependencies
composer install
npm install

# Build frontend assets
npm run build

# Run all services concurrently
npm run start
# This starts: php artisan serve + queue:work + reverb:start + vite dev
```

### Manual Start (Each Service)

```bash
# Terminal 1: Laravel dev server
php artisan serve

# Terminal 2: Reverb WebSocket server
php artisan reverb:start

# Terminal 3: Queue worker
php artisan queue:work redis --tries=3 --timeout=60

# Terminal 4: Vite dev server (for hot reload during development)
npm run dev
```

### Production Deployment

```bash
# Build optimized assets
npm run build

# Start Reverb (background, port 8080)
php artisan reverb:start --port=8080 &

# Start queue worker (supervised)
php artisan queue:work redis --tries=3 --timeout=60 --sleep=3 &

# Start Laravel (via nginx/php-fpm or similar)
# Ensure Redis is running for queues, sessions, cache
```

### Service Management

```bash
# Start all at once
npm run start:prod

# Start just Reverb
npm run reverb:start

# Start just queue worker
npm run queue:work
```

---

## 12. File Reference

### Backend Files

| File | Purpose |
|------|---------|
| `app/Events/UserRegistered.php` | Broadcasts new farmer registration to admins |
| `app/Events/ProductCreated.php` | Broadcasts new product listing to marketplace + admins |
| `app/Events/ProductApproved.php` | Broadcasts product approval to seller + marketplace |
| `app/Events/OrderPlaced.php` | Broadcasts new order to buyer, seller, admins |
| `app/Events/OrderStatusChanged.php` | Broadcasts order status change to buyer, seller, admins |
| `app/Events/DeliveryStatusUpdated.php` | Broadcasts delivery status to buyer, driver, admins |
| `app/Events/DriverLocationUpdated.php` | Broadcasts real-time GPS to buyer (synchronous) |
| `app/Events/CourseEnrolled.php` | Broadcasts enrollment confirmation to user |
| `app/Events/LessonCompleted.php` | Broadcasts lesson completion to user |
| `app/Events/CourseCompleted.php` | Broadcasts course completion to user |
| `app/Events/NotificationCreated.php` | Broadcasts notification to user (synchronous) |
| `app/Events/InnovationVoteUpdated.php` | Broadcasts vote count to innovation hub |
| `app/Events/FarmAlertCreated.php` | Broadcasts farm alert to farmer + admins |
| `app/Events/DiseaseAlertCreated.php` | Broadcasts disease alert to admins + all users |
| `app/Jobs/BroadcastNotification.php` | Queued job: creates notification + broadcasts via WebSocket |
| `app/Services/PaymentService.php` | Payment webhook handler with broadcast integration |
| `routes/channels.php` | Channel authorization rules |

### Frontend Files

| File | Purpose |
|------|---------|
| `resources/js/app.js` | Laravel Echo + Reverb WebSocket client initialization |
| `resources/js/realtime.js` | Real-time engine: listeners, toasts, badges, DOM updates |
| `resources/views/layouts/app.blade.php` | Layout with meta tags, toast container, script loading |
| `resources/views/partials/navbar.blade.php` | Notification bell, badge, dropdown (cleaned of duplicate JS) |

### Config Files

| File | Purpose |
|------|---------|
| `config/broadcasting.php` | Broadcasting driver configuration (Reverb) |
| `config/reverb.php` | Reverb WebSocket server configuration |
| `config/queue.php` | Queue connection configuration (Redis) |
| `bootstrap/app.php` | CSRF exemption for broadcast auth, middleware aliases |
| `.env` | Environment variables for Reverb, Redis, queue |
| `package.json` | NPM scripts for dev, build, Reverb, queue worker |

---

## Summary of Changes Made

### Bug Fixes
1. **Fixed navbar `markAllRead` removing badge DOM element** — now hides via CSS instead of removing, preventing broken badge updates
2. **Removed duplicate `markNotifRead`/`markAllRead` functions** from navbar that conflicted with `realtime.js` definitions

### Missing Event Dispatches Added
3. **`DeliveryController::notifyBuyer()`** — added `BroadcastNotification::dispatch()` for 5 delivery statuses
4. **`AdminController::rejectProduct()`** — added seller notification + broadcast
5. **`AdminController::approveInnovation()`** — added innovator notification + broadcast
6. **`AdminController::rejectInnovation()`** — added innovator notification + broadcast
7. **`AdminController::suspendFarmer()`** — added user notification broadcast
8. **`AdminController::activateFarmer()`** — added user notification broadcast
9. **`FarmRecordsController::adminIntervene()`** — added `BroadcastNotification::dispatch()`
10. **`MarketplaceController::reportIssue()`** — added `BroadcastNotification::dispatch()` for admins
11. **`PaymentService::handleWebhook()`** — added buyer/seller notifications + `OrderStatusChanged` event on payment success/failure

### Real-Time Client Enhancements
12. **Fixed admin `order.status_changed` no-op listener** — now shows toast + increments badge + animates counter
13. **Fixed marketplace `product.created` no-op listener** — now updates DOM
14. **Added admin `delivery.status_updated` listener** — admins see delivery status toasts
15. **Added live notification page updates** — new notifications prepend to list when on `/notifications`
16. **Added admin activity feed live prepend** — new farmer registrations appear in activity table

### Performance Optimization
17. **Added `ShouldQueue` to 12 of 14 broadcast events** — non-blocking HTTP requests, Redis-backed background broadcasting
18. **Kept 2 events synchronous** — `DriverLocationUpdated` (GPS latency critical) and `NotificationCreated` (toast must be instant)

### Production Readiness
19. **Added CSRF exemption** for Reverb broadcast auth endpoint
20. **Added `window.Laravel` initialization script** — passes Reverb config from PHP to JavaScript
21. **Updated `app.js`** — reads config from `window.Laravel` with `import.meta.env` fallback
22. **Built Vite assets** — compiled Echo/Pusher into optimized `public/build/` bundle
23. **Added npm scripts** — `reverb:start`, `queue:work`, `start`, `start:prod` for easy service management
