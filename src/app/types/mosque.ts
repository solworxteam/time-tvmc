export interface PrayerTimes {
  fajr: string;
  sunrise: string;
  dhuhr: string;
  asr: string;
  maghrib: string;
  isha: string;
}

export interface Mosque {
  id: string;
  name: string;
  address: string;
  postcode: string;
  area: string;
  direction: string;
  distance: number;
  coordinates: {
    lat: number;
    lng: number;
  };
  prayerTimes: PrayerTimes;
}

export type PrayerName = keyof PrayerTimes;
