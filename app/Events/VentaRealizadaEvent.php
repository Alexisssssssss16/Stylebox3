<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VentaRealizadaEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sale;
    public $stats;

    /**
     * Create a new event instance.
     */
    public function __construct($sale, array $stats = [])
    {
        $this->sale = $sale;
        $this->stats = $stats;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('vendedor.' . $this->sale->user_id),
            new Channel('stats-channel'), // Mantener canal público para Admin
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'venta.realizada';
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'sale' => [
                'id' => $this->sale->id,
                'total' => number_format($this->sale->total, 2),
                'client' => $this->sale->client?->name ?? 'General',
                'method' => $this->sale->payments->first()?->paymentMethod->name ?? 'N/A',
                'time' => $this->sale->created_at->diffForHumans(),
                'hora' => $this->sale->created_at->format('H:i'),
                'estado' => $this->sale->estado
            ],
            'stats' => $this->stats
        ];
    }
}
