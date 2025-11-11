# RESM Project - Testing Documentation

## Overview

This document describes the comprehensive test suite and CI/CD pipeline implemented for the RESM (Room/Event Scheduling Management) project.

## Test Coverage

### Unit Tests

#### 1. BookingService Tests (`tests/Unit/BookingServiceTest.php`)
Tests the critical booking clash detection logic with 12 test cases:
- No conflicts when booking slots are available
- Complete overlap detection
- Partial overlap detection (before/after/inside)
- Adjacent booking handling (start/end times touching)
- Multi-room isolation
- Multiple booking scenarios

#### 2. Model Tests
- **UserModelTest.php**: Tests User model relationships and isAdmin() helper method
- **BookingModelTest.php**: Tests Booking relationships, timezone conversion, and status casting
- **EventModelTest.php**: Tests Event model relationships and datetime casting
- **RoomModelTest.php**: Tests Room relationships and unique name constraint

#### 3. Policy Tests (`tests/Unit/BookingPolicyTest.php`)
Comprehensive authorization tests for BookingPolicy:
- Admin permissions (view, update, delete any booking)
- Regular user permissions (own bookings only)
- Pending vs approved booking edit restrictions
- Cross-user access control

### Feature Tests

#### 1. Booking Controller Tests (`tests/Feature/BookingControllerTest.php`)
23 comprehensive tests covering:
- Index view (admin sees all, users see own)
- Booking creation with validation
- Clash detection during creation and updates
- Edit/Update authorization
- Delete authorization
- Approve/Reject workflow (admin only)
- API endpoints (by room, by room and date)
- Email notifications

#### 2. Event Controller Tests (`tests/Feature/EventControllerTest.php`)
10 tests covering:
- Event listing (creator and staff visibility)
- Event creation
- Staff member assignment
- Update authorization
- Delete authorization
- API endpoint (JSON response)

#### 3. Room Controller Tests (`tests/Feature/RoomControllerTest.php`)
10 tests covering:
- Room listing
- CRUD operations
- Admin-only create/update/delete restrictions

### Factory Classes

All model factories have been fully implemented:
- **BookingFactory**: Includes approved(), rejected(), and pending() states
- **EventFactory**: With realistic date ranges
- **RoomFactory**: With unique name generation
- **UserFactory**: Already implemented

## Running Tests

### Local Development

Using Docker:
```bash
docker-compose exec app php artisan test
```

Using Pest PHP directly:
```bash
docker-compose exec app ./vendor/bin/pest
```

Run specific test file:
```bash
docker-compose exec app php artisan test tests/Unit/BookingServiceTest.php
```

Run with coverage (requires xdebug):
```bash
docker-compose exec app php artisan test --coverage
```

### CI/CD Pipeline

The project uses GitHub Actions for automated testing on every push and pull request.

#### Workflow File
`.github/workflows/laravel.yml`

#### Pipeline Steps

**Test Job:**
1. PostgreSQL service setup
2. PHP 8.2 installation with required extensions
3. Environment configuration (.env.testing)
4. Composer dependency installation
5. Application key generation
6. Database migrations
7. PHPUnit/Pest test execution
8. Laravel Pint code style check

**Build Job:**
1. Node.js 20 setup
2. NPM dependency installation
3. Frontend asset compilation

#### Triggers
- Push to `main` or `develop` branches
- Pull requests to `main` or `develop` branches

## Test Configuration

### PHPUnit Configuration (`phpunit.xml`)
- Test database: SQLite in-memory
- RefreshDatabase trait applied to all feature tests via Pest.php
- Test environment variables configured

### Pest Configuration (`tests/Pest.php`)
```php
uses(
    Tests\TestCase::class,
    RefreshDatabase::class
)->in('Feature');

uses(Tests\TestCase::class)->in('Unit');
```

## Test Statistics

### Coverage Summary
- **Unit Tests**: 6 files, 50+ tests
- **Feature Tests**: 3 files, 43 tests
- **Total Tests**: 93+ comprehensive test cases

### Critical Areas Covered
- Booking clash detection (business logic)
- Authorization and policies
- Email notifications
- CRUD operations
- API endpoints
- Role-based access control

### Areas Pending
- Mail content tests
- Form request validation tests
- QR Code generation tests
- Localization middleware tests
- Browser tests (Dusk - pending zip extension setup)

## Best Practices Implemented

1. **No Emojis**: All test code follows professional coding standards
2. **Minimal Comments**: Code is self-documenting
3. **Factory Usage**: Proper use of model factories for test data
4. **Mail Faking**: Email tests use Mail::fake() to avoid sending real emails
5. **Database Transactions**: RefreshDatabase ensures clean state between tests
6. **Authorization Testing**: Comprehensive policy tests for security
7. **Real-World Scenarios**: Tests cover actual use cases and edge cases

## Continuous Integration Benefits

The CI/CD pipeline ensures:
1. All tests pass before code is merged
2. Code style is consistent (Laravel Pint)
3. Database migrations work correctly
4. Frontend builds successfully
5. Dependencies are up to date
6. No breaking changes are introduced

## Next Steps

To further improve test coverage:
1. Install Laravel Dusk (requires zip PHP extension in Docker)
2. Add browser tests for complete user workflows
3. Add mail content assertion tests
4. Add form request validation tests
5. Add integration tests for complex multi-step workflows
6. Set up code coverage reporting in CI/CD
7. Add performance/load testing

## Running Specific Test Suites

Unit tests only:
```bash
docker-compose exec app php artisan test tests/Unit
```

Feature tests only:
```bash
docker-compose exec app php artisan test tests/Feature
```

Specific test method:
```bash
docker-compose exec app php artisan test --filter="test name"
```

## Troubleshooting

### Database Issues
If tests fail due to database issues:
```bash
docker-compose exec app php artisan migrate:fresh --env=testing
```

### Permission Issues
If factory or seeder issues occur:
```bash
docker-compose exec app composer dump-autoload
```

### Cache Issues
Clear all caches:
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

## Additional Resources

- [Pest PHP Documentation](https://pestphp.com/)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
