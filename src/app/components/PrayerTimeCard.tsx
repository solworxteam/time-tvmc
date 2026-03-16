import type { ReactNode } from 'react';
import { Sun, Sunrise, Moon, Clock } from 'lucide-react';
import type { Mosque, PrayerName } from '../types/mosque';
import { prayerOrder, prayerDisplayNames, formatTime } from '../utils/prayer-utils';
import { Badge } from './ui/badge';

interface PrayerTimeCardProps {
  mosque: Mosque;
  isNearest: boolean;
  nextPrayer: PrayerName | null;
  currentPrayer: PrayerName | null;
  is24Hour: boolean;
  isFavorite: boolean;
  onToggleFavorite: (mosqueId: string) => void;
}

const prayerIcons: Record<PrayerName, ReactNode> = {
  fajr: <Moon className="h-4 w-4" />,
  sunrise: <Sunrise className="h-4 w-4" />,
  dhuhr: <Sun className="h-4 w-4" />,
  asr: <Sun className="h-4 w-4" />,
  maghrib: <Sun className="h-4 w-4" />,
  isha: <Moon className="h-4 w-4" />
};

export function PrayerTimeCard({
  mosque,
  isNearest,
  nextPrayer,
  currentPrayer,
  is24Hour,
  isFavorite,
  onToggleFavorite
}: PrayerTimeCardProps) {
  return (
    <div
      className={`rounded-2xl bg-white p-6 shadow-sm transition-all dark:bg-gray-800 ${
        isNearest ? 'border-l-4 border-yellow-400' : 'border border-gray-200 dark:border-gray-700'
      }`}
    >
      <div className="mb-4 flex items-start justify-between gap-4">
        <div className="flex-1">
          <div className="flex items-center gap-2">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{mosque.name}</h3>
            <button
              onClick={() => onToggleFavorite(mosque.id)}
              className="text-yellow-500 transition-colors hover:text-yellow-600"
              aria-label={isFavorite ? 'Remove from favorites' : 'Add to favorites'}
            >
              {isFavorite ? '★' : '☆'}
            </button>
          </div>
          <p className="text-sm text-gray-600 dark:text-gray-400">{mosque.address}</p>
          <div className="mt-1 flex flex-wrap gap-3 text-xs text-gray-500 dark:text-gray-500">
            <span>{mosque.postcode}</span>
            <span>•</span>
            <span className="flex items-center gap-1">
              <Clock className="h-3 w-3" />
              {mosque.distance.toFixed(1)} km
            </span>
            <span>•</span>
            <span>{mosque.direction}</span>
          </div>
        </div>
        {isNearest && (
          <Badge
            variant="outline"
            className="border-yellow-300 bg-yellow-50 text-yellow-700 dark:border-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-500"
          >
            Nearest
          </Badge>
        )}
      </div>

      <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-6">
        {prayerOrder.map((prayer) => {
          const isNext = prayer === nextPrayer;
          const isCurrent = prayer === currentPrayer;

          return (
            <div
              key={prayer}
              className={`rounded-xl p-3 transition-all ${
                isNext
                  ? 'border-2 border-green-500 bg-green-100 dark:border-green-600 dark:bg-green-900/30'
                  : isCurrent
                    ? 'border border-blue-300 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20'
                    : 'border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-700/50'
              }`}
            >
              <div className="mb-1 flex items-center gap-2">
                <span className={isNext ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'}>
                  {prayerIcons[prayer]}
                </span>
                <div className="text-xs font-medium text-gray-700 dark:text-gray-300">
                  {prayerDisplayNames[prayer]}
                </div>
              </div>
              <div
                className={`text-lg font-semibold ${
                  isNext
                    ? 'text-green-800 dark:text-green-300'
                    : isCurrent
                      ? 'text-blue-700 dark:text-blue-400'
                      : 'text-gray-900 dark:text-white'
                }`}
              >
                {formatTime(mosque.prayerTimes[prayer], is24Hour)}
              </div>
              {isNext && <div className="mt-1 text-xs font-medium text-green-700 dark:text-green-400">Next</div>}
              {isCurrent && !isNext && <div className="mt-1 text-xs text-blue-600 dark:text-blue-400">Current</div>}
            </div>
          );
        })}
      </div>
    </div>
  );
}
