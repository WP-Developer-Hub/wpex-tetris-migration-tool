# wpex-tetris-migration-tool

## Important Notice for Users

If you encounter errors or warnings related to class names and constructor names (a legacy PHP issue), please use the [WPEX Tetris Migration Tool](https://github.com/WP-Developer-Hub/wpex-tetris-migration-tool) to update your site. This tool will automatically fix the compatibility bug and ensure your theme works on all modern PHP versions.

### What the Migration Tool Does

- **Scans all posts and content** for legacy meta keys used by the original `wpex-tetris` theme.
- **Migrates old media meta keys** (images, audio, video, embeds) to new universal meta keys required by the updated theme fork.
- **Migrates your logo and copyright info:**  
  - Transfers your old `wpex_logo` setting to the standard WordPress `custom_logo` theme mod.
  - Moves your `wpex_copyright` setting to the new `universal_copyright_layout` theme mod.
- **Converts data formats** as needed (for example, URLs to attachment IDs for media fields).
- **Deletes obsolete meta keys** after successful migration to keep your database clean.
- **Cleans up unused or redundant data** left by the previous theme’s meta structure.
- **Provides a migration summary** showing which fields were updated, converted, or removed.
- **Displays warnings and instructions** for users with customizations or child themes based on the original theme, helping prevent compatibility issues.
- **Can be installed as a temporary plugin or utility**—no need to keep it active after migration. It is safe to delete after use for a leaner, more secure WordPress installation.
- **Supports auto-update for the theme** (if configured), ensuring your theme stays up to date with the latest features and fixes (not the migration tool itself).

### Instructions

1. Download and install the migration tool plugin.
2. Follow the on-screen instructions to complete the update.
3. Once finished, you can safely remove the migration tool.

If you have any questions or run into issues, please open an issue on [GitHub](https://github.com/WP-Developer-Hub/wpex-tetris-migration-tool/issues) or contact support.

