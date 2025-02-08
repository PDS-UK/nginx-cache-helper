# NGINX Cache Helper

## Description
NGINX Cache Helper is a WordPress plugin that automatically clears the NGINX FastCGI/Proxy cache whenever content is updated. It ensures that changes to posts, comments, plugins, and themes are immediately reflected on the frontend without needing manual intervention.

## Features
- Automatically purges NGINX cache when:
    - A post, page, or custom post type is published, updated, or deleted.
    - A comment is added, approved, unapproved, or deleted.
    - A plugin is activated or deactivated.
    - A theme is switched.
    - WordPress is updated.
    - A WooCommerce product is modified.
    - A WooCommerce order status changes.
- Manual cache purging via a WordPress admin menu item.
- Lightweight and optimised for performance.

## Installation
### Manual Installation
```sh
1. Download the plugin zip file or clone the repository.
2. Upload the plugin folder to `/wp-content/plugins/`.
3. Activate the plugin from the WordPress admin dashboard.
```

### Composer Installation
```sh
composer require pds-uk/nginx-cache-helper
```

