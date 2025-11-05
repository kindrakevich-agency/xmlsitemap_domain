# XML Sitemap Domain

A Drupal 11 module that integrates XML Sitemap with Domain Access to generate per-domain sitemaps.

## Features

- Choose domain when adding custom sitemap links
- Automatic sitemap regeneration when content is added or updated
- Per-domain sitemap filtering
- Support for multi-domain content distribution

## Requirements

- Drupal 11
- XML Sitemap module (xmlsitemap)
- Domain Access module (domain)

## Installation

1. Place this module in your Drupal modules directory (e.g., `modules/custom/xmlsitemap_domain`)

2. Install required dependencies:
   ```bash
   composer require drupal/xmlsitemap drupal/domain
   ```

3. Enable the module using Drush:
   ```bash
   drush en xmlsitemap_domain -y
   ```

   Or enable via the admin interface:
   - Go to Admin > Extend
   - Find "XML Sitemap Domain" and check the box
   - Click "Install"

## Usage

### Adding Custom Sitemap Links with Domain Selection

1. Navigate to Configuration > Search and metadata > XML Sitemap > Custom links
2. Click "Add custom link"
3. Fill in the link details
4. **Select a domain** from the "Domain" dropdown (or leave as "All domains")
5. Save the link

### Automatic Sitemap Regeneration

The module automatically regenerates sitemaps when:
- New content is created
- Content is updated
- Content is deleted

This ensures your sitemaps are always up to date.

### Configuration

The module includes a configuration option for automatic regeneration:
- Auto-regenerate: Enabled by default
- This can be modified in the module settings if needed

## How It Works

1. **Domain Selection**: Adds a domain field to custom sitemap link forms, allowing you to associate links with specific domains.

2. **Automatic Updates**: Hooks into entity operations (insert, update, delete) to trigger sitemap regeneration.

3. **Domain Filtering**: Filters sitemap links based on the current domain context, ensuring each domain shows only relevant content.

4. **Database Storage**: Uses a custom database table (`xmlsitemap_domain`) to store domain associations for custom links.

## Technical Details

### Files

- `xmlsitemap_domain.info.yml` - Module definition
- `xmlsitemap_domain.module` - Main module hooks and functions
- `xmlsitemap_domain.install` - Database schema and installation hooks
- `config/schema/xmlsitemap_domain.schema.yml` - Configuration schema

### Database Schema

The module creates a `xmlsitemap_domain` table:
- `link_id` - Custom link identifier
- `domain_id` - Associated domain identifier

### Hooks Implemented

- `hook_form_FORM_ID_alter()` - Adds domain field to forms
- `hook_entity_insert()` - Triggers regeneration on new content
- `hook_entity_update()` - Triggers regeneration on content update
- `hook_entity_delete()` - Triggers regeneration on content deletion
- `hook_xmlsitemap_link_alter()` - Filters links by domain
- `hook_xmlsitemap_links_alter()` - Filters custom links by domain

## Support

For issues, feature requests, or questions, please use the project's issue queue.

## License

This module is licensed under the GPL-2.0+ license.
