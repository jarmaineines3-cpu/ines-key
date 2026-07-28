<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Console\Command;

class AssignAnnualWellnessLeaveCredits extends Command
{
    protected $signature = 'leave:assign-wellness-credits {--year=}';

    protected $description = 'Assign 5 wellness leave credits to all employees for the given year';

    public function handle(): int
    {
        $year = (int) ($this->option('year') ?: now()->year);
        $startOfYear = sprintf('%d-01-01', $year);
        $endOfYear = sprintf('%d-12-31', $year);

        $employees = Employee::query()->get();

        $count = 0;

        foreach ($employees as $employee) {
            $alreadyExists = Leave::query()
                ->where('employee_id', $employee->id)
                ->where('leave_type', 'wellness')
                ->where('as_of_date', '>=', $startOfYear)
                ->where('as_of_date', '<=', $endOfYear)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            Leave::create([
                'employee_id' => $employee->id,
                'leave_type' => 'wellness',
                'leave_credits' => 5,
                'as_of_date' => $startOfYear,
                'details' => 'Annual wellness leave credits',
            ]);

            $count++;
        }

        $this->info("Assigned wellness leave credits to {$count} employees for {$year}.");

        return self::SUCCESS;
    }
}
