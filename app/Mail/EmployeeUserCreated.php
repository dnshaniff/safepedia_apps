<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeUserCreated extends Mailable implements ShouldQueue
{
  use Queueable, SerializesModels;

  public $user;
  public $password;

  public function __construct($user, $password)
  {
    $this->user = $user;
    $this->password = $password;
  }

  public function build()
  {
    return $this
      ->subject('Your Account Has Been Created')
      ->markdown('emails.employee.user-created');
  }
}
