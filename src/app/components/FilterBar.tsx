import { Search, SlidersHorizontal, MapPin, Clock12, Clock } from 'lucide-react';
import { Input } from './ui/input';
import { Button } from './ui/button';

interface FilterBarProps {
  searchQuery: string;
  onSearchChange: (query: string) => void;
  sortBy: 'distance' | 'name';
  onSortChange: (sort: 'distance' | 'name') => void;
  selectedArea: string;
  onAreaChange: (area: string) => void;
  areas: string[];
  is24Hour: boolean;
  onTimeFormatToggle: () => void;
}

export function FilterBar({
  searchQuery,
  onSearchChange,
  sortBy,
  onSortChange,
  selectedArea,
  onAreaChange,
  areas,
  is24Hour,
  onTimeFormatToggle
}: FilterBarProps) {
  return (
    <div className="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div className="flex flex-col gap-4 md:flex-row">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
          <Input
            type="text"
            placeholder="Search by mosque name or postcode..."
            value={searchQuery}
            onChange={(e) => onSearchChange(e.target.value)}
            className="border-gray-300 bg-white pl-10 dark:border-gray-600 dark:bg-gray-700"
          />
        </div>

        <div className="flex flex-col gap-2 sm:flex-row">
          <label className="relative">
            <MapPin className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <select
              value={selectedArea}
              onChange={(e) => onAreaChange(e.target.value)}
              className="h-10 min-w-[180px] appearance-none rounded-lg border border-gray-300 bg-white pl-10 pr-8 text-sm text-gray-900 outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
              <option value="all">All Areas</option>
              {areas.map((area) => (
                <option key={area} value={area}>
                  {area}
                </option>
              ))}
            </select>
          </label>

          <label className="relative">
            <SlidersHorizontal className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <select
              value={sortBy}
              onChange={(e) => onSortChange(e.target.value as 'distance' | 'name')}
              className="h-10 min-w-[180px] appearance-none rounded-lg border border-gray-300 bg-white pl-10 pr-8 text-sm text-gray-900 outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
            >
              <option value="distance">Sort by Distance</option>
              <option value="name">Sort by Name</option>
            </select>
          </label>

          <Button
            variant="outline"
            size="icon"
            onClick={onTimeFormatToggle}
            className="border-gray-300 bg-white hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600"
            title={is24Hour ? 'Switch to 12-hour format' : 'Switch to 24-hour format'}
          >
            {is24Hour ? <Clock className="h-4 w-4" /> : <Clock12 className="h-4 w-4" />}
          </Button>
        </div>
      </div>
    </div>
  );
}
