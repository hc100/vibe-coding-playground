# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Application Overview

**SkillTrack** is a Laravel 11 daily learning log application that allows users to track their learning progress through journal-style entries with Markdown support, tagging, and calendar visualization. Each user's data is completely isolated.

## Development Commands

### Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/LogControllerTest.php

# Run tests with verbose output (use ./vendor/bin/phpunit for more options)
./vendor/bin/phpunit
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Access database via Tinker
php artisan tinker
```

### Development Server
```bash
# Start Laravel development server
php artisan serve

# Build frontend assets (development)
npm run dev

# Build frontend assets (production)
npm run build

# Watch assets for changes
npm run dev
```

### Code Quality
```bash
# Format PHP code
./vendor/bin/pint

# Clear various caches during development
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Architecture Overview

### Database Schema
- **users**: Standard Laravel authentication
- **logs**: Main content (user_id, title, content, date) - user-scoped
- **tags**: Global tags with unique names
- **log_tag**: Many-to-many pivot table

### Key Models & Relationships
- **User** hasMany **Log** (with user isolation on all queries)
- **Log** belongsToMany **Tag** (many-to-many via log_tag pivot)
- **Log** includes query scopes: `forUser()`, `byDate()`, `withTag()`, `betweenDates()`

### Controllers
- **LogController**: Full CRUD + calendar API endpoint + date filtering
- **TagController**: Read-only tag management (index/show)
- All controllers enforce user isolation via policies and query scopes

### Security & Content Processing
- **Markdown**: Uses League\CommonMark with Table extension
- **XSS Protection**: HTMLPurifier sanitizes all rendered Markdown content
- **Authorization**: LogPolicy ensures users only access their own logs
- **Input Validation**: Comprehensive validation on all forms

### Frontend Stack
- **TailwindCSS** for styling with forms plugin
- **Alpine.js** for lightweight JavaScript reactivity
- **FullCalendar** library for interactive calendar display
- **Vite** for asset bundling with hot reload

### Calendar Integration
- Calendar view loads events from `/api/calendar/events` endpoint
- Events are clickable to view logs, dates clickable to create new logs
- Calendar shows log titles as events with tag information in tooltips

### Tag System
- Tags are created automatically when logs are saved (comma-separated input)
- `Tag::findOrCreateByNames()` handles creation/retrieval
- Popular tags are available via `Tag::popular()` scope
- All tag queries are filtered by user's logs only

### Routes Structure
```php
// All routes require authentication except auth routes
Route::resource('logs', LogController::class);           // Full CRUD
Route::get('/calendar', [LogController::class, 'calendar']);
Route::get('/logs/date/{date}', [LogController::class, 'byDate']);
Route::get('/api/calendar/events', [LogController::class, 'calendarEvents']);
Route::resource('tags', TagController::class)->only(['index', 'show']);
```

## Testing Patterns

### User Isolation Testing
Always test that users cannot access other users' data:
```php
$log = Log::factory()->create(['user_id' => $this->otherUser->id]);
$response = $this->actingAs($this->user)->get(route('logs.show', $log));
$response->assertStatus(403);
```

### Tag Processing Testing
Test comma-separated tag input with duplicates and whitespace:
```php
'tags' => 'Laravel, PHP, Laravel,  Vue  , '  // Tests deduplication and trimming
```

### Markdown Security Testing
Test XSS prevention in Markdown content:
```php
$logData['content'] = '<script>alert("xss")</script>';
// Should be sanitized in output
```

## Database Configuration

- **Development**: SQLite (database/database.sqlite)
- **Mail**: Log driver (emails saved to storage/logs/laravel.log)
- **Session**: File-based session storage

## Key Files to Understand

- `app/Models/Log.php` - Core model with all query scopes
- `app/Http/Controllers/LogController.php` - Main application logic
- `app/Policies/LogPolicy.php` - Authorization rules
- `resources/views/logs/calendar.blade.php` - FullCalendar integration
- `tests/Feature/LogControllerTest.php` - Comprehensive test patterns
- `routes/web.php` - Application routing structure

## Common Development Tasks

When adding new features, always ensure:
- User isolation via query scopes or policies
- Input validation and XSS protection
- Comprehensive feature tests
- Proper authorization checks