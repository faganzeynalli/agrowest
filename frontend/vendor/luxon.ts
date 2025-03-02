import { DateTime, Duration, Settings } from "luxon";
import { useAzuraCast } from "~/vendor/azuracast";

interface TimestampToRelative {
    (timestamp: number | null | undefined): string;
}

interface UseLuxon {
    DateTime: typeof DateTime,
    Duration: typeof Duration,
    timestampToRelative: TimestampToRelative
}

// Force Luxon to use English globally
Settings.defaultLocale = "en";

// Function to override relative time formatting safely
function customRelativeTime(timestamp: number | null | undefined): string {
    if (typeof timestamp !== 'number') {
        return '';
    }

    let timeString = DateTime.fromSeconds(timestamp).toRelative({ locale: 'en' });

    // Ensure timeString is not null before replacing words
    if (!timeString) return '';

    return timeString
        .replace(/in (\d+) hours/, "in $1 hrs")   // Modify hours
        .replace(/in (\d+) days/, "in $1 d")      // Modify days
        .replace(/in (\d+) weeks/, "in $1 w")     // Modify weeks
        .replace(/in (\d+) months/, "in $1 mo")   // Modify months
        .replace(/in (\d+) years/, "in $1 yrs");  // Modify years
}

export function useLuxon(): UseLuxon {
    const { localeWithDashes, timeConfig } = useAzuraCast();
    Settings.defaultLocale = localeWithDashes;

    // Ensure timestampToRelative is defined before returning
    const timestampToRelative: TimestampToRelative = (timestamp: number | null | undefined): string => {
        return customRelativeTime(timestamp);  // Use fixed function
    };

    return {
        DateTime,
        Duration,
        timestampToRelative  // Now this exists in the return object
    };
}
