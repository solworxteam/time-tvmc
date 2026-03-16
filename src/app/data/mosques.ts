import type { Mosque } from '../types/mosque';

export const mosques: Mosque[] = [
  {
    id: '1',
    name: 'TVMC - Teesside Volunteer Masjid & Community Centre',
    address: '75 Waterloo Rd, Middlesbrough',
    postcode: 'TS1 3DU',
    area: 'Middlesbrough',
    direction: '↑ N',
    distance: 0.5,
    coordinates: { lat: 54.5742, lng: -1.235 },
    prayerTimes: {
      fajr: '05:05',
      sunrise: '06:48',
      dhuhr: '12:13',
      asr: '14:24',
      maghrib: '17:39',
      isha: '19:20'
    }
  },
  {
    id: '2',
    name: 'Jamia Masjid Ghousia',
    address: '118 Southfield Rd, Middlesbrough',
    postcode: 'TS1 3BZ',
    area: 'Middlesbrough',
    direction: '→ E',
    distance: 1.2,
    coordinates: { lat: 54.57, lng: -1.228 },
    prayerTimes: {
      fajr: '05:05',
      sunrise: '06:48',
      dhuhr: '12:15',
      asr: '14:25',
      maghrib: '17:40',
      isha: '19:22'
    }
  },
  {
    id: '3',
    name: 'Masjid-E-Noor',
    address: '45 Borough Rd, Middlesbrough',
    postcode: 'TS1 2HJ',
    area: 'Middlesbrough',
    direction: '↗ NE',
    distance: 0.8,
    coordinates: { lat: 54.578, lng: -1.23 },
    prayerTimes: {
      fajr: '05:03',
      sunrise: '06:46',
      dhuhr: '12:12',
      asr: '14:23',
      maghrib: '17:38',
      isha: '19:19'
    }
  },
  {
    id: '4',
    name: 'Middlesbrough Islamic Centre',
    address: '85 Parliament Rd, Middlesbrough',
    postcode: 'TS1 4HY',
    area: 'Middlesbrough',
    direction: '↓ S',
    distance: 1.5,
    coordinates: { lat: 54.565, lng: -1.234 },
    prayerTimes: {
      fajr: '05:06',
      sunrise: '06:49',
      dhuhr: '12:14',
      asr: '14:26',
      maghrib: '17:41',
      isha: '19:23'
    }
  },
  {
    id: '5',
    name: 'Masjid Al-Rahman',
    address: '22 Cannon St, Middlesbrough',
    postcode: 'TS1 1EB',
    area: 'Middlesbrough',
    direction: '← W',
    distance: 2.1,
    coordinates: { lat: 54.576, lng: -1.245 },
    prayerTimes: {
      fajr: '05:04',
      sunrise: '06:47',
      dhuhr: '12:13',
      asr: '14:24',
      maghrib: '17:39',
      isha: '19:21'
    }
  },
  {
    id: '6',
    name: 'Stockton Masjid',
    address: '12 Dovecot St, Stockton-on-Tees',
    postcode: 'TS18 1LH',
    area: 'Stockton',
    direction: '↘ SE',
    distance: 6.4,
    coordinates: { lat: 54.565, lng: -1.315 },
    prayerTimes: {
      fajr: '05:07',
      sunrise: '06:50',
      dhuhr: '12:15',
      asr: '14:27',
      maghrib: '17:42',
      isha: '19:24'
    }
  }
];
