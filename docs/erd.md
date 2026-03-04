# ERD Notes

## Entities

### users
Stores customer account identity and authentication data.

### addresses
Stores billing and shipping addresses for each customer.

### payment_methods
Stores mock payment records tied to a customer.

### products
Stores the shirt design-level catalog data.

### product_variants
Stores purchasable variants by color and size.

### cart_items
Stores in-progress cart selections for a logged-in user.

### orders
Stores submitted orders and pricing summary.

### order_items
Stores a snapshot of each ordered variant and quantity.

### password_resets
Stores reset tokens and expiration timestamps.

### contact_messages
Stores Contact Us submissions.

## Relationships

- users 1:N addresses
- users 1:N payment_methods
- users 1:N cart_items
- users 1:N orders
- products 1:N product_variants
- orders 1:N order_items
- product_variants 1:N cart_items
- product_variants 1:N order_items

## Inventory model

Inventory is stored on `product_variants.quantity_in_stock` so every design/color/size combination has a finite available quantity.
