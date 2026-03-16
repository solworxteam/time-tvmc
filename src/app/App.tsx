import { useEffect, useMemo, useState } from 'react';
import { MapPinOff, Star } from 'lucide-react';
import { Header } from './components/Header';
import { FilterBar } from './components/FilterBar';
import { CountdownTimer } from './components/CountdownTimer';
import { PrayerTimeCard } from './components/PrayerTimeCard';
import { mosques as initialMosques } from './data/mosques';
import { useGeolocation } from './hooks/useGeolocation';
import { calculateDistance, getCurrentPrayer, getNextPrayer, prayerDisplayNames } from './utils/prayer-utils';
import type { Mosque } from './types/mosque';

export default function App() {
  const [darkMode, setDarkMode] = useState(false);
  const [is24Hour, setIs24Hour] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [sortBy, setSortBy] = useState<'distance' | 'name'>('distance');
  const [selectedArea, setSelectedArea] = useState('all');
  const [favorites, setFavorites] = useState<Set<string>>(new Set());
  const [mosques, setMosques] = useState<Mosque[]>(initialMosques);

  const { latitude, longitude, error: geoError, loading: geoLoading } = useGeolocation();

  useEffect(() => {
    if (latitude && longitude) {
      const updatedMosques = initialMosques.map((mosque) => ({
        ...mosque,
        distance: calculateDistance(latitude, longitude, mosque.coordinates.lat, mosque.coordinates.lng)
      }));
      setMosques(updatedMosques);
    }
  }, [latitude, longitude]);

  useEffect(() => {
    if (darkMode) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }, [darkMode]);

  const areas = useMemo(() => Array.from(new Set(mosques.map((mosque) => mosque.area))).sort(), [mosques]);

  const filteredMosques = useMemo(() => {
    let filtered = [...mosques];

    if (searchQuery) {
      const query = searchQuery.toLowerCase();
      filtered = filtered.filter(
        (mosque) =>
          mosque.name.toLowerCase().includes(query) ||
          mosque.postcode.toLowerCase().includes(query) ||
          mosque.address.toLowerCase().includes(query)
      );
    }

    if (selectedArea !== 'all') {
      filtered = filtered.filter((mosque) => mosque.area === selectedArea);
    }

    filtered.sort((a, b) => {
      const aIsFavorite = favorites.has(a.id);
      const bIsFavorite = favorites.has(b.id);

      if (aIsFavorite && !bIsFavorite) {
        return -1;
      }
      if (!aIsFavorite && bIsFavorite) {
        return 1;
      }

      return sortBy === 'distance' ? a.distance - b.distance : a.name.localeCompare(b.name);
    });

    return filtered;
  }, [favorites, mosques, searchQuery, selectedArea, sortBy]);

  const nextPrayerInfo = useMemo(() => {
    if (filteredMosques.length === 0) {
      return null;
    }

    const nearest = filteredMosques[0];
    const nextPrayer = getNextPrayer(nearest.prayerTimes);

    return nextPrayer
      ? { ...nextPrayer, displayName: prayerDisplayNames[nextPrayer.name] }
      : null;
  }, [filteredMosques]);

  const nearestMosque = filteredMosques[0];

  const toggleFavorite = (mosqueId: string) => {
    setFavorites((previous) => {
      const next = new Set(previous);
      if (next.has(mosqueId)) {
        next.delete(mosqueId);
      } else {
        next.add(mosqueId);
      }
      return next;
    });
  };

  return (
    <div className="min-h-screen bg-gray-50 transition-colors dark:bg-gray-900">
      <Header darkMode={darkMode} onToggleDarkMode={() => setDarkMode((value) => !value)} />

      <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {nextPrayerInfo && (
          <div className="mb-6">
            <CountdownTimer targetTime={nextPrayerInfo.time} prayerName={nextPrayerInfo.displayName} />
          </div>
        )}

        {geoLoading && (
          <div className="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            Detecting your location for accurate mosque distances...
          </div>
        )}

        {geoError && (
          <div className="mb-6 flex items-start gap-3 rounded-2xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
            <MapPinOff className="mt-0.5 h-5 w-5 text-yellow-600 dark:text-yellow-500" />
            <div className="text-sm text-yellow-800 dark:text-yellow-300">
              <strong>Location access denied.</strong> Distances are approximate. Enable location access for accurate sorting.
            </div>
          </div>
        )}

        {favorites.size > 0 && (
          <div className="mb-4 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <Star className="h-4 w-4 fill-yellow-500 text-yellow-500" />
            <span>
              {favorites.size} favorite mosque{favorites.size !== 1 ? 's' : ''}
            </span>
          </div>
        )}

        <div className="mb-6">
          <FilterBar
            searchQuery={searchQuery}
            onSearchChange={setSearchQuery}
            sortBy={sortBy}
            onSortChange={setSortBy}
            selectedArea={selectedArea}
            onAreaChange={setSelectedArea}
            areas={areas}
            is24Hour={is24Hour}
            onTimeFormatToggle={() => setIs24Hour((value) => !value)}
          />
        </div>

        <div className="space-y-4">
          {filteredMosques.length > 0 ? (
            filteredMosques.map((mosque, index) => {
              const nextPrayer = getNextPrayer(mosque.prayerTimes);
              const currentPrayer = getCurrentPrayer(mosque.prayerTimes);

              return (
                <PrayerTimeCard
                  key={mosque.id}
                  mosque={mosque}
                  isNearest={index === 0 && mosque.id === nearestMosque?.id}
                  nextPrayer={nextPrayer?.name || null}
                  currentPrayer={currentPrayer}
                  is24Hour={is24Hour}
                  isFavorite={favorites.has(mosque.id)}
                  onToggleFavorite={toggleFavorite}
                />
              );
            })
          ) : (
            <div className="py-12 text-center text-gray-500 dark:text-gray-400">
              <p className="text-lg">No mosques found matching your criteria.</p>
              <p className="mt-2 text-sm">Try adjusting your search or filters.</p>
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
