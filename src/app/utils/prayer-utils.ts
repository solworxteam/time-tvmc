import type { PrayerName, PrayerTimes } from '../types/mosque';

export const prayerOrder: PrayerName[] = ['fajr', 'sunrise', 'dhuhr', 'asr', 'maghrib', 'isha'];

export const prayerDisplayNames: Record<PrayerName, string> = {
  fajr: 'Fajr',
  sunrise: 'Sunrise',
  dhuhr: 'Dhuhr',
  asr: 'Asr',
  maghrib: 'Maghrib',
  isha: 'Isha'
};

export function getNextPrayer(prayerTimes: PrayerTimes): { name: PrayerName; time: string } | null {
  const now = new Date();
  const currentTime = now.getHours() * 60 + now.getMinutes();

  for (const prayer of prayerOrder) {
    const [hours, minutes] = prayerTimes[prayer].split(':').map(Number);
    const prayerTime = hours * 60 + minutes;

    if (prayerTime > currentTime) {
      return { name: prayer, time: prayerTimes[prayer] };
    }
  }

  return { name: 'fajr', time: prayerTimes.fajr };
}

export function getCurrentPrayer(prayerTimes: PrayerTimes): PrayerName | null {
  const now = new Date();
  const currentTime = now.getHours() * 60 + now.getMinutes();

  let currentPrayer: PrayerName | null = null;

  for (const prayer of prayerOrder) {
    const [hours, minutes] = prayerTimes[prayer].split(':').map(Number);
    const prayerTime = hours * 60 + minutes;

    if (prayerTime <= currentTime) {
      currentPrayer = prayer;
    } else {
      break;
    }
  }

  return currentPrayer;
}

export function getTimeUntilPrayer(targetTime: string): string {
  const now = new Date();
  const [hours, minutes] = targetTime.split(':').map(Number);

  const target = new Date(now);
  target.setHours(hours, minutes, 0, 0);

  if (target < now) {
    target.setDate(target.getDate() + 1);
  }

  const diff = target.getTime() - now.getTime();
  const hoursRemaining = Math.floor(diff / (1000 * 60 * 60));
  const minutesRemaining = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

  if (hoursRemaining > 0) {
    return `${hoursRemaining}h ${minutesRemaining}m`;
  }

  return `${minutesRemaining}m`;
}

export function formatTime(time: string, is24Hour: boolean): string {
  if (is24Hour) {
    return time;
  }

  const [hours, minutes] = time.split(':').map(Number);
  const period = hours >= 12 ? 'PM' : 'AM';
  const displayHours = hours % 12 || 12;

  return `${displayHours}:${minutes.toString().padStart(2, '0')} ${period}`;
}

export function calculateDistance(lat1: number, lon1: number, lat2: number, lon2: number): number {
  const earthRadiusKm = 6371;
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return earthRadiusKm * c;
}
