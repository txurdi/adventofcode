# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Running Challenges

```bash
php bin/console txurdi:challenge-launch <YEAR> <DAY> <HALF> [TEST] [DEBUG]

# Examples:
php bin/console txurdi:challenge-launch 2025 3 1        # Run day 3, part 1, test set 1
php bin/console txurdi:challenge-launch 2025 3 1 0      # Use test set 0 (example input)
php bin/console txurdi:challenge-launch 2025 3 2 1 true # With debug output
```

Parameters: `TEST` defaults to `1` (main input); `0` is typically the example/sample input from the puzzle.

## Architecture

This is a Symfony 7.3 / PHP 8.4 app for solving Advent of Code puzzles.

### Adding a New Challenge

```bash
make new DAY=4 YEAR=2025
```

Esto crea `src/Challenges/{YEAR}/Year{YEAR}Day{DAY}Challenge.php` y los ficheros de datos vacíos.

Los challenges se organizan por año: `src/Challenges/Year2025/`, `src/Challenges/Year2026/`, etc.
El namespace sigue la misma estructura: `App\Challenges\Year2025`, `App\Challenges\Year2026`, etc.
`ChallengeFactory` auto-descubre las clases con el patrón `App\Challenges\Year{year}\Year{year}Day{day}Challenge`.

### Challenge Class Structure

```php
namespace App\Challenges\Year2025;

use App\Challenges\YearDayChallenge;

class Year2025DayXChallenge extends YearDayChallenge
{
    // Default format is DATA_FORMAT_LINES. Override only if needed:
    // protected string $format = fileDataHelper::DATA_FORMAT_STRING;
    // protected string $format = fileDataHelper::DATA_FORMAT_COLS;
    // protected string $format = fileDataHelper::DATA_FORMAT_CHARS;

    protected function executePart1(): void
    {
        // $this->data is the parsed input; set $this->result
    }

    protected function executePart2(): void
    {
        // same pattern
    }
}
```

### Data Formats (`fileDataHelper` constants)

| Constant | Value | Description |
|---|---|---|
| `DATA_FORMAT_STRING` | 1 | Entire file as one string (`$this->dataStr`) |
| `DATA_FORMAT_LINES` | 2 | Array of lines |
| `DATA_FORMAT_COLS` | 3 | 2D array split by whitespace/commas |
| `DATA_FORMAT_CHARS` | 4 | 2D array of characters per line |

### Data File Naming

```
src/Challenges/data/<YEAR>/day<DAY>H<HALF>T<TEST>.txt
```

Example: `day3H1T0.txt` = Day 3, Part 1, test variant 0 (the example from the puzzle).

### Utilities

- `src/util/fileDataHelper.php` — file reading and format conversion
- `src/util/tableDataHelper.php` — 2D array helpers (`getTableCol`, `getNumberRepeated`, `mapToString`, etc.)

## Docker

```bash
docker compose up --wait     # Start services
docker compose down          # Stop services
```

The app runs on ports 80/443 via FrankenPHP + Caddy. MySQL is available but rarely needed for puzzles.
