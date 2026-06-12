<?php
$user = \App\Models\User::where('name', 'Dr. Arafat Hossain')->first();
$payout = \App\Models\EmployeePayout::where('user_id', $user->id)->where('month', '2026-06')->first();
dump($payout ? $payout->toArray() : 'No payout found for June');
