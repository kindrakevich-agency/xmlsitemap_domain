# XML Sitemap Domain

A Drupal 11 module that integrates XML Sitemap with Domain Access to create separate sitemaps for each domain.

## Features

- Enable per-domain sitemaps via simple checkbox configuration
- Automatic content filtering based on domain access
- Automatic sitemap regeneration when content is added, updated, or deleted
- Domain-specific sitemap URLs
- Support for domain-specific base URLs in sitemaps

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

### Enabling Per-Domain Sitemaps

1. Navigate to **Configuration > Search and metadata > XML Sitemap > Settings** (`/admin/config/search/xmlsitemap/settings`)

2. You will see a new section called **"Domain-Specific Sitemaps"**

3. Check the boxes for the domains you want to enable sitemaps for

4. Click **"Save configuration"**

5. The module will automatically:
   - Generate separate sitemaps for each enabled domain
   - Filter content based on domain assignments
   - Provide sitemap URLs for each domain

### Sitemap URLs

Once configured, each domain will have its own sitemap available at:
- `/sitemap-[domain_id].xml`

For example, if you have domains with IDs:
- `example_com` → `/sitemap-example_com.xml`
- `blog_example_com` → `/sitemap-blog_example_com.xml`
- `shop_example_com` → `/sitemap-shop_example_com.xml`

The settings page will show you the exact URLs for each enabled domain.

### Automatic Sitemap Regeneration

The module automatically regenerates the appropriate domain sitemaps when:
- New content is created and assigned to specific domains
- Content is updated or domain assignments change
- Content is deleted

This ensures your sitemaps stay up-to-date without manual intervention.

### How Content is Filtered

Content is included in a domain's sitemap based on:
- The **field_domain_access** field on the content
- The **field_domain_all_affiliates** field (if content is available to all domains)

Only content assigned to a sitemap's domain will appear in that sitemap.

## Configuration

### Auto-Regeneration

The module includes automatic regeneration settings:
- **Auto-regenerate**: Enabled by default
- Automatically queues sitemaps for regeneration when relevant content changes

You can configure this at: Configuration > XML Sitemap Domain Settings

### Domain Setup

Before using this module, ensure you have:
1. Domain Access module installed and configured
2. At least one domain created in **Configuration > Domain Access**
3. Content types configured with `field_domain_access` field

## Troubleshooting

### "Domain-Specific Sitemaps" section not appearing

1. **Clear cache**: Run `drush cr` or clear cache via admin interface
2. **Check dependencies**: Ensure Domain Access module is enabled
3. **Check form ID**: Look in your Drupal logs for "Form ID:" messages from xmlsitemap_domain
4. **Check permissions**: Ensure you have permission to configure XML Sitemap

### Content not appearing in domain sitemap

1. Verify the content has the correct domain assigned in the **Domain Access** field
2. Check that the content type is enabled in XML Sitemap settings
3. Clear cache and regenerate the sitemap
4. Check that the domain is enabled in the Domain-Specific Sitemaps section

### Sitemap returns 404

1. Ensure the domain is enabled in the settings
2. Clear all caches
3. Run cron to regenerate sitemaps
4. Check the exact sitemap URL in the settings page

## Technical Details

### Files

- `xmlsitemap_domain.info.yml` - Module definition
- `xmlsitemap_domain.module` - Main module hooks and functions
- `xmlsitemap_domain.install` - Installation and uninstallation hooks
- `config/schema/xmlsitemap_domain.schema.yml` - Configuration schema

### Data Storage

The module uses Drupal State API to store configuration:
- **State key**: `xmlsitemap_domain_enabled` - Array of enabled domain IDs
- **State key**: `xmlsitemap_domain_current` - Current domain context during generation

### Hooks Implemented

- `hook_form_alter()` - Adds domain checkboxes to XML Sitemap settings form
- `hook_entity_insert()` - Triggers regeneration on new content
- `hook_entity_update()` - Triggers regeneration on content updates
- `hook_entity_delete()` - Triggers regeneration on content deletion
- `hook_xmlsitemap_links_alter()` - Filters content by domain in sitemaps
- `hook_page_attachments()` - Adds sitemap link to HTML head

### Domain Field Requirements

For the module to work properly, your content types should have:
- **field_domain_access** - Entity reference field to domain entities
- **field_domain_all_affiliates** (optional) - Boolean field for "all domains" content

These fields are typically created by the Domain Access module.

### Debugging

The module logs form IDs to help with troubleshooting. Check your Drupal logs at:
- **Admin > Reports > Recent log messages**
- Look for messages from `xmlsitemap_domain`

## Known Limitations

- Sitemap URLs use domain IDs, not domain hostnames
- Per-domain sitemap index files not yet supported
- Requires manual cache clear after configuration changes

## Support

For issues, feature requests, or questions, please use the project's issue queue.

## License

This module is licensed under the GPL-2.0+ license.
