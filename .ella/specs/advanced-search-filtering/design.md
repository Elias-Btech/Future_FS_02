# Design Document: Advanced Search & Filtering

## Overview

The Advanced Search & Filtering feature extends the existing CRM Pulse lead management system with comprehensive multi-criteria search, saved filter management, result statistics, and CSV export capabilities. This design maintains the existing MVC-inspired architecture, integrates seamlessly with the current Lead model, and ensures performance with large datasets through strategic database indexing and query optimization.

### Design Goals

1. **Seamless Integration**: Extend existing Lead model and maintain current architecture patterns
2. **Performance**: Support 1000+ leads with sub-500ms query response times
3. **Security**: Use PDO prepared statements throughout to prevent SQL injection
4. **Usability**: Provide intuitive filter UI with saved filter management
5. **Maintainability**: Follow existing code conventions and design patterns

### Key Design Decisions

1. **Filter_Manager as Core Class**: Create new `app/Core/FilterManager.php` to handle saved filter operations, keeping Lead model focused on lead data
2. **Query Builder Pattern**: Extend Lead model with advanced query building methods rather than creating separate query builder class
3. **JSON Serialization**: Store filter criteria as JSON in database for flexibility and easy deserialization
4. **URL-Based State**: Encode all filter criteria in URL query parameters for bookmarking and sharing
5. **Statistics via SQL Aggregates**: Calculate filter statistics using single optimized GROUP BY query
6. **Collapsible UI**: Use JavaScript-enhanced collapsible filter panel to reduce visual clutter

## Architecture

### Component Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      Filter UI Layer                         │
│  (public/leads/index.php + JavaScript enhancement)          │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ├─── GET Parameters ───┐
                 │                      │
                 ▼                      ▼
┌────────────────────────┐   ┌──────────────────────────┐
│   FilterManager        │   │   Lead Model (Extended)  │
│   (Core Class)         │   │                          │
│                        │   │  - getAll() enhanced     │
│  - saveFilter()        │   │  - count() enhanced      │
│  - getFilters()        │   │  - getStatistics()       │
│  - deleteFilter()      │   │  - exportFiltered()      │
│  - validateCriteria()  │   │                          │
└────────────┬───────────┘   └──────────┬───────────────┘
             │                          │
             │                          │
             ▼                          ▼
┌─────────────────────────────────────────────────────────────┐
│                    Database Layer (PDO)                      │
│                                                              │
│  Tables: saved_filters, leads                                │
│  Indexes: status, priority, source, created_at,              │
│           next_followup_date                                 │
└─────────────────────────────────────────────────────────────┘
```

### Data Flow

#### Search Flow
1. User submits filter form → GET request with query parameters
2. `public/leads/index.php` extracts filter criteria from `$_GET`
3. Lead model `getAll()` builds dynamic WHERE clause with PDO parameters
4. Database executes query using indexes
5. Results returned to view with pagination
6. Statistics calculated via `getStatistics()` method

#### Save Filter Flow
1. User clicks "Save Filter" → POST to `public/leads/save_filter.php`
2. FilterManager validates filter name and criteria
3. FilterManager serializes criteria to JSON
4. Insert into `saved_filters` table with admin_id
5. Redirect back to leads page with success message

#### Load Filter Flow
1. User clicks saved filter → GET request with `filter_id` parameter
2. FilterManager retrieves filter by ID and admin_id
3. Deserialize JSON criteria
4. Redirect to leads page with criteria as URL parameters

#### Export Flow
1. User clicks "Export Filtered Results" → GET request to `public/leads/export.php`
2. Extract same filter criteria from URL parameters
3. Lead model `exportFiltered()` executes same query without pagination
4. Export_Handler generates CSV with proper headers
5. Stream CSV file to browser with download headers

## Components and Interfaces

### 1. FilterManager Class

**Location**: `app/Core/FilterManager.php`

**Purpose**: Manage saved filter CRUD operations and validation

**Interface**:

```php
namespace App\Core;

class FilterManager extends BaseModel
{
    /**
     * Save a new filter for the current user
     * 
     * @param int $adminId User ID
     * @param string $name Filter name (3-50 chars)
     * @param array $criteria Filter criteria array
     * @return int Filter ID
     * @throws \InvalidArgumentException if validation fails
     */
    public function saveFilter(int $adminId, string $name, array $criteria): int;
    
    /**
     * Get all saved filters for a user
     * 
     * @param int $adminId User ID
     * @return array Array of filters with id, name, criteria, created_at
     */
    public function getFilters(int $adminId): array;
    
    /**
     * Get a single filter by ID (with ownership check)
     * 
     * @param int $filterId Filter ID
     * @param int $adminId User ID
     * @return array|null Filter data or null if not found/not owned
     */
    public function getFilter(int $filterId, int $adminId): ?array;
    
    /**
     * Delete a filter (with ownership check)
     * 
     * @param int $filterId Filter ID
     * @param int $adminId User ID
     * @return bool Success status
     */
    public function deleteFilter(int $filterId, int $adminId): bool;
    
    /**
     * Validate filter name
     * 
     * @param string $name Filter name
     * @return bool Valid status
     */
    public function validateFilterName(string $name): bool;
    
    /**
     * Check if filter name exists for user
     * 
     * @param int $adminId User ID
     * @param string $name Filter name
     * @return bool Exists status
     */
    public function filterNameExists(int $adminId, string $name): bool;
    
    /**
     * Validate filter criteria structure
     * 
     * @param array $criteria Filter criteria
     * @return bool Valid status
     */
    public function validateCriteria(array $criteria): bool;
}
```

### 2. Lead Model Extensions

**Location**: `app/Models/Lead.php` (extend existing class)

**New/Modified Methods**:

```php
/**
 * Get all leads with enhanced filtering including follow-up dates
 * 
 * @param array $filters Filter criteria
 * @param int $limit Results per page
 * @param int $offset Pagination offset
 * @return array Lead records
 */
public function getAll(array $filters = [], int $limit = 15, int $offset = 0): array;

/**
 * Count leads with enhanced filtering including follow-up dates
 * 
 * @param array $filters Filter criteria
 * @return int Total count
 */
public function count(array $filters = []): int;

/**
 * Get statistics for filtered results
 * 
 * @param array $filters Filter criteria
 * @return array Statistics with status_breakdown, priority_breakdown, source_breakdown, total
 */
public function getStatistics(array $filters = []): array;

/**
 * Export filtered leads (no pagination)
 * 
 * @param array $filters Filter criteria
 * @return array All matching lead records
 */
public function exportFiltered(array $filters = []): array;

/**
 * Build WHERE clause and parameters from filter criteria
 * 
 * @param array $filters Filter criteria
 * @return array ['where' => array, 'params' => array]
 */
private function buildWhereClause(array $filters): array;
```

### 3. Export Handler

**Location**: `public/leads/export.php` (new file)

**Purpose**: Generate and stream CSV files from filtered results

**Functionality**:
- Extract filter criteria from GET parameters
- Call `Lead::exportFiltered()` with criteria
- Generate CSV with headers
- Set HTTP headers for download
- Stream CSV content

### 4. Filter UI Component

**Location**: `public/leads/index.php` (enhanced existing file)

**Structure**:

```html
<!-- Filter Panel (Collapsible) -->
<div class="filter-panel">
  <!-- Basic Filters (Always Visible) -->
  <div class="basic-filters">
    - Text search
    - Status dropdown
    - Priority dropdown
    - Source dropdown
  </div>
  
  <!-- Advanced Filters (Collapsible) -->
  <div class="advanced-filters" id="advancedFilters">
    - Created Date From/To
    - Follow-up Date From/To
  </div>
  
  <!-- Filter Actions -->
  <div class="filter-actions">
    - Apply Filter button
    - Clear Filters button
    - Save Filter button (if criteria applied)
  </div>
  
  <!-- Saved Filters Sidebar -->
  <div class="saved-filters">
    - List of user's saved filters
    - Load and Delete actions
  </div>
</div>

<!-- Filter Statistics Panel -->
<div class="filter-stats">
  - Total count
  - Status breakdown
  - Priority breakdown
  - Source breakdown
</div>

<!-- Results Table (Existing) -->
<div class="results-table">
  <!-- Existing table structure -->
</div>
```

## Data Models

### saved_filters Table Schema

```sql
CREATE TABLE IF NOT EXISTS saved_filters (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id   INT UNSIGNED NOT NULL,
    name       VARCHAR(50) NOT NULL,
    criteria   JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    UNIQUE KEY unique_filter_name (admin_id, name),
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
```

**Column Descriptions**:
- `id`: Primary key
- `admin_id`: Foreign key to admins table (owner of filter)
- `name`: User-defined filter name (3-50 characters, unique per user)
- `criteria`: JSON-encoded filter criteria object
- `created_at`: Timestamp for ordering saved filters

**Criteria JSON Structure**:

```json
{
  "search": "TechCorp",
  "status": "New",
  "priority": "High",
  "source": "Google",
  "date_from": "2025-01-01",
  "date_to": "2025-01-31",
  "followup_from": "2025-02-01",
  "followup_to": "2025-02-28"
}
```

### Enhanced leads Table Indexes

**Existing Indexes** (from schema.sql):
- `idx_status` on `status`
- `idx_priority` on `priority`
- `idx_email` on `email`
- `idx_created` on `created_at`

**New Indexes Required**:

```sql
-- Add index for source filtering
ALTER TABLE leads ADD INDEX idx_source (source);

-- Add index for follow-up date filtering
ALTER TABLE leads ADD INDEX idx_followup_date (next_followup_date);

-- Composite index for common filter combinations
ALTER TABLE leads ADD INDEX idx_status_priority (status, priority);
```

### Filter Criteria Data Structure

**PHP Array Format** (used throughout application):

```php
[
    'search'        => string,  // Text search term
    'status'        => string,  // Status value or empty
    'priority'      => string,  // Priority value or empty
    'source'        => string,  // Source value or empty
    'date_from'     => string,  // YYYY-MM-DD or empty
    'date_to'       => string,  // YYYY-MM-DD or empty
    'followup_from' => string,  // YYYY-MM-DD or empty
    'followup_to'   => string,  // YYYY-MM-DD or empty
]
```

**Validation Rules**:
- All fields optional (empty string if not provided)
- Dates must match YYYY-MM-DD format if provided
- Status must be one of: New, Contacted, Follow-up, Converted, Closed
- Priority must be one of: High, Medium, Low
- Source must exist in lead_sources table or be empty
- Text search trimmed and sanitized

## URL Parameter Structure

### Query String Format

**Base URL**: `/public/leads/index.php`

**Parameters**:
- `search` - Text search term (URL encoded)
- `status` - Status filter value
- `priority` - Priority filter value
- `source` - Source filter value
- `date_from` - Created date start (YYYY-MM-DD)
- `date_to` - Created date end (YYYY-MM-DD)
- `followup_from` - Follow-up date start (YYYY-MM-DD)
- `followup_to` - Follow-up date end (YYYY-MM-DD)
- `page` - Pagination page number

**Example URLs**:

```
# High priority leads from Google
/public/leads/index.php?priority=High&source=Google

# Text search with date range
/public/leads/index.php?search=TechCorp&date_from=2025-01-01&date_to=2025-01-31

# Follow-up date range with status
/public/leads/index.php?status=Follow-up&followup_from=2025-02-01&followup_to=2025-02-28

# Pagination with filters
/public/leads/index.php?status=New&priority=High&page=2
```

**URL Generation**:
- Use `http_build_query()` for proper encoding
- Preserve all filter parameters in pagination links
- Clear filters redirects to base URL without parameters

## Error Handling

### Validation Errors

**Date Validation**:
```php
// In Lead model buildWhereClause()
if (!empty($filters['date_from']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filters['date_from'])) {
    throw new \InvalidArgumentException('Invalid date format for date_from. Use YYYY-MM-DD.');
}

if (!empty($filters['date_from']) && !empty($filters['date_to']) 
    && $filters['date_from'] > $filters['date_to']) {
    throw new \InvalidArgumentException('Start date must be before or equal to end date.');
}
```

**Filter Name Validation**:
```php
// In FilterManager
public function validateFilterName(string $name): bool
{
    $trimmed = trim($name);
    return strlen($trimmed) >= 3 && strlen($trimmed) <= 50;
}
```

### Database Errors

**Error Handling Strategy**:
- Catch PDOException in model methods
- Log error details to error log
- Return user-friendly error messages
- Never expose SQL errors to users

**Example**:
```php
try {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
} catch (\PDOException $e) {
    error_log("Lead query error: " . $e->getMessage());
    throw new \RuntimeException('Unable to complete search. Please try again.');
}
```

### User-Facing Error Messages

**Display Strategy**:
- Use session flash messages for errors
- Display in red alert box at top of page
- Preserve user input in form fields
- Provide actionable guidance

**Error Message Examples**:
- "Invalid date format. Please use YYYY-MM-DD."
- "Start date must be before or equal to end date."
- "Filter name must be between 3 and 50 characters."
- "A filter with this name already exists. Please choose a different name."
- "Unable to complete search. Please try again."

## Testing Strategy

### Unit Testing Approach

This feature involves database interactions, UI rendering, and configuration validation. Property-based testing is **NOT appropriate** for this feature because:

1. **Database Operations**: FilterManager and Lead model methods interact with external database state
2. **UI Rendering**: Filter UI is primarily HTML generation and layout
3. **Configuration Validation**: Simple validation rules with specific examples

**Testing Strategy**:
- **Unit Tests**: Test individual methods with mocked database connections
- **Integration Tests**: Test complete filter workflows with test database
- **Manual Testing**: Verify UI responsiveness and user experience

### Unit Test Coverage

**FilterManager Tests**:
- `testSaveFilterSuccess()` - Valid filter save
- `testSaveFilterDuplicateName()` - Duplicate name rejection
- `testSaveFilterInvalidName()` - Name validation (too short, too long, empty)
- `testGetFiltersForUser()` - Retrieve user's filters
- `testGetFilterOwnershipCheck()` - Prevent accessing other user's filters
- `testDeleteFilterSuccess()` - Successful deletion
- `testDeleteFilterOwnershipCheck()` - Prevent deleting other user's filters
- `testValidateFilterName()` - Name validation edge cases
- `testValidateCriteria()` - Criteria structure validation

**Lead Model Tests**:
- `testGetAllWithTextSearch()` - Text search across multiple fields
- `testGetAllWithStatusFilter()` - Status filtering
- `testGetAllWithPriorityFilter()` - Priority filtering
- `testGetAllWithSourceFilter()` - Source filtering
- `testGetAllWithDateRange()` - Created date range filtering
- `testGetAllWithFollowupDateRange()` - Follow-up date range filtering
- `testGetAllWithMultipleCriteria()` - Combined filters (AND logic)
- `testGetAllWithInvalidDateFormat()` - Date validation error
- `testGetAllWithInvalidDateRange()` - Start > end date error
- `testCountWithFilters()` - Count matches getAll results
- `testGetStatistics()` - Statistics calculation accuracy
- `testExportFiltered()` - Export returns all matching records

**Export Handler Tests**:
- `testExportCSVHeaders()` - Correct HTTP headers
- `testExportCSVFilename()` - Filename format with timestamp
- `testExportCSVContent()` - CSV structure and data accuracy
- `testExportWithFilters()` - Export respects filter criteria

### Integration Test Scenarios

1. **Complete Search Flow**: Apply filters → View results → Verify count
2. **Save and Load Filter**: Apply filters → Save → Clear → Load → Verify criteria restored
3. **Export Filtered Results**: Apply filters → Export → Verify CSV matches displayed results
4. **Pagination with Filters**: Apply filters → Navigate pages → Verify filters preserved
5. **Statistics Accuracy**: Apply filters → Verify statistics match actual result breakdown
6. **URL Persistence**: Apply filters → Copy URL → Open in new tab → Verify filters applied

### Manual Testing Checklist

**UI Responsiveness**:
- [ ] Filter panel displays correctly on desktop (1920px)
- [ ] Filter panel displays correctly on laptop (1366px)
- [ ] Filter panel displays correctly on tablet (768px)
- [ ] Collapsible advanced filters work smoothly
- [ ] Saved filters sidebar is accessible

**Filter Functionality**:
- [ ] Text search finds leads across name, email, company, notes
- [ ] Status filter returns only matching status
- [ ] Priority filter returns only matching priority
- [ ] Source filter returns only matching source
- [ ] Date range filters work correctly (from only, to only, both)
- [ ] Follow-up date filters work correctly
- [ ] Multiple filters combine with AND logic
- [ ] Clear filters resets to all leads

**Saved Filters**:
- [ ] Save filter with valid name succeeds
- [ ] Save filter with duplicate name shows error
- [ ] Save filter with invalid name shows error
- [ ] Load saved filter applies all criteria
- [ ] Delete saved filter removes from list
- [ ] Saved filters are user-specific (not shared)

**Export**:
- [ ] Export button appears when filters applied
- [ ] Export downloads CSV file
- [ ] CSV filename includes timestamp
- [ ] CSV contains all filtered results
- [ ] CSV columns match specification

**Performance**:
- [ ] Search completes within 500ms for 1000+ leads
- [ ] Statistics calculation is fast
- [ ] Pagination is responsive
- [ ] No N+1 query issues

## Performance Strategy

### Database Indexing

**Required Indexes**:

```sql
-- Existing indexes (already in schema.sql)
INDEX idx_status (status)
INDEX idx_priority (priority)
INDEX idx_created (created_at)

-- New indexes to add
INDEX idx_source (source)
INDEX idx_followup_date (next_followup_date)
INDEX idx_status_priority (status, priority)  -- Composite for common combination
```

**Index Usage**:
- Single-column indexes for individual filter criteria
- Composite index for frequently combined filters (status + priority)
- Date indexes for range queries
- MySQL query optimizer will choose best index based on query

### Query Optimization

**Single Query Approach**:
- Build complete WHERE clause with all criteria
- Use single SELECT with all conditions
- Avoid multiple sequential queries
- Use LIMIT/OFFSET for pagination

**Prepared Statement Benefits**:
- Query plan caching
- Parameter binding prevents SQL injection
- Reduced parsing overhead

**Statistics Query Optimization**:

```sql
-- Single query with GROUP BY for all statistics
SELECT 
    COUNT(*) as total,
    status,
    priority,
    source,
    COUNT(*) as count
FROM leads
WHERE [filter conditions]
GROUP BY status, priority, source
```

**Avoid N+1 Queries**:
- Fetch all data in single query
- No per-row queries in loops
- Use JOINs for related data if needed

### Pagination Strategy

**Benefits**:
- Limits result set size to 15 leads per page
- Reduces memory usage
- Faster rendering
- Better user experience

**Implementation**:
- Use LIMIT and OFFSET in SQL
- Calculate total pages from count
- Preserve filters in pagination links
- Show page range (current ± 2 pages)

### Caching Considerations

**Not Implemented Initially** (future enhancement):
- Cache saved filters in session
- Cache filter statistics for repeated queries
- Use Redis/Memcached for high-traffic scenarios

**Current Approach**:
- Rely on MySQL query cache
- Rely on database indexes
- Optimize queries for speed

## UI Component Structure

### Filter Panel Layout

**Desktop Layout (>1024px)**:

```
┌─────────────────────────────────────────────────────────────┐
│  Advanced Filters                                    [▼]     │
├─────────────────────────────────────────────────────────────┤
│  [Search Input                    ] [Status ▼] [Priority ▼] │
│  [Source ▼] [From Date] [To Date] [Apply] [Clear] [Save]   │
│                                                              │
│  Follow-up Dates: [From Date] [To Date]                     │
└─────────────────────────────────────────────────────────────┘
```

**Tablet/Mobile Layout (<1024px)**:

```
┌──────────────────────────────────┐
│  Advanced Filters         [▼]    │
├──────────────────────────────────┤
│  [Search Input              ]    │
│  [Status ▼]                      │
│  [Priority ▼]                    │
│  [Source ▼]                      │
│  Created: [From] [To]            │
│  Follow-up: [From] [To]          │
│  [Apply] [Clear] [Save]          │
└──────────────────────────────────┘
```

### Saved Filters Sidebar

**Location**: Right side of filter panel or collapsible section

**Structure**:

```
┌─────────────────────────┐
│  Saved Filters          │
├─────────────────────────┤
│  ○ High Priority Leads  │
│     [Load] [Delete]     │
│                         │
│  ○ Google New Leads     │
│     [Load] [Delete]     │
│                         │
│  ○ Follow-up This Week  │
│     [Load] [Delete]     │
│                         │
│  [+ Save Current]       │
└─────────────────────────┘
```

### Statistics Panel

**Location**: Between filter panel and results table

**Structure**:

```
┌─────────────────────────────────────────────────────────────┐
│  Filter Results: 47 leads                                    │
├─────────────────────────────────────────────────────────────┤
│  Status: New (12) | Contacted (18) | Follow-up (15) | ...   │
│  Priority: High (8) | Medium (25) | Low (14)                │
│  Source: Google (15) | Website (12) | LinkedIn (10) | ...   │
└─────────────────────────────────────────────────────────────┘
```

### Responsive Breakpoints

**Tailwind CSS Classes**:
- `lg:` prefix for desktop (1024px+)
- `md:` prefix for tablet (768px+)
- Default for mobile (<768px)

**Layout Adjustments**:
- Desktop: Horizontal grid layout, 3-4 columns
- Tablet: 2 columns, stacked sections
- Mobile: Single column, full-width inputs

### Color Scheme (Existing CRM Pulse)

**Primary Colors**:
- Indigo: `bg-indigo-600`, `text-indigo-600` (primary actions)
- Slate: `bg-slate-50`, `text-slate-600` (neutral elements)
- Amber: `bg-amber-100`, `text-amber-700` (warnings)
- Red: `bg-red-100`, `text-red-700` (errors, high priority)
- Emerald: `bg-emerald-100`, `text-emerald-700` (success, converted)

**Border Styles**:
- Rounded: `rounded-xl`, `rounded-2xl`
- Border: `border border-slate-200`
- Shadow: `shadow-sm`

### JavaScript Enhancement

**Collapsible Filter Panel**:

```javascript
// Toggle advanced filters visibility
document.getElementById('toggleAdvanced').addEventListener('click', function() {
    const panel = document.getElementById('advancedFilters');
    panel.classList.toggle('hidden');
    this.querySelector('i').classList.toggle('bi-chevron-down');
    this.querySelector('i').classList.toggle('bi-chevron-up');
});
```

**Save Filter Modal**:

```javascript
// Show save filter modal
document.getElementById('saveFilterBtn').addEventListener('click', function() {
    const modal = document.getElementById('saveFilterModal');
    modal.classList.remove('hidden');
});

// Submit save filter form
document.getElementById('saveFilterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/public/leads/save_filter.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.error);
        }
    });
});
```

**Active Filter Summary**:

```javascript
// Display active filter count when collapsed
function updateFilterSummary() {
    const filters = document.querySelectorAll('.filter-input');
    let activeCount = 0;
    filters.forEach(input => {
        if (input.value && input.value !== '') activeCount++;
    });
    document.getElementById('filterSummary').textContent = 
        activeCount > 0 ? `${activeCount} filters active` : 'No filters';
}
```

## Implementation Notes

### File Structure

**New Files**:
- `app/Core/FilterManager.php` - Saved filter management
- `public/leads/save_filter.php` - Save filter endpoint
- `public/leads/load_filter.php` - Load filter endpoint (or handle in index.php)
- `public/leads/delete_filter.php` - Delete filter endpoint
- `public/leads/export.php` - CSV export endpoint

**Modified Files**:
- `app/Models/Lead.php` - Add new methods and enhance existing ones
- `public/leads/index.php` - Enhanced filter UI and statistics display
- `database/schema.sql` - Add saved_filters table and new indexes

### Migration Script

**Database Changes**:

```sql
-- Add saved_filters table
CREATE TABLE IF NOT EXISTS saved_filters (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id   INT UNSIGNED NOT NULL,
    name       VARCHAR(50) NOT NULL,
    criteria   JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    UNIQUE KEY unique_filter_name (admin_id, name),
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;

-- Add new indexes to leads table
ALTER TABLE leads ADD INDEX idx_source (source);
ALTER TABLE leads ADD INDEX idx_followup_date (next_followup_date);
ALTER TABLE leads ADD INDEX idx_status_priority (status, priority);
```

### Security Considerations

**SQL Injection Prevention**:
- Use PDO prepared statements for all queries
- Bind all user input as parameters
- Never concatenate user input into SQL strings

**XSS Prevention**:
- Use `Helper::e()` for all output
- Sanitize user input before display
- Use `htmlspecialchars()` with ENT_QUOTES

**CSRF Protection** (future enhancement):
- Add CSRF tokens to save/delete filter forms
- Validate tokens on POST requests

**Authorization**:
- Check admin authentication on all pages
- Verify filter ownership before load/delete operations
- Use `Auth::require()` at top of all pages

### Backward Compatibility

**Existing Functionality Preserved**:
- Current filter bar continues to work
- Existing Lead model methods unchanged (only extended)
- Current pagination logic maintained
- Existing export functionality remains

**Graceful Degradation**:
- If JavaScript disabled, basic filters still work
- Saved filters accessible via server-side rendering
- No JavaScript required for core functionality

### Future Enhancements

**Potential Additions** (not in current scope):
- Filter sharing between users
- Filter templates for common scenarios
- Advanced query builder with OR logic
- Custom field filtering
- Bulk actions on filtered results
- Scheduled filter reports via email
- Filter usage analytics

---

## Summary

This design provides a comprehensive, performant, and maintainable solution for advanced search and filtering in CRM Pulse. The architecture extends existing patterns, maintains security best practices, and delivers an intuitive user experience. The implementation follows the existing codebase conventions and integrates seamlessly with current functionality.

**Key Strengths**:
- ✅ Seamless integration with existing Lead model
- ✅ Performance optimized with strategic indexing
- ✅ Security-first approach with PDO prepared statements
- ✅ User-friendly collapsible UI design
- ✅ Comprehensive error handling and validation
- ✅ Maintainable component architecture
- ✅ Backward compatible with existing features

**Implementation Priority**:
1. Database schema changes (saved_filters table, indexes)
2. Lead model enhancements (query building, statistics)
3. FilterManager class implementation
4. Enhanced filter UI in leads/index.php
5. Export functionality
6. Saved filter management endpoints
7. JavaScript enhancements for UX
8. Testing and optimization
