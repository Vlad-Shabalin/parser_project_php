<?php

declare(strict_types = 1);

function getTransactionFiles(string $dirPath): array {
    $files = []; // Initialize an empty array to store file paths

    // Loop through the files in the specified directory
    foreach (scandir($dirPath) as $file) {
        // Skip directories, only process files
        if (is_dir($file)) {
            continue;
        }

        // Add the file path to the $files array
        $files[] = $dirPath . $file;
    }

    // Return the array of file paths
    return $files;
}

function getTransactions(string $fileName, ?callable $transactionHandler = null): array {
    // Check if the file exists
    if (!file_exists($fileName)) {
        // If the file does not exist, trigger an error and halt execution
        trigger_error('File "' . $fileName . '" does not exist.', E_USER_ERROR);
    }

    // Open the file for reading
    $file = fopen($fileName, 'r');

    // Skip the first line (header) of the CSV file
    fgetcsv($file);

    // Initialize an empty array to store transactions
    $transactions = [];

    // Read the file line by line and convert each line to an array (CSV format)
    while (($transaction = fgetcsv($file)) !== false) {
        if($transactionHandler !== null){
            // If a transaction handler is provided, call it to process the transaction
            $transaction = extractTransaction($transaction);
        }

        // Add the transaction array to the $transactions array
        $transactions[] = $transaction;
    }

    // Close the file
    fclose($file);

    // Return the array of transactions
    return $transactions;
}

function extractTransaction(array $transactionRow): array {
    // Destructure the $transactionRow array into individual variables
    [$date, $checkNumber, $description, $amount] = $transactionRow;

    // Convert the amount to a float, removing any '$' or ',' characters
    $amount = (float) str_replace(['$', ','], '', $amount);

    // Return a new array with processed transaction data
    return [
        'date'         => $date,
        'checkNumber'  => $checkNumber,
        'description'  => $description,
        'amount'       => $amount,
    ];
}

function calculateTotals(array $transactions): array {
    // Initialize an array to store the totals
    $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    // Loop through each transaction in the $transactions array
    foreach ($transactions as $transaction) {
        // Check if the transaction amount is positive (income) or negative (expense)
        if ($transaction['amount'] >= 0) {
            // If positive, add the amount to totalIncome
            $totals['totalIncome'] += $transaction['amount'];
        } else {
            // If negative, add the amount to totalExpense
            $totals['totalExpense'] += $transaction['amount'];
        }

        // Update the netTotal using totalIncome and totalExpense
        $totals['netTotal'] = $totals['totalIncome'] + $totals['totalExpense'];
    }

    // Return the array of calculated totals
    return $totals;
}