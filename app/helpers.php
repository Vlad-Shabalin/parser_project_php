<?php

declare(strict_types = 1);

function formatDollarAmount(float $amount): string {
    // Check if the amount is negative
    $isNegative = $amount < 0;

    // Construct the formatted string
    return ($isNegative ? '-' : '') . '$' . number_format(abs($amount), 2);
}

function formatDate(string $date): string{
    return date('M j, Y', strtotime($date));
}