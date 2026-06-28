<?php

declare(strict_types=1);

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Types;

use ArrayIterator;
use Deprecated;
use IteratorAggregate;
use Traversable;

/**
 * A Struct describing dimensions (x, y, width, height).
 *
 * Provides some static methods to create common dimensions
 *
 * @author Loris Sigrist
 *
 * @implements IteratorAggregate<int, int|float>
 */
class Rect implements IteratorAggregate
{
    public function __construct(public int|float $x = 0, public int|float $y = 0, public int|float $width = 0, public int|float $height = 0)
    {
    }

    /**
     * A4 Portrait.
     */
    public static function A4(bool $portrait = false): self
    {
        $dim = new self();

        $dim->x = 0;
        $dim->y = 0;

        if ($portrait) {
            $dim->width = 210;
            $dim->height = 297;
        } else {
            $dim->width = 297;
            $dim->height = 210;
        }

        return $dim;
    }

    /**
     * Inset the dimensions by the given margin.
     */
    public function inset(float|int $margin): static
    {
        $this->x += $margin;
        $this->y += $margin;
        $this->width -= $margin * 2;
        $this->height -= $margin * 2;

        return $this;
    }

    public function offset(float|int $x, float|int $y): static
    {
        $this->x += $x;
        $this->y += $y;

        return $this;
    }

    #[Deprecated(message: 'use copy() instead')]
    public function clone(): self
    {
        return $this->copy();
    }

    public function copy(): self
    {
        return new self($this->x, $this->y, $this->width, $this->height);
    }

    /**
     * Returns an iterator with order x, y, width, height.
     *
     * @return Traversable<int, int|float>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            $this->x,
            $this->y,
            $this->width,
            $this->height,
        ]);
    }
}
