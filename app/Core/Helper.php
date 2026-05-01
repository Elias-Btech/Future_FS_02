<?php
// ============================================================
// Helper.php — Global utility functions
// ============================================================
namespace App\Core;

class Helper
{
    // Safely output HTML-escaped string (prevents XSS)
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    // Redirect to a URL
    public static function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    // Format a date for display
    public static function formatDate(?string $date, string $format = 'M j, Y'): string
    {
        if (!$date) return '—';
        return date($format, strtotime($date));
    }

    // Time ago (e.g. "2 hours ago")
    public static function timeAgo(string $datetime): string
    {
        $diff = time() - strtotime($datetime);
        if ($diff < 60)     return $diff . 's ago';
        if ($diff < 3600)   return floor($diff / 60) . 'm ago';
        if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
        if ($diff < 604800) return floor($diff / 86400) . 'd ago';
        return date('M j, Y', strtotime($datetime));
    }

    // Generate initials from a name (e.g. "John Doe" → "JD")
    public static function initials(string $name): string
    {
        $words = explode(' ', trim($name));
        $init  = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $init .= strtoupper($w[0] ?? '');
        }
        return $init;
    }

    // Status badge CSS classes (Tailwind)
    public static function statusClass(string $status): string
    {
        return match($status) {
            'New'       => 'bg-indigo-100 text-indigo-700',
            'Contacted' => 'bg-amber-100 text-amber-700',
            'Follow-up' => 'bg-blue-100 text-blue-700',
            'Converted' => 'bg-emerald-100 text-emerald-700',
            'Closed'    => 'bg-slate-100 text-slate-600',
            default     => 'bg-gray-100 text-gray-600',
        };
    }

    // Priority badge CSS classes (Tailwind)
    public static function priorityClass(string $priority): string
    {
        return match($priority) {
            'High'   => 'bg-red-100 text-red-700',
            'Medium' => 'bg-amber-100 text-amber-700',
            'Low'    => 'bg-slate-100 text-slate-600',
            default  => 'bg-gray-100 text-gray-600',
        };
    }

    // Paginate an array of results
    public static function paginate(int $total, int $perPage, int $current): array
    {
        $totalPages = (int) ceil($total / $perPage);
        return [
            'total'       => $total,
            'per_page'    => $perPage,
            'current'     => $current,
            'total_pages' => $totalPages,
            'offset'      => ($current - 1) * $perPage,
            'has_prev'    => $current > 1,
            'has_next'    => $current < $totalPages,
        ];
    }
}
