# XML Sitemap Domain

A Drupal 11 module that integrates XML Sitemap with Domain Access to create separate sitemaps for each domain.

## Features

- Enable per-domain sitemaps via checkbox configuration
- Automatic content filtering based on domain assignment
- Automatic sitemap regeneration when content changes
- Domain-specific URLs in sitemaps
- Bypasses access checks for domain-filtered content
- Support for "All affiliates" content (appears in all domain sitemaps)

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

3. Enable the module:
   ```bash
   drush en xmlsitemap_domain -y
   ```

   Or via admin interface:
   - Go to Admin > Extend
   - Find "XML Sitemap Domain" and check the box
   - Click "Install"

## Usage

### Enabling Per-Domain Sitemaps

1. Navigate to **Configuration > Search and metadata > XML Sitemap > Settings**
   (`/admin/config/search/xmlsitemap/settings`)

2. Scroll to the **"Domain-Specific Sitemaps"** section

3. Check the boxes for domains you want to enable sitemaps for

4. Click **"Save configuration"**

5. Rebuild sitemaps:
   ```bash
   drush xmlsitemap:rebuild
   ```
   Or via UI at `/admin/config/search/xmlsitemap/rebuild`

### Accessing Domain Sitemaps

Once configured, each domain will have its own sitemap at:
- `https://domain1.com/sitemap.xml`
- `https://domain2.com/sitemap.xml`
- etc.

Each sitemap contains only content assigned to that specific domain.

### Content Filtering Logic

Content appears in a domain's sitemap if:

1. **Node is assigned to the domain** - The `field_domain_access` field includes the domain ID
2. **"All affiliates" is enabled** - The `field_domain_all_affiliates` field is set to TRUE
3. **No domain field exists** - Nodes without domain restrictions appear in all sitemaps

Content is **excluded** if:
- Node is assigned to OTHER domains only (not this domain)

### Automatic Sitemap Regeneration

The module automatically triggers sitemap regeneration when:
- New content is created
- Content is updated
- Content is deleted
- Domain assignments change

This ensures sitemaps stay current without manual intervention.

## How It Works

### Technical Architecture

The module uses XML Sitemap's context system to generate separate sitemaps:

1. **Context Registration** (`hook_xmlsitemap_context_info()`)
   - Registers "domain" as a context type

2. **Context Detection** (`hook_xmlsitemap_context()`)
   - Detects the active domain when sitemap is accessed
   - Returns context `['domain' => 'domain_id']`

3. **Query Filtering** (`hook_query_xmlsitemap_generate_alter()`)
   - Modifies the database query to filter by domain
   - Joins with `node__field_domain_access` table
   - **Removes access check** (Domain Access blocks it incorrectly)
   - Filters nodes by domain assignment

4. **URL Rewriting** (`hook_xmlsitemap_element_alter()`)
   - Rewrites URLs to use the active domain's base URL
   - Ensures all sitemap URLs match the domain being accessed

### Why Access Check is Bypassed

Domain Access module sets `access = 0` for nodes when checked from the "wrong" domain context. Since we're already filtering by domain assignment, the access check is redundant and causes all nodes to be excluded. The module removes this filter for domain-specific sitemaps.

**Security**: Only published nodes (`status = 1`) are included. Domain assignment provides access control.

## Configuration

### Auto-Regeneration

Auto-regeneration is enabled by default in `xmlsitemap_domain.install`. Sitemaps regenerate automatically when content with domain assignments changes.

### Domain Field Requirements

Your content types should have these fields (created by Domain Access module):
- **field_domain_access** - Entity reference to domain entities (required)
- **field_domain_all_affiliates** - Boolean for "all domains" content (optional)

## Files

- `xmlsitemap_domain.info.yml` - Module definition and dependencies
- `xmlsitemap_domain.module` - Main implementation (hooks)
- `xmlsitemap_domain.install` - Installation and uninstallation
- `config/schema/xmlsitemap_domain.schema.yml` - Configuration schema
- `README.md` - This file

## Troubleshooting

### No "Domain-Specific Sitemaps" section in settings

1. **Clear cache**: `drush cr`
2. **Verify Domain module is enabled**: `drush pm:list --status=enabled | grep domain`
3. **Check for domains**: Go to Configuration > Domain Access

### Sitemap shows only 1 link (frontpage)

This was caused by access checks. The current version bypasses this. If you still see the issue:

1. **Update to latest version** of this module
2. **Clear cache**: `drush cr`
3. **Delete old sitemaps**: `rm -f web/sites/default/files/sitemap*.xml*`
4. **Rebuild**: `drush xmlsitemap:rebuild`

### Different domains show same content

1. **Verify domain assignments**: Check that content has `field_domain_access` set
2. **Rebuild sitemaps**: `drush xmlsitemap:rebuild`
3. **Check enabled domains**: Verify domains are checked in module settings

### Sitemap URLs show wrong domain

1. **Access via correct domain**: Sitemaps are generated dynamically based on the domain you access them from
2. **Clear cache**: The domain context is cached: `drush cr`

## Support

For issues, feature requests, or questions, please use the project's issue queue.

## License

This module is licensed under the GPL-2.0+ license.
