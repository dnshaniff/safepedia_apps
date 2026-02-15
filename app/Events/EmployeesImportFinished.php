<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmployeesImportFinished implements ShouldBroadcast
{
  public function broadcastOn(): Channel
  {
    return new Channel('employees-import');
  }

  public function broadcastAs(): string
  {
    return 'employees.import.finished';
  }
}
