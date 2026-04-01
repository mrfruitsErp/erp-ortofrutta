<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\DeliveryTimeSlot;

class DeliveryService
{
    public function calculate($order)
    {
        $client = $order->client;

        // ⏱️ tempo preparazione (modificabile)
        $prepMinutes = 480;

        $readyAt = Carbon::parse($order->date)->addMinutes($prepMinutes);

        $date = $readyAt->copy();

        while (true) {

            // ❌ giorni chiusura cliente
            if (in_array($date->dayOfWeek, $client->giorni_chiusura ?? [])) {
                $date->addDay();
                continue;
            }

            $slots = DeliveryTimeSlot::attivi()->get();

            foreach ($slots as $slot) {

                $slotTime = Carbon::parse(
                    $date->format('Y-m-d') . ' ' . $slot->orario_inizio
                );

                // ❌ slot troppo presto
                if ($slotTime->lt($readyAt)) {
                    continue;
                }

                // ❌ evita slot mattina se ordine tardivo
                if ($readyAt->hour > 2 && $slot->orario_inizio < '12:00') {
                    continue;
                }

                // 🔥 CONTROLLO CAPACITÀ
                $count = Order::whereDate('delivery_date', $date->format('Y-m-d'))
                    ->where('delivery_slot', $slot->id)
                    ->count();

                if ($count >= $slot->max_orders) {
                    continue; // slot pieno
                }

                return [
                    'date' => $date->format('Y-m-d'),
                    'slot_id' => $slot->id
                ];
            }

            // giorno successivo
            $date->addDay()->startOfDay();
        }
    }
}