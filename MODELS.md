# Eloquent Model Map

This document maps every Eloquent model in the application, their relationships, and data flows for key operations across the Shop, Blog, and HR modules.

---

## Table of Contents

1. [Model Overview](#model-overview)
2. [Core Models](#core-models)
3. [Blog Module](#blog-module)
4. [Shop Module](#shop-module)
5. [HR Module](#hr-module)
6. [Cross-Cutting Concerns](#cross-cutting-concerns)
7. [Data Flows](#data-flows)

---

## Model Overview

| Model | Namespace | Table | Soft Deletes | Media | Tags |
|-------|-----------|-------|-------------|-------|------|
| User | `App\Models` | `users` | — | — | — |
| Team | `App\Models` | `teams` | — | — | — |
| Address | `App\Models` | `addresses` | — | — | — |
| Comment | `App\Models` | `comments` | — | — | — |
| Author | `App\Models\Blog` | `authors` | — | — | — |
| Post | `App\Models\Blog` | `posts` | — | ✓ | ✓ |
| PostCategory | `App\Models\Blog` | `post_categories` | — | — | — |
| Brand | `App\Models\Shop` | `brands` | — | ✓ | — |
| Product | `App\Models\Shop` | `products` | — | ✓ | — |
| ProductCategory | `App\Models\Shop` | `product_categories` | — | ✓ | — |
| Customer | `App\Models\Shop` | `customers` | ✓ | — | — |
| Order | `App\Models\Shop` | `orders` | ✓ | — | — |
| OrderItem | `App\Models\Shop` | `order_items` | — | — | — |
| OrderAddress | `App\Models\Shop` | `order_addresses` | — | — | — |
| Payment | `App\Models\Shop` | `payments` | — | — | — |
| Department | `App\Models\HR` | `departments` | — | — | — |
| Employee | `App\Models\HR` | `employees` | ✓ | — | — |
| Project | `App\Models\HR` | `projects` | ✓ | — | — |
| Task | `App\Models\HR` | `tasks` | — | — | — |
| Timesheet | `App\Models\HR` | `timesheets` | — | — | — |
| LeaveRequest | `App\Models\HR` | `leave_requests` | — | — | — |
| Expense | `App\Models\HR` | `expenses` | — | — | — |
| ExpenseLine | `App\Models\HR` | `expense_lines` | — | — | — |

---

## Core Models

### User
`app/Models/User.php` — `users` table

Implements: `FilamentUser`, `HasTenants`, `MustVerifyEmail`
Traits: `HasApiTokens`, `HasFactory`, `Notifiable`

| Field | Cast |
|-------|------|
| `email_verified_at` | `datetime` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `teams()` | BelongsToMany | Team |

**Filament Integration**
- `canAccessPanel()` — controls panel access
- `canAccessTenant()` — controls per-tenant access
- `getTenants()` — returns the user's teams

---

### Team
`app/Models/Team.php` — `teams` table

Implements: `HasCurrentTenantLabel`
Traits: `HasFactory`

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `users()` | BelongsToMany | User |

Teams are the tenancy unit. A User belongs to one or more Teams; all Filament panel data is scoped per Team.

---

### Address
`app/Models/Address.php` — `addresses` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `country` | `CountryCode` enum |

**Relationships**

| Method | Type | Target | Pivot |
|--------|------|--------|-------|
| `customers()` | MorphedByMany | Customer | `addressables` |
| `brands()` | MorphedByMany | Brand | `addressables` |

Shared address book used polymorphically by Customer and Brand via the `addressables` pivot table.

---

### Comment
`app/Models/Comment.php` — `comments` table

`guarded = []` (fully mass-assignable)
Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `is_visible` | `boolean` |

**Relationships**

| Method | Type | Target | Notes |
|--------|------|--------|-------|
| `customer()` | BelongsTo | Customer | author of the comment |
| `commentable()` | MorphTo | Post \| Product | polymorphic target |

---

## Blog Module

### Author
`app/Models/Blog/Author.php` — `authors` table

Traits: `HasFactory`

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `posts()` | HasMany | Post (FK: `author_id`) |

---

### Post
`app/Models/Blog/Post.php` — `posts` table

Implements: `HasMedia`
Traits: `HasFactory`, `HasTags` (Spatie), `InteractsWithMedia` (Spatie)

| Field | Cast |
|-------|------|
| `published_at` | `date` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `author()` | BelongsTo | Author (FK: `author_id`) |
| `postCategory()` | BelongsTo | PostCategory (FK: `post_category_id`) |
| `comments()` | MorphMany | Comment (via `commentable`) |

**Media Collections**
- `post-images` — single JPEG file; generates `thumb` conversion (40×40)

---

### PostCategory
`app/Models/Blog/PostCategory.php` — `post_categories` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `is_visible` | `boolean` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `posts()` | HasMany | Post (FK: `post_category_id`) |

---

## Shop Module

### Brand
`app/Models/Shop/Brand.php` — `brands` table

Implements: `HasMedia`
Traits: `HasFactory`, `InteractsWithMedia`

| Field | Cast |
|-------|------|
| `is_visible` | `boolean` |

**Relationships**

| Method | Type | Target | Pivot |
|--------|------|--------|-------|
| `addresses()` | MorphToMany | Address | `addressables` |
| `products()` | HasMany | Product (FK: `brand_id`) | — |

---

### Product
`app/Models/Shop/Product.php` — `products` table

Implements: `HasMedia`
Traits: `HasFactory`, `InteractsWithMedia`

| Field | Cast |
|-------|------|
| `featured` | `boolean` |
| `is_visible` | `boolean` |
| `backorder` | `boolean` |
| `requires_shipping` | `boolean` |
| `published_at` | `date` |

Key columns: `name`, `slug`, `sku`, `barcode`, `qty`, `security_stock`, `description`, `old_price`, `price`, `cost`, `type` (enum: `deliverable`/`downloadable`), dimensional fields (`weight`, `height`, `width`, `depth`, `volume`) with unit fields, `seo_title`, `seo_description`.

**Relationships**

| Method | Type | Target | Pivot |
|--------|------|--------|-------|
| `brand()` | BelongsTo | Brand (FK: `brand_id`) | — |
| `productCategories()` | BelongsToMany | ProductCategory | `product_category_product` (with timestamps) |
| `comments()` | MorphMany | Comment (via `commentable`) | — |

**Media Collections**
- `product-images` — multiple JPEG files; generates `thumb` conversion (40×40)

---

### ProductCategory
`app/Models/Shop/ProductCategory.php` — `product_categories` table

Implements: `HasMedia`
Traits: `HasFactory`, `InteractsWithMedia`

| Field | Cast |
|-------|------|
| `is_visible` | `boolean` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `parent()` | BelongsTo | ProductCategory (FK: `parent_id`) |
| `children()` | HasMany | ProductCategory (FK: `parent_id`) |
| `products()` | BelongsToMany | Product (pivot: `product_category_product`) |

Self-referential tree — supports nested category hierarchies.

---

### Customer
`app/Models/Shop/Customer.php` — `customers` table

Traits: `HasFactory`, `SoftDeletes`

| Field | Cast |
|-------|------|
| `birthday` | `date` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `addresses()` | MorphToMany | Address (via `addressable`) |
| `comments()` | HasMany | Comment |
| `orders()` | HasMany | Order (FK: `customer_id`) |
| `payments()` | HasManyThrough | Payment — through Order (FK: `customer_id`) |

---

### Order
`app/Models/Shop/Order.php` — `orders` table

`fillable = ['number', 'total_price', 'status', 'currency', 'shipping_price', 'shipping_method', 'notes']`
Traits: `HasFactory`, `SoftDeletes`

| Field | Cast |
|-------|------|
| `currency` | `CurrencyCode` enum |
| `status` | `OrderStatus` enum |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `customer()` | BelongsTo | Customer (FK: `customer_id`) |
| `address()` | MorphOne | OrderAddress (via `addressable`) |
| `orderItems()` | HasMany | OrderItem (FK: `order_id`) |
| `payments()` | HasMany | Payment |

---

### OrderItem
`app/Models/Shop/OrderItem.php` — `order_items` table

Traits: `HasFactory`

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `order()` | BelongsTo | Order |
| `product()` | BelongsTo | Product |

---

### OrderAddress
`app/Models/Shop/OrderAddress.php` — `order_addresses` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `country` | `CountryCode` enum |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `addressable()` | MorphTo | Order |

Snapshot of the shipping address at the time of order, stored separately from the shared `addresses` table.

---

### Payment
`app/Models/Shop/Payment.php` — `payments` table

`guarded = []` (fully mass-assignable)
Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `currency` | `CurrencyCode` enum |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `order()` | BelongsTo | Order |

---

## HR Module

### Department
`app/Models/HR/Department.php` — `departments` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `is_active` | `boolean` |
| `budget` | `decimal:2` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `parent()` | BelongsTo | Department (FK: `parent_id`) |
| `children()` | HasMany | Department (FK: `parent_id`) |
| `employees()` | HasMany | Employee |
| `projects()` | HasMany | Project |

Self-referential tree — supports nested org structures.

---

### Employee
`app/Models/HR/Employee.php` — `employees` table

Traits: `HasFactory`, `SoftDeletes`

| Field | Cast |
|-------|------|
| `employment_type` | `EmploymentType` enum |
| `is_active` | `boolean` |
| `skills` | `array` (JSON) |
| `metadata` | `array` (JSON) |
| `salary` | `decimal:2` |
| `hourly_rate` | `decimal:2` |
| `date_of_birth` | `date` |
| `hire_date` | `date` |

**Relationships**

| Method | Type | Target | FK |
|--------|------|--------|----|
| `department()` | BelongsTo | Department | `department_id` |
| `leaveRequests()` | HasMany | LeaveRequest | `employee_id` |
| `approvedLeaveRequests()` | HasMany | LeaveRequest | `approver_id` |
| `tasks()` | HasMany | Task | `assigned_to` |
| `timesheets()` | HasMany | Timesheet | — |
| `expenses()` | HasMany | Expense | — |

An Employee can appear twice on LeaveRequest — once as the requestor and once as the approver.

---

### Project
`app/Models/HR/Project.php` — `projects` table

Traits: `HasFactory`, `SoftDeletes`

| Field | Cast |
|-------|------|
| `status` | `ProjectStatus` enum |
| `priority` | `TaskPriority` enum |
| `budget` | `decimal:2` |
| `spent` | `decimal:2` |
| `estimated_hours` | `decimal:1` |
| `actual_hours` | `decimal:1` |
| `start_date` | `date` |
| `end_date` | `date` |
| `plan` | `array` (JSON) |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `department()` | BelongsTo | Department |
| `tasks()` | HasMany | Task |
| `timesheets()` | HasMany | Timesheet |
| `expenses()` | HasMany | Expense |

---

### Task
`app/Models/HR/Task.php` — `tasks` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `status` | `TaskStatus` enum |
| `priority` | `TaskPriority` enum |
| `estimated_hours` | `decimal:1` |
| `actual_hours` | `decimal:1` |
| `due_date` | `date` |
| `completed_at` | `datetime` |
| `labels` | `array` (JSON) |

**Relationships**

| Method | Type | Target | FK |
|--------|------|--------|----|
| `project()` | BelongsTo | Project | — |
| `assignee()` | BelongsTo | Employee | `assigned_to` |
| `timesheets()` | HasMany | Timesheet | — |

---

### Timesheet
`app/Models/HR/Timesheet.php` — `timesheets` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `date` | `date` |
| `hours` | `decimal:1` |
| `hourly_rate` | `decimal:2` |
| `total_cost` | `decimal:2` |
| `is_billable` | `boolean` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `employee()` | BelongsTo | Employee |
| `task()` | BelongsTo | Task |
| `project()` | BelongsTo | Project |

A Timesheet entry sits at the intersection of Employee, Task, and Project, tracking time worked and its cost.

---

### LeaveRequest
`app/Models/HR/LeaveRequest.php` — `leave_requests` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `type` | `LeaveType` enum |
| `status` | `LeaveStatus` enum |
| `start_date` | `date` |
| `end_date` | `date` |
| `days_requested` | `decimal:1` |
| `reviewed_at` | `datetime` |

**Relationships**

| Method | Type | Target | FK |
|--------|------|--------|----|
| `employee()` | BelongsTo | Employee | `employee_id` |
| `approver()` | BelongsTo | Employee | `approver_id` |

---

### Expense
`app/Models/HR/Expense.php` — `expenses` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `status` | `ExpenseStatus` enum |
| `category` | `ExpenseCategory` enum |
| `total_amount` | `decimal:2` |
| `submitted_at` | `datetime` |
| `approved_at` | `datetime` |

**Relationships**

| Method | Type | Target | FK |
|--------|------|--------|----|
| `employee()` | BelongsTo | Employee | `employee_id` (claimant) |
| `project()` | BelongsTo | Project | `project_id` |
| `approvedByEmployee()` | BelongsTo | Employee | `approved_by` (approver) |
| `expenseLines()` | HasMany | ExpenseLine | `expense_id` |

An Expense references Employee twice — once as the claimant (`employee_id`) and once as the approver (`approved_by`). These are two distinct FK columns on the `expenses` table pointing to the same `employees` table.

---

### ExpenseLine
`app/Models/HR/ExpenseLine.php` — `expense_lines` table

Traits: `HasFactory`

| Field | Cast |
|-------|------|
| `amount` | `decimal:2` |
| `unit_price` | `decimal:2` |
| `date` | `date` |

**Relationships**

| Method | Type | Target |
|--------|------|--------|
| `expense()` | BelongsTo | Expense |

---

## Cross-Cutting Concerns

### Polymorphic Relationships

```
Comment.commentable  ──► Post
                     ──► Product

OrderAddress.addressable ──► Order

Address ◄── (addressables pivot) ──► Customer
        ◄── (addressables pivot) ──► Brand
```

### Self-Referential Trees

```
ProductCategory ──parent_id──► ProductCategory (parent)
                ◄─────────── (children)

Department ──parent_id──► Department (parent)
           ◄─────────── (children)
```

### Spatie Packages

- **Spatie Media Library** (`InteractsWithMedia` / `HasMedia`): Post, Product, Brand, ProductCategory
- **Spatie Tags** (`HasTags`): Post only

### Enums

| Enum | Used On |
|------|---------|
| `CountryCode` | Address, OrderAddress |
| `CurrencyCode` | Order, Payment |
| `OrderStatus` | Order |
| `EmploymentType` | Employee |
| `ExpenseCategory` | Expense |
| `ExpenseStatus` | Expense |
| `LeaveStatus` | LeaveRequest |
| `LeaveType` | LeaveRequest |
| `ProjectStatus` | Project |
| `TaskPriority` | Task, Project |
| `TaskStatus` | Task |

### Mass Assignment

| Model | Strategy |
|-------|----------|
| Order | `$fillable` list |
| Comment, Payment | `$guarded = []` |
| All others | Implicit (unguarded globally via `AppServiceProvider`) |

### Soft Deletes

Customer, Order, Employee, Project — records are never hard-deleted; use `withTrashed()` or `onlyTrashed()` to query deleted records.

---

## Data Flows

### Creating an Order

```
1. Customer exists (or is created)
   └── Customer has one or more Address records (via addressables pivot)

2. Order is created
   ├── FK: customer_id → Customer
   ├── status: OrderStatus::New (initial)
   └── currency, shipping_price, shipping_method, notes

3. OrderAddress is created (snapshot of shipping address)
   └── MorphOne on Order via addressable (order_addresses table)
       ► Stores street, city, country, zip at the moment of order
       ► Independent of Customer's Address records

4. OrderItems are created (one per line)
   ├── FK: order_id → Order
   └── FK: product_id → Product
       ► Records qty, unit_price at time of order

5. Payment is created (when payment is captured)
   ├── FK: order_id → Order
   └── currency, amount, method (PaymentMethod enum)

6. Customer.payments is accessible via HasManyThrough:
   Customer → orders() → payments()
```

**Entity graph**

```
Customer
  └──hasMany──► Order
                 ├──morphOne──► OrderAddress
                 ├──hasMany───► OrderItem ──belongsTo──► Product
                 └──hasMany───► Payment
```

---

### Publishing a Post

```
1. Author exists (separate from User; a blog author profile)

2. PostCategory optionally exists
   └── is_visible controls front-end visibility

3. Post is created
   ├── FK: author_id → Author
   ├── FK: post_category_id → PostCategory (nullable)
   ├── published_at: date when the post goes live
   └── Tags attached via Spatie HasTags (tag pivot table)

4. Cover image is attached via Spatie Media Library
   └── Collection: 'post-images' (single JPEG)
       ► thumb conversion (40×40) generated automatically

5. Readers can comment
   └── Comment.commentable → Post (MorphTo)
       FK: customer_id → Customer (comment author)
       is_visible controls whether the comment is shown
```

**Entity graph**

```
Author ──hasMany──► Post ──belongsTo──► PostCategory
                     ├── tags (Spatie HasTags)
                     ├── media (post-images collection)
                     └──morphMany──► Comment ──belongsTo──► Customer
```

---

### Submitting an Expense

```
1. Employee exists, assigned to a Department

2. Project optionally exists (expense may be project-linked)
   └── FK: department_id → Department

3. Expense is created (the header/claim)
   ├── FK: employee_id → Employee (claimant)
   ├── FK: project_id → Project (optional)
   ├── status: ExpenseStatus::Draft (initial)
   ├── category: ExpenseCategory enum
   ├── total_amount: sum of all ExpenseLines
   └── submitted_at set when status moves to Submitted

4. ExpenseLines are created (one per receipt/item)
   ├── FK: expense_id → Expense
   ├── date, description
   ├── unit_price × qty → amount
   └── amount rolls up to Expense.total_amount

5. Approval flow
   ├── Expense.status → Submitted
   ├── Approver (another Employee) reviews
   ├── FK: approved_by → Employee (via approvedByEmployee())
   └── approved_at set when approved

6. Project.expenses aggregates costs across all project expenses
   Project.spent can be compared to Project.budget for burn tracking
```

**Entity graph**

```
Department ──hasMany──► Employee (claimant) ──hasMany──► Expense ──hasMany──► ExpenseLine
           │                                               │
           │             Employee (approver) ◄─approved_by┤
           │                                               │
           └──hasMany──► Project ◄──────────belongsTo──────┘
```
