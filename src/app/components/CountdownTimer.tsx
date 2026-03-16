import { useEffect, useState } from 'react';
import { Clock } from 'lucide-react';
import { getTimeUntilPrayer } from '../utils/prayer-utils';

interface CountdownTimerProps {
  targetTime: string;
  prayerName: string;
}

export function CountdownTimer({ targetTime, prayerName }: CountdownTimerProps) {
  const [timeRemaining, setTimeRemaining] = useState(getTimeUntilPrayer(targetTime));

  useEffect(() => {
    const interval = window.setInterval(() => {
      setTimeRemaining(getTimeUntilPrayer(targetTime));
    }, 60000);

    return () => window.clearInterval(interval);
  }, [targetTime]);

  return (
    <div className="rounded-2xl bg-gradient-to-r from-green-500 to-green-600 p-6 text-white shadow-lg dark:from-green-600 dark:to-green-700">
      <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <div className="mb-1 text-sm font-medium opacity-90">Next Prayer</div>
          <div className="text-3xl font-bold">{prayerName}</div>
        </div>
        <div className="text-left sm:text-right">
          <div className="mb-1 flex items-center gap-2 text-sm font-medium opacity-90 sm:justify-end">
            <Clock className="h-4 w-4" />
            <span>Time Remaining</span>
          </div>
          <div className="text-3xl font-bold">{timeRemaining}</div>
        </div>
      </div>
    </div>
  );
}
