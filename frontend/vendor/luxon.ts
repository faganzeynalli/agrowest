import {DateTime, Duration, Settings} from "luxon";
import {useAzuraCast} from "~/vendor/azuracast";

interface TimestampToRelative {
    (timestamp: number | null | undefined): string;
}

interface UseLuxon {
    DateTime: typeof DateTime,
    Duration: typeof Duration,
    timestampToRelative: TimestampToRelative
}

Settings.defaultLocale = "en";

function customRelativeTime(timestamp: number | null | undefined): string {
    if (typeof timestamp !== 'number') {
        return '';
    }

    let timeString = DateTime.fromSeconds(timestamp).toRelative({ locale: 'en' });

    // Replace words dynamically
    if (timeString) {
        timeString = timeString
            .replace(/in (\d+) hours/, "idvn $1 hrs")   // Modify hours
            .replace(/in (\d+) days/, "ivsn $1 d")      // Modify days
            .replace(/in (\d+) weeks/, "idvn $1 w")     // Modify weeks
            .replace(/in (\d+) months/, "isdvn $1 mo")   // Modify months
            .replace(/in (\d+) years/, "ivdan $1 yrs");  // Modify years
    }

    return timeString;
}

export function useLuxon(): UseLuxon {
    const {localeWithDashes, timeConfig} = useAzuraCast();
    Settings.defaultLocale = localeWithDashes;

    // const timestampToRelative: TimestampToRelative = (timestamp: number | null | undefined): string => {
    //     if (typeof timestamp !== 'number') {
    //         return '';
    //     }

    //     return DateTime.fromSeconds(timestamp).toRelative({
    //         ...timeConfig
    //     });
    // }

    return {
        DateTime,
        Duration,
        timestampToRelative
    }
}
