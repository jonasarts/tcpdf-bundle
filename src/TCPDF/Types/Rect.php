<?php

declare(strict_types=1);

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Types;

use ArrayIterator;
use Traversable;
use IteratorAggregate;

/**
 * A Struct describing dimensions (x, y, width, height)
 * 
 * Provides some static methods to create common dimensions
 * 
 * @author Loris Sigrist
 */
class Rect implements IteratorAggregate
{
    public int | float  $x;
    public int | float  $y;
    public int | float  $width;
    public int | float  $height;

    /**
     * @param int|float $x
     * @param int|float $y
     * @param int|float $width
     * @param int|float $height
     */
    public function __construct(int | float $x = 0, int | float $y = 0, int | float $width = 0, int | float $height = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    /**
     * A4 Portrait
     */
    public static function A4($portrait = false): Rect
    {
        $dim = new Rect();

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
     * Inset the dimensions by the given margin
     */
    public function inset($margin): static
    {
        $this->x += $margin;
        $this->y += $margin;
        $this->width -= $margin * 2;
        $this->height -= $margin * 2;

        return $this;
    }

    /**
     * @param $x
     * @param $y
     * @return $this
     */
    public function offset($x, $y): static
    {
        $this->x += $x;
        $this->y += $y;

        return $this;
    }

    /**
     * @return Rect
     */
    public function clone(): Rect
    {
        return new Rect($this->x, $this->y, $this->width, $this->height);
    }

    /**
     * Returns an iterator with order x, y, width, height
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator([
            $this->x,
            $this->y,
            $this->width,
            $this->height
        ]);
    }
}
