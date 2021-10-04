<?php

namespace Illuminate\Contracts\Broadcasting;

interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]|string[]|string
=======
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
>>>>>>> 22c0e54 (table changes)
=======
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]|string[]|string
>>>>>>> f330c64 (optimization in progress)
     */
    public function broadcastOn();
}
