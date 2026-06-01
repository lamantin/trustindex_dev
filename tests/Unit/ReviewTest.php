<?php

namespace App\Tests\Unit;

use App\Entity\Review;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
    public function testReviewEntityGettersAndSetters(): void
    {
        $review = new Review();

        $review->setCompanyName('Test Kft.')
            ->setRating(5)
            ->setReviewText('Kiváló szolgáltatás!')
            ->setAuthorEmail('test@example.com');

        $this->assertSame('Test Kft.', $review->getCompanyName());
        $this->assertSame(5, $review->getRating());
        $this->assertSame('Kiváló szolgáltatás!', $review->getReviewText());
        $this->assertSame('test@example.com', $review->getAuthorEmail());
    }

    public function testTimestampLifecycleCallback(): void
    {
        $review = new Review();
        $this->assertNull($review->getCreatedAt());

        $review->setInitialTimestamps();

        $this->assertInstanceOf(\DateTimeInterface::class, $review->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $review->getUpdatedAt());
    }
}
