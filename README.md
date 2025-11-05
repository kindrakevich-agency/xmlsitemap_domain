# XML Sitemap Domain

A Drupal 11 module that integrates XML Sitemap with Domain Access to create separate sitemaps for each domain.

## Features

- Create individual sitemaps for each domain
- Select domain when adding a new XML sitemap
- Automatic sitemap regeneration when content is added, updated, or deleted
- Per-domain content filtering in sitemaps
- Support for domain-specific URLs in sitemaps

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

### Creating Per-Domain Sitemaps

1. Navigate to **Configuration > Search and metadata > XML Sitemap** (`/admin/config/search/xmlsitemap`)

2. Click **"Add sitemap"** (`/admin/config/search/xmlsitemap/add`)

3. Fill in the sitemap details:
   - **Label**: Give your sitemap a descriptive name (e.g., "Main Site Sitemap" or "Blog Domain Sitemap")
   - **Domain**: **Select the domain** for this sitemap from the dropdown
   - Configure other sitemap settings as needed

4. Click **"Save"** to create the sitemap

5. Repeat steps 2-4 for each domain to create separate sitemaps

### Example: Setting Up Multiple Domain Sitemaps

If you have three domains:
- example.com (main site)
- blog.example.com (blog)
- shop.example.com (shop)

Create three separate sitemaps:
1. Sitemap #1: Label "Main Site", Domain: example.com
2. Sitemap #2: Label "Blog Site", Domain: blog.example.com
3. Sitemap #3: Label "Shop Site", Domain: shop.example.com

Each sitemap will only include content assigned to its respective domain.

### Automatic Sitemap Regeneration

The module automatically regenerates the appropriate domain sitemaps when:
- New content is created (assigned to specific domains)
- Content is updated or domain assignments change
- Content is deleted

This ensures your sitemaps stay up-to-date without manual intervention.

### How Content is Filtered

Content is included in a domain's sitemap based on:
- The **field_domain_access** field on the content
- The **field_domain_all_affiliates** field (if content is available to all domains)

Only content assigned to a sitemap's domain will appear in that sitemap.

## Configuration

The module includes automatic regeneration settings:
- **Auto-regenerate**: Enabled by default
- Automatically queues sitemaps for regeneration when relevant content changes

## Technical Details

### Files

- `xmlsitemap_domain.info.yml` - Module definition
- `xmlsitemap_domain.module` - Main module hooks and functions
- `xmlsitemap_domain.install` - Installation and uninstallation hooks
- `config/schema/xmlsitemap_domain.schema.yml` - Configuration schema

### Data Storage

The module uses Drupal State API to store domain-to-sitemap associations:
- **State key**: `xmlsitemap_domain_mapping`
- **Format**: Array mapping sitemap context keys to domain IDs

### Hooks Implemented

- `hook_form_FORM_ID_alter()` - Adds domain field to sitemap add/edit forms
- `hook_entity_insert()` - Triggers regeneration on new content
- `hook_entity_update()` - Triggers regeneration on content updates
- `hook_entity_delete()` - Triggers regeneration on content deletion
- `hook_xmlsitemap_link_alter()` - Filters content by domain in sitemaps
- `hook_xmlsitemap_context_alter()` - Adds domain info to sitemap context
- `hook_xmlsitemap_context_url_options_alter()` - Sets domain-specific base URLs

### Domain Field Requirements

For the module to work properly, your content types should have:
- **field_domain_access** - Entity reference field to domain entities
- **field_domain_all_affiliates** (optional) - Boolean field for "all domains" content

These fields are typically created by the Domain Access module.

## Troubleshooting

### Content not appearing in sitemap

1. Verify the content has the correct domain assigned in the **Domain Access** field
2. Check that the content type is enabled in XML Sitemap settings
3. Clear cache and regenerate the sitemap

### Sitemap not regenerating automatically

1. Check that **Auto-regenerate** is enabled in module settings
2. Verify cron is running properly
3. Manually regenerate from the XML Sitemap admin interface

## Support

For issues, feature requests, or questions, please use the project's issue queue.

## License

This module is licensed under the GPL-2.0+ license.
