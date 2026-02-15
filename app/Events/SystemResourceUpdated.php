<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SystemResourceUpdated implements ShouldBroadcast
{
  public string $resource;
  public string $action;
  public string $performedBy;
  public ?string $message;
  public bool $notifyAuthor;

  public function __construct(string $resource, string $action, string $performedBy, ?string $message = null, bool $notifyAuthor = false)
  {
    $this->resource = $resource;
    $this->action = $action;
    $this->performedBy = $performedBy;
    $this->message = $message;
    $this->notifyAuthor = $notifyAuthor;
  }

  public function broadcastOn(): array
  {
    $channels = [
      new Channel('system.resource.updated'),
    ];

    if ($this->notifyAuthor) {
      $channels[] = new PrivateChannel('App.Models.User.' . $this->performedBy);
    }

    return $channels;
  }

  public function broadcastAs(): string
  {
    return 'system.resource.updated';
  }

  public function broadcastWith(): array
  {
    return [
      'resource' => $this->resource,
      'action' => $this->action,
      'performedBy' => $this->performedBy,
      'message' => $this->message,
    ];
  }
}
