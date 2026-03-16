import { Moon, Sun } from 'lucide-react';
import { Button } from './ui/button';

interface HeaderProps {
  darkMode: boolean;
  onToggleDarkMode: () => void;
}

export function Header({ darkMode, onToggleDarkMode }: HeaderProps) {
  const currentDate = new Date().toLocaleDateString('en-GB', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });

  return (
    <header className="bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg dark:from-green-700 dark:to-green-800">
      <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between gap-4">
          <div>
            <h1 className="text-3xl font-bold">Prayer Times</h1>
            <p className="mt-1 text-green-100">{currentDate}</p>
          </div>
          <Button
            variant="ghost"
            size="icon"
            onClick={onToggleDarkMode}
            className="text-white hover:bg-green-700 dark:hover:bg-green-600"
            aria-label="Toggle dark mode"
          >
            {darkMode ? <Sun className="h-5 w-5" /> : <Moon className="h-5 w-5" />}
          </Button>
        </div>
      </div>
    </header>
  );
}
