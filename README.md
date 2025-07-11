# wpex-tetris-migration-tool

## Features

- **Scans all posts and content** for legacy meta keys used by the original `wpex-tetris` theme.
- **Migrates old media meta keys** (images, audio, video, embeds) to new universal meta keys required by the updated theme fork.
- **Converts data formats** as needed (e.g., URLs to attachment IDs for media fields).
- **Deletes obsolete meta keys** after successful migration to keep the database clean.
- **Cleans up unused or redundant data** left by the previous theme’s meta structure.
- **Provides a migration summary** showing which fields were updated, converted, or removed.
- **Can be installed as a temporary plugin or utility**—no need to keep it active after migration.
- **Safe to delete after use** for a leaner, more secure WordPress installation.
- **Minimizes risk of data loss** by automating all key conversions and cleanup.
- **Displays warnings and instructions** for users with customizations or child themes based on the original `wpex-tetris`, helping prevent compatibility issues.
- **Supports auto-update for the theme** (if configured), ensuring your theme stays up to date with the latest features and fixes—not the migration tool itself[1][2][4][5].

## Typical Workflow

1. **Install the migration tool** as a plugin or utility.
2. **Run the migration** to scan, convert, and clean up meta keys and data.
3. **Review the migration summary** for any issues or warnings.
4. **Delete the migration tool** after successful migration.
5. **The theme will auto-update itself** to the latest version once the migration tool is removed (if auto-update is enabled).
 
> This tool is designed for one-time use during your theme migration process.  
> After migration and cleanup, your theme will automatically update itself to ensure you are running the latest version.
