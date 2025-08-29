<?php

namespace Tests\Unit;

use App\Models\Tour;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Str;

class TourModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function filter_tours_by_min_and_max_price()
    {
        // Arrange
        $min = 15000;
        $max = 25000;
        Tour::factory(12)->create();

        // Act
        $tours = Tour::filterByPrice($min, $max);

        // Assert
        $tours->each(
            function ($tour) use ($min, $max) {
                $this->assertGreaterThanOrEqual($min, $tour->price);
                $this->assertLessThanOrEqual($max, $tour->price);
            }
        );
    }

    #[Test]
    public function filter_tours_by_date()
    {
        // Arrange
        $date_min = Carbon::createFromDate(2025, 04, 15);
        $date_max = Carbon::createFromDate(2025, 07, 30);
        Tour::factory(50)->create();

        // Act
        $tours = Tour::filterByDates($date_min, $date_max);
        //dd($tours);

        // Assert
        $tours->each(
            function ($tour) use ($date_min, $date_max) {
                $date = new Carbon($tour->date);
                $this->assertGreaterThanOrEqual($date_min->timestamp, $date->timestamp);
                $this->assertLessThanOrEqual($date_max->timestamp, $date->timestamp);
            }
        );
    }

    #[Test]
    public function order_by_availability()
    {
        // Arrange
        $date_min = Carbon::createFromDate(2025, 04, 15);
        $date_max = Carbon::createFromDate(2025, 07, 30);
        Tour::factory(50)->create();

        // Act
        $tours = Tour::filterByDates($date_min, $date_max);
        //dd($tours);

        // Assert
        $tours->each(
            function ($tour) use ($date_min, $date_max) {
                $date = new Carbon($tour->date);
                $this->assertGreaterThanOrEqual($date_min->timestamp, $date->timestamp);
                $this->assertLessThanOrEqual($date_max->timestamp, $date->timestamp);
            }
        );
    }
}
