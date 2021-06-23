<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $order_status = ['pending', 'shipped', 'delivered'];

        foreach($order_status as $status) {
            $newStatus = Status::create([
                'name' => $status
            ]);

            $newStatus->save();
        }
    }
}
