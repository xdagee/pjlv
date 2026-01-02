<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Services\LeaveCalculatorService;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeaveCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LeaveCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new LeaveCalculatorService();
    }

    #[Test]
    public function calculates_working_days_correctly_without_holidays()
    {
        // Monday to Friday (5 working days)
        $days = $this->calculator->calculateWorkingDays('2025-01-06', '2025-01-10');

        $this->assertEquals(5, $days);
    }

    #[Test]
    public function excludes_weekends_from_calculation()
    {
        // Monday to Sunday (should be 5 working days, excluding Sat-Sun)
        $days = $this->calculator->calculateWorkingDays('2025-01-06', '2025-01-12');

        $this->assertEquals(5, $days);
    }

    #[Test]
    public function excludes_public_holidays()
    {
        // Create a holiday on Wednesday
        Holiday::create([
            'date' => '2025-01-08',
            'name' => 'Test Holiday',
        ]);

        // Monday to Friday, but Wednesday is holiday (4 working days)
        $days = $this->calculator->calculateWorkingDays('2025-01-06', '2025-01-10');

        $this->assertEquals(4, $days);
    }

    #[Test]
    public function excludes_both_weekends_and_holidays()
    {
        // Create holiday on Monday
        Holiday::create([
            'date' => '2025-01-06',
            'name' => 'Test Holiday',
        ]);

        // Mon(holiday) to Sun (should be 4 working days: Tue, Wed, Thu, Fri)
        $days = $this->calculator->calculateWorkingDays('2025-01-06', '2025-01-12');

        $this->assertEquals(4, $days);
    }

    #[Test]
    public function returns_zero_for_invalid_date_range()
    {
        // End date before start date
        $days = $this->calculator->calculateWorkingDays('2025-01-10', '2025-01-06');

        $this->assertEquals(0, $days);
    }

    #[Test]
    public function handles_single_day_correctly()
    {
        // Single weekday
        $days = $this->calculator->calculateWorkingDays('2025-01-06', '2025-01-06');

        $this->assertEquals(1, $days);
    }

    #[Test]
    public function single_weekend_day_returns_zero()
    {
        // Saturday
        $days = $this->calculator->calculateWorkingDays('2025-01-11', '2025-01-11');

        $this->assertEquals(0, $days);
    }

    #[Test]
    public function calculates_long_period_correctly()
    {
        // Full month (January 2025: 31 days, 23 working days)
        $days = $this->calculator->calculateWorkingDays('2025-01-01', '2025-01-31');

        // Jan 2025: 23 working days (excluding weekends)
        $this->assertEquals(23, $days);
    }
}
